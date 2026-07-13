<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreContractClauseRequest;
use App\Http\Requests\UpdateContractClauseRequest;
use App\Models\ContractClause;
use App\Repositories\IContractClauseRepository;
use App\Services\IContractClauseService;
use App\Support\ContractClauseDefaults;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContractClauseService implements IContractClauseService
{
    public function __construct(
        private readonly IContractClauseRepository $repository,
        private readonly ContractClauseDefaults $defaults
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

    public function store(StoreContractClauseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->maybeExtractBody($data);
            $data['body'] = $data['body'] ?? null;
            $data['heading'] = $data['heading'] ?? null;
            $maxOrder = ContractClause::where('template_key', $data['template_key'])->max('sort_order') ?? -1;
            $data['sort_order'] = $maxOrder + 1;
            $data['is_default'] = false;

            return response()->json([
                'status' => true,
                'message' => [__('contract_clause.created')],
                'data' => $this->repository->create($data),
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateContractClauseRequest $request, ContractClause $clause): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->maybeExtractBody($data);

            return response()->json([
                'status' => true,
                'message' => [__('contract_clause.updated')],
                'data' => $this->repository->update($clause, $data),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(ContractClause $clause): JsonResponse
    {
        try {
            $this->repository->delete($clause);

            return response()->json([
                'status' => true,
                'message' => [__('contract_clause.deleted')],
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
                'message' => [__('contract_clause.reordered')],
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
                    'message' => __('contract_clause.no_defaults'),
                ], 404);
            }

            ContractClause::where('template_key', $templateKey)->delete();
            $this->repository->seedDefaults($templateKey, $defaults);

            return response()->json([
                'status' => true,
                'message' => [__('contract_clause.reset')],
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

    public function preview(string $templateKey): JsonResponse
    {
        try {
            $clauses = $this->repository->getByTemplate($templateKey)->filter(fn ($c) => $c->is_active);
            $templates = $this->defaults->getAvailableTemplates();
            $templateLabel = $templates[$templateKey]['label'] ?? $templateKey;

            $sectionsHtml = '';
            foreach ($clauses as $clause) {
                $sectionsHtml .= $this->renderSection($clause);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'template_label' => $templateLabel,
                    'html' => "<!doctype html><html lang=\"es\"><head><meta charset=\"UTF-8\"><style>{$this->getPreviewCss()}</style></head><body><div class=\"preview-doc\">{$sectionsHtml}</div></body></html>",
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
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
            return $node['text'];
        }

        $text = '';
        foreach ($node['content'] ?? [] as $child) {
            $text .= $this->extractBodyFromJson($child, $placeholderMap);
        }

        $blockTypes = ['paragraph', 'heading', 'bulletList', 'orderedList', 'listItem', 'blockquote', 'horizontalRule'];
        if (in_array($node['type'], $blockTypes, true) && $text !== '') {
            $text .= "\n";
        }

        return $text;
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

    private function renderPartySection(array $config): string
    {
        $role = $config['role'] ?? 'arrendatario';
        $fields = $config['fields'] ?? ['name', 'document'];

        $roleLabels = [
            'arrendatario' => 'ARRENDATARIO',
            'propietario' => 'PROPIETARIO',
            'codeudor' => 'CODEUDOR',
        ];
        $fieldLabels = [
            'name' => 'Nombre completo',
            'document' => 'Tipo y número de documento',
            'phone' => 'Teléfono',
            'email' => 'Correo electrónico',
            'address' => 'Dirección',
        ];
        $sampleData = [
            'arrendatario' => ['name' => 'Juan Carlos Pérez Gómez', 'document' => 'CC 1.000.123.456', 'phone' => '310 000 0001', 'email' => 'arrendatario@ejemplo.com', 'address' => 'Calle 50 N° 20-35 Apto 202'],
            'propietario' => ['name' => 'Constructora Ejemplo S.A.S.', 'document' => 'NIT 900.000.001-9', 'phone' => '601 000 0000', 'email' => 'propietario@ejemplo.com', 'address' => 'Cra 7 N° 71-21 Of. 502'],
            'codeudor' => ['name' => 'María López Torres', 'document' => 'CC 1.000.654.321', 'phone' => '311 000 0002', 'email' => 'codeudor@ejemplo.com', 'address' => 'Av. Calle 72 N° 10-07'],
        ];

        $roleLabel = $roleLabels[$role] ?? strtoupper($role);
        $data = $sampleData[$role] ?? $sampleData['arrendatario'];

        $rows = '';
        foreach ($fields as $f) {
            $label = $fieldLabels[$f] ?? $f;
            $val = $data[$f] ?? '—';
            $rows .= "<tr><th>{$label}</th><td><span class=\"sample-val\">{$val}</span></td></tr>";
        }

        return "<table class=\"info-table\"><thead><tr><th colspan=\"2\">{$roleLabel}</th></tr></thead><tbody>{$rows}</tbody></table>";
    }

    private function renderPropertySection(array $config): string
    {
        $fields = $config['fields'] ?? ['address', 'city'];
        $fieldLabels = ['address' => 'Dirección', 'city' => 'Municipio / Ciudad', 'neighborhood' => 'Barrio', 'registration' => 'Matrícula inmobiliaria', 'type' => 'Tipo de inmueble', 'area' => 'Área'];
        $sampleData = ['address' => 'Carrera 15 N° 80-25 Apto 301', 'city' => 'Bogotá D.C.', 'neighborhood' => 'Chapinero', 'registration' => '50-C-1234567', 'type' => 'Apartamento', 'area' => '68 m²'];

        $rows = '';
        foreach ($fields as $f) {
            $label = $fieldLabels[$f] ?? $f;
            $val = $sampleData[$f] ?? '—';
            $rows .= "<tr><th>{$label}</th><td><span class=\"sample-val\">{$val}</span></td></tr>";
        }

        return "<table class=\"info-table\"><thead><tr><th colspan=\"2\">INMUEBLE</th></tr></thead><tbody>{$rows}</tbody></table>";
    }

    private function renderContractInfoSection(array $config): string
    {
        $fields = $config['fields'] ?? ['canon', 'start_date', 'end_date'];
        $fieldLabels = ['canon' => 'Canon mensual', 'start_date' => 'Fecha de inicio', 'end_date' => 'Fecha de terminación', 'duration_months' => 'Duración', 'increment_type' => 'Tipo de incremento', 'admin_included' => 'Administración incluida'];
        $sampleData = ['canon' => '$1.800.000', 'start_date' => '1 de agosto de 2026', 'end_date' => '31 de julio de 2027', 'duration_months' => '12 meses', 'increment_type' => 'IPC del año anterior', 'admin_included' => 'No'];

        $rows = '';
        foreach ($fields as $f) {
            $label = $fieldLabels[$f] ?? $f;
            $val = $sampleData[$f] ?? '—';
            $rows .= "<tr><th>{$label}</th><td><span class=\"sample-val\">{$val}</span></td></tr>";
        }

        return "<table class=\"info-table\"><thead><tr><th colspan=\"2\">TÉRMINOS DEL CONTRATO</th></tr></thead><tbody>{$rows}</tbody></table>";
    }

    private function renderSignatureSection(array $config): string
    {
        $signatories = $config['signatories'] ?? [
            ['role' => 'arrendador', 'label' => 'EL ARRENDADOR', 'side' => 'left'],
            ['role' => 'arrendatario', 'label' => 'EL ARRENDATARIO', 'side' => 'right'],
        ];

        $left = array_filter($signatories, fn ($s) => ($s['side'] ?? 'left') === 'left');
        $right = array_filter($signatories, fn ($s) => ($s['side'] ?? 'left') === 'right');

        $buildCol = function (array $sigs): string {
            $html = '';
            foreach ($sigs as $s) {
                $label = htmlspecialchars($s['label'] ?? 'FIRMANTE');
                $html .= "<div class=\"sig-block\"><div class=\"sig-line\"></div><p class=\"sig-label\">{$label}</p></div>";
            }

            return $html;
        };

        return "<div class=\"sig-row\"><div class=\"sig-col\">{$buildCol(array_values($left))}</div><div class=\"sig-col\">{$buildCol(array_values($right))}</div></div>";
    }

    private function renderSection(ContractClause $clause): string
    {
        $type = $clause->section_type ?? 'clause';
        $config = $clause->section_config ?? [];
        $heading = htmlspecialchars($clause->heading ?? '');
        $headingHtml = $heading ? "<h4 class=\"section-heading\">{$heading}</h4>" : '';

        return match ($type) {
            'clause', 'observation' => "<div class=\"section section--{$type}\">{$headingHtml}<div class=\"section-body\">{$this->replaceSampleValues($clause->body ?? '')}</div></div>",
            'header' => '<div class="section section--header"><div class="doc-header"><p class="doc-company"><span class="sample-val">'.$this->previewSampleValues['NOMBRE_EMPRESA']."</span></p>{$headingHtml}</div></div>",
            'party_info' => "<div class=\"section section--info\">{$headingHtml}{$this->renderPartySection($config)}</div>",
            'property_info' => "<div class=\"section section--info\">{$headingHtml}{$this->renderPropertySection($config)}</div>",
            'contract_info' => "<div class=\"section section--info\">{$headingHtml}{$this->renderContractInfoSection($config)}</div>",
            'signature' => "<div class=\"section section--signature\">{$headingHtml}{$this->renderSignatureSection($config)}</div>",
            'separator' => ($config['style'] ?? 'line') === 'page_break'
                ? '<div class="page-break-indicator"><span>— Salto de página —</span></div>'
                : '<hr class="section-separator">',
            'table' => '<div class="section"><p class="placeholder-note">[Tabla — configuración pendiente]</p></div>',
            default => "<div class=\"section\">{$headingHtml}</div>",
        };
    }

    private function getPreviewCss(): string
    {
        return '
            *{box-sizing:border-box;margin:0;padding:0}
            body{font-family:"Times New Roman",Times,serif;font-size:11pt;color:#1a1a1a;background:#fff;padding:0}
            .preview-doc{max-width:760px;margin:0 auto;padding:48px 56px}
            .section{margin-bottom:24px}
            .section-heading{font-size:11pt;font-weight:700;text-transform:uppercase;margin-bottom:8px;color:#111}
            .section-body{font-size:11pt;line-height:1.9;text-align:justify;color:#222}
            .section-body p{margin-bottom:4px}
            .sample-val{background:#fef9c3;color:#854d0e;padding:1px 4px;border-radius:2px;font-weight:600}
            .info-table{width:100%;border-collapse:collapse;font-size:10.5pt;margin-top:4px}
            .info-table thead th{background:#f3f4f6;text-align:left;padding:7px 12px;font-size:9.5pt;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border:1px solid #d1d5db;color:#374151}
            .info-table th{width:220px;padding:6px 12px;font-weight:600;border:1px solid #d1d5db;vertical-align:top;color:#374151;background:#f9fafb}
            .info-table td{padding:6px 12px;border:1px solid #d1d5db;vertical-align:top}
            .section--header .doc-header{text-align:center;padding:16px 0 24px;border-bottom:2px solid #111;margin-bottom:8px}
            .doc-company{font-size:13pt;font-weight:700;margin-bottom:4px}
            .sig-row{display:flex;gap:32px;margin-top:48px}
            .sig-col{flex:1;display:flex;flex-direction:column;gap:32px}
            .sig-block{}
            .sig-line{border-top:1px solid #111;width:100%;margin-bottom:6px}
            .sig-label{font-size:9.5pt;text-align:center;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#374151}
            .section-separator{border:none;border-top:1px solid #9ca3af;margin:20px 0}
            .page-break-indicator{border:1px dashed #9ca3af;border-radius:4px;padding:6px 12px;text-align:center;color:#6b7280;font-size:9pt;margin:20px 0}
            .placeholder-note{color:#9ca3af;font-style:italic;font-size:10pt;padding:12px;border:1px dashed #d1d5db;border-radius:4px}
        ';
    }
}
