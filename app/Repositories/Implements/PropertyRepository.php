<?php

namespace App\Repositories\Implements;

use App\Models\Property;
use App\Repositories\IPropertyRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyRepository implements IPropertyRepository
{
    public function getPropertiesByFilters(): LengthAwarePaginator
    {
        return Property::query()
            ->with(['status', 'propertyType'])
            ->allowedFilters([
                'code',
                'cadastral_number',
                'is_active',
                'status.alias',
                'propertyType.alias',
                'created_at'
            ])
            ->allowedSorts([
                'code',
                'cadastral_number',
                'is_active',
                'status.alias',
                'propertyType.alias',
                'created_at'
            ])
            ->jsonPaginate();
    }

    public function getPropertyWithRelations(Property $property): Property
    {
        return $property->load([
            'status:id,name',
            'offerType:id,name',
            'propertyType:id,name',
            'garageType:id,name',
            'parkingType:id,name',
            'areas',
            'prices',
            'publishChannels',
            'features',
            'obligations',
            'ownerships.person:id,full_name,company_name,document_number,document_type_id,organization_type_id',
        ]);
    }

    public function create(array $data): Property
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, Property $property): void
    {
        // TODO: Implement update() method.
    }

    public function delete(Property $property): void
    {
        // TODO: Implement delete() method.
    }
}
