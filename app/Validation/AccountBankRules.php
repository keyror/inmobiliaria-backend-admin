<?php

namespace App\Validation;

class AccountBankRules
{
    public static function store(): array
    {
        return [
            'account_banks.*.account_type_id' => 'required|uuid|exists:lookups,id',
            'account_banks.*.bank_id' => 'required|uuid|exists:lookups,id',
            'account_banks.*.account_number' => 'required|string|max:255',
            'account_banks.*.is_principal' => 'sometimes|required|boolean',
        ];
    }

    public static function update(): array
    {
        return [
            'account_banks.*.account_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'account_banks.*.bank_id' => 'sometimes|required|uuid|exists:lookups,id',
            'account_banks.*.account_number' => 'sometimes|required|string|max:255',
            'account_banks.*.is_principal' => 'sometimes|required|boolean',
        ];
    }
}
