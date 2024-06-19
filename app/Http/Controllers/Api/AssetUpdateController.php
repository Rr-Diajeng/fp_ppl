<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use App\Models\AssetUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AssetUpdateResource;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class AssetUpdateController extends Controller {
    public function index() {
        try {
            // Dapatkan semua data update asset dengan relasi asset dan user yang mengupdate
            $assetUpdate = AssetUpdate::with('asset:id,namaAsset', 'user:id,name,role')->get();
            $assetUpdateResource = AssetUpdateResource::collection($assetUpdate);
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Berhasil menampilkan data asset update',
                ],
                'data' => $assetUpdateResource,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat menampilkan data asset update',
                ],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDataByPIC(Request $request) {
        $validated = $request->validate([
            'namaAsset' => 'required|max:100',
            'description' => 'required',
            'depreciation' => 'required|numeric',
            'assetImage' => 'required|image',
            'purchaseDate' => 'required',
            'price' => 'required|numeric',
            'slug' => 'required|exists:assets,slug',
        ]);

        // Periksa apakah asset dengan slug yang diberikan ada atau tidak
        $asset = Asset::where('slug', $validated['slug'])->first();

        if (!$asset) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Asset dengan slug yang diberikan tidak ditemukan.',
                ]
            ], 404);
        }

        // Periksa apakah nama aset diubah atau tidak
        if ($asset->namaAsset !== $validated['namaAsset']) {
            $slug = Str::slug($validated['namaAsset']);
            $count = 1;
            while (AssetUpdate::where('slug', $slug)->exists()) {
                $slug = Str::slug($validated['namaAsset']) . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        } else {
            $slug = $asset->slug;
            $count = 1;
            while (AssetUpdate::where('slug', $slug)->exists()) {
                $slug = Str::slug($validated['namaAsset']) . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        // Generate QR Code
        $qrCode = QrCode::format('png')->size(200)->generate(route('assets.show', $slug));
        $qrCodePath = 'QRCode/' . $slug . '.jpg';

        Storage::put('public/' . $qrCodePath, $qrCode);
        $validated['QRCode'] = $qrCodePath;

        // Upload assetImage
        $assetImage = $request->file('assetImage');
        $assetImagePath = 'assetImage/' . $slug . '.jpg';

        Storage::putFileAs('public/', $assetImage, $assetImagePath);
        $validated['assetImage'] = $assetImagePath;

        // Simpan data update asset
        $validated['purchaseDate'] = Carbon::parse($validated['purchaseDate'])->format('Y-m-d H:i:s');
        $validated['status'] = 'Pending';
        $validated['reason'] = $request->reason;
        $validated['assetID'] = $asset->id;
        $validated['UpdatedBy'] = auth()->user()->id;

        $update = AssetUpdate::create($validated);
        $post = new AssetUpdateResource($update);

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Berhasil mengajukan update data asset, Tunggu konfirmasi admin untuk melanjutkan proses selanjutnya',
            ],
            'data' => $post,
        ]);
    }

    public function update(Request $request, $slug){
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $assetUpdate = AssetUpdate::where('slug', $slug)->first();

        if (!$assetUpdate) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Asset update not found.',
                ],
            ], 404);
        }

        if ($request->status === 'Approved') {
            $asset = Asset::find($assetUpdate->assetID);

            // Check if asset slug exists in the asset table, then increment the slug
            $slugCount = Asset::where('slug', $assetUpdate->slug)->count();
            if ($slugCount > 0) {
                $assetUpdate->slug = $assetUpdate->slug . '-' . ($slugCount + 1);
                // Change the qr code and asset image to the new slug
                $qrCode = QrCode::format('png')->size(200)->generate(route('assets.show', $assetUpdate->slug));
                $qrCodePath = 'QRCode/' . $assetUpdate->slug . '.jpg';
                if ($asset->QRCode) {
                    Storage::delete('public/' . $asset->QRCode);
                }
                Storage::put('public/' . $qrCodePath, $qrCode);
                $assetUpdate->QRCode = $qrCodePath;

                $assetImage = $request->file('assetImage');
                $assetImagePath = 'assetImage/' . $assetUpdate->slug . '.jpg';
                if ($asset->assetImage) {
                    Storage::delete('public/' . $asset->assetImage);
                }
                Storage::putFileAs('public/', $assetImage, $assetImagePath);
                $assetUpdate->assetImage = $assetImagePath;
            }

            if (!$asset) {
                return response()->json([
                    'status' => [
                        'code' => 404,
                        'message' => 'Asset not found.',
                    ],
                ], 404);
            }

            $asset->update([
                'namaAsset' => $assetUpdate->namaAsset,
                'description' => $assetUpdate->description,
                'depreciation' => $assetUpdate->depreciation,
                'purchaseDate' => $assetUpdate->purchaseDate,
                'price' => $assetUpdate->price,
                'slug' => $assetUpdate->slug,
                'QRCode' => $assetUpdate->QRCode,
                'assetImage' => $assetUpdate->assetImage,
            ]);

            $assetUpdate->status = $request->status;
            $assetUpdate->save();

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Successfully updated asset update data.',
                ],
                'data' => $assetUpdate,
            ]);
        } elseif ($request->status === 'Rejected') {
            $validated = $request->validate([
                'reason' => 'required',
            ]);

            $assetUpdate->status = $request->status;
            $assetUpdate->reason = $validated['reason'];
            $assetUpdate->save();

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Successfully rejected asset update.',
                ],
                'data' => $assetUpdate,
            ]);
        } else {
            return response()->json([
                'status' => [
                    'code' => 400,
                    'message' => 'Invalid status.',
                ],
            ], 400);
        }
    }

    public function indexByPIC() {
        try {
            $assetUpdate = AssetUpdate::where('UpdatedBy', auth()->user()->id)->with('asset:id,namaAsset', 'user:id,name,role')->get();
            $assetUpdateResource = AssetUpdateResource::collection($assetUpdate);
            if ($assetUpdate->isEmpty()) {
                return response()->json([
                    'status' => [
                        'code' => 404,
                        'message' => 'Data history data asset update tidak ditemukan.',
                    ],
                ], 404);
            }
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Berhasil menampilkan history data approval asset update',
                ],
                'data' => $assetUpdateResource,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat menampilkan data asset update',
                ],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
