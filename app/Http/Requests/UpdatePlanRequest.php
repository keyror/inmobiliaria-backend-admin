<?php

namespace App\Http\Requests;

use App\Models\Plan;
use App\Validation\PlanRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Plan $plan */
        $plan = $this->route('plan');

        return PlanRules::update($plan->id);
    }
}
