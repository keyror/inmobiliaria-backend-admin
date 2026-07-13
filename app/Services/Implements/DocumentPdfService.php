<?php

namespace App\Services\Implements;

use App\Models\Company;
use App\Models\Document;
use App\Models\Rent;
use App\Models\TemplateSection;
use App\Support\DocumentTemplateMap;
use App\Support\TemplateSectionDefaults;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class DocumentPdfService
{
    public function __construct(
        private readonly TemplateSectionDefaults $clauseDefaults
    ) {}

    public function generate(Document $document): array
    {
        $templateKey = $this->resolveTemplateKey($document);

        if (! DocumentTemplateMap::hasView($templateKey)) {
            throw new Exception(__('template_section.template_missing', ['key' => $templateKey]));
        }

        $rent = $document->documentable;

        if (! ($rent instanceof Rent)) {
            throw new Exception(__('document.not_rent'));
        }

        $rent->load([
            'property.addresses.city:id,name',
            'property.stratum:id,name',
            'property.propertyType:id,name',
            'property.owners:id,full_name,company_name,document_number,document_type_id',
            'property.owners.documentType:id,alias',
            'contractType:id,name,alias',
            'incrementType:id,name,alias',
            'paymentBank:id,name,alias',
            'rentTenantCodebtors.tenant:id,full_name,company_name,document_number,document_type_id',
            'rentTenantCodebtors.tenant.documentType:id,alias',
            'rentTenantCodebtors.codebtor:id,full_name,company_name,document_number,document_type_id',
            'rentTenantCodebtors.codebtor.documentType:id,alias',
        ]);

        $company = Company::with(['legalRepresentative:id,full_name,document_number', 'logo'])->first();
        $logoDataUri = $this->getLogoDataUri($company);

        $this->ensureContractNumber($rent);
        $this->ensureDocumentNumber($document);

        $viewName = DocumentTemplateMap::view($templateKey);

        $clauses = null;
        if (array_key_exists($templateKey, $this->clauseDefaults->getAvailableTemplates())) {
            $clauses = $this->loadClauses($templateKey, $rent, $company);
        }

        $pdf = Pdf::loadView($viewName, [
            'rent' => $rent,
            'company' => $company,
            'document' => $document,
            'clauses' => $clauses,
            'logoDataUri' => $logoDataUri,
        ])
            ->setOptions(['enable_php' => true])
            ->setPaper('letter', 'portrait');

        $ref = $rent->contract_number ?? $rent->id;
        $prefix = str_replace('.', '_', $templateKey);
        $filename = $prefix.'_'.$ref.'_'.now()->format('Ymd_His').'.pdf';
        $storagePath = 'documents/'.$rent->id.'/'.$filename;
        $pdfContent = $pdf->output();

        Storage::disk('public')->put($storagePath, $pdfContent);

        return [
            'path' => $storagePath,
            'filename' => $filename,
            'size' => strlen($pdfContent),
        ];
    }

    private function ensureContractNumber(Rent $rent): void
    {
        if ($rent->contract_number) {
            return;
        }
        $year = now()->year;
        $maxSeq = Rent::where('contract_number', 'LIKE', $year.'-%')
            ->get()
            ->map(fn ($r) => (int) substr((string) $r->contract_number, strlen((string) $year) + 1))
            ->filter(fn ($n) => $n > 0)
            ->max() ?? 0;
        $rent->contract_number = $year.'-'.str_pad($maxSeq + 1, 4, '0', STR_PAD_LEFT);
        $rent->saveQuietly();
    }

    private function ensureDocumentNumber(Document $document): void
    {
        if ($document->number) {
            return;
        }
        $year = now()->year;
        $maxSeq = Document::where('number', 'LIKE', $year.'-%')
            ->get()
            ->map(fn ($d) => (int) substr((string) $d->number, strlen((string) $year) + 1))
            ->filter(fn ($n) => $n > 0)
            ->max() ?? 0;
        $document->number = $year.'-'.str_pad($maxSeq + 1, 4, '0', STR_PAD_LEFT);
        $document->saveQuietly();
    }

    private function getLogoDataUri(?Company $company): ?string
    {
        $logo = $company?->logo;
        if (! $logo || ! $logo->file_path) {
            return null;
        }

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists($logo->file_path)) {
                return null;
            }
            $content = $disk->get($logo->file_path);
            $mime = $disk->mimeType($logo->file_path) ?? 'image/jpeg';

            return 'data:'.$mime.';base64,'.base64_encode($content);
        } catch (Exception) {
            return null;
        }
    }

    private function resolveTemplateKey(Document $document): string
    {
        if ($document->template_key) {
            return $document->template_key;
        }

        $rent = $document->documentable;
        if ($rent instanceof Rent && $rent->contractType) {
            $alias = strtolower($rent->contractType->alias ?? '');
            $mapped = DocumentTemplateMap::templateFromContractType($alias);
            if ($mapped) {
                return $mapped;
            }
        }

        return 'contract.rental.residential';
    }

    private function loadClauses(string $templateKey, Rent $rent, ?Company $company): Collection
    {
        $clauses = TemplateSection::where('template_key', $templateKey)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $clauses->map(function (TemplateSection $clause) use ($rent, $company) {
            $clause->setAttribute('rendered_body', $this->replaceVariables($clause->body ?? '', $rent, $company));

            return $clause;
        });
    }

    private function replaceVariables(string $body, Rent $rent, ?Company $company): string
    {
        $tenantPairs = $rent->rentTenantCodebtors;
        $mainTenant = $tenantPairs->first()?->tenant;
        $mainCodebtor = $tenantPairs->first()?->codebtor;
        $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
            ?? $rent->property->addresses->first();
        $mainOwner = $rent->property->owners->first();

        $ivaTexto = '';
        if ($rent->iva) {
            $ivaValor = '$'.number_format($rent->canon * $rent->iva / 100, 0, ',', '.');
            $ivaTexto = " más {$ivaValor} por concepto de IVA ({$rent->iva}%)";
        }

        $variables = [
            '{{CANON_MENSUAL}}' => '$'.number_format($rent->canon ?? 0, 0, ',', '.'),
            '{{TOTAL_MENSUAL}}' => '$'.number_format(($rent->canon ?? 0) + (($rent->canon ?? 0) * ($rent->iva ?? 0) / 100), 0, ',', '.'),
            '{{IVA_PORCENTAJE}}' => ($rent->iva ?? 0).'%',
            '{{IVA_TEXTO}}' => $ivaTexto,
            '{{DESTINACION}}' => strtolower($rent->destination ?? 'vivienda urbana'),
            '{{ACTIVIDAD_COMERCIAL}}' => strtolower($rent->activity ?? 'comercial'),
            '{{ADMINISTRACION_INCLUIDA}}' => $rent->administration_included
                ? '(incluye cuota de administración)'
                : '(sin incluir cuota de administración)',
            '{{TIPO_INCREMENTO}}' => $rent->incrementType?->name ?? ($rent->interest_rate ?? 'IPC'),
            '{{FECHA_REAJUSTE}}' => $rent->adjustment_date?->format('d \d\e F') ?? 'aniversario del contrato',
            '{{DURACION_MESES}}' => $rent->duration ? $rent->duration.' meses' : 'un (1) año',
            '{{FECHA_INICIO}}' => $rent->start_date?->format('d \d\e F \d\e Y') ?? '---',
            '{{FECHA_FIN}}' => $rent->end_date ? 'y hasta el '.$rent->end_date->format('d \d\e F \d\e Y') : '',
            '{{NOMBRE_ARRENDATARIO}}' => $mainTenant?->full_name ?? $mainTenant?->company_name ?? '---',
            '{{DOCUMENTO_ARRENDATARIO}}' => ($mainTenant?->documentType?->alias ?? 'C.C.').' '.($mainTenant?->document_number ?? ''),
            '{{TELEFONO_ARRENDATARIO}}' => $mainTenant?->phone ?? '---',
            '{{EMAIL_ARRENDATARIO}}' => $mainTenant?->email ?? '---',
            '{{NOMBRE_CODEUDOR}}' => $mainCodebtor?->full_name ?? $mainCodebtor?->company_name ?? '---',
            '{{DOCUMENTO_CODEUDOR}}' => ($mainCodebtor?->documentType?->alias ?? 'C.C.').' '.($mainCodebtor?->document_number ?? ''),
            '{{NOMBRE_PROPIETARIO}}' => $mainOwner?->full_name ?? $mainOwner?->company_name ?? '---',
            '{{DOCUMENTO_PROPIETARIO}}' => ($mainOwner?->documentType?->alias ?? 'C.C.').' '.($mainOwner?->document_number ?? ''),
            '{{NOMBRE_EMPRESA}}' => $company?->company_name ?? '---',
            '{{NIT_EMPRESA}}' => $company?->nit ?? '---',
            '{{DIRECCION_EMPRESA}}' => $company?->address ?? '---',
            '{{TELEFONO_EMPRESA}}' => $company?->phone ?? '---',
            '{{DIRECCION_INMUEBLE}}' => $principalAddress?->address ?? '---',
            '{{MUNICIPIO_INMUEBLE}}' => $principalAddress?->city?->name ?? $rent->property->city?->name ?? '---',
            '{{BARRIO_INMUEBLE}}' => $principalAddress?->neighborhood ?? '---',
            '{{MATRICULA_INMUEBLE}}' => $rent->property->registration_number ?? '---',
            '{{CIUDAD_FIRMA}}' => $rent->signed_city ?? '---',
            '{{FECHA_FIRMA}}' => $rent->signed_at?->format('d \d\e F \d\e Y') ?? '---',
            '{{COMISION_PORCENTAJE}}' => ($rent->commissions ?? 0).'%',
            '{{HONORARIOS_COLOCACION}}' => isset($rent->content['placement_fee'])
                ? '$'.number_format($rent->content['placement_fee'], 0, ',', '.')
                : '---',
            '{{NUMERO_CONTRATO}}' => $rent->contract_number ?? $rent->id ?? '---',
        ];

        return str_replace(array_keys($variables), array_values($variables), $body);
    }
}
