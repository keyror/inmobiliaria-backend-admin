<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory(['status']);
        $statusId = $lookups->get('status')?->first()?->id ?? null;

        $usersData = [
            ['email' => 'camilomancipe@outlook.com', 'password' => '123456789a', 'role' => 'Super Admin'],
            ['email' => 'jhon.doe@example.com',      'password' => '123456789a', 'role' => 'Admin'],
            ['email' => 'maria.perez@example.com',   'password' => '123456789a', 'role' => 'Admin'],
        ];

        foreach ($usersData as $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status_type_id' => $statusId,
            ]);

            $user->assignRole($data['role']);
        }
    }
}
