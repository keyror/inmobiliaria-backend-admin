<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Contact;

interface IContactRepository
{
    public function create(array $data): Contact;
    public function update(Contact $contact, array $data): void;
    public function delete(Contact $contact): void;
}
