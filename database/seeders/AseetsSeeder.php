<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AseetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            [
                'namaAsset' => 'Laptop ROG Zephyrus RTX 3080',
                'description' => 'Laptop gaming dengan spesifikasi tinggi',
                'depreciation' => '10',
                'assetImage' => 'laptop.jpg',
                'price' => '10000000',
                'purchaseDate' => '2021-01-01',
                'qrCode' => 'Laptop-2021-01-01',
                'slug' => 'laptop-rog-zephyrus-rtx-3080'
            ],
            [
                'namaAsset' => 'Monitor LG 4K 32"',
                'description' => 'Monitor 4K dengan refresh rate tinggi',
                'depreciation' => '5',
                'assetImage' => 'monitor.jpg',
                'price' => '5000000',
                'purchaseDate' => '2021-01-01',
                'qrCode' => 'Monitor-2021-01-01',
                'slug' => 'monitor-lg-4k-32'
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
