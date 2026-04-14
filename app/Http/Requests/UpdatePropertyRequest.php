<?php

namespace App\Http\Requests;

use App\Validation\AccountBankRules;
use App\Validation\ContactRules;
use App\Validation\PropertyAreaRules;
use App\Validation\PropertyFeatureRules;
use App\Validation\PropertyObligationRules;
use App\Validation\PropertyOwnershipRules;
use App\Validation\PropertyPriceRules;
use App\Validation\PropertyPublishChannelRules;
use App\Validation\PropertyRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $property = $this->route('property');

        $existingContactIds = $property
            ->contacts()
            ->pluck('id')
            ->toArray();

        return array_merge(
            PropertyRules::update($property->id),
            PropertyAreaRules::update(),
            PropertyPriceRules::update(),
            PropertyFeatureRules::update(),
            PropertyObligationRules::update(),
            PropertyPublishChannelRules::update(),
            PropertyOwnershipRules::update(),
            ContactRules::update($existingContactIds),
            AccountBankRules::update(),
        );
    }
}
