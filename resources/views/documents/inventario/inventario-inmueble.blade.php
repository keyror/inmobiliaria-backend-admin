<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario del Inmueble N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #1a5c4a; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #1a5c4a; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #1a5c4a; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #1a5c4a; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #1a5c4a; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #1a5c4a; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #1a5c4a; margin: 6px 0 3px 0; page-break-after: avoid; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #eef7f4; }
  .inv-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 8px; page-break-inside: avoid; }
  .inv-table th { background: #1a5c4a; color: #fff; font-weight: bold; padding: 3px 6px; text-align: left; }
  .inv-table td { padding: 3px 6px; border-bottom: 1px solid #cce5de; }
  .inv-table tr:nth-child(even) td { background: #eef7f4; }
  .condition-box { background: #f8f9fa; border-left: 3px solid #1a5c4a; padding: 5px 8px; font-size: 8.5pt; margin-bottom: 6px; }
  .badge-bueno { background: #d4edda; color: #155724; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; }
  .badge-regular { background: #fff3cd; color: #856404; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; }
  .badge-malo { background: #f8d7da; color: #721c24; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; }
  .badge-yes { background: #d4edda; color: #155724; padding: 1px 6px; border-radius: 3px; font-size: 8pt; }
  .badge-no  { background: #f8d7da; color: #721c24; padding: 1px 6px; border-radius: 3px; font-size: 8pt; }
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
  $content          = $document->content ?? [];
  $generalCondition = $content['general_condition'] ?? null;
  $inventoryNotes   = $content['inventory_notes'] ?? ($content['obligations_notes'] ?? null);
  $rooms            = $content['rooms'] ?? [];
  $photosTaken      = $content['photos_taken'] ?? false;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;

  // Helper: condition badge class
  $condClass = function($cond) {
    return match(strtolower($cond ?? '')) {
      'bueno', 'good', 'bien' => 'badge-bueno',
      'regular', 'medio' => 'badge-regular',
      'malo', 'bad', 'daño', 'dañado' => 'badge-malo',
      default => ''
    };
  };
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
      <div class="header-doc-title">INVENTARIO DEL INMUEBLE</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Inventario de Elementos del Inmueble</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- IDENTIFICACION --}}
<div class="section">
  <div class="section-title">1. Datos del Inmueble</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    @if($mainTenant)
    <tr>
      <td class="label">Arrendatario:</td>
      <td class="value" colspan="3">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</td>
    </tr>
    @endif
    <tr>
      <td class="label">Fecha del inventario:</td>
      <td class="value">{{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</td>
      <td class="label">Registro fotográfico:</td>
      <td class="value">
        @if($photosTaken)<span class="badge-yes">Sí</span>@else<span class="badge-no">No</span>@endif
      </td>
    </tr>
  </table>
</div>

{{-- ESTADO GENERAL --}}
@if($generalCondition)
<div class="section">
  <div class="section-title">2. Estado General del Inmueble</div>
  <div class="condition-box">{{ $generalCondition }}</div>
</div>
@endif

{{-- INVENTARIO POR ÁREAS (si viene en formato rooms[]) --}}
@if(count($rooms) > 0)
<div class="section">
  <div class="section-title">{{ $generalCondition ? '3' : '2' }}. Inventario por Áreas</div>
  @foreach($rooms as $room)
  <div class="section-subtitle">{{ $room['name'] ?? 'Área sin nombre' }}</div>
  @if(!empty($room['items']))
  <table class="inv-table">
    <thead>
      <tr>
        <th>Elemento</th>
        <th style="width:10%;text-align:center;">Cant.</th>
        <th style="width:22%;text-align:center;">Estado</th>
        <th style="width:30%;">Observaciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($room['items'] as $item)
      <tr>
        <td>{{ $item['name'] ?? '---' }}</td>
        <td style="text-align:center;">{{ $item['quantity'] ?? 1 }}</td>
        <td style="text-align:center;">
          @php $cc = $condClass($item['condition'] ?? ''); @endphp
          @if($cc)
            <span class="{{ $cc }}">{{ ucfirst($item['condition'] ?? '---') }}</span>
          @else
            {{ $item['condition'] ?? '---' }}
          @endif
        </td>
        <td>{{ $item['notes'] ?? '' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <p style="font-size:8pt;color:#888;margin-bottom:6px;">Sin elementos registrados para esta área.</p>
  @endif
  @endforeach
</div>

@else
{{-- Inventario en formato simple (sin rooms) --}}
<div class="section">
  <div class="section-title">{{ $generalCondition ? '3' : '2' }}. Lista de Elementos</div>
  <table class="inv-table">
    <thead>
      <tr>
        <th>Elemento / Área</th>
        <th style="width:12%;text-align:center;">Cant.</th>
        <th style="width:22%;text-align:center;">Estado</th>
        <th style="width:32%;">Observaciones</th>
      </tr>
    </thead>
    <tbody>
      @php $itemsList = $content['items'] ?? []; @endphp
      @if(count($itemsList) > 0)
        @foreach($itemsList as $item)
        <tr>
          <td>{{ $item['name'] ?? $item['concept'] ?? '---' }}</td>
          <td style="text-align:center;">{{ $item['quantity'] ?? 1 }}</td>
          <td style="text-align:center;">
            @php $cc = $condClass($item['condition'] ?? ''); @endphp
            @if($cc)<span class="{{ $cc }}">{{ ucfirst($item['condition'] ?? '---') }}</span>
            @else{{ $item['condition'] ?? '---' }}@endif
          </td>
          <td>{{ $item['notes'] ?? '' }}</td>
        </tr>
        @endforeach
      @else
        @for($r = 0; $r < 15; $r++)
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        @endfor
      @endif
    </tbody>
  </table>
  <p style="font-size:7.5pt;color:#888;margin-top:3px;font-style:italic;">
    * Registre todos los elementos, muebles y accesorios presentes en el inmueble al momento del inventario.
  </p>
</div>
@endif

@if($inventoryNotes)
<div class="section">
  <div class="section-title">Observaciones Adicionales</div>
  <div class="condition-box">{{ $inventoryNotes }}</div>
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">Cláusulas Adicionales</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">Firmas de Conformidad</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:4px;">
    Las partes declaran haber revisado y aceptado el presente inventario en la ciudad de
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
  </p>
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
      @if($mainTenant)
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
          <div class="sig-role">ARRENDATARIO</div>
          <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
        </div>
      </td>
      @endif
    </tr>
  </table>
  <p class="legal-note">
    El presente inventario hace parte integral del contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}.
    Cualquier elemento aquí registrado que al momento de la devolución presente deterioro superior al normal
    por el uso será responsabilidad del ARRENDATARIO, quien deberá restituirlo o compensar su valor.
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
