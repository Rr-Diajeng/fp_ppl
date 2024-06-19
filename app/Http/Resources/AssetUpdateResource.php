<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AssetUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'UpdateID' => $this->UpdateID,
            'namaAsset' => $this->namaAsset,
            'description' => $this->description,
            'depreciation' => $this->depreciation,
            'assetImage' => Storage::url($this->assetImage),
            'price' => $this->price,
            'purchaseDate' => Carbon::parse($this->purchaseDate)->format('Y-m-d H:i:s'),
            'QRCode' => Storage::url($this->QRCode),
            'slug' => $this->slug,
            'status' => $this->status,
            'reason' => $this->reason,
            'assetID' => $this->whenLoaded('asset'),
            'UpdatedBy' => $this->whenLoaded('user'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
