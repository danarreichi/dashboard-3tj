<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveMenuPriceResource extends JsonResource
{
    use RelationShortcut;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'revision' => $this->revision,
            'price' => "Rp" . number_format($this->price, 2, ",", "."),
            'status' => $this->status,
            'stock_remaining' => $this->stock_remaining,
            'availability' => ($this->availability) ? true : false,
            'updated_at' => $this->updated_at,
            'name' => $this->getPropWhenLoaded('menu', 'name'),
            'image' => $this->getPropWhenLoaded('menu.image', 'path'),
        ];
    }
}
