<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreTemplateSectionRequest;
use App\Http\Requests\UpdateTemplateSectionRequest;
use App\Models\TemplateSection;
use App\Repositories\ITemplateSectionRepository;
use App\Services\ITemplateSectionService;
use App\Support\DocumentTemplateMap;
use App\Support\TemplateSectionDefaults;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TemplateSectionService implements ITemplateSectionService
{
    public function __construct(
        private readonly ITemplateSectionRepository $repository,
        private readonly TemplateSectionDefaults $defaults
    ) {}

    public function getByTemplate(string $templateKey): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->repository->getByTemplate($templateKey),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function store(StoreTemplateSectionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->maybeExtractBody($data);
            $data['body'] = $data['body'] ?? null;
            $data['heading'] = $data['heading'] ?? null;
            $maxOrder = TemplateSection::where('template_key', $data['template_key'])->max('sort_order') ?? -1;
            $data['sort_order'] = $maxOrder + 1;
            $data['is_default'] = false;

            return response()->json([
                'status' => true,
                'message' => [__('template_section.created')],
                'data' => $this->repository->create($data),
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateTemplateSectionRequest $request, TemplateSection $templateSection): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->maybeExtractBody($data);

            return response()->json([
                'status' => true,
                'message' => [__('template_section.updated')],
                'data' => $this->repository->update($templateSection, $data),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(TemplateSection $templateSection): JsonResponse
    {
        try {
            $this->repository->delete($templateSection);

            return response()->json([
                'status' => true,
                'message' => [__('template_section.deleted')],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function reorder(string $templateKey, array $orderedIds): JsonResponse
    {
        try {
            $this->repository->reorder($templateKey, $orderedIds);

            return response()->json([
                'status' => true,
                'message' => [__('template_section.reordered')],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function resetToDefaults(string $templateKey): JsonResponse
    {
        try {
            $defaults = $this->defaults->getDefaults($templateKey);

            if (! $defaults) {
                return response()->json([
                    'status' => false,
                    'message' => __('template_section.no_defaults'),
                ], 404);
            }

            TemplateSection::where('template_key', $templateKey)->delete();
            $this->repository->seedDefaults($templateKey, $defaults);

            return response()->json([
                'status' => true,
                'message' => [__('template_section.reset')],
                'data' => $this->repository->getByTemplate($templateKey),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function meta(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'templates' => $this->defaults->getAvailableTemplates(),
                'variables' => $this->defaults->getVariableDescriptions(),
                'variable_groups' => $this->defaults->getVariableGroups(),
                'dotted_to_placeholder' => $this->defaults->getDottedToPlaceholderMap(),
            ],
        ]);
    }

    public function preview(string $templateKey): Response
    {
        try {
            $clauses = $this->repository->getByTemplate($templateKey)
                ->filter(fn ($c) => $c->is_active)
                ->map(function (TemplateSection $clause) {
                    $clause->setAttribute('rendered_body', $this->replaceSampleValues($clause->body ?? ''));

                    return $clause;
                });

            $viewName = DocumentTemplateMap::view($templateKey) ?? 'documents.contracts.residential';
            [$previewRent, $previewCompany, $previewDocument] = $this->buildPreviewData();

            return Pdf::loadView($viewName, [
                'rent' => $previewRent,
                'company' => $previewCompany,
                'document' => $previewDocument,
                'clauses' => $clauses,
                'logoDataUri' => null,
            ])
                ->setOptions(['enable_php' => true])
                ->setPaper('letter', 'portrait')
                ->stream('preview.pdf');
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function buildPreviewData(): array
    {
        $docType = (object) ['alias' => 'C.C.'];
        $cityLookup = (object) ['name' => 'Bogotá D.C.'];

        $tenant = (object) [
            'full_name' => 'Juan Carlos Pérez Gómez',
            'company_name' => null,
            'document_number' => '1.000.123.456',
            'documentType' => $docType,
            'phone' => '310 000 0001',
            'email' => 'arrendatario@ejemplo.com',
            'address' => 'Calle 50 N° 20-35 Apto 202',
        ];
        $codeudor = (object) [
            'full_name' => 'María López Torres',
            'company_name' => null,
            'document_number' => '1.000.654.321',
            'documentType' => (object) ['alias' => 'C.C.'],
            'phone' => '311 000 0002',
            'email' => 'codeudor@ejemplo.com',
            'address' => 'Av. Calle 72 N° 10-07',
        ];
        $pair = (object) ['tenant' => $tenant, 'codebtor' => $codeudor];

        $address = (object) [
            'address' => 'Carrera 15 N° 80-25 Apto 301',
            'is_principal' => true,
            'city' => $cityLookup,
            'neighborhood' => 'Chapinero',
        ];

        $property = (object) [
            'code' => 'DEMO-001',
            'title' => 'Apartamento de ejemplo',
            'registration_number' => '50-C-1234567',
            'stratum' => (object) ['name' => '4'],
            'propertyType' => null,
            'city' => $cityLookup,
            'area' => null,
            'owners' => collect([]),
            'addresses' => collect([$address]),
        ];

        $rent = (object) [
            'contract_number' => $this->previewSampleValues['NUMERO_CONTRATO'],
            'canon' => 1800000,
            'iva' => null,
            'administration_included' => false,
            'interest_rate' => 'IPC',
            'duration' => 12,
            'destination' => 'vivienda urbana',
            'activity' => null,
            'is_ph' => false,
            'is_insured' => false,
            'commissions' => 10,
            'consignment_account' => null,
            'signed_city' => $this->previewSampleValues['CIUDAD_FIRMA'],
            'signed_at' => null,
            'additional_clauses' => [],
            'content' => [],
            'start_date' => Carbon::parse('2026-08-01'),
            'end_date' => Carbon::parse('2027-07-31'),
            'adjustment_date' => null,
            'period' => null,
            'property' => $property,
            'rentTenantCodebtors' => collect([$pair]),
            'contractType' => null,
            'incrementType' => null,
            'paymentBank' => null,
        ];

        $company = (object) [
            'company_name' => $this->previewSampleValues['NOMBRE_EMPRESA'],
            'tradename' => null,
            'nit' => $this->previewSampleValues['NIT_EMPRESA'],
            'legalRepresentative' => null,
        ];

        $document = (object) [
            'number' => 'PREV-'.now()->format('Ymd'),
            'document_date' => Carbon::now(),
            'content' => [],
        ];

        return [$rent, $company, $document];
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function maybeExtractBody(array &$data): void
    {
        if (! empty($data['content_json'])) {
            $map = $this->defaults->getDottedToPlaceholderMap();
            $extracted = trim($this->extractBodyFromJson($data['content_json'], $map));
            if ($extracted !== '') {
                $data['body'] = $extracted;
            }
        }
    }

    /**
     * Recursively walks a Tiptap JSON document and extracts plain text.
     * Variable nodes (type=variable) are converted to {{PLACEHOLDER}} using the dotted-key map.
     */
    private function extractBodyFromJson(array $node, array $placeholderMap): string
    {
        if ($node['type'] === 'variable') {
            $id = $node['attrs']['id'] ?? '';

            return $placeholderMap[$id] ?? '{{'.Str::upper(str_replace('.', '_', $id)).'}}';
        }

        if (isset($node['text'])) {
            $text = $node['text'];

            // Apply marks (bold, italic, underline)
            $marks = array_column($node['marks'] ?? [], 'type');
            if (in_array('bold', $marks, true)) {
                $text = "<strong>{$text}</strong>";
            }
            if (in_array('italic', $marks, true)) {
                $text = "<em>{$text}</em>";
            }
            if (in_array('underline', $marks, true)) {
                $text = "<u>{$text}</u>";
            }

            return $text;
        }

        $inner = '';
        foreach ($node['content'] ?? [] as $child) {
            $inner .= $this->extractBodyFromJson($child, $placeholderMap);
        }

        $align = $node['attrs']['textAlign'] ?? null;
        $styleAttr = $align ? " style=\"text-align:{$align}\"" : '';

        return match ($node['type']) {
            'paragraph' => "<p{$styleAttr}>{$inner}</p>",
            'bulletList' => "<ul>{$inner}</ul>",
            'orderedList' => "<ol>{$inner}</ol>",
            'listItem' => "<li>{$inner}</li>",
            'blockquote' => "<blockquote>{$inner}</blockquote>",
            'hardBreak' => '<br>',
            default => $inner,
        };
    }

    private array $previewSampleValues = [
        'NOMBRE_ARRENDATARIO' => 'JUAN CARLOS PÉREZ GÓMEZ',
        'DOCUMENTO_ARRENDATARIO' => 'CC 1.000.123.456',
        'TELEFONO_ARRENDATARIO' => '310 000 0001',
        'EMAIL_ARRENDATARIO' => 'arrendatario@ejemplo.com',
        'NOMBRE_CODEUDOR' => 'MARÍA LÓPEZ TORRES',
        'DOCUMENTO_CODEUDOR' => 'CC 1.000.654.321',
        'NOMBRE_PROPIETARIO' => 'CONSTRUCTORA EJEMPLO S.A.S.',
        'DOCUMENTO_PROPIETARIO' => 'NIT 900.000.001-9',
        'DIRECCION_INMUEBLE' => 'Carrera 15 N° 80-25 Apto 301',
        'MUNICIPIO_INMUEBLE' => 'Bogotá D.C.',
        'BARRIO_INMUEBLE' => 'Chapinero',
        'MATRICULA_INMUEBLE' => '50-C-1234567',
        'CANON_MENSUAL' => '$1.800.000',
        'TOTAL_MENSUAL' => '$2.142.000',
        'IVA_TEXTO' => 'más IVA del 19% ($342.000)',
        'FECHA_INICIO' => '1 de agosto de 2026',
        'FECHA_FIN' => 'hasta el 31 de julio de 2027',
        'DURACION_MESES' => 'doce (12) meses',
        'TIPO_INCREMENTO' => 'IPC del año anterior',
        'FECHA_REAJUSTE' => '1 de agosto de 2027',
        'DESTINACION' => 'vivienda urbana',
        'ACTIVIDAD_COMERCIAL' => 'comercio al por menor',
        'ADMINISTRACION_INCLUIDA' => '(administración no incluida)',
        'COMISION_PORCENTAJE' => '10',
        'HONORARIOS_COLOCACION' => '$900.000',
        'CIUDAD_FIRMA' => 'Bogotá D.C.',
        'NUMERO_CONTRATO' => '2026-0001',
        'NOMBRE_EMPRESA' => 'INMOBILIARIA EJEMPLO S.A.S.',
        'NIT_EMPRESA' => 'NIT 900.000.002-1',
    ];

    private function replaceSampleValues(string $body): string
    {
        foreach ($this->previewSampleValues as $key => $val) {
            $body = str_replace(
                '{{'.$key.'}}',
                '<span class="sample-val">'.$val.'</span>',
                $body
            );
        }

        return $body;
    }
}
