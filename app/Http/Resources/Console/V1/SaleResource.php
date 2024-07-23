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
            'price_per_unit' => "Rp" . number_format($this->getPropWhenLoaded('price', 'price'), 2, ",", "."),
            'sales_sum' => "Rp" . number_format($price, 2, ",", "."),
            'updated_at' => $this->updated_at,
        ];
    }
}
