<?php

namespace App\Support;

final class DocumentTemplateMap
{
    /** Maps template_key → Blade view path */
    public const VIEWS = [
        // Contratos
        'arrendamiento_vivienda' => 'documents.contracts.residential',
        'arrendamiento_comercial' => 'documents.contracts.commercial',
        'administracion_mandato' => 'documents.contracts.administracion-mandato',
        'comodato' => 'documents.contracts.comodato',
        'colocacion' => 'documents.contracts.colocacion',
        // Actas
        'entrega_inmueble' => 'documents.actas.entrega',
        'devolucion_inmueble' => 'documents.actas.devolucion',
        'inspeccion' => 'documents.actas.inspeccion',
        // Facturas
        'canon' => 'documents.facturas.canon',
        'liquidacion' => 'documents.facturas.liquidacion',
        // Pólizas
        'seguro_arrendamiento' => 'documents.polizas.seguro-arrendamiento',
        // Garantías
        'codeudor' => 'documents.garantias.codeudor',
        // Inventario
        'inventario_inmueble' => 'documents.inventario.inventario-inmueble',
        // Preaviso
        'preaviso_terminacion' => 'documents.preaviso.terminacion',
        // Alias legacy
        'contract.rental.residential' => 'documents.contracts.residential',
        'contract.rental.commercial' => 'documents.contracts.commercial',
        'acta.entrega_inmueble' => 'documents.actas.entrega',
        'acta.devolucion_inmueble' => 'documents.actas.devolucion',
    ];

    /** Maps Rent.contractType.alias → template_key */
    public const CONTRACT_TYPE_TO_TEMPLATE = [
        'arrendamiento_vivienda' => 'arrendamiento_vivienda',
        'arrendamiento_comercial' => 'arrendamiento_comercial',
    ];

    public static function view(string $key): ?string
    {
        return self::VIEWS[$key] ?? null;
    }

    public static function hasView(string $key): bool
    {
        return isset(self::VIEWS[$key]);
    }

    public static function templateFromContractType(string $alias): ?string
    {
        return self::CONTRACT_TYPE_TO_TEMPLATE[$alias] ?? null;
    }
}
