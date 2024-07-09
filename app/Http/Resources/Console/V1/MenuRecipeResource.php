<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuRecipeResource extends JsonResource
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
            'name' => $this->getPropWhenLoaded('history.inventory', 'name'),
            'qty' => $this->qty,
            'unit' => $this->getPropWhenLoaded('history.inventory', 'unit'),
            'per_serving_price' => "Rp" . number_format($this->getPropWhenLoaded('history', 'price') / $this->getPropWhenLoaded('history', 'qty') * $this->qty, 2, ",", "."),
        ];
    }
}
