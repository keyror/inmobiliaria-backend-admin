<?php

namespace App\Repositories\Central;

use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPersonRepository
{
    public function getPeopleByFilters(?string $companyId = null): LengthAwarePaginator;

    public function getPersonWithRelations(Person $person): Person;

    public function create(array $data): Person;

    public function update(array $data, Person $person): void;

    public function delete(Person $person): void;
}
