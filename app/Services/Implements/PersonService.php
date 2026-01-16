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
        private readonly IContactRepository $contactRepository,
        private readonly IAddressRepository $addressRepository,
        private readonly IAccountBankRepository $accountBankRepository,
        private readonly IFiscalProfileService $fiscalProfileService,
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

            $this->fiscalProfileService->syncForEconomicActivity(
                $person,
                $requestData['fiscal_profile']['economic_activities'] ?? []
            );

            $this->fiscalProfileService->syncForTaxeType(
                $person,
                $requestData['fiscal_profile']['taxe_types'] ?? []
            );

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
            $requestData = $request->all();

            if ($request->fiscal_profile) {
                $this->fiscalProfileRepository->update($person->fiscalProfile, $request->fiscal_profile);

                $requestData['person']['fiscal_profile_id'] = $person->fiscal_profile_id ?? null;
                $this->fiscalProfileService->syncForEconomicActivity(
                    $person,
                        $requestData['fiscal_profile']['economic_activities'] ?? []
                );
                $this->fiscalProfileService->syncForTaxeType(
                    $person,
                        $requestData['fiscal_profile']['taxe_types'] ?? []
                );
            }

            $this->personRepository->update($requestData['person'], $person);

            if ($request->addresses) {
                $ids = collect($requestData['addresses'])->pluck('id')->filter();

                $person->addresses()
                    ->whereNotIn('id', $ids)
                    ->delete();

                foreach ($requestData['addresses'] as $address) {
                    $address['person_id'] = $person->id;
                    if (isset($address['id']) && $address['id']) {
                        $addressModel = Address::find($address['id']);
                        $this->addressRepository->update($addressModel, $address);
                    } else {
                        $this->addressRepository->create($address);
                    }

                }
            }

            if ($request->contacts) {
                $ids = collect($requestData['contacts'])->pluck('id')->filter();

                $person->contacts()
                    ->whereNotIn('id', $ids)
                    ->delete();

                foreach ($requestData['contacts'] as $contact) {
                    $contact['person_id'] = $person->id;
                    if (isset($contact['id']) && $contact['id']) {
                        $contactModel = Contact::find($contact['id']);
                        $this->contactRepository->update($contactModel, $contact);
                    } else {
                        $this->contactRepository->create($contact);
                    }
                }
            }

            if ($request->account_banks) {
                $ids = collect($requestData['account_banks'])->pluck('id')->filter();

                $person->accountBanks()
                    ->whereNotIn('id', $ids)
                    ->delete();

                foreach ($requestData['account_banks'] as $accountBank) {
                    $accountBank['person_id'] = $person->id;
                    if (isset($accountBank['id']) && $accountBank['id']) {
                        $accountBankModel = AccountBank::find($accountBank['id']);
                        $this->accountBankRepository->update($accountBankModel, $accountBank);
                    } else {
                        $this->accountBankRepository->create($accountBank);
                    }
                }
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
