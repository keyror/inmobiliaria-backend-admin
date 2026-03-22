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
                'status.alias',
                'propertyType.alias',
                'created_at'
            ])
            ->allowedSorts([
                'code',
                'cadastral_number',
                'status.alias',
                'propertyType.alias',
                'created_at',
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
            'areas',
            'price',
            'publishChannels',
            'features',
            'obligations',
            'addresses',
            'contacts',
            'ownerships.person:id,full_name,company_name,document_number,document_type_id,organization_type_id',
            'images'
        ]);
    }

    public function create(array $data): Property
    {
        return Property::create([
            'code' => $data['code'],
            'status_property_id' => $data['status_property_id'] ?? true,
            'status_id' => $data['status_id'],
            'title' => $data['title'],
            'offer_type_id' => $data['offer_type_id'],
            'property_type_id' => $data['property_type_id'],
            'social_strata' => $data['social_strata'] ?? null,
            'year_built' => $data['year_built'] ?? null,
            'rooms' => $data['rooms'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'garage_type_id' => $data['garage_type_id'] ?? null,
            'garage_spots' => $data['garage_spots'] ?? null,
            'cadastral_number' => $data['cadastral_number'] ?? null,
            'url_google_map' => $data['url_google_map'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'boundaries' => $data['boundaries'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(array $data, Property $property): void
    {
        $property->update([
            'code' => $data['code'] ?? $property->code,
            'status_property_id' => $data['is_active'] ?? $property->status_property_id,
            'status_id' => $data['status_id'] ?? $property->status_id,
            'title' => $data['title'] ?? $property->title,
            'offer_type_id' => $data['offer_type_id'] ?? $property->offer_type_id,
            'property_type_id' => $data['property_type_id'] ?? $property->property_type_id,
            'social_strata' => $data['social_strata'] ?? $property->social_strata,
            'year_built' => $data['year_built'] ?? $property->year_built,
            'rooms' => $data['rooms'] ?? $property->rooms,
            'bedrooms' => $data['bedrooms'] ?? $property->bedrooms,
            'bathrooms' => $data['bathrooms'] ?? $property->bathrooms,
            'garage_type_id' => $data['garage_type_id'] ?? $property->garage_type_id,
            'garage_spots' => $data['garage_spots'] ?? $property->garage_spots,
            'cadastral_number' => $data['cadastral_number'] ?? $property->cadastral_number,
            'url_google_map' => $data['url_google_map'] ?? $property->url_google_map,
            'latitude' => $data['latitude'] ?? $property->latitude,
            'longitude' => $data['longitude'] ?? $property->longitude,
            'boundaries' => $data['boundaries'] ?? $property->boundaries,
            'description' => $data['description'] ?? $property->description,
        ]);
    }

    public function delete(Property $property): void
    {
        $property->delete();
    }
}
