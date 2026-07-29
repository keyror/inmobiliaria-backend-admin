<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantUsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory(['status']);
        $statusId = $lookups->get('status')?->first()?->id ?? null;

        $hqCompanyId = Company::whereNull('parent_company_id')->value('id');

        $user = User::create([
            'email' => 'admin@inmobiliaria.com',
            'password' => Hash::make('123456789a'),
            'status_type_id' => $statusId,
        ]);

        $user->assignRole('Admin');

        if ($hqCompanyId) {
            DB::table('company_user')->insert([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'company_id' => $hqCompanyId,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
