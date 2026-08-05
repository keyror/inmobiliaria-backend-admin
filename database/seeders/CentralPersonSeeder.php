<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lookup;
use App\Models\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CentralPersonSeeder extends Seeder
{
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $company = Company::query()->where('nit', '901123456-7')->first();

        if (! $company) {
            return;
        }

        $ccTypeId = Lookup::where('alias', 'CC')
            ->whereIn('category', ['document_type', 'document-type', 'DocumentType'])
            ->value('id');

        $bogotaId = Lookup::where('category', 'city')->where('name', 'Bogotá')->value('id');
        $pnId = Lookup::where('category', 'organization_type')->where('alias', 'PN')->value('id');

        $person = Person::query()->updateOrCreate(
            [
                'company_id' => $company->id,
                'document_number' => '1000000001',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Juan',
                'last_name' => 'Veltra',
                'full_name' => 'Juan Veltra',
                'document_type_id' => $ccTypeId,
                'document_from_id' => $bogotaId,
                'organization_type_id' => $pnId,
            ],
        );

        $company->update([
            'legal_representative_id' => $person->id,
            'person_attendant_id' => $person->id,
        ]);
    }
}
