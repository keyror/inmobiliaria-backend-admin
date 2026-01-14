<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Repositories\IAccountBankRepository;
use App\Repositories\IAddressRepository;
use App\Repositories\IContactRepository;
use App\Repositories\IEconomicActivityRepository;
use App\Repositories\IFiscalProfileRepository;
use App\Repositories\IPersonRepository;
use App\Repositories\ITaxeTypeRepository;
use App\Services\IPersonService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PersonService implements IPersonService
{
    public function __construct(
        private readonly IPersonRepository $personRepository,
        private readonly IFiscalProfileRepository $fiscalProfileRepository,
        private readonly IContactRepository $contactRepository,
        private readonly IAddressRepository $addressRepository,
        private readonly IAccountBankRepository $accountBankRepository,
        private readonly IEconomicActivityRepository $economicActivityRepository,
        private readonly ITaxeTypeRepository $taxeTypeRepository
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

                foreach ($request->fiscal_profile['economic_activities'] as $activityTypeId) {
                    $this->economicActivityRepository->create([
                        'economic_activity_type_id' => $activityTypeId,
                        'fiscal_profile_id' => $fiscalProfile->id,
                    ]);
                }

                foreach ($request->fiscal_profile['taxe_types'] as $taxeType) {
                    $this->taxeTypeRepository->create([
                        'taxe_type_id' => $taxeType,
                        'fiscal_profile_id' => $fiscalProfile->id,
                    ]);
                }

            }

           $person = $this->personRepository->create($requestData['person']);

            if ($request->addresses) {
                foreach ($requestData['addresses'] as $address) {
                    $address['person_id'] = $person->id;
                    $this->addressRepository->create($address);
                }
            }

            if ($request->contacts) {
                foreach ($requestData['contacts'] as $contact) {
                    $contact['person_id'] = $person->id;
                    $this->contactRepository->create($contact);
                }
            }

            if ($request->account_banks) {
                foreach ($requestData['account_banks'] as $accountBank) {
                    $accountBank['person_id'] = $person->id;
                    $this->accountBankRepository->create($accountBank);
                }
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
