<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\IPropertyService;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function __construct(
        private readonly IPropertyService $propertyService
    ) {}

    /**
     * Listar propiedades con filtros
     * GET /properties
     */
    public function index(): JsonResponse
    {
        return $this->propertyService->getProperties();
    }

    /**
     * Mostrar propiedad específica
     * GET /properties/{property}
     */
    public function show(Property $property): JsonResponse
    {
        return $this->propertyService->getProperty($property);
    }

    /**
     * Crear nueva propiedad
     * POST /properties
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        return $this->propertyService->createProperty($request);
    }

    /**
     * Actualizar propiedad
     * PUT /properties/{property}
     */
    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        return $this->propertyService->updateProperty($request, $property);
    }

    /**
     * Eliminar propiedad
     * DELETE /properties/{property}
     */
    public function destroy(Property $property): JsonResponse
    {
        return $this->propertyService->deleteProperty($property);
    }
}
