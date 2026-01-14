<?php

namespace App\Repositories\Implements;

use App\Models\Address;
use App\Repositories\IAddressRepository;

class AddressRepository implements IAddressRepository
{

    public function create(array $data): Address
    {
        return Address::create([
            'address' => $data['address'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'sector' => $data['sector'] ?? null,
            'stratum_id' => $data['stratum_id'] ?? null,
            'complement' => $data['complement'] ?? null,
            'person_id' => $data['person_id'] ?? null,
            'company_id' => $data['company_id'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
        ]);
    }

    public function update(Address $address, array $data): void
    {
        // TODO: Implement update() method.
    }

    public function delete(Address $address): void
    {
        // TODO: Implement delete() method.
    }
}
