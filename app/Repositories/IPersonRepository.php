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
    public function create(StorePersonRequest $request): void;
    public function update(UpdatePersonRequest $request, Person $person): void;
    public function delete(Person $person): void;
}
