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
                'document_number',
                'company_name',
                'created_at'
            ])
            ->allowedSorts([
                'first_name',
                'last_name',
                'full_name',
                'document_number',
                'created_at'
            ])
            ->jsonPaginate();
    }

    public function getPersonWithRelations(Person $person): Person
    {
        return $person->load([
            'user',
            'fiscalProfile',
            'documentType',
            'organizationType',
            'contacts',
            'addresses'
        ]);
    }

    public function create(StorePersonRequest $request): void
    {
         Person::create([
            'user_id' => $request->user_id,
            'fiscal_profile_id' => $request->fiscal_profile_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'company_name' => $request->company_name,
            'document_type_id' => $request->document_type_id,
            'document_number' => $request->document_number,
            'document_from' => $request->document_from,
            'organization_type_id' => $request->organization_type_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);
    }

    public function update(UpdatePersonRequest $request, Person $person): void
    {
        $person->update([
            'user_id' => $request->user_id,
            'fiscal_profile_id' => $request->fiscal_profile_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'company_name' => $request->company_name,
            'document_type_id' => $request->document_type_id,
            'document_number' => $request->document_number,
            'document_from' => $request->document_from,
            'organization_type_id' => $request->organization_type_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);
    }

    public function delete(Person $person): void
    {
        $person->delete();
    }
}
