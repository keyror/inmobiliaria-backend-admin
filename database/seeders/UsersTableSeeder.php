<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory(['status']);
        $statusId = $lookups->get('status')?->first()?->id ?? null;

        $hqCompanyId = Company::whereNull('parent_company_id')->value('id');

        $usersData = [
            ['email' => 'camilomancipe@outlook.com', 'password' => '123456789a', 'role' => 'Super Admin'],
            ['email' => 'jhon.doe@example.com',      'password' => '123456789a', 'role' => 'Admin'],
            ['email' => 'maria.perez@example.com',   'password' => '123456789a', 'role' => 'Admin'],
        ];

        $now = now();

        foreach ($usersData as $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status_type_id' => $statusId,
            ]);

            $user->assignRole($data['role']);

            if ($hqCompanyId) {
                DB::table('company_user')->insert([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'company_id' => $hqCompanyId,
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
