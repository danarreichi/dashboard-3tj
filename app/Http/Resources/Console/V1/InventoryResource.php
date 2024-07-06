<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'unit' => $this->unit,
            'qty' => $this->qty,
            'updated_at' => $this->updated_at,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'recent_history' => $this->whenLoaded('histories'),
        ];
    }
}
