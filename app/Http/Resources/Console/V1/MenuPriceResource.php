<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuPriceResource extends JsonResource
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
        // Calculate the total per serving price
        $totalPerServingPrice = $this->whenLoaded('recipes')->sum(function($recipe) {
            return $recipe->getPropWhenLoaded('history', 'price') / $recipe->getPropWhenLoaded('history', 'qty') * $recipe->qty;
        });

        return [
            'uuid' => $this->uuid,
            'revision' => $this->revision,
            'price' => "Rp" . number_format($this->price, 2, ",", "."),
            'total_per_serving_price' => "Rp" . number_format($totalPerServingPrice, 2, ",", "."),
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'recipes' => MenuRecipeResource::collection(
                $this->whenLoaded('recipes')->sortBy(function ($recipe) {
                    return $recipe->getPropWhenLoaded('history.inventory', 'name');
                }
            )),
        ];
    }
}
