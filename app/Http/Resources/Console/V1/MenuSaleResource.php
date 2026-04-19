<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuSaleResource extends JsonResource
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
        $sales = $this->whenLoaded('sales');
        $sumSales = (count($sales) > 0) ? $sales->sum(fn ($sale) => $sale->qty * $sale->price->price) : 0;
        $countSales = (count($sales) > 0) ? $sales->sum(fn ($sale) => $sale->qty) : 0;
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'category' => $this->getPropWhenLoaded('category', 'name'),
            'image' => $this->getPropWhenLoaded('image', 'path'),
            'sales_sum' => "Rp" . number_format($sumSales, 2, ",", "."),
            'sales_count' => $countSales,
            'updated_at' => $this->updated_at,
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
