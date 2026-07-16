<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Póliza Seguro de Arrendamiento N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #4a235a; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #4a235a; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #4a235a; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #4a235a; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #4a235a; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #4a235a; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #4a235a; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f5f0f9; }
  .policy-box { background: #f0eaf5; border: 2px solid #4a235a; border-radius: 6px; padding: 10px 14px; margin-bottom: 10px; }
  .policy-number { font-size: 14pt; font-weight: bold; color: #4a235a; text-align: center; margin-bottom: 4px; }
  .policy-insurer { font-size: 10pt; text-align: center; color: #555; margin-bottom: 8px; }
  .coverage-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
  .coverage-table td { padding: 4px 6px; border-bottom: 1px solid #ddd; }
  .coverage-table td.lbl { font-weight: bold; color: #4a235a; width: 45%; }
  .status-active { background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 3px; font-weight: bold; }
  .status-expired { background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 3px; font-weight: bold; }
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
  $content        = $document->content ?? [];
  $insurerName    = $content['insurer_name'] ?? null;
  $policyNumber   = $content['policy_number'] ?? null;
  $coverageAmount = $content['coverage_amount'] ?? 0;
  $premiumAmount  = $content['premium_amount'] ?? 0;
  $policyStart    = $content['policy_start_date'] ?? ($rent->start_date?->format('Y-m-d') ?? null);
  $policyEnd      = $content['policy_end_date'] ?? ($rent->end_date?->format('Y-m-d') ?? null);
  $beneficiary    = $content['beneficiary'] ?? $company->company_name;
  $contentNotes   = $content['notes'] ?? null;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;

  $policyStartFmt = $policyStart ? \Carbon\Carbon::parse($policyStart)->format('d/m/Y') : '---';
  $policyEndFmt   = $policyEnd   ? \Carbon\Carbon::parse($policyEnd)->format('d/m/Y')   : '---';

  $isActive = false;
  if ($policyEnd) {
    $isActive = \Carbon\Carbon::parse($policyEnd)->isFuture();
  }
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
      <div class="header-doc-title">PÓLIZA DE SEGURO DE ARRENDAMIENTO</div>
      <div class="header-doc-number">Ref. {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Ficha de Póliza de Seguro de Arrendamiento</div>
<div class="doc-date">
  Contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }} —
  Fecha de registro: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- DATOS POLIZA --}}
<div class="section">
  <div class="policy-box">
    <div class="policy-number">Póliza N° {{ $policyNumber ?? '_______________' }}</div>
    <div class="policy-insurer">Aseguradora: <strong>{{ $insurerName ?? 'No registrada' }}</strong></div>
    @if($policyEnd)
    <div style="text-align:center;">
      Estado:
      @if($isActive)
        <span class="status-active">VIGENTE</span>
      @else
        <span class="status-expired">VENCIDA</span>
      @endif
    </div>
    @endif
  </div>
</div>

{{-- COBERTURAS --}}
<div class="section">
  <div class="section-title">1. Datos de Cobertura</div>
  <table class="coverage-table">
    <tr>
      <td class="lbl">Aseguradora:</td>
      <td>{{ $insurerName ?? 'Sin registrar' }}</td>
      <td class="lbl">N° Póliza:</td>
      <td>{{ $policyNumber ?? 'Sin registrar' }}</td>
    </tr>
    <tr>
      <td class="lbl">Vigencia desde:</td>
      <td>{{ $policyStartFmt }}</td>
      <td class="lbl">Vigencia hasta:</td>
      <td>{{ $policyEndFmt }}</td>
    </tr>
    <tr>
      <td class="lbl">Suma asegurada (cobertura):</td>
      <td>{{ $coverageAmount > 0 ? '$ '.number_format($coverageAmount, 0, ',', '.') : 'Ver póliza original' }}</td>
      <td class="lbl">Prima mensual:</td>
      <td>{{ $premiumAmount > 0 ? '$ '.number_format($premiumAmount, 0, ',', '.') : '---' }}</td>
    </tr>
    <tr>
      <td class="lbl">Beneficiario:</td>
      <td colspan="3">{{ $beneficiary }}</td>
    </tr>
  </table>
</div>

{{-- INMUEBLE Y CONTRATO --}}
<div class="section">
  <div class="section-title">2. Inmueble y Contrato Asegurado</div>
  <table class="data-table">
    <tr>
      <td class="label">Código inmueble:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    <tr>
      <td class="label">Canon mensual:</td>
      <td class="value">${{ number_format($rent->canon ?? 0, 0, ',', '.') }}</td>
      <td class="label">Vigencia contrato:</td>
      <td class="value">{{ $rent->start_date?->format('d/m/Y') }} al {{ $rent->end_date?->format('d/m/Y') ?? 'Indef.' }}</td>
    </tr>
  </table>
</div>

{{-- ASEGURADO --}}
@if($tenantPairs->filter(fn($p) => $p->tenant)->isNotEmpty())
<div class="section">
  <div class="section-title">3. Asegurado / Tomador</div>
  @foreach($tenantPairs->filter(fn($p) => $p->tenant) as $pair)
  @php $t = $pair->tenant; @endphp
  <table class="data-table" style="{{ $loop->first ? '' : 'margin-top:4px;' }}">
    <tr><td class="label">Nombre:</td><td class="value">{{ $t->full_name ?? $t->company_name }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td></tr>
  </table>
  @endforeach
</div>
@endif

@if($contentNotes)
<div class="section">
  <div class="section-title">{{ $mainTenant ? '4' : '3' }}. Observaciones</div>
  <div style="background:#f8f9fa;border-left:3px solid #4a235a;padding:5px 8px;font-size:8.5pt;">{{ $contentNotes }}</div>
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">{{ ($mainTenant ? 4 : 3) + ($contentNotes ? 1 : 0) }}. Cláusulas y Condiciones</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- NOTA --}}
<div class="section">
  <div class="section-title">{{ ($mainTenant ? 4 : 3) + ($contentNotes ? 1 : 0) + ($clauses && $clauses->isNotEmpty() ? 1 : 0) }}. Aviso Legal</div>
  <p style="font-size:8.5pt;text-align:justify;line-height:1.5;">
    Este documento es un <strong>registro de referencia</strong> de la póliza de seguro de arrendamiento asociada al
    contrato indicado. La póliza original emitida por la aseguradora <strong>{{ $insurerName ?? '_______________' }}</strong>
    es el documento vinculante en caso de siniestro. En caso de siniestro, el tomador debe contactar directamente
    a la aseguradora con el número de póliza referenciado.
  </p>
  <p style="font-size:8.5pt;text-align:justify;margin-top:5px;line-height:1.5;">
    Expedido por: <strong>{{ $company->company_name }}</strong> —
    Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
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
