<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::create([
        'name' => 'Admin Dinkop',
        'email' => 'admin@proyekumkm.com',
        'password' => Hash::make('KediriMapan2025'), // Ganti dengan pas
        ]);
    }
}
