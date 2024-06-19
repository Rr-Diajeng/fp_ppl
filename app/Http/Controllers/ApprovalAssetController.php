<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetUpdate;
use App\Http\Resources\AssetResource;
use Egulias\EmailValidator\Result\Reason\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApprovalAssetController extends Controller
{
    public function index()
    {
        $approvalAsset = AssetUpdate::where('status', 'Pending')->orderBy('created_at', 'desc')->get();

        return view('dashboard.approvalAsset', compact('approvalAsset'));
    }

    public function approve(Request $request, $slug){
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $assetUpdate = AssetUpdate::where('slug', $slug)->first();

        if (!$assetUpdate) {
            return redirect()->route('approvalAsset')->with('error', 'Asset update not found.');
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
                return redirect()->route('approvalAsset')->with('error', 'Asset not found.');
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

            return redirect()->route('approvalAsset')->with('success', 'Successfully approved asset update.');
        } elseif ($request->status === 'Rejected') {
            // $validated = $request->validate([
            //     'reason' => 'required',
            // ]);

            $assetUpdate->status = $request->status;
            // $assetUpdate->reason = $validated['reason'];
            $assetUpdate->save();

            return redirect()->route('approvalAsset')->with('success', 'Successfully rejected asset update.');
        } else {
            return redirect()->route('approvalAsset')->with('error', 'Invalid status.');
        }
    }
    public function edit($slug){
        $assetApproval = Asset::where('slug', $slug)->first();

        return view('dashboard.assetUpdatePIC', compact('assetApproval'));
    }

    public function store(Request $request, $slug){

        $asset = Asset::where('slug', $slug)->first();
        $id_asset = $asset->id;

        $user_id = Auth::id();

        $validated = $request->validate([
            'namaAsset' => 'required|max:100',
            'description' => 'required',
            'depreciation' => 'required|numeric',
            'assetImage' => 'image|nullable',
            'purchaseDate' => 'required',
            'price' => 'required|numeric',
            'reason' => 'required',
        ]);

        if ($request->hasFile('assetImage')) {
            $path = $request->file('assetImage')->store('assetImages', 'public');
            $validated['assetImage'] = $path;
        }
        else {
            $validated['assetImage'] = $request->input('existingAssetImage');
        }

        $slug = Str::slug($validated['namaAsset']);
        $counter = 1;

        while (Asset::where('slug', $slug)->where('namaAsset', '!=', $validated['namaAsset'])->exists()) {
            $slug = Str::slug($validated['namaAsset']) . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['purchaseDate'] = Carbon::parse($validated['purchaseDate'])->format('Y-m-d H:i:s');

        try {
            // dd($validated);
            // Generate QR Code
            $qrCode = QrCode::format('png')->size(200)->generate(route('assets.show', $slug));
            $qrCodePath = 'QRCode/' . $slug . '.jpg';

            if ($asset->QRCode) {
                Storage::delete('public/' . $asset->QRCode);
            }

            Storage::put('public/' . $qrCodePath, $qrCode);
            $validated['QRCode'] = $qrCodePath;

            // AssetUpdate::create([
            //     'namaAsset' => $validated['namaAsset'],
            //     'description' => $validated['description'],
            //     'depreciation' => $validated['depreciation'],
            //     'assetImage' => $validated['assetImage'],
            //     'price' => $validated['price'],
            //     'purchaseDate' => $validated['purchaseDate'],
            //     'QRCode' => $validated['QRCode'],
            //     'slug' => $validated['slug'],
            //     'status'=> 'Pending',
            //     'reason'=> $validated['reason'],
            //     'assetID'=> $id_asset
            // ]);

            try {
                $assetUpdate = new AssetUpdate();
                $assetUpdate->namaAsset = $validated['namaAsset'];
                $assetUpdate->description = $validated['description'];
                $assetUpdate->depreciation = $validated['depreciation'];
                $assetUpdate->assetImage = $validated['assetImage'];
                $assetUpdate->price = $validated['price'];
                $assetUpdate->purchaseDate = $validated['purchaseDate'];
                $assetUpdate->QRCode = $validated['QRCode'];
                $assetUpdate->slug = $validated['slug'];
                $assetUpdate->status = 'Pending';
                $assetUpdate->reason = $validated['reason'];
                $assetUpdate->assetID = $id_asset;

                if (Auth::check()) { // Memastikan bahwa pengguna sudah login
                    $assetUpdate->UpdatedBy = $user_id;
                }

                $assetUpdate->save();

                // return response()->json(['message' => 'Asset updated successfully']);
                return redirect()->route('dashboard')->with('success', 'Berhasil melakukan request');
            } catch (\Exception $e) {
                Log::error("Error updating asset: " . $e->getMessage());
                dd($e-> getMessage());
                return response()->json(['error' => 'Failed to update asset', 'message' => $e->getMessage()], 500);
            }

            // return redirect()->route('dashboard')->with('success', 'Berhasil melakukan request');

        } catch (\Exception $e) {
            return redirect()-> route('dashboard')-> with('error', 'Gagal melakukan request');
        }
    }

    public function history(){
        $user_id = Auth::id();

        $approvalAsset = AssetUpdate::where('UpdatedBy', $user_id)
        -> orderByDesc('created_at')
        -> get();

        return view('dashboard.historyAssetPIC', compact('approvalAsset'));
    }
}
