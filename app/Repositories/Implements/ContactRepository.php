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
            'mobile' => $data['phone'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
            'person_id' => $data['person_id'] ?? null,
            'company_id' => $data['company_id'] ?? null,
        ]);
    }

    public function update(Contact $contact, array $data): void
    {
        // TODO: Implement update() method.
    }

    public function delete(Contact $contact): void
    {
        // TODO: Implement delete() method.
    }
}
