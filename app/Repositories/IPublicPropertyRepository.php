<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPublicPropertyRepository
{
    public function getPropertiesByFilters(): LengthAwarePaginator;

    public function getPropertyWithRelations(Property $property): Property;
}
