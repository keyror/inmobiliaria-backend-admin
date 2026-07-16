<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrato de Colocación N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #1a4a5c; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #1a4a5c; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #1a4a5c; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #1a4a5c; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #1a4a5c; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #1a4a5c; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #1a4a5c; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f2f6f8; }
  .clause { margin-bottom: 7px; font-size: 9pt; text-align: justify; }
  .clause-title { font-weight: bold; color: #1a4a5c; margin-bottom: 2px; }
  .fee-box { background: #e8f4f8; border: 1px solid #b8d4e8; border-radius: 4px; padding: 5px 10px; font-size: 9pt; margin: 6px 0; }
  .clauses-box { background: #f2f6f8; border: 1px solid #c0cfd8; padding: 6px 10px; font-size: 8.5pt; margin-bottom: 6px; }
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
  $content           = $document->content ?? [];
  $placementFee      = $content['placement_fee'] ?? null;
  $feeNotes          = $content['placement_fee_notes'] ?? null;
  $clausesAdditional = $content['clauses_additional'] ?? [];
  $contentNotes      = $content['notes'] ?? null;

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
      <div class="header-doc-title">CONTRATO DE COLOCACIÓN</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Contrato de Colocación Inmobiliaria</div>
<div class="doc-date">
  Ref. N° {{ $rent->contract_number ?? '---' }} —
  Ciudad: {{ $rent->signed_city ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- PARTES --}}
<div class="section">
  <div class="section-title">PRIMERA — Partes</div>

  <div class="section-subtitle">INMOBILIARIA (Gestora)</div>
  <table class="data-table">
    <tr><td class="label">Nombre / Razón Social:</td><td class="value">{{ $company->company_name }}</td></tr>
    <tr><td class="label">NIT:</td><td class="value">{{ $company->nit }}</td></tr>
    @if($company->legalRepresentative)
    <tr><td class="label">Rep. Legal:</td><td class="value">{{ $company->legalRepresentative->full_name }}</td></tr>
    @endif
  </table>

  @if($mainOwner)
  <div class="section-subtitle mt-4">PROPIETARIO (Contratante)</div>
  <table class="data-table">
    <tr><td class="label">Nombre:</td><td class="value">{{ $mainOwner->full_name ?? $mainOwner->company_name }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $mainOwner->documentType?->alias ?? 'C.C.' }} {{ $mainOwner->document_number }}</td></tr>
  </table>
  @endif

  @php $tenantCount = $tenantPairs->filter(fn($p) => $p->tenant)->count(); $tenantIdx = 0; @endphp
  @foreach($tenantPairs->filter(fn($p) => $p->tenant) as $pair)
  @php $tenantIdx++; $t = $pair->tenant; @endphp
  <div class="section-subtitle mt-4">{{ $tenantCount > 1 ? 'ARRENDATARIO CONSEGUIDO ' . $tenantIdx : 'ARRENDATARIO CONSEGUIDO' }}</div>
  <table class="data-table">
    <tr><td class="label">Nombre:</td><td class="value">{{ $t->full_name ?? $t->company_name }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td></tr>
  </table>
  @endforeach
</div>

{{-- INMUEBLE --}}
<div class="section">
  <div class="section-title">SEGUNDA — Inmueble Objeto de la Colocación</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    @if($rent->canon)
    <tr>
      <td class="label">Canon pactado:</td>
      <td class="value" colspan="3"><strong>${{ number_format($rent->canon, 0, ',', '.') }}/mes</strong></td>
    </tr>
    @endif
  </table>
</div>

{{-- OBJETO --}}
<div class="section">
  <div class="section-title">TERCERA — Objeto del Contrato</div>
  <div class="clause">
    La INMOBILIARIA ha prestado al PROPIETARIO el servicio de <strong>colocación inmobiliaria</strong>, consistente
    en la búsqueda, selección y presentación de un candidato apto para arrendar el inmueble descrito, incluyendo el
    estudio de solicitud, verificación de referencias y demás gestiones previas a la celebración del contrato de
    arrendamiento. Este servicio se presta por <strong>una sola vez</strong> y no implica la administración continua
    del inmueble.
  </div>
</div>

{{-- HONORARIOS --}}
<div class="section">
  <div class="section-title">CUARTA — Honorarios de Colocación</div>
  @if($placementFee)
  <div class="fee-box">
    Valor del servicio de colocación: <strong>$ {{ number_format($placementFee, 0, ',', '.') }}</strong>
    @if($feeNotes) — {{ $feeNotes }} @endif
  </div>
  @elseif($rent->canon)
  <div class="fee-box">
    Valor del servicio de colocación: <strong>$ {{ number_format($rent->canon, 0, ',', '.') }}</strong>
    (equivalente a un (1) mes de canon, valor acordado entre las partes)
  </div>
  @else
  <div class="fee-box">
    Valor del servicio: según lo acordado entre las partes.
  </div>
  @endif
  <div class="clause">
    El pago de los honorarios se realizará de contado a la firma del contrato de arrendamiento. Una vez pagados,
    no habrá lugar a devolución por ningún concepto, salvo que el arrendatario conseguido no suscriba el contrato
    por causas imputables exclusivamente a la INMOBILIARIA.
  </div>
</div>

{{-- ALCANCE --}}
<div class="section">
  <div class="section-title">QUINTA — Alcance y Limitaciones</div>
  <div class="clause">
    La INMOBILIARIA no asume responsabilidad por el cumplimiento de las obligaciones del contrato de arrendamiento
    celebrado entre el PROPIETARIO y el arrendatario, ni por los cánones dejados de pagar, los daños al inmueble
    o cualquier otra obligación que surja de dicho contrato. La relación contractual continua (si se requiere)
    deberá regularse mediante un contrato de administración/mandato independiente.
  </div>
</div>

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">SEXTA — Cláusulas Adicionales</div>
  @foreach($clauses as $clause)
  <div class="clause">
    <div class="clause-title">{{ $clause->heading }}</div>
    {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@elseif(count($clausesAdditional) > 0)
<div class="section">
  <div class="section-title">SEXTA — Cláusulas Adicionales</div>
  @foreach($clausesAdditional as $i => $clause)
  <div class="clause">
    <div class="clause-title">6.{{ $i + 1 }}</div>
    {{ $clause }}
  </div>
  @endforeach
</div>
@endif

@if($contentNotes)
<div class="section">
  <div class="section-title">Observaciones</div>
  <div class="clauses-box">{{ $contentNotes }}</div>
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($clauses && $clauses->isNotEmpty()) || count($clausesAdditional) > 0 ? 'SÉPTIMA' : 'SEXTA' }} — Firma y Aceptación</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:4px;">
    Las partes suscriben el presente contrato en la ciudad de
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
  </p>
  <table class="sig-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">INMOBILIARIA — GESTORA</div>
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
          @else
          <div class="sig-name">___________________________</div>
          @endif
          <div class="sig-role">PROPIETARIO — CONTRATANTE</div>
        </div>
      </td>
    </tr>
  </table>
  <p class="legal-note">
    Contrato de prestación de servicios de colocación inmobiliaria, de naturaleza mercantil, suscrito bajo las
    normas del Código de Comercio colombiano. No establece relación laboral ni administrativa continua entre las partes.
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
