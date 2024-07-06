<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleDropdownResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
