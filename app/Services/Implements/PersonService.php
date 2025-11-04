<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Repositories\IPersonRepository;
use App\Services\IPersonService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PersonService implements IPersonService
{
    public function __construct(
        private readonly IPersonRepository $personRepository,
    ) {}

    public function getPeople(): JsonResponse
    {
        try {
            $people = $this->personRepository->getPeopleByFilters();
            return response()->json([
                'status' => true,
                'data' => $people,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getPerson(Person $person): JsonResponse
    {
        try {
            $personData = $this->personRepository->getPersonWithRelations($person);
            return response()->json([
                'status' => true,
                'data' => $personData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function createPerson(StorePersonRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->personRepository->create($request);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('people.created')]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function updatePerson(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->personRepository->update($request, $person);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('people.updated')]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function deletePerson(Person $person): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->personRepository->delete($person);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('people.deleted')]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
