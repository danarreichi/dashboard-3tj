<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
        $price = ($this->qty * $this->getPropWhenLoaded('price', 'price'));
        return [
            'qty' => $this->qty,
            'price_per_unit' => $this->getPropWhenLoaded('price', 'price'),
            'sales_sum' => $price,
            'updated_at' => $this->updated_at,
        ];
    }
}
