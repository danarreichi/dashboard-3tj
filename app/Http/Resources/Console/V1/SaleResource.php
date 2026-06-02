<?php

namespace App\Http\Resources\Console\V1;

use App\Models\User;
use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        $hpp = $this->qty * $this->getPropWhenLoaded('price', 'hpp');
        if (Auth::user()->user_role_id == User::ROLE_USER) $hpp = 0;
        $price = ($this->qty * $this->getPropWhenLoaded('price', 'price')) - $hpp;
        return [
            'qty' => $this->qty,
            'hpp' => "Rp" . number_format($hpp, 2, ",", "."),
            'price_per_unit' => "Rp" . number_format($this->getPropWhenLoaded('price', 'price'), 2, ",", "."),
            'sales_sum' => "Rp" . number_format($price, 2, ",", "."),
            'updated_at' => $this->updated_at,
        ];
    }
}
