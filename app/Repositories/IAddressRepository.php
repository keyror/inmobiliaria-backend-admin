<?php

namespace App\Repositories;

use App\Models\AccountBank;
use App\Models\Address;

interface IAddressRepository
{
    public function create(array $data): Address;
    public function update(Address $address, array $data): void;
    public function delete(Address $address): void;
}
