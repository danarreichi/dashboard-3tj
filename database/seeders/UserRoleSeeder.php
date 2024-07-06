<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserRole::updateOrCreate([
            'id' => 'admin'
        ], [
            'name' => 'Admin'
        ]);

        UserRole::updateOrCreate([
            'id' => 'user'
        ], [
            'name' => 'User'
        ]);
    }
}
