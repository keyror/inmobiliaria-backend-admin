<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantUsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory(['status']);
        $statusId = $lookups->get('status')?->first()?->id ?? null;

        $user = User::create([
            'email' => 'admin@inmobiliaria.com',
            'password' => Hash::make('123456789a'),
            'status_type_id' => $statusId,
        ]);

        $user->assignRole('Admin');
    }
}
