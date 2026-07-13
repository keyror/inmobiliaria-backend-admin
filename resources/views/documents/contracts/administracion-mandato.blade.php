<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrato Administración/Mandato N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #196f3d; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #196f3d; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #196f3d; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #196f3d; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #196f3d; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #196f3d; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #196f3d; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f0f9f4; }
  .clause { margin-bottom: 7px; font-size: 9pt; text-align: justify; }
  .clause-title { font-weight: bold; color: #196f3d; margin-bottom: 2px; }
  .highlight-box { background: #e9f7ef; border-left: 3px solid #196f3d; padding: 5px 8px; font-size: 9pt; margin-bottom: 6px; }
  .clauses-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 6px 10px; font-size: 8.5pt; margin-bottom: 6px; }
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 18px; page-break-inside: avoid; }
  .sig-table td { width: 50%; padding: 0 12px; vertical-align: bottom; text-align: center; }
  .sig-line { border-top: 1px solid #1a1a1a; margin-top: 46px; padding-top: 3px; }
  .sig-name { font-weight: bold; font-size: 8.5pt; }
  .sig-role { font-size: 7.5pt; color: #555; }
  .sig-doc  { font-size: 7.5pt; color: #555; }
  .legal-note { font-size: 7pt; color: #666; text-align: justify; margin-top: 8px; border-top: 1px solid #ddd; padding-top: 4px; }
  .mt-4 { margin-top: 4px; }
</style>
</head>
<body>

@php
  $content            = $document->content ?? [];
  $commissionPct      = $content['commission_percentage'] ?? ($rent->commissions ?? null);
  $clausesAdditional  = $content['clauses_additional'] ?? [];
  $mandateNotes       = $content['notes'] ?? null;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;

  $owners = $rent->property->owners ?? collect();
  $mainOwner = $owners->first();
@endphp

{{-- ENCABEZADO --}}
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
      <div class="header-doc-title">CONTRATO DE ADMINISTRACIÓN/MANDATO</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Contrato de Administración y Mandato Inmobiliario</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Ciudad: {{ $rent->signed_city ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- PARTES --}}
<div class="section">
  <div class="section-title">PRIMERA — Partes</div>

  <div class="section-subtitle">MANDATARIO (Inmobiliaria)</div>
  <table class="data-table">
    <tr><td class="label">Nombre / Razón Social:</td><td class="value">{{ $company->company_name }}</td></tr>
    <tr><td class="label">NIT:</td><td class="value">{{ $company->nit }}</td></tr>
    @if($company->legalRepresentative)
    <tr><td class="label">Rep. Legal:</td><td class="value">{{ $company->legalRepresentative->full_name }}</td></tr>
    @endif
  </table>

  @if($mainOwner)
  <div class="section-subtitle mt-4">MANDANTE (Propietario)</div>
  <table class="data-table">
    <tr><td class="label">Nombre:</td><td class="value">{{ $mainOwner->full_name ?? $mainOwner->company_name }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $mainOwner->documentType?->alias ?? 'C.C.' }} {{ $mainOwner->document_number }}</td></tr>
  </table>
  @elseif($mainTenant)
  <div class="section-subtitle mt-4">PROPIETARIO</div>
  <table class="data-table">
    <tr><td class="label">Nombre:</td><td class="value">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</td></tr>
  </table>
  @endif
</div>

{{-- INMUEBLE --}}
<div class="section">
  <div class="section-title">SEGUNDA — Inmueble Objeto del Mandato</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    @if($rent->property->registration_number)
    <tr><td class="label">Matrícula Inmobiliaria:</td><td class="value" colspan="3">{{ $rent->property->registration_number }}</td></tr>
    @endif
  </table>
</div>

{{-- OBJETO Y FACULTADES --}}
<div class="section">
  <div class="section-title">TERCERA — Objeto y Facultades</div>
  <div class="clause">
    <div class="clause-title">3.1 Objeto</div>
    El MANDANTE otorga al MANDATARIO un mandato de administración para que, en su nombre y representación,
    gestione el arrendamiento del inmueble descrito en la cláusula anterior, incluyendo la búsqueda de arrendatario,
    suscripción de contratos, cobro de cánones, liquidación de cuentas, y todas las actuaciones derivadas del
    arrendamiento.
  </div>
  <div class="clause">
    <div class="clause-title">3.2 Facultades del Mandatario</div>
    El MANDATARIO queda facultado para: (a) celebrar y terminar contratos de arrendamiento; (b) cobrar cánones,
    depósitos y demás pagos del arrendatario; (c) realizar el mantenimiento menor del inmueble; (d) retener de los
    ingresos los honorarios pactados; (e) demandar el pago en caso de mora.
  </div>
</div>

{{-- HONORARIOS --}}
<div class="section">
  <div class="section-title">CUARTA — Remuneración</div>
  @if($commissionPct)
  <div class="highlight-box">
    Comisión de administración: <strong>{{ $commissionPct }}%</strong> sobre el valor mensual del canon recaudado,
    más IVA cuando aplique según el régimen tributario del MANDATARIO.
  </div>
  @else
  <div class="highlight-box">
    La comisión de administración se acordará por separado entre las partes según las condiciones del mercado
    y lo establecido en el contrato de arrendamiento.
  </div>
  @endif
  <div class="clause">
    Los honorarios se retendrán mensualmente al momento de la liquidación y se acreditarán en el estado de cuenta
    que el MANDATARIO entregará al MANDANTE a más tardar el último día hábil de cada mes.
  </div>
</div>

{{-- DURACION --}}
<div class="section">
  <div class="section-title">QUINTA — Duración</div>
  <table class="data-table">
    <tr>
      <td class="label">Inicio del mandato:</td>
      <td class="value">{{ $rent->start_date?->format('d/m/Y') }}</td>
      <td class="label">Terminación:</td>
      <td class="value">{{ $rent->end_date?->format('d/m/Y') ?? 'Indefinido' }}</td>
    </tr>
  </table>
  <div class="clause" style="margin-top:4px;">
    El contrato se prorrogará automáticamente por períodos iguales si ninguna de las partes manifiesta su
    intención de no renovarlo con al menos 30 días de anticipación a la fecha de vencimiento.
  </div>
</div>

{{-- OBLIGACIONES --}}
<div class="section">
  <div class="section-title">SEXTA — Obligaciones de las Partes</div>
  <div class="clause">
    <div class="clause-title">6.1 Obligaciones del Mandatario</div>
    Administrar diligentemente el inmueble; rendir cuentas mensualmente; mantener informado al Mandante sobre el
    estado del arrendamiento; trasladar los pagos recibidos, descontados sus honorarios, dentro de los cinco (5)
    días hábiles siguientes a su recaudo.
  </div>
  <div class="clause">
    <div class="clause-title">6.2 Obligaciones del Mandante</div>
    Entregar el inmueble en condiciones habitables; mantener al día los pagos de predial, administración e hipoteca
    (si aplica); autorizar las reparaciones mayores que sean necesarias; no interferir directamente en las relaciones
    con el arrendatario sin previa comunicación al Mandatario.
  </div>
</div>

@if($clauses && $clauses->isNotEmpty())
{{-- CLAUSULAS EDITABLES DEL CONSTRUCTOR DE PLANTILLAS --}}
<div class="section">
  <div class="section-title">SÉPTIMA — Cláusulas Adicionales</div>
  @foreach($clauses as $clause)
  <div class="clause">
    <div class="clause-title">{{ $clause->heading }}</div>
    {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@elseif(count($clausesAdditional) > 0)
{{-- CLAUSULAS LEGACY DEL CONTENIDO DEL DOCUMENTO --}}
<div class="section">
  <div class="section-title">SÉPTIMA — Cláusulas Adicionales</div>
  @foreach($clausesAdditional as $i => $clause)
  <div class="clause">
    <div class="clause-title">7.{{ $i + 1 }}</div>
    {{ $clause }}
  </div>
  @endforeach
</div>
@endif

@if($mandateNotes)
<div class="section">
  <div class="section-title">Observaciones</div>
  <div class="clauses-box">{{ $mandateNotes }}</div>
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($clauses && $clauses->isNotEmpty()) || count($clausesAdditional) > 0 ? 'OCTAVA' : 'SÉPTIMA' }} — Firma y Aceptación</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:4px;">
    En constancia de lo anterior, las partes firman el presente contrato en la ciudad de
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
  </p>
  <table class="sig-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">MANDATARIO</div>
          @if($company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      <td>
        <div class="sig-line">
          @if($mainOwner)
          <div class="sig-name">{{ $mainOwner->full_name ?? $mainOwner->company_name }}</div>
          <div class="sig-doc">{{ $mainOwner->documentType?->alias ?? 'C.C.' }} {{ $mainOwner->document_number }}</div>
          @elseif($mainTenant)
          <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
          <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
          @else
          <div class="sig-name">___________________________</div>
          @endif
          <div class="sig-role">MANDANTE (PROPIETARIO)</div>
        </div>
      </td>
    </tr>
  </table>
  <p class="legal-note">
    Contrato de mandato suscrito al amparo del Código Civil colombiano (art. 2142 y ss.) y el Código de
    Comercio (art. 1262 y ss.). Las controversias que surjan se resolverán conforme a la ley colombiana.
  </p>
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
