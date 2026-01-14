<?php

namespace App\Repositories\Implements;

use App\Models\AccountBank;
use App\Repositories\IAccountBankRepository;

class AccountBankRepository implements IAccountBankRepository
{

    public function create(array $data): AccountBank
    {
        return AccountBank::create([
            'account_type_id' => $data['account_type_id'] ?? null,
            'bank_id' => $data['bank_id'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
            'person_id' => $data['person_id'] ?? null,
        ]);
    }

    public function update(AccountBank $accountBank, array $data): void
    {
        // TODO: Implement update() method.
    }

    public function delete(AccountBank $accountBank): void
    {
        // TODO: Implement delete() method.
    }
}
