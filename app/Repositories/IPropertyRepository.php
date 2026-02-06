<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPropertyRepository
{
    public function getPropertiesByFilters(): LengthAwarePaginator;
    public function getPropertyWithRelations(Property $property): Property;
    public function create(array $data): Property;
    public function update(array $data, Property $property): void;
    public function delete(Property $property): void;
}
