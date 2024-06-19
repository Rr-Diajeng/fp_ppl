<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetUpdate;

class AseetsSeederUpdate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assetUpdate = [
            [
                'namaAsset' => 'Asset 1',
                'description' => 'Asset 1 description',
                'depreciation' => 1000000,
                'assetImage' => 'asset1.jpg',
                'price' => 10000000,
                'purchaseDate' => '2022-01-01',
                'QRCode' => 'QRCode1',
                'slug' => 'asset-1',
                'status' => 'Pending',
                'reason' => 'Update nama asset',
                'assetID' => 1,
                'UpdatedBy' => 2,
            ],
            [
                'namaAsset' => 'Asset 2',
                'description' => 'Asset 2 description',
                'depreciation' => 2000000,
                'assetImage' => 'asset2.jpg',
                'price' => 20000000,
                'purchaseDate' => '2022-01-02',
                'QRCode' => 'QRCode2',
                'slug' => 'asset-2',
                'status' => 'Pending',
                'reason' => 'Update nama asset',
                'assetID' => 2,
                'UpdatedBy' => 3,
            ],
            [
                'namaAsset' => 'Asset 3',
                'description' => 'Asset 3 description',
                'depreciation' => 3000000,
                'assetImage' => 'asset3.jpg',
                'price' => 30000000,
                'purchaseDate' => '2022-01-03',
                'QRCode' => 'QRCode3',
                'slug' => 'asset-3',
                'status' => 'Pending',
                'reason' => 'Update nama asset',
                'assetID' => 1,
                'UpdatedBy' => 4,
            ],
        ];


        foreach ($assetUpdate as $asset) {
            AssetUpdate::create($asset);
        }
    }
}
