<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\AssetUpdate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(){
        $asset = Asset::all();
        $post = AssetResource::collection($asset);

        try {
            return response()->json([
                "status" => [
                    "code" => 200,
                    "message" => "Berhasil menampilkan data asset",
                ],
                "data" => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => [
                    "code" => 404,
                    "message" => "Data asset tidak ditemukan",
                ],
                "data" => null
            ], 404);
        }
    }

    public function store(Request $request){
        $validated = $request->validate([
            'namaAsset' => 'required|max:100',
            'description' => 'required',
            'depreciation' => 'required|numeric',
            'assetImage' => 'required|image',
            'purchaseDate' => 'required',
            'price' => 'required|numeric',
        ]);

        $slug = Str::slug($validated['namaAsset']);
        $counter = 1;

        while (Asset::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['namaAsset']) . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['purchaseDate'] = Carbon::parse($validated['purchaseDate'])->format('Y-m-d H:i:s');

        try {
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

            $assetPost = Asset::create($validated);
            $post = new AssetResource($assetPost);

            return response()->json([
                "status" => [
                    "code" => 200,
                    "message" => "Berhasil menambahkan data asset",
                ],
                "data" => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => [
                    "code" => 500,
                    "message" => "Terjadi kesalahan saat menambahkan data asset",
                ],
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show($slug){
        try {
            $asset = Asset::where('slug', $slug)->first();
            if ($asset) {
                $assetResource = new AssetResource($asset);
                return response()->json([
                    "status" => [
                        "code" => 200,
                        "message" => "Berhasil menampilkan data asset",
                    ],
                    "data" => $assetResource
                ], 200);
            } else {
                return response()->json([
                    "status" => [
                        "code" => 404,
                        "message" => "Data asset tidak ditemukan",
                    ],
                    "data" => null
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => [
                    "code" => 500,
                    "message" => "Terjadi kesalahan saat menampilkan data asset",
                ],
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'namaAsset' => 'required|max:100',
            'description' => 'required',
            'depreciation' => 'required|numeric',
            'assetImage' => 'required|image',
            'price' => 'required|numeric',
            'purchaseDate' => 'required'
        ]);

        $slug = Str::slug($validated['namaAsset']);
        $counter = 1;

        while (Asset::where('slug', $slug)->where('id', '!=', $asset->id)->exists()) {
            $slug = Str::slug($validated['namaAsset']) . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['purchaseDate'] = Carbon::parse($validated['purchaseDate'])->format('Y-m-d H:i:s');

        try {
            // Generate QR Code
            $qrCode = QrCode::format('png')->size(200)->generate(route('assets.show', $slug));
            $qrCodePath = 'QRCode/' . $slug . '.jpg';

            if ($asset->QRCode) {
                Storage::delete('public/' . $asset->QRCode);
            }

            Storage::put('public/' . $qrCodePath, $qrCode);
            $validated['QRCode'] = $qrCodePath;

            // Update assetImage
            $assetImage = $request->file('assetImage');
            $assetImagePath = 'assetImage/' . $slug . '.jpg';

            if ($asset->assetImage) {
                Storage::delete('public/' . $asset->assetImage);
            }

            Storage::putFileAs('public/', $assetImage, $assetImagePath);
            $validated['assetImage'] = $assetImagePath;

            $asset->update($validated);
            $post = new AssetResource($asset);

            return response()->json([
                "status" => [
                    "code" => 200,
                    "message" => "Berhasil mengubah data asset",
                ],
                "data" => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => [
                    "code" => 500,
                    "message" => "Terjadi kesalahan saat mengubah data asset",
                ],
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Asset $asset){
        try {
            DB::transaction(function () use ($asset) {
                // Delete the QR Code file
                if ($asset->QRCode) {
                    Storage::delete('public/' . $asset->QRCode);
                }

                // Delete the assetImage file
                if ($asset->assetImage) {
                    Storage::delete('public/' . $asset->assetImage);
                }

                // Delete data from AssetUpdate
                AssetUpdate::where('assetID', $asset->id)->delete();

                $asset->delete();
            });

            return response()->json([
                "status" => [
                    "code" => 200,
                    "message" => "Berhasil menghapus data asset",
                ],
                "data" => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => [
                    "code" => 500,
                    "message" => "Terjadi kesalahan saat menghapus data asset",
                ],
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
