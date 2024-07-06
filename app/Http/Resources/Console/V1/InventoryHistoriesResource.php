<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryHistoriesResource extends JsonResource
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
            'name' => $this->getPropWhenLoaded('inventory', 'name') ?? $this->getPropWhenLoaded('user', 'name'),
            'status' => $this->status,
            'qty' => $this->qty,
            'old_qty' => $this->payload['old_qty'],
            'new_qty' => $this->payload['new_qty'],
            'created_at' => $this->created_at,
        ];
    }
}
