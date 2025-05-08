<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = ['name' => 'Super Admin', 'email' => 'admin@admin.admin', 'password' => Hash::make('admin@123456')];
        User::query()->firstOrCreate($user);
    }
}
