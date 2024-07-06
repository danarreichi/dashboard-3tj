<?php

namespace App\Http\Resources\Console\V1;

use App\Traits\RelationShortcut;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->getPropWhenLoaded('userRole', 'name'),
            'role_id' => $this->getPropWhenLoaded('userRole', 'id'),
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'updated_at' => $this->updated_at,
        ];
    }
}
