<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrato de Arrendamiento Comercial N° {{ $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .page { }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  .header-company { font-size: 13pt; font-weight: bold; color: #1a3a5c; }
  .header-subtitle { font-size: 8pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 8pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 10pt; font-weight: bold; background: #1a3a5c; color: #fff; padding: 4px 10px; text-align: center; }
  .header-doc-number { font-size: 11pt; font-weight: bold; color: #1a3a5c; text-align: center; margin-top: 4px; }
  .divider { border: none; border-top: 2px solid #1a3a5c; margin: 8px 0; }
  .contract-title { text-align: center; font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 10px 0 4px 0; color: #1a3a5c; letter-spacing: 1px; }
  .contract-legal { text-align: center; font-size: 8pt; color: #666; margin-bottom: 10px; }
  .section { margin-bottom: 10px; }
  .section-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #1a3a5c; padding: 3px 8px; margin-bottom: 6px; page-break-after: avoid; }
  .section-subtitle { font-size: 9pt; font-weight: bold; color: #1a3a5c; margin: 6px 0 3px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 6px; }
  .data-table td { padding: 3px 6px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 35%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f4f6f9; }
  .conditions-table { width: 100%; border-collapse: collapse; font-size: 9pt; border: 1px solid #ddd; margin-bottom: 6px; }
  .conditions-table th { background: #e8edf3; font-weight: bold; padding: 4px 8px; border: 1px solid #ddd; text-align: left; font-size: 8.5pt; }
  .conditions-table td { padding: 4px 8px; border: 1px solid #ddd; }
  .clause { margin-bottom: 7px; font-size: 9pt; text-align: justify; }
  .clause-num { font-weight: bold; color: #1a3a5c; }
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
  .sig-table td { width: 50%; padding: 0 15px; vertical-align: bottom; text-align: center; }
  .sig-line { border-top: 1px solid #1a1a1a; margin-top: 50px; padding-top: 4px; }
  .sig-name { font-weight: bold; font-size: 9pt; }
  .sig-role { font-size: 8pt; color: #555; }
  .sig-doc { font-size: 8pt; color: #555; }
  .highlight { font-weight: bold; }
  .text-small { font-size: 8pt; }
  .mt-4 { margin-top: 4px; }
  .mt-8 { margin-top: 8px; }
  .text-center { text-align: center; }
  .iva-box { background: #fff3cd; border: 1px solid #ffc107; padding: 4px 8px; font-size: 9pt; margin-bottom: 6px; }
</style>
</head>
<body>
<div class="page">

  @php
    $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
    $propertyAddress = $principalAddress?->address ?? 'Sin dirección registrada';
    $tenantPairs = $rent->rentTenantCodebtors;
    $canonFormatted = '$' . number_format($rent->canon, 0, ',', '.');
    $ivaAmount     = $rent->iva ? $rent->canon * $rent->iva / 100 : 0;
    $ivaFormatted  = $ivaAmount > 0 ? '$' . number_format($ivaAmount, 0, ',', '.') : null;
    $totalAmount   = $rent->canon + $ivaAmount;
    $totalFormatted = '$' . number_format($totalAmount, 0, ',', '.');
  @endphp

  <table class="header-table">
    <tr>
      <td style="width:65%;">
        @if(!empty($logoDataUri))
          <img src="{{ $logoDataUri }}" alt="" style="display:block;max-height:45px;max-width:160px;margin-bottom:4px;">
        @endif
        <div class="header-company">{{ $company->company_name }}</div>
        @if($company->tradename)
          <div class="header-subtitle">{{ $company->tradename }}</div>
        @endif
        <div class="header-nit">NIT: {{ $company->nit }}</div>
      </td>
      <td style="width:35%;" class="header-doc-box">
        <div class="header-doc-title">CONTRATO ARRENDAMIENTO COMERCIAL</div>
        <div class="header-doc-number">N° {{ $rent->contract_number ?? '---' }}</div>
      </td>
    </tr>
  </table>
  <hr class="divider">

  <div class="contract-title">Contrato de Arrendamiento de Local Comercial</div>
  <div class="contract-legal">Decreto 410 de 1971 (Código de Comercio) — República de Colombia</div>

  @php
    $hasDynamicParty        = $clauses && $clauses->where('section_type', 'party_info')->isNotEmpty();
    $hasDynamicProperty     = $clauses && $clauses->where('section_type', 'property_info')->isNotEmpty();
    $hasDynamicContractInfo = $clauses && $clauses->where('section_type', 'contract_info')->isNotEmpty();
    $hasDynamicSignature    = $clauses && $clauses->where('section_type', 'signature')->isNotEmpty();
  @endphp

  {{-- PARTES --}}
  @if(!$hasDynamicParty)
  <div class="section">
    <div class="section-title">1. Partes del Contrato</div>

    <div class="section-subtitle">ARRENDADOR</div>
    <table class="data-table">
      <tr>
        <td class="label">Nombre / Razón Social:</td>
        <td class="value">{{ $company->company_name }}</td>
      </tr>
      <tr>
        <td class="label">NIT:</td>
        <td class="value">{{ $company->nit }}</td>
      </tr>
      @if($company->legalRepresentative)
      <tr>
        <td class="label">Representante Legal:</td>
        <td class="value">{{ $company->legalRepresentative->full_name }}
          — {{ $company->legalRepresentative->document_number }}</td>
      </tr>
      @endif
    </table>

    @foreach($tenantPairs as $index => $pair)
      <div class="section-subtitle mt-4">ARRENDATARIO{{ $tenantPairs->count() > 1 ? ' ' . ($index + 1) : '' }}</div>
      @php $t = $pair->tenant; @endphp
      @if($t)
      <table class="data-table">
        <tr>
          <td class="label">Nombre / Razón Social:</td>
          <td class="value">{{ $t->full_name ?? $t->company_name }}</td>
        </tr>
        <tr>
          <td class="label">Documento / NIT:</td>
          <td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td>
        </tr>
        @if($t->company_name && $t->full_name)
        <tr>
          <td class="label">Empresa:</td>
          <td class="value">{{ $t->company_name }}</td>
        </tr>
        @endif
      </table>
      @endif

      @if($pair->codebtor)
      @php $c = $pair->codebtor; @endphp
      <div class="section-subtitle mt-4">CODEUDOR{{ $tenantPairs->count() > 1 ? ' ' . ($index + 1) : '' }}</div>
      <table class="data-table">
        <tr>
          <td class="label">Nombre:</td>
          <td class="value">{{ $c->full_name ?? $c->company_name }}</td>
        </tr>
        <tr>
          <td class="label">Documento:</td>
          <td class="value">{{ $c->documentType?->alias ?? 'C.C.' }} {{ $c->document_number }}</td>
        </tr>
      </table>
      @endif
    @endforeach
  </div>
  @endif

  {{-- INMUEBLE --}}
  @if(!$hasDynamicProperty)
  <div class="section">
    <div class="section-title">2. El Inmueble</div>
    <table class="data-table">
      <tr>
        <td class="label">Código:</td>
        <td class="value">{{ $rent->property->code }}</td>
        <td class="label">Descripción:</td>
        <td class="value">{{ $rent->property->title }}</td>
      </tr>
      <tr>
        <td class="label">Dirección:</td>
        <td class="value" colspan="3">{{ $propertyAddress }}</td>
      </tr>
      <tr>
        <td class="label">Sometido a P.H.:</td>
        <td class="value">{{ $rent->is_ph ? 'Sí' : 'No' }}</td>
        <td class="label">Destinación:</td>
        <td class="value">{{ ucfirst($rent->destination ?? 'Comercial') }}</td>
      </tr>
      @if($rent->activity)
      <tr>
        <td class="label">Actividad comercial:</td>
        <td class="value" colspan="3">{{ $rent->activity }}</td>
      </tr>
      @endif
    </table>
  </div>
  @endif

  {{-- CONDICIONES ECONÓMICAS --}}
  @if(!$hasDynamicContractInfo)
  <div class="section">
    <div class="section-title">3. Condiciones Económicas</div>

    @if($ivaFormatted)
    <div class="iva-box">
      <strong>⚠ Contrato con IVA:</strong> Este arrendamiento incluye IVA del {{ $rent->iva }}%
      sobre el canon neto, de conformidad con el régimen tributario del propietario.
    </div>
    @endif

    <table class="conditions-table">
      <tr>
        <th>Concepto</th>
        <th>Valor</th>
        <th>Concepto</th>
        <th>Valor</th>
      </tr>
      <tr>
        <td><strong>Canon neto</strong></td>
        <td>{{ $canonFormatted }}</td>
        <td>IVA ({{ $rent->iva ?? 0 }}%)</td>
        <td>{{ $ivaFormatted ?? '$0' }}</td>
      </tr>
      <tr>
        <td><strong>Total mensual</strong></td>
        <td><strong>{{ $totalFormatted }}</strong></td>
        <td>Administración incluida</td>
        <td>{{ $rent->administration_included ? 'Sí' : 'No' }}</td>
      </tr>
      <tr>
        <td>Tipo de incremento</td>
        <td>{{ $rent->incrementType?->name ?? ($rent->interest_rate ?? 'Según acuerdo') }}</td>
        <td>Fecha de reajuste</td>
        <td>{{ $rent->adjustment_date?->format('d/m/Y') ?? '---' }}</td>
      </tr>
      <tr>
        <td>Banco de consignación</td>
        <td>{{ $rent->paymentBank?->name ?? '---' }}</td>
        <td>Cuenta / Nro.</td>
        <td>{{ $rent->consignment_account ?? '---' }}</td>
      </tr>
      <tr>
        <td>Asegurado</td>
        <td>{{ $rent->is_insured ? 'Sí' : 'No' }}</td>
        <td>Comisión administración</td>
        <td>{{ $rent->commissions ?? '---' }}</td>
      </tr>
    </table>
  </div>

  {{-- DURACIÓN --}}
  <div class="section">
    <div class="section-title">4. Duración del Contrato</div>
    <table class="conditions-table">
      <tr>
        <th>Fecha de inicio</th>
        <th>Fecha de terminación</th>
        <th>Duración</th>
        <th>Período de pago</th>
      </tr>
      <tr>
        <td>{{ $rent->start_date?->format('d/m/Y') }}</td>
        <td>{{ $rent->end_date?->format('d/m/Y') ?? 'Término indefinido' }}</td>
        <td>{{ $rent->duration ? $rent->duration . ' meses' : '---' }}</td>
        <td>{{ $rent->period?->format('d/m/Y') ?? 'Mensual' }}</td>
      </tr>
    </table>
  </div>
  @endif

  {{-- CLÁUSULAS / SECCIONES DINÁMICAS --}}
  <div class="section">
    @if($clauses && $clauses->isNotEmpty())
      @foreach($clauses->filter(fn($c) => $c->section_type !== 'signature') as $clause)
        @include('documents.partials.dynamic-section', ['clause' => $clause, 'rent' => $rent, 'company' => $company])
      @endforeach
    @endif

    @if($rent->additional_clauses && count($rent->additional_clauses) > 0)
    <div class="clause">
      <span class="clause-num">CLÁUSULAS ADICIONALES</span>
      @foreach($rent->additional_clauses as $idx => $clause)
        <div class="mt-4">— {{ $clause }}</div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- FIRMAS --}}
  @if($hasDynamicSignature)
    @foreach($clauses->filter(fn($c) => $c->section_type === 'signature') as $clause)
      @include('documents.partials.dynamic-section', ['clause' => $clause, 'rent' => $rent, 'company' => $company])
    @endforeach
  @else
  <div class="section mt-8">
    <div class="section-title">6. Firmas</div>
    <div class="text-small text-center" style="margin-bottom:6px;">
      El presente contrato se firma en la ciudad de
      <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
      el {{ $rent->signed_at?->format('d \\d\\e F \\d\\e Y') ?? 'día ___ de ____________ de ______' }}.
    </div>
    <table class="sig-table">
      <tr>
        <td>
          <div class="sig-line">
            <div class="sig-name">{{ $company->company_name }}</div>
            <div class="sig-role">ARRENDADOR</div>
            @if($company->legalRepresentative)
            <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
            @endif
          </div>
        </td>
        @if($tenantPairs->first()?->tenant)
        @php $mainTenant = $tenantPairs->first()->tenant; @endphp
        <td>
          <div class="sig-line">
            <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
            <div class="sig-role">ARRENDATARIO</div>
            <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
          </div>
        </td>
        @endif
      </tr>
      @if($tenantPairs->first()?->codebtor)
      @php $mainCodebtor = $tenantPairs->first()->codebtor; @endphp
      <tr>
        <td style="padding-top:24px;">
          <div class="sig-line">
            <div class="sig-name">{{ $mainCodebtor->full_name ?? $mainCodebtor->company_name }}</div>
            <div class="sig-role">CODEUDOR</div>
            <div class="sig-doc">{{ $mainCodebtor->documentType?->alias ?? 'C.C.' }} {{ $mainCodebtor->document_number }}</div>
          </div>
        </td>
        <td></td>
      </tr>
      @endif
    </table>
  </div>
  @endif

</div>

<script type="text/php">
  if (isset($pdf)) {
    $font = $fontMetrics->getFont("Arial", "normal");
    $size = 7;
    $color = [0.6, 0.6, 0.6];
    $w = $pdf->get_width();
    $h = $pdf->get_height();
    $pdf->line(0, $h - 26, $w, $h - 26, $color, 0.5);
    $pdf->page_text($w / 2 - 45, $h - 20, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size, $color);
  }
</script>
</body>
</html>
