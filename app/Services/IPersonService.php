<?php

namespace App\Services;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

interface IPersonService
{
    public function getPeople(): JsonResponse;
    public function getPerson(Person $person): JsonResponse;
    public function createPerson(StorePersonRequest $request): JsonResponse;
    public function updatePerson(UpdatePersonRequest $request, Person $person): JsonResponse;
    public function deletePerson(Person $person): JsonResponse;
}
