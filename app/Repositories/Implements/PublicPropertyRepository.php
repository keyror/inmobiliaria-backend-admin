<?php

namespace App\Repositories\Implements;

use App\Models\Lookup;
use App\Models\Property;
use App\Repositories\IPublicPropertyRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicPropertyRepository implements IPublicPropertyRepository
{
    public function getPropertiesByFilters(): LengthAwarePaginator
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
                'prices' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'price_type_id', 'price_min', 'price_max', 'price', 'currency'])
                        ->with('priceType:id,name,alias');
                },
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
                'contacts' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'phone', 'mobile', 'email', 'is_principal'])
                        ->orderByDesc('is_principal');
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
                $query->whereHas('prices', function ($query) {
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

    public function getPropertyWithRelations(Property $property): Property
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
                'prices' => function ($query) {
                    $query
                        ->select(['id', 'property_id', 'price_type_id', 'price_min', 'price_max', 'price', 'currency'])
                        ->with('priceType:id,name,alias');
                },
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
}
