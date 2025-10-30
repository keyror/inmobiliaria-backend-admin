<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Services\IPersonService;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function __construct(
        private readonly IPersonService $personService
    ) {}

    /**
     * Listar personas con filtros
     * GET /people
     */
    public function index(): JsonResponse
    {
        return $this->personService->getPeople();
    }

    /**
     * Mostrar persona específica
     * GET /people/{person}
     */
    public function show(Person $person): JsonResponse
    {
        return $this->personService->getPerson($person);
    }

    /**
     * Crear nueva persona
     * POST /people
     */
    public function store(StorePersonRequest $request): JsonResponse
    {
        return $this->personService->createPerson($request);
    }

    /**
     * Actualizar persona
     * PUT /people/{person}
     */
    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        return $this->personService->updatePerson($request, $person);
    }

    /**
     * Eliminar persona
     * DELETE /people/{person}
     */
    public function destroy(Person $person): JsonResponse
    {
        return $this->personService->deletePerson($person);
    }
}
