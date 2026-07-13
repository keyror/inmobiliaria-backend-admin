<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Lookup;
use App\Models\Property;
use App\Models\Rent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $rent = $this->resolveRent();

        if (! $rent) {
            $this->command->warn('DocumentTemplatesSeeder: no hay contratos (Rents) en la base de datos. Omitiendo.');

            return;
        }

        // Lookups de estado y categoría
        $statusId = Lookup::where('category', 'document_status')->where('code', 'borrador')->value('id');

        $categoryIds = Lookup::where('category', 'document_category')
            ->whereIn('code', ['contrato', 'acta', 'factura', 'poliza', 'garantia', 'inventario', 'preaviso'])
            ->pluck('id', 'code');

        // Lookups de tipo de plantilla
        $typeIds = Lookup::where('category', 'document_template_type')
            ->pluck('id', 'code');

        $today = now()->toDateString();
        $contractRef = $rent->contract_number ?? 'DEMO-001';

        foreach ($this->templates($contractRef) as $tpl) {
            $code = $tpl['template_key'];
            $catCode = $tpl['category'];

            if (Document::where('documentable_id', $rent->id)->where('template_key', $code)->exists()) {
                continue;
            }

            Document::create([
                'documentable_id' => $rent->id,
                'documentable_type' => Rent::class,
                'document_type_id' => $typeIds[$code] ?? null,
                'document_category_id' => $categoryIds[$catCode] ?? null,
                'status_id' => $statusId,
                'title' => $tpl['title'],
                'number' => $tpl['number'],
                'template_key' => $code,
                'document_date' => $tpl['document_date'] ?? $today,
                'content' => $tpl['content'],
                'file_name' => 'pending',
                'file_path' => '',
                'file_extension' => '',
                'mime_type' => 'application/octet-stream',
                'file_size' => 0,
            ]);
        }

        $this->command->info('✓ DocumentTemplatesSeeder: documentos de ejemplo creados para contrato '.$contractRef);
    }

    private function resolveRent(): ?Rent
    {
        $rent = Rent::with([
            'property',
            'rentTenantCodebtors.tenant',
        ])->first();

        if ($rent) {
            return $rent;
        }

        // Crear un Rent mínimo si no existe ninguno
        $property = Property::first();

        if (! $property) {
            return null;
        }

        $contractTypeId = Lookup::where('category', 'contract_type')
            ->where('code', 'arrendamiento_vivienda')
            ->value('id');

        $incrementTypeId = Lookup::where('category', 'increment_type')->value('id');

        return Rent::create([
            'property_id' => $property->id,
            'contract_number' => 'DEMO-'.now()->format('Ymd'),
            'contract_type_id' => $contractTypeId,
            'increment_type_id' => $incrementTypeId,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->startOfMonth()->addYear(),
            'duration' => 12,
            'canon' => 2_500_000,
            'iva' => 0,
            'administration_included' => false,
            'is_ph' => false,
            'signed_city' => 'Bogotá',
            'signed_at' => now()->toDateString(),
            'destination' => 'vivienda',
        ]);
    }

    private function templates(string $contractRef): array
    {
        $today = now()->toDateString();
        $nextMonth = now()->addMonth()->toDateString();
        $twoMonths = now()->addMonths(2)->toDateString();
        $threeMonths = now()->addMonths(3)->toDateString();
        $termDate = now()->addMonths(9)->toDateString();

        $seq = fn (int $n) => 'DOC-'.str_pad($n, 4, '0', STR_PAD_LEFT).'-'.now()->format('Y');

        return [
            // ── CONTRATOS ───────────────────────────────────────────────
            [
                'template_key' => 'arrendamiento_vivienda',
                'category' => 'contrato',
                'title' => 'Contrato de Arrendamiento de Vivienda '.$contractRef,
                'number' => $seq(1),
                'document_date' => $today,
                'content' => [
                    'additional_clauses' => 'El arrendatario se obliga a no subarrendar el inmueble sin autorización escrita del arrendador. Las mascotas quedan prohibidas salvo acuerdo especial. El arrendatario debe mantener el inmueble en perfecto estado de aseo y conservación.',
                ],
            ],
            [
                'template_key' => 'arrendamiento_comercial',
                'category' => 'contrato',
                'title' => 'Contrato de Arrendamiento Comercial '.$contractRef,
                'number' => $seq(2),
                'document_date' => $today,
                'content' => [
                    'additional_clauses' => 'El local se destinará exclusivamente a actividades comerciales de venta al por menor. El arrendatario es responsable de la instalación y pago de todos los servicios públicos. Se prohíbe la instalación de vallas publicitarias sin autorización previa.',
                ],
            ],
            [
                'template_key' => 'administracion_mandato',
                'category' => 'contrato',
                'title' => 'Contrato de Administración y Mandato '.$contractRef,
                'number' => $seq(3),
                'document_date' => $today,
                'content' => [
                    'commission_percentage' => 10,
                    'clauses_additional' => [
                        'El mandatario queda facultado para suscribir contratos de arrendamiento, recibir cánones y realizar los cobros necesarios en nombre del mandante.',
                        'El mandatario deberá rendir cuentas al mandante dentro de los primeros 5 días de cada mes, adjuntando comprobantes de recaudo y pagos realizados.',
                    ],
                    'notes' => 'Contrato de administración con facultades amplias según instrucciones del propietario.',
                ],
            ],
            [
                'template_key' => 'comodato',
                'category' => 'contrato',
                'title' => 'Contrato de Comodato '.$contractRef,
                'number' => $seq(4),
                'document_date' => $today,
                'content' => [
                    'comodatario_purpose' => 'El inmueble se entrega en préstamo de uso gratuito para habitación de la familia del comodatario. No se podrá destinar a actividades comerciales ni industriales.',
                    'clauses_additional' => [
                        'El comodatario se obliga a conservar el inmueble con la misma diligencia que emplearía en la conservación de sus propias cosas.',
                        'Todos los gastos de conservación y mantenimiento ordinario serán a cargo del comodatario.',
                    ],
                ],
            ],
            [
                'template_key' => 'colocacion',
                'category' => 'contrato',
                'title' => 'Contrato de Colocación Inmobiliaria '.$contractRef,
                'number' => $seq(5),
                'document_date' => $today,
                'content' => [
                    'placement_fee' => 2_500_000,
                    'placement_fee_notes' => 'Honorarios equivalentes a un mes de canon. Pago único al momento de la firma del contrato de arrendamiento.',
                    'clauses_additional' => [
                        'Los honorarios de colocación son de cargo del propietario contratante y serán pagados dentro de los 5 días hábiles siguientes a la firma del contrato de arrendamiento con el inquilino presentado.',
                    ],
                ],
            ],

            // ── ACTAS ────────────────────────────────────────────────────
            [
                'template_key' => 'entrega_inmueble',
                'category' => 'acta',
                'title' => 'Acta de Entrega del Inmueble '.$contractRef,
                'number' => $seq(6),
                'document_date' => $today,
                'content' => [
                    'property_condition' => 'El inmueble se entrega en excelentes condiciones generales: pisos en buen estado, paredes recién pintadas, baños con todos los accesorios en funcionamiento, cocina con mesón de granito sin manchas ni rayones. Las ventanas y puertas cierran correctamente.',
                    'pending_services' => false,
                    'pending_services_notes' => 'Lecturas al momento de entrega — Acueducto: 001234 · Energía: 045678 · Gas: 000890',
                    'pending_payments' => [],
                    'total_pending' => 0,
                    'photos_taken' => true,
                    'obligations_notes' => 'Se entregan 2 juegos de llaves, 1 control del portón eléctrico y 1 tarjeta de acceso al conjunto.',
                ],
            ],
            [
                'template_key' => 'devolucion_inmueble',
                'category' => 'acta',
                'title' => 'Acta de Devolución del Inmueble '.$contractRef,
                'number' => $seq(7),
                'document_date' => $nextMonth,
                'content' => [
                    'property_condition' => 'El inmueble se recibe con deterioro normal por el uso: pintura con marcas menores en paredes de sala, un rayón en la puerta de la habitación principal que el arrendatario se compromete a reparar. Los demás elementos en buen estado.',
                    'pending_services' => true,
                    'pending_services_notes' => 'Facturas del mes en curso quedan a cargo del arrendatario. Lecturas — Acueducto: 001890 · Energía: 048100 · Gas: 001020.',
                    'pending_payments' => [
                        ['concept' => 'Canon proporcional mes de devolución (15 días)', 'amount' => 1_250_000],
                        ['concept' => 'Servicio de acueducto pendiente', 'amount' => 85_000],
                    ],
                    'total_pending' => 1_335_000,
                    'photos_taken' => true,
                    'obligations_notes' => 'Se reciben 2 juegos de llaves y 1 control del portón. El arrendatario deberá realizar la pintura del rayón antes del día 15 del próximo mes.',
                ],
            ],
            [
                'template_key' => 'inspeccion',
                'category' => 'acta',
                'title' => 'Acta de Inspección Periódica '.$contractRef,
                'number' => $seq(8),
                'document_date' => $today,
                'content' => [
                    'property_condition' => 'Inmueble en buen estado general. Cocina con leve acumulación de grasa en extractor que debe ser limpiado. Baño principal presenta humedad en la unión del piso con la pared — se recomienda revisión de impermeabilización.',
                    'pending_services' => false,
                    'pending_services_notes' => null,
                    'pending_payments' => [],
                    'total_pending' => 0,
                    'photos_taken' => true,
                    'obligations_notes' => 'Se solicita al arrendatario limpiar el extractor de cocina antes de la próxima inspección.',
                    'inspector_name' => 'Carlos Andrés Martínez',
                    'next_inspection_date' => $threeMonths,
                ],
            ],

            // ── FACTURAS ─────────────────────────────────────────────────
            [
                'template_key' => 'canon',
                'category' => 'factura',
                'title' => 'Factura Canon de Arrendamiento — '.now()->format('F Y'),
                'number' => $seq(9),
                'document_date' => $today,
                'content' => [
                    'period_from' => now()->startOfMonth()->toDateString(),
                    'period_to' => now()->endOfMonth()->toDateString(),
                    'payment_due_date' => now()->startOfMonth()->addDays(4)->toDateString(),
                    'late_fee' => 0,
                    'administration_amount' => 0,
                ],
            ],
            [
                'template_key' => 'liquidacion',
                'category' => 'factura',
                'title' => 'Liquidación Final de Contrato '.$contractRef,
                'number' => $seq(10),
                'document_date' => $nextMonth,
                'content' => [
                    'pending_payments' => [
                        ['type' => 'debit', 'concept' => 'Canon mes de devolución (proporcional 15 días)', 'amount' => 1_250_000],
                        ['type' => 'debit', 'concept' => 'Servicio de acueducto y alcantarillado pendiente', 'amount' => 85_000],
                        ['type' => 'debit', 'concept' => 'Reparación rayón puerta habitación principal', 'amount' => 120_000],
                        ['type' => 'credit', 'concept' => 'Depósito de garantía a devolver', 'amount' => 2_500_000],
                    ],
                    'observations' => 'La liquidación final incluye los días proporcionales del mes de devolución, los servicios pendientes y el descuento de la garantía. El saldo a favor del arrendatario será consignado dentro de los 5 días hábiles siguientes.',
                ],
            ],

            // ── PÓLIZA ───────────────────────────────────────────────────
            [
                'template_key' => 'seguro_arrendamiento',
                'category' => 'poliza',
                'title' => 'Póliza de Seguro de Arrendamiento '.$contractRef,
                'number' => $seq(11),
                'document_date' => $today,
                'content' => [
                    'insurer_name' => 'Seguros Bolívar S.A.',
                    'policy_number' => 'POL-'.now()->format('Y').'-'.Str::upper(Str::random(6)),
                    'coverage_amount' => 30_000_000,
                    'premium_amount' => 185_000,
                    'policy_start_date' => $today,
                    'policy_end_date' => now()->addYear()->toDateString(),
                    'beneficiary' => 'Inmobiliaria Demo S.A.S. — En calidad de arrendador',
                ],
            ],

            // ── GARANTÍA ─────────────────────────────────────────────────
            [
                'template_key' => 'codeudor',
                'category' => 'garantia',
                'title' => 'Garantía de Codeudor Solidario '.$contractRef,
                'number' => $seq(12),
                'document_date' => $today,
                'content' => [
                    'additional_clauses' => 'El codeudor declara bajo la gravedad del juramento que los bienes denunciados son de su propiedad y no tienen gravámenes que impidan asumir la presente garantía solidaria.',
                ],
            ],

            // ── INVENTARIO ───────────────────────────────────────────────
            [
                'template_key' => 'inventario_inmueble',
                'category' => 'inventario',
                'title' => 'Inventario del Inmueble '.$contractRef,
                'number' => $seq(13),
                'document_date' => $today,
                'content' => [
                    'general_condition' => 'Inmueble en excelentes condiciones generales. Recién pintado, pisos en perfecto estado, todos los accesorios de baño y cocina en funcionamiento.',
                    'photos_taken' => true,
                    'inventory_notes' => 'Se incluye nevera marca Samsung de dos puertas y estufa eléctrica de cuatro puestos como bienes entregados con el inmueble.',
                    'rooms' => [
                        [
                            'name' => 'Sala — Comedor',
                            'items' => [
                                ['name' => 'Piso porcelanato', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Sin rayones ni manchas'],
                                ['name' => 'Pintura de paredes', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Recién pintada color blanco hueso'],
                                ['name' => 'Ventanas con vidrio', 'quantity' => 3, 'condition' => 'bueno', 'notes' => 'Cierran correctamente'],
                                ['name' => 'Puerta principal', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Cerradura en buen estado'],
                            ],
                        ],
                        [
                            'name' => 'Cocina',
                            'items' => [
                                ['name' => 'Mesón de granito', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Sin manchas ni rayones'],
                                ['name' => 'Nevera Samsung dos puertas', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Incluida con el inmueble — modelo RS25J5198SR'],
                                ['name' => 'Estufa eléctrica 4 puestos', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Incluida — todos los quemadores funcionan'],
                                ['name' => 'Extractor de grasa', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Funcional'],
                                ['name' => 'Alacenas y gabinetes', 'quantity' => 6, 'condition' => 'bueno', 'notes' => 'Con bisagras en buen estado'],
                            ],
                        ],
                        [
                            'name' => 'Habitación Principal',
                            'items' => [
                                ['name' => 'Piso laminado de madera', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Sin abolladuras'],
                                ['name' => 'Closet con puertas corredizas', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Correderas funcionan bien'],
                                ['name' => 'Ventana con persiana', 'quantity' => 1, 'condition' => 'bueno', 'notes' => ''],
                                ['name' => 'Puntos de luz y tomacorrientes', 'quantity' => 4, 'condition' => 'bueno', 'notes' => ''],
                            ],
                        ],
                        [
                            'name' => 'Baño Principal',
                            'items' => [
                                ['name' => 'Sanitario', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Descargue en perfecto estado'],
                                ['name' => 'Lavamanos con grifería', 'quantity' => 1, 'condition' => 'bueno', 'notes' => ''],
                                ['name' => 'Ducha con mezcladora', 'quantity' => 1, 'condition' => 'bueno', 'notes' => ''],
                                ['name' => 'Espejo de baño', 'quantity' => 1, 'condition' => 'bueno', 'notes' => 'Sin grietas'],
                                ['name' => 'Gabinete bajo lavamanos', 'quantity' => 1, 'condition' => 'bueno', 'notes' => ''],
                            ],
                        ],
                    ],
                ],
            ],

            // ── PREAVISO ─────────────────────────────────────────────────
            [
                'template_key' => 'preaviso_terminacion',
                'category' => 'preaviso',
                'title' => 'Preaviso de No Renovación — Contrato '.$contractRef,
                'number' => $seq(14),
                'document_date' => $today,
                'content' => [
                    'sender_role' => 'arrendador',
                    'days_notice' => 90,
                    'termination_date' => $termDate,
                    'reason' => 'El propietario requiere el inmueble para uso propio, de conformidad con lo establecido en el numeral 1 del artículo 22 de la Ley 820 de 2003.',
                ],
            ],
        ];
    }
}
