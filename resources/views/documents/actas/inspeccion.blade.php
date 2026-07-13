<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Acta de Inspección N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #3d5a80; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #3d5a80; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #3d5a80; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #3d5a80; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #3d5a80; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #3d5a80; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #3d5a80; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f0f4f8; }
  .condition-box { background: #f8f9fa; border-left: 3px solid #3d5a80; padding: 5px 8px; font-size: 8.5pt; margin-bottom: 6px; }
  .badge-yes { background: #d4edda; color: #155724; padding: 1px 6px; border-radius: 3px; font-size: 8pt; }
  .badge-no  { background: #f8d7da; color: #721c24; padding: 1px 6px; border-radius: 3px; font-size: 8pt; }
  .payments-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; border: 1px solid #ccc; margin-bottom: 5px; }
  .payments-table th { background: #e8edf3; font-weight: bold; padding: 3px 6px; border: 1px solid #ccc; text-align: left; }
  .payments-table td { padding: 3px 6px; border: 1px solid #ccc; }
  .payments-table tfoot td { font-weight: bold; background: #f0f4f8; }
  .next-inspection { background: #fff8e1; border: 1px solid #ffc107; border-radius: 4px; padding: 5px 10px; font-size: 9pt; margin-bottom: 6px; }
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
  $condition         = $content['property_condition'] ?? null;
  $pendingSvc        = $content['pending_services'] ?? false;
  $pendingSvcNote    = $content['pending_services_notes'] ?? null;
  $payments          = $content['pending_payments'] ?? [];
  $totalPending      = $content['total_pending'] ?? 0;
  $photosTaken       = $content['photos_taken'] ?? false;
  $obligationsNote   = $content['obligations_notes'] ?? null;
  $inspectorName     = $content['inspector_name'] ?? null;
  $nextInspection    = $content['next_inspection_date'] ?? null;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;
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
      <div class="header-doc-title">ACTA DE INSPECCIÓN</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Acta de Inspección Periódica del Inmueble</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Fecha de inspección: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- DATOS GENERALES --}}
<div class="section">
  <div class="section-title">1. Datos Generales</div>
  <table class="data-table">
    <tr>
      <td class="label">Inspector:</td>
      <td class="value">{{ $inspectorName ?? $company->company_name }}</td>
      <td class="label">Registro fotográfico:</td>
      <td class="value">
        @if($photosTaken)
          <span class="badge-yes">Sí — adjunto</span>
        @else
          <span class="badge-no">No</span>
        @endif
      </td>
    </tr>
    <tr>
      <td class="label">Arrendatario:</td>
      <td class="value">{{ $mainTenant?->full_name ?? $mainTenant?->company_name ?? '---' }}</td>
      <td class="label">Contrato vigente desde:</td>
      <td class="value">{{ $rent->start_date?->format('d/m/Y') }}</td>
    </tr>
  </table>
</div>

{{-- INMUEBLE --}}
<div class="section">
  <div class="section-title">2. Inmueble Inspeccionado</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    <tr>
      <td class="label">Canon mensual:</td>
      <td class="value">${{ number_format($rent->canon ?? 0, 0, ',', '.') }}</td>
      <td class="label">Sometido a P.H.:</td>
      <td class="value">{{ $rent->is_ph ? 'Sí' : 'No' }}</td>
    </tr>
  </table>
</div>

{{-- ESTADO DEL INMUEBLE --}}
<div class="section">
  <div class="section-title">3. Estado del Inmueble</div>
  @if($condition)
    <div class="condition-box">{{ $condition }}</div>
  @else
    <p style="color:#888;font-size:8pt;">Sin observaciones registradas sobre el estado del inmueble.</p>
  @endif
</div>

{{-- SERVICIOS --}}
<div class="section">
  <div class="section-title">4. Servicios Públicos</div>
  <table class="data-table">
    <tr>
      <td class="label">Servicios pendientes de pago:</td>
      <td class="value">
        @if($pendingSvc)
          <span class="badge-yes">Sí</span>
        @else
          <span class="badge-no">No</span>
        @endif
      </td>
    </tr>
    @if($pendingSvc && $pendingSvcNote)
    <tr>
      <td class="label">Detalle:</td>
      <td class="value">{{ $pendingSvcNote }}</td>
    </tr>
    @endif
  </table>

  @if(count($payments) > 0)
  <div class="section-subtitle mt-4">Valores en mora al momento de la inspección</div>
  <table class="payments-table">
    <thead>
      <tr><th>Concepto</th><th style="width:30%;text-align:right;">Valor</th></tr>
    </thead>
    <tbody>
      @foreach($payments as $payment)
      <tr>
        <td>{{ $payment['concept'] ?? '' }}</td>
        <td style="text-align:right;">$ {{ number_format($payment['amount'] ?? 0, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
    @if($totalPending > 0)
    <tfoot>
      <tr>
        <td>TOTAL EN MORA</td>
        <td style="text-align:right;">$ {{ number_format($totalPending, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
    @endif
  </table>
  @endif
</div>

{{-- OBSERVACIONES --}}
@if($obligationsNote)
<div class="section">
  <div class="section-title">5. Observaciones y Compromisos</div>
  <div class="condition-box">{{ $obligationsNote }}</div>
</div>
@endif

{{-- PROXIMA INSPECCION --}}
@if($nextInspection)
<div class="section">
  <div class="section-title">{{ $obligationsNote ? '6' : '5' }}. Próxima Inspección</div>
  <div class="next-inspection">
    Fecha programada para la próxima inspección: <strong>{{ \Carbon\Carbon::parse($nextInspection)->format('d/m/Y') }}</strong>
  </div>
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">Cláusulas Adicionales</div>
  @foreach($clauses as $clause)
  <div class="clause">
    <div class="clause-title">{{ $clause->heading }}</div>
    {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($obligationsNote ? 6 : 5) + ($nextInspection ? 1 : 0) + ($clauses && $clauses->isNotEmpty() ? 1 : 0) }}. Firmas</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:4px;">
    Las partes suscriben la presente acta de inspección en la ciudad de
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
  </p>
  <table class="sig-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $inspectorName ?? $company->company_name }}</div>
          <div class="sig-role">INSPECTOR — ARRENDADOR</div>
          @if(!$inspectorName && $company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      @if($mainTenant)
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
          <div class="sig-role">ARRENDATARIO — PRESENTE</div>
          <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
        </div>
      </td>
      @endif
    </tr>
  </table>
  <p class="legal-note">
    La presente acta de inspección hace parte integral del contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}
    y sirve de evidencia del estado del inmueble en la fecha señalada. Los compromisos adquiridos en esta acta
    son de obligatorio cumplimiento para ambas partes.
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
