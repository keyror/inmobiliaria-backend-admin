<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'password' => Hash::make('123456789'),
            'email' => 'camilomancipe@outlook.com',
        ]);

        User::create([
            'email' => 'jhon.doe@example.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'email' => 'maria.perez@example.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'email' => 'carlos.sanchez@example.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'email' => 'ana.lopez@example.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'email' => 'pedro.ramirez@example.com',
            'password' => Hash::make('123456789'),
        ]);
    }
}
