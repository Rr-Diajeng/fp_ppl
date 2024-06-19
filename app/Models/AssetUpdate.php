<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assets_updates';

    protected $primaryKey = 'slug';

    public $incrementing = false;

    protected $fillable = [
        'UpdateID',
        'namaAsset',
        'description',
        'depreciation',
        'assetImage',
        'price',
        'purchaseDate',
        'QRCode',
        'slug',
        'status',
        'reason',
        'assetID',
        'UpdatedBy',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'assetID', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UpdatedBy', 'id');
    }
}
