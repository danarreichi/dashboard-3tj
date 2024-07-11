<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->getPropWhenLoaded('category', 'name'),
            'category_uuid' => $this->getPropWhenLoaded('category', 'uuid'),
            'image' => $this->getPropWhenLoaded('image', 'path'),
            'price' => $this->whenLoaded('price'),
            'updated_at' => $this->updated_at,
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
