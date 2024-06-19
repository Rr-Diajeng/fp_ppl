<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'namaAsset',
        'description',
        'depreciation',
        'assetImage',
        'price',
        'purchaseDate',
        'QRCode',
        'slug',
    ];
}
