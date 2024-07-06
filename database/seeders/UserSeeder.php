<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'name' => 'Admin'
        ], [
            'user_role_id' => 'admin',
            'username' => 'admin',
            'password' => Hash::make('admin')
        ]);

        User::updateOrCreate([
            'name' => 'Danar'
        ], [
            'user_role_id' => 'user',
            'username' => 'danar',
            'password' => Hash::make('danar')
        ]);
    }
}
