<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountHistoriesResource extends JsonResource
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
            'name' => $this->getPropWhenLoaded('inventory', 'name'),
            'status' => $this->status,
            'qty' => $this->qty . $this->getPropWhenLoaded('inventory', 'unit'),
            'price' => "Rp" . number_format($this->price, 2, ",", "."),
            'old_qty' => $this->payload['old_qty'] . $this->getPropWhenLoaded('inventory', 'unit'),
            'new_qty' => $this->payload['new_qty'] . $this->getPropWhenLoaded('inventory', 'unit'),
            'created_at' => $this->created_at,
        ];
    }
}
