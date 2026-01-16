<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Repositories\IPersonRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonRepository implements IPersonRepository
{
    public function getPeopleByFilters(): LengthAwarePaginator
    {
        return Person::query()
            ->with(['user', 'fiscalProfile', 'documentType', 'organizationType', 'contacts', 'addresses'])
            ->allowedFilters([
                'full_name',
                'documentType.alias',
                'document_number',
                'created_at'
            ])
            ->allowedSorts([
                'full_name',
                'documentType.alias',
                'document_number',
                'created_at'
            ])
            ->jsonPaginate();
    }

    public function getPersonWithRelations(Person $person): Person
    {
        return $person->load([
            'user',
            'fiscalProfile.economicActivities.type',
            'fiscalProfile.taxeTypes.type:id,name',
            'documentType',
            'organizationType',
            'contacts',
            'addresses',
            'accountBanks'
        ]);
    }

    public function create(array $data): Person
    {
        return Person::create([
            'user_id'=> $data['user_id'] ?? null,
            'fiscal_profile_id'=> $data['fiscal_profile_id'] ?? null,
            'first_name'=> $data['first_name'],
            'last_name'=> $data['last_name'],
            'full_name'=> $data['first_name'].' '.$data['last_name'],
            'company_name'=> $data['company_name'] ?? null,
            'document_type_id'=> $data['document_type_id'],
            'document_number'=> $data['document_number'],
            'document_from_id'=> $data['document_from_id'],
            'organization_type_id'=> $data['organization_type_id'],
            'birth_date'=> $data['birth_date'],
            'gender_type_id'=> $data['gender_type_id'],
        ]);
    }


    public function update(array $data, Person $person): void
    {
        $person->update([
            'user_id'=> $data['user_id'] ?? null,
            'fiscal_profile_id'=> $data['fiscal_profile_id'] ?? null,
            'first_name'=> $data['first_name'],
            'last_name'=> $data['last_name'],
            'full_name'=> $data['first_name'].' '.$data['last_name'],
            'company_name'=> $data['company_name'] ?? null,
            'document_type_id'=> $data['document_type_id'],
            'document_number'=> $data['document_number'],
            'document_from_id'=> $data['document_from_id'],
            'organization_type_id'=> $data['organization_type_id'],
            'birth_date'=> $data['birth_date'],
            'gender_type_id'=> $data['gender_type_id'],
        ]);
    }

    public function delete(Person $person): void
    {
        $person->delete();
    }

}
