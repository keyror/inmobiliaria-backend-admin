<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Services\Central\IPersonService;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function __construct(
        private readonly IPersonService $personService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->personService->getPeople();
    }

    public function show(Person $person): JsonResponse
    {
        return $this->personService->getPerson($person);
    }

    public function store(StorePersonRequest $request): JsonResponse
    {
        return $this->personService->createPerson($request);
    }

    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        return $this->personService->updatePerson($request, $person);
    }

    public function destroy(Person $person): JsonResponse
    {
        return $this->personService->deletePerson($person);
    }
}
