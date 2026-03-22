<?php

namespace App\Http\Requests;

use App\Validation\AddressRules;
use App\Validation\ContactRules;
use App\Validation\PropertyAreaRules;
use App\Validation\PropertyFeatureRules;
use App\Validation\PropertyObligationRules;
use App\Validation\PropertyOwnershipRules;
use App\Validation\PropertyPriceRules;
use App\Validation\PropertyPublishChannelRules;
use App\Validation\PropertyRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            PropertyRules::store(),
            PropertyAreaRules::store(),
            PropertyPriceRules::store(),
            PropertyFeatureRules::store(),
            PropertyObligationRules::store(),
            PropertyPublishChannelRules::store(),
            PropertyOwnershipRules::store(),
            AddressRules::store(),
            ContactRules::store(),
        );
    }
}
