<?php

namespace App\Repositories\Implements;

use App\Models\Contact;
use App\Repositories\IContactRepository;

class ContactRepository implements IContactRepository
{
    public function create(array $data): Contact
    {
        return Contact::create([
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'name' => $data['name'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
            'contactable_type' => $data['contactable_type'] ?? null,
            'contactable_id' => $data['contactable_id'] ?? null,
        ]);
    }

    public function update(Contact $contact, array $data): void
    {
        $contact->update([
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'name' => $data['name'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
        ]);
    }

    public function delete(Contact $contact): void
    {
        // TODO: Implement delete() method.
    }
}
