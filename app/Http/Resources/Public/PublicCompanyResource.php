<?php

namespace App\Http\Resources\Public;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $principalContact = $this->contacts->firstWhere('is_principal', true) ?? $this->contacts->first();
        $principalAddress = $this->addresses->firstWhere('is_principal', true) ?? $this->addresses->first();

        return [
            'id' => $this->id,
            'name' => $this->tradename ?: $this->company_name,
            'company_name' => $this->company_name,
            'tradename' => $this->tradename,
            'nit' => $this->nit,
            'theme' => $this->themeData(),
            'logo' => $this->logo ? [
                'id' => $this->logo->id,
                'title' => $this->logo->title,
                'url' => $this->logo->url,
            ] : null,
            'contact' => $this->contactData($principalContact),
            'contacts' => $this->contacts
                ->map(fn (mixed $contact): ?array => $this->contactData($contact))
                ->values()
                ->all(),
            'address' => $this->addressData($principalAddress),
            'addresses' => $this->addresses
                ->map(fn (mixed $address): ?array => $this->addressData($address))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{id: string, phone: string|null, mobile: string|null, email: string|null, is_principal: bool}|null
     */
    private function contactData(mixed $contact): ?array
    {
        if (! $contact) {
            return null;
        }

        return [
            'id' => $contact->id,
            'phone' => $contact->phone,
            'mobile' => $contact->mobile,
            'email' => $contact->email,
            'is_principal' => $contact->is_principal,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function themeData(): array
    {
        return Company::normalizeTheme($this->theme);
    }

    /**
     * @return array{id: string, address: string|null, city: array{id: string, name: string|null, alias: string|null}|null, department: array{id: string, name: string|null, alias: string|null}|null, country: array{id: string, name: string|null, alias: string|null}|null, is_principal: bool}|null
     */
    private function addressData(mixed $address): ?array
    {
        if (! $address) {
            return null;
        }

        return [
            'id' => $address->id,
            'address' => $address->address,
            'city' => $this->lookupData($address->city),
            'department' => $this->lookupData($address->department),
            'country' => $this->lookupData($address->country),
            'is_principal' => $address->is_principal,
        ];
    }

    /**
     * @return array{id: string, name: string|null, alias: string|null}|null
     */
    private function lookupData(mixed $lookup): ?array
    {
        if (! $lookup) {
            return null;
        }

        return [
            'id' => $lookup->id,
            'name' => $lookup->name,
            'alias' => $lookup->alias,
        ];
    }
}
