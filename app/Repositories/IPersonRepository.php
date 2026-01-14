<?php

namespace App\Repositories;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPersonRepository
{
    public function getPeopleByFilters(): LengthAwarePaginator;
    public function getPersonWithRelations(Person $person): Person;
    public function create(array $data): Person;
    public function update(array $data, Person $person): void;
    public function delete(Person $person): void;
}
