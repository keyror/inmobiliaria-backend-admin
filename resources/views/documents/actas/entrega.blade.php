<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Acta de Entrega N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #1a3a5c; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #1a3a5c; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #1a3a5c; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #1a3a5c; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #1a3a5c; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #1a3a5c; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #1a3a5c; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f4f6f9; }
  .payments-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; border: 1px solid #ccc; margin-bottom: 5px; }
  .payments-table th { background: #e8edf3; font-weight: bold; padding: 3px 6px; border: 1px solid #ccc; text-align: left; }
  .payments-table td { padding: 3px 6px; border: 1px solid #ccc; }
  .payments-table tfoot td { font-weight: bold; background: #f4f6f9; }
  .total-box { background: #fff3cd; border: 1px solid #ffc107; padding: 4px 8px; font-size: 9pt; margin-bottom: 6px; }
  .condition-box { background: #f8f9fa; border-left: 3px solid #1a3a5c; padding: 5px 8px; font-size: 8.5pt; margin-bottom: 6px; }
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
  .text-center { text-align: center; }
</style>
</head>
<body>

@php
  $content        = $document->content ?? [];
  $condition      = $content['property_condition'] ?? null;
  $pendingSvc     = $content['pending_services'] ?? false;
  $pendingSvcNote = $content['pending_services_notes'] ?? null;
  $payments       = $content['pending_payments'] ?? [];
  $totalPending   = $content['total_pending'] ?? 0;
  $photosTaken    = $content['photos_taken'] ?? false;
  $obligationsNote= $content['obligations_notes'] ?? null;
  $signatories    = $content['signatories'] ?? [];

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
      <div class="header-doc-title">ACTA DE ENTREGA DE INMUEBLE</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Acta de Entrega de Inmueble</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Ciudad: {{ $rent->signed_city ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- PARTES --}}
<div class="section">
  <div class="section-title">1. Partes</div>

  <div class="section-subtitle">ARRENDADOR (Inmobiliaria)</div>
  <table class="data-table">
    <tr><td class="label">Nombre / Razón Social:</td><td class="value">{{ $company->company_name }}</td></tr>
    <tr><td class="label">NIT:</td><td class="value">{{ $company->nit }}</td></tr>
    @if($company->legalRepresentative)
    <tr><td class="label">Rep. Legal:</td><td class="value">{{ $company->legalRepresentative->full_name }}</td></tr>
    @endif
  </table>

  @php $tenantCount = $tenantPairs->filter(fn($p) => $p->tenant)->count(); $tenantIdx = 0; @endphp
  @foreach($tenantPairs as $pair)
    @if($pair->tenant)
    @php $tenantIdx++; $t = $pair->tenant; @endphp
    <div class="section-subtitle mt-4">{{ $tenantCount > 1 ? 'ARRENDATARIO ' . $tenantIdx : 'ARRENDATARIO' }}</div>
    <table class="data-table">
      <tr><td class="label">Nombre:</td><td class="value">{{ $t->full_name ?? $t->company_name }}</td></tr>
      <tr><td class="label">Documento:</td><td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td></tr>
    </table>
    @endif
  @endforeach
</div>

{{-- INMUEBLE --}}
<div class="section">
  <div class="section-title">2. Inmueble</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    <tr>
      <td class="label">Destinación:</td><td class="value">{{ ucfirst($rent->destination ?? 'Vivienda urbana') }}</td>
      <td class="label">Sometido a P.H.:</td><td class="value">{{ $rent->is_ph ? 'Sí' : 'No' }}</td>
    </tr>
  </table>
</div>

{{-- CONDICIÓN DEL INMUEBLE --}}
<div class="section">
  <div class="section-title">3. Estado del Inmueble al Momento de la Entrega</div>
  @if($condition)
    <div class="condition-box">{{ $condition }}</div>
  @else
    <p style="color:#888;font-size:8pt;">Sin observaciones registradas.</p>
  @endif
</div>

{{-- SERVICIOS PÚBLICOS --}}
<div class="section">
  <div class="section-title">4. Servicios Públicos y Deudas Pendientes</div>
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

  @if(is_array($payments) && count($payments) > 0)
  <div class="section-subtitle mt-4">Obligaciones económicas pendientes</div>
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
    <tfoot>
      <tr>
        <td>TOTAL PENDIENTE</td>
        <td style="text-align:right;">$ {{ number_format($totalPending, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
  </table>
  @endif
</div>

{{-- OBSERVACIONES --}}
@if($obligationsNote || $photosTaken)
<div class="section">
  <div class="section-title">5. Observaciones Generales</div>
  <table class="data-table">
    <tr>
      <td class="label">Fotos tomadas al momento del acta:</td>
      <td class="value">{{ $photosTaken ? 'Sí' : 'No' }}</td>
    </tr>
    @if($obligationsNote)
    <tr>
      <td class="label">Observaciones:</td>
      <td class="value">{{ $obligationsNote }}</td>
    </tr>
    @endif
  </table>
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">6. Cláusulas Adicionales</div>
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
  <div class="section-title">{{ ($clauses && $clauses->isNotEmpty()) ? '7' : '6' }}. Firmas</div>
  <div style="font-size:8pt;text-align:center;margin-bottom:4px;">
    Las partes suscriben la presente acta en constancia de lo acordado, en
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
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
      @php $firstTenant2 = $tenantPairs->first()?->tenant; @endphp
      @if($firstTenant2)
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $firstTenant2->full_name ?? $firstTenant2->company_name }}</div>
          <div class="sig-role">{{ $tenantPairs->filter(fn($p) => $p->tenant)->count() > 1 ? 'ARRENDATARIO 1' : 'ARRENDATARIO' }}</div>
          <div class="sig-doc">{{ $firstTenant2->documentType?->alias ?? 'C.C.' }} {{ $firstTenant2->document_number }}</div>
        </div>
      </td>
      @else
      <td></td>
      @endif
    </tr>
    @php $tIdx2 = 1; @endphp
    @foreach($tenantPairs->skip(1) as $pair)
      @if($pair->tenant)
      @php $tIdx2++; $t2 = $pair->tenant; @endphp
      <tr>
        <td style="padding-top:20px;"></td>
        <td style="padding-top:20px;">
          <div class="sig-line">
            <div class="sig-name">{{ $t2->full_name ?? $t2->company_name }}</div>
            <div class="sig-role">ARRENDATARIO {{ $tIdx2 }}</div>
            <div class="sig-doc">{{ $t2->documentType?->alias ?? 'C.C.' }} {{ $t2->document_number }}</div>
          </div>
        </td>
      </tr>
      @endif
    @endforeach
    @foreach($signatories as $signatory)
    <tr>
      <td style="padding-top:20px;">
        <div class="sig-line">
          <div class="sig-name">{{ $signatory['name'] ?? '_______________' }}</div>
          <div class="sig-role">{{ strtoupper($signatory['role'] ?? 'TESTIGO') }}</div>
          @if(!empty($signatory['cc']))
          <div class="sig-doc">C.C. {{ $signatory['cc'] }}</div>
          @endif
        </div>
      </td>
      <td></td>
    </tr>
    @endforeach
  </table>
  <p class="legal-note">
    La entrega del inmueble en el estado descrito no exime al arrendatario de las obligaciones pendientes relacionadas con el contrato.
    El presente documento es prueba de la entrega física del inmueble y hace parte integral del contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}.
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
