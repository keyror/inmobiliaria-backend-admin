<?php

namespace App\Repositories\Implements;

use App\Models\Lookup;
use App\Models\Property;
use App\Repositories\IPropertyRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

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
                'created_at',
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

    public function getPublicPropertiesByFilters(): LengthAwarePaginator
    {
        $statusId = request()->query('status_id') ?: Lookup::query()
            ->where('category', 'status')
            ->where('alias', 'A')
            ->value('id');

        return Property::query()
            ->select([
                'id',
                'code',
                'status_id',
                'title',
                'offer_type_id',
                'property_type_id',
                'rooms',
                'bedrooms',
                'bathrooms',
                'description',
                'created_at',
            ])
            ->with([
                'status:id,name,alias',
                'offerType:id,name,alias',
                'propertyType:id,name,alias',
                'price:id,property_id,price_min,price_max,price,currency',
                'areas' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'area_type_id', 'area_unit_id', 'area_value'])
                        ->with([
                            'areaType:id,name,alias',
                            'areaUnit:id,name,alias',
                        ]);
                },
                'addresses' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'address', 'department_id', 'city_id', 'is_principal'])
                        ->orderByDesc('is_principal')
                        ->with([
                            'department:id,name,alias',
                            'city:id,name,alias',
                        ]);
                },
                'images' => function ($query) {
                    $query
                        ->select(['id', 'imageable_id', 'imageable_type', 'file_path', 'title', 'sort_order', 'is_cover', 'is_public'])
                        ->where('is_public', true)
                        ->orderByDesc('is_cover')
                        ->orderBy('sort_order');
                },
            ])
            ->withCount([
                'images' => function ($query) {
                    $query->where('is_public', true);
                },
            ])
            ->when($statusId, function ($query) use ($statusId) {
                $query->where('status_id', $statusId);
            })
            ->when(request()->query('offer_type_id'), function ($query, string $offerTypeId) {
                $query->where('offer_type_id', $offerTypeId);
            })
            ->when(request()->query('property_type_id'), function ($query, string $propertyTypeId) {
                $query->where('property_type_id', $propertyTypeId);
            })
            ->when(request()->query('department_id'), function ($query, string $departmentId) {
                $query->whereHas('addresses', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                });
            })
            ->when(request()->query('city_id'), function ($query, string $cityId) {
                $query->whereHas('addresses', function ($query) use ($cityId) {
                    $query->where('city_id', $cityId);
                });
            })
            ->when(request()->query('rooms') !== null, function ($query) {
                $query->where('rooms', request()->integer('rooms'));
            })
            ->when(request()->query('bedrooms') !== null, function ($query) {
                $query->where('bedrooms', request()->integer('bedrooms'));
            })
            ->when(request()->query('bathrooms') !== null, function ($query) {
                $query->where('bathrooms', request()->integer('bathrooms'));
            })
            ->when(request()->query('price_min') !== null || request()->query('price_max') !== null, function ($query) {
                $query->whereHas('price', function ($query) {
                    $query
                        ->when(request()->query('price_min') !== null, function ($query) {
                            $query->where('price', '>=', request()->float('price_min'));
                        })
                        ->when(request()->query('price_max') !== null, function ($query) {
                            $query->where('price', '<=', request()->float('price_max'));
                        });
                });
            })
            ->when(request()->query('area_min') !== null || request()->query('area_max') !== null, function ($query) {
                $query->whereHas('areas', function ($query) {
                    $query
                        ->when(request()->query('area_min') !== null, function ($query) {
                            $query->where('area_value', '>=', request()->float('area_min'));
                        })
                        ->when(request()->query('area_max') !== null, function ($query) {
                            $query->where('area_value', '<=', request()->float('area_max'));
                        });
                });
            })
            ->allowedFilters([
                'title',
                'description',
                'code',
                'offerType.name',
                'propertyType.name',
                'addresses.city.name',
                'addresses.department.name',
            ])
            ->allowedSorts()
            ->jsonPaginate();
    }

    public function getPublicPropertyWithRelations(Property $property): Property
    {
        $activeStatusId = Lookup::query()
            ->where('category', 'status')
            ->where('alias', 'A')
            ->value('id');

        return Property::query()
            ->select([
                'id',
                'code',
                'status_id',
                'title',
                'offer_type_id',
                'property_type_id',
                'rooms',
                'bedrooms',
                'bathrooms',
                'description',
                'url_google_map',
                'latitude',
                'longitude',
                'created_at',
            ])
            ->with([
                'status:id,name,alias',
                'offerType:id,name,alias',
                'propertyType:id,name,alias',
                'price:id,property_id,price_min,price_max,price,currency',
                'areas' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'area_type_id', 'area_unit_id', 'area_value'])
                        ->with([
                            'areaType:id,name,alias',
                            'areaUnit:id,name,alias',
                        ]);
                },
                'addresses' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'address', 'department_id', 'city_id', 'is_principal'])
                        ->orderByDesc('is_principal')
                        ->with([
                            'department:id,name,alias',
                            'city:id,name,alias',
                        ]);
                },
                'features' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'feature_type_id', 'feature_description'])
                        ->with('featureType:id,name,alias,icon');
                },
                'publishChannels' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'property_id',
                            'channel_id',
                            'external_link',
                            'status_id',
                            'published_at',
                            'channel_specific_data',
                        ])
                        ->with([
                            'channel:id,name,alias',
                            'status:id,name,alias',
                        ]);
                },
                'images' => function ($query) {
                    $query
                        ->select(['id', 'imageable_id', 'imageable_type', 'file_path', 'title', 'sort_order', 'is_cover', 'is_public'])
                        ->where('is_public', true)
                        ->orderByDesc('is_cover')
                        ->orderBy('sort_order');
                },
            ])
            ->when($activeStatusId, function ($query) use ($activeStatusId) {
                $query->where('status_id', $activeStatusId);
            })
            ->whereKey($property->getKey())
            ->firstOrFail();
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
            'images',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function create(array $data): Property
    {
        return Property::create([
            'code' => $data['code'] ?? Property::generateSequentialCode(),
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
