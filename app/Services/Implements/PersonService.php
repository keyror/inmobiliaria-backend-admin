<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\AccountBank;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EconomicActivity;
use App\Models\Person;
use App\Models\TaxeType;
use App\Repositories\IAccountBankRepository;
use App\Repositories\IAddressRepository;
use App\Repositories\IContactRepository;
use App\Repositories\IEconomicActivityRepository;
use App\Repositories\IFiscalProfileRepository;
use App\Repositories\IPersonRepository;
use App\Repositories\ITaxeTypeRepository;
use App\Services\IFiscalProfileService;
use App\Services\IPersonService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class PersonService implements IPersonService
{
    public function __construct(
        private readonly IPersonRepository $personRepository,
        private readonly IFiscalProfileRepository $fiscalProfileRepository,
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

            $requestData = $request->all();

            if ($request->fiscal_profile) {
                $fiscalProfile = $this->fiscalProfileRepository->create($request->fiscal_profile);
                $requestData['person']['fiscal_profile_id'] = $fiscalProfile->id;
            }

            $person = $this->personRepository->create($requestData['person']);

            $fiscalProfile = $person->fiscalProfile;

            $fiscalProfile->syncHasMany(
                'economicActivities',
                $requestData['fiscal_profile']['economic_activities'],
                'economic_activity_type_id'
            );

            $fiscalProfile->syncHasMany(
                'taxeTypes',
                $requestData['fiscal_profile']['taxe_types'],
                'taxe_type_id'
            );

            if ($request->addresses) {
                $person->syncHasMany('addresses', $requestData['addresses']);
            }

            if ($request->contacts) {
                $person->syncHasMany('contacts', $requestData['contacts']);
            }

            if ($request->account_banks) {
                $person->syncHasMany('accountBanks', $requestData['account_banks']);
            }

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
            $requestData = $request->all();

            if ($request->fiscal_profile) {
                $this->fiscalProfileRepository->update($person->fiscalProfile, $request->fiscal_profile);
                $requestData['person']['fiscal_profile_id'] = $person->fiscal_profile_id ?? null;
            }

            $fiscalProfile = $person->fiscalProfile;

            $fiscalProfile->syncHasMany(
                'economicActivities',
                $requestData['fiscal_profile']['economic_activities'],
                'economic_activity_type_id'
            );

            $fiscalProfile->syncHasMany(
                'taxeTypes',
                $requestData['fiscal_profile']['taxe_types'],
                'taxe_type_id'
            );

            $this->personRepository->update($requestData['person'], $person);

            if ($request->addresses) {
                $person->syncHasMany('addresses', $requestData['addresses']);
            }

            if ($request->contacts) {
                $person->syncHasMany('contacts', $requestData['contacts']);
            }

            if ($request->account_banks) {
                $person->syncHasMany('accountBanks', $requestData['account_banks']);
            }

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
