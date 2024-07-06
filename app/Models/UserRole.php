<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends BaseModel
{
    use HasFactory;

    // Disable auto-incrementing IDs
    public $incrementing = false;

    // Specify the key type as 'string'
    protected $keyType = 'string';

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
