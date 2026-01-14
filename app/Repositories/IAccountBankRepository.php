<?php

namespace App\Repositories;

use App\Models\AccountBank;

interface IAccountBankRepository
{
    public function create(array $data): AccountBank;
    public function update(AccountBank $accountBank, array $data): void;
    public function delete(AccountBank $accountBank): void;

}
