<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrato de Arrendamiento N° {{ $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .page { }

  /* Header */
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  .header-company { font-size: 13pt; font-weight: bold; color: #2c3e50; }
  .header-subtitle { font-size: 8pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 8pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 10pt; font-weight: bold; background: #2c3e50; color: #fff; padding: 4px 10px; text-align: center; }
  .header-doc-number { font-size: 11pt; font-weight: bold; color: #2c3e50; text-align: center; margin-top: 4px; }
  .divider { border: none; border-top: 2px solid #2c3e50; margin: 8px 0; }
  .divider-thin { border: none; border-top: 1px solid #ccc; margin: 6px 0; }

  /* Contract title */
  .contract-title { text-align: center; font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 10px 0 4px 0; color: #2c3e50; letter-spacing: 1px; }
  .contract-legal { text-align: center; font-size: 8pt; color: #666; margin-bottom: 10px; }

  /* Sections */
  .section { margin-bottom: 10px; }
  .section-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #2c3e50; padding: 3px 8px; margin-bottom: 6px; page-break-after: avoid; }
  .section-subtitle { font-size: 9pt; font-weight: bold; color: #2c3e50; margin: 6px 0 3px 0; }

  /* Data table */
  .data-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 6px; }
  .data-table td { padding: 3px 6px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 35%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f7f7f7; }

  /* Conditions grid */
  .conditions-table { width: 100%; border-collapse: collapse; font-size: 9pt; border: 1px solid #ddd; margin-bottom: 6px; }
  .conditions-table th { background: #eef0f3; font-weight: bold; padding: 4px 8px; border: 1px solid #ddd; text-align: left; font-size: 8.5pt; }
  .conditions-table td { padding: 4px 8px; border: 1px solid #ddd; }

  /* Clauses */
  .clause { margin-bottom: 7px; font-size: 9pt; text-align: justify; }
  .clause-num { font-weight: bold; color: #2c3e50; }

  /* Signatures */
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
</style>
</head>
<body>
<div class="page">

  {{-- ENCABEZADO --}}
  @php
    $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
    $propertyAddress = $principalAddress?->address ?? 'Sin dirección registrada';
    $tenantPairs = $rent->rentTenantCodebtors;
    $canonFormatted = '$' . number_format($rent->canon, 0, ',', '.');
    $ivaFormatted   = $rent->iva ? '$' . number_format($rent->canon * $rent->iva / 100, 0, ',', '.') : null;
    $totalFormatted = $ivaFormatted
      ? '$' . number_format($rent->canon + ($rent->canon * $rent->iva / 100), 0, ',', '.')
      : $canonFormatted;
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
        <div class="header-doc-title">CONTRATO DE ARRENDAMIENTO</div>
        <div class="header-doc-number">N° {{ $rent->contract_number ?? '---' }}</div>
      </td>
    </tr>
  </table>
  <hr class="divider">

  <div class="contract-title">Contrato de Arrendamiento de Vivienda Urbana</div>
  <div class="contract-legal">Ley 820 de 2003 — República de Colombia</div>

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
          <td class="label">Nombre:</td>
          <td class="value">{{ $t->full_name ?? $t->company_name }}</td>
        </tr>
        <tr>
          <td class="label">Documento:</td>
          <td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td>
        </tr>
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
        <td class="label">Nombre/Descripción:</td>
        <td class="value">{{ $rent->property->title }}</td>
      </tr>
      <tr>
        <td class="label">Dirección:</td>
        <td class="value" colspan="3">{{ $propertyAddress }}</td>
      </tr>
      @if($rent->property->stratum)
      <tr>
        <td class="label">Estrato:</td>
        <td class="value">{{ $rent->property->stratum->name }}</td>
        <td class="label">Sometido a P.H.:</td>
        <td class="value">{{ $rent->is_ph ? 'Sí' : 'No' }}</td>
      </tr>
      @endif
      <tr>
        <td class="label">Destinación:</td>
        <td class="value">{{ ucfirst($rent->destination ?? 'Vivienda urbana') }}</td>
        @if($rent->activity)
        <td class="label">Actividad:</td>
        <td class="value">{{ $rent->activity }}</td>
        @endif
      </tr>
    </table>
  </div>
  @endif

  {{-- CONDICIONES ECONÓMICAS --}}
  @if(!$hasDynamicContractInfo)
  <div class="section">
    <div class="section-title">3. Condiciones Económicas</div>
    <table class="conditions-table">
      <tr>
        <th>Concepto</th>
        <th>Valor</th>
        <th>Concepto</th>
        <th>Valor</th>
      </tr>
      <tr>
        <td><strong>Canon mensual</strong></td>
        <td>{{ $canonFormatted }}</td>
        <td>Administración incluida</td>
        <td>{{ $rent->administration_included ? 'Sí' : 'No' }}</td>
      </tr>
      @if($ivaFormatted)
      <tr>
        <td>IVA ({{ $rent->iva }}%)</td>
        <td>{{ $ivaFormatted }}</td>
        <td><strong>Total a pagar</strong></td>
        <td><strong>{{ $totalFormatted }}</strong></td>
      </tr>
      @endif
      <tr>
        <td>Tipo de incremento</td>
        <td>{{ $rent->incrementType?->name ?? ($rent->interest_rate ?? 'IPC') }}</td>
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
        <td>{{ $rent->start_date?->format('d/m/Y') ?? '---' }}</td>
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
        {{-- Arrendador --}}
        <td>
          <div class="sig-line">
            <div class="sig-name">{{ $company->company_name }}</div>
            <div class="sig-role">ARRENDADOR</div>
            @if($company->legalRepresentative)
            <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
            @endif
          </div>
        </td>
        {{-- Arrendatario principal --}}
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
