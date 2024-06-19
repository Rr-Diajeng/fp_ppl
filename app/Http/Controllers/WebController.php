<?php

namespace App\Http\Controllers;

use Alert;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\AssetUpdate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\AssetResource;

class WebController extends Controller
{
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

            Asset::create($validated);
                return redirect()->route('dashboard')->with('success', 'Data asset berhasil ditambahkan');
            } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Data asset gagal ditambahkan');
        }
    }

    public function show($slug){
        $asset = Asset::where('slug', $slug)->first();

        return view('dashboard.assetUpdate', compact('asset'));
    }
    public function index()
    {
        $assets = Asset::orderByDesc('created_at')->get();
        $posts = AssetResource::collection($assets);
        $title = 'Delete Asset Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view('dashboard.index', compact('posts'));
    }

    public function create()
    {
        return view('dashboard.create_asset');
    }

    public function edit($slug){
        $asset = Asset::where('slug', $slug)->first();

        return view('dashboard.assetUpdate', compact('asset'));
    }

    public function update(Request $request, $asset){

        $asset = Asset::where('slug', $asset)->first();
        $validated = $request->validate([
            'namaAsset' => 'required|max:100',
            'description' => 'required',
            'depreciation' => 'required|numeric',
            'assetImage' => 'image|nullable',
            'purchaseDate' => 'required',
            'price' => 'required|numeric',
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
            // Generate QR Code
            $qrCode = QrCode::format('png')->size(200)->generate(route('assets.show', $slug));
            $qrCodePath = 'QRCode/' . $slug . '.jpg';

            if ($asset->QRCode) {
                Storage::delete('public/' . $asset->QRCode);
            }

            Storage::put('public/' . $qrCodePath, $qrCode);
            $validated['QRCode'] = $qrCodePath;

            $asset-> update([
                'namaAsset' => $validated['namaAsset'],
                'description' => $validated['description'],
                'depreciation' => $validated['depreciation'],
                'slug' => $validated['slug'],
                'purchaseDate' => $validated['purchaseDate'],
                'price' => $validated['price'],
                'assetImage' => $validated['assetImage'],
                'QRCode' => $validated['QRCode'],
            ]);

            return redirect()->route('dashboard')->with('success', 'Data asset berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()-> route('dashboard')-> with('error', 'Data asset gagal diupdate');
        }
    }
    public function remove($slug){
        try {
            $asset = Asset::where('slug', $slug)->first();

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
                alert()->success('Asset berhasil dihapus.', 'Sukses!');
            });
            return redirect()->route('dashboard')->with('success', 'Asset berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal menghapus asset.');
        }
    }

    public function history(){
        $approvalAsset = AssetUpdate::where('status', 'approved')
            ->orWhere('status', 'rejected')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.historyAsset', compact('approvalAsset'));
    }
}
