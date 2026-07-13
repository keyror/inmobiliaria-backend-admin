<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Liquidación Final N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #7b241c; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #7b241c; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #7b241c; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #7b241c; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #7b241c; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #7b241c; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #7b241c; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #fdf0ef; }
  .liq-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .liq-table th { font-weight: bold; padding: 3px 6px; text-align: left; border-bottom: 2px solid #7b241c; }
  .liq-table td { padding: 3px 6px; border-bottom: 1px solid #eee; }
  .liq-table .debit td { background: #fdf0ef; }
  .liq-table .credit td { background: #eaf5ea; }
  .liq-table tfoot td { font-weight: bold; border-top: 2px solid #7b241c; padding: 5px 6px; }
  .balance-box { padding: 8px 12px; font-size: 11pt; font-weight: bold; margin: 6px 0; border-radius: 4px; }
  .balance-positive { background: #fdf0ef; border: 1px solid #e8b0ab; color: #7b241c; }
  .balance-negative { background: #e9f7ef; border: 1px solid #a8d5b5; color: #1a6e30; }
  .balance-zero { background: #f8f8f8; border: 1px solid #ccc; color: #444; }
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 18px; page-break-inside: avoid; }
  .sig-table td { width: 50%; padding: 0 12px; vertical-align: bottom; text-align: center; }
  .sig-line { border-top: 1px solid #1a1a1a; margin-top: 46px; padding-top: 3px; }
  .sig-name { font-weight: bold; font-size: 8.5pt; }
  .sig-role { font-size: 7.5pt; color: #555; }
  .sig-doc  { font-size: 7.5pt; color: #555; }
  .legal-note { font-size: 7pt; color: #666; text-align: justify; margin-top: 8px; border-top: 1px solid #ddd; padding-top: 4px; }
  .mt-4 { margin-top: 4px; }
  .text-right { text-align: right; }
  .tag-debit  { background: #f8d7da; color: #721c24; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; }
  .tag-credit { background: #d4edda; color: #155724; padding: 1px 5px; border-radius: 2px; font-size: 7.5pt; }
</style>
</head>
<body>

@php
  $content        = $document->content ?? [];
  $items          = $content['pending_payments'] ?? [];
  $totalDebits    = $content['total_debits'] ?? 0;
  $totalCredits   = $content['total_credits'] ?? 0;
  $balance        = $content['total_pending'] ?? ($totalDebits - $totalCredits);
  $contentNotes   = $content['obligations_notes'] ?? null;

  // Separar débitos y créditos
  $debits  = array_filter($items, fn($i) => ($i['type'] ?? 'debit') === 'debit');
  $credits = array_filter($items, fn($i) => ($i['type'] ?? 'debit') === 'credit');

  // Si no tiene tipo definido, todos son débitos (compatibilidad con actas)
  if (empty($debits) && empty($credits) && !empty($items)) {
    $debits = $items;
    $balance = array_sum(array_column($items, 'amount'));
  }

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
      <div class="header-doc-title">LIQUIDACIÓN FINAL</div>
      <div class="header-doc-number">N° {{ $document->number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Liquidación Final del Contrato de Arrendamiento</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Vigencia: {{ $rent->start_date?->format('d/m/Y') }} al {{ $rent->end_date?->format('d/m/Y') ?? 'Indefinido' }} —
  Fecha liquidación: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- PARTES --}}
<div class="section">
  <div class="section-title">1. Partes del Contrato</div>
  <table class="data-table">
    <tr>
      <td class="label">Arrendador:</td>
      <td class="value">{{ $company->company_name }} — NIT: {{ $company->nit }}</td>
    </tr>
    @if($mainTenant)
    <tr>
      <td class="label">Arrendatario:</td>
      <td class="value">{{ $mainTenant->full_name ?? $mainTenant->company_name }} — {{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</td>
    </tr>
    @endif
    <tr>
      <td class="label">Inmueble:</td>
      <td class="value">{{ $propertyAddress }}</td>
    </tr>
    <tr>
      <td class="label">Canon mensual:</td>
      <td class="value">${{ number_format($rent->canon ?? 0, 0, ',', '.') }}</td>
    </tr>
  </table>
</div>

{{-- LIQUIDACION --}}
<div class="section">
  <div class="section-title">2. Detalle de la Liquidación</div>

  @if(count($debits) > 0)
  <div class="section-subtitle">Cargos a Favor del Arrendador (Débitos)</div>
  <table class="liq-table">
    <thead>
      <tr>
        <th>Concepto</th>
        <th class="text-right" style="width:28%;text-align:right;">Valor</th>
      </tr>
    </thead>
    <tbody>
      @foreach($debits as $item)
      <tr class="debit">
        <td>{{ $item['concept'] ?? '' }}</td>
        <td class="text-right">$ {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
    @if($totalDebits > 0)
    <tfoot>
      <tr>
        <td>Subtotal Débitos</td>
        <td class="text-right">$ {{ number_format($totalDebits, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
    @endif
  </table>
  @endif

  @if(count($credits) > 0)
  <div class="section-subtitle mt-4">Abonos a Favor del Arrendatario (Créditos)</div>
  <table class="liq-table">
    <thead>
      <tr>
        <th>Concepto</th>
        <th class="text-right" style="width:28%;text-align:right;">Valor</th>
      </tr>
    </thead>
    <tbody>
      @foreach($credits as $item)
      <tr class="credit">
        <td>{{ $item['concept'] ?? '' }}</td>
        <td class="text-right">$ {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
    @if($totalCredits > 0)
    <tfoot>
      <tr>
        <td>Subtotal Créditos</td>
        <td class="text-right">$ {{ number_format($totalCredits, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
    @endif
  </table>
  @endif

  @if(count($items) === 0)
  <p style="color:#888;font-size:8pt;">Sin ítems de liquidación registrados.</p>
  @endif
</div>

{{-- SALDO --}}
<div class="section">
  <div class="section-title">3. Saldo a Pagar</div>
  @php $absBalance = abs($balance); @endphp
  @if($balance > 0)
  <div class="balance-box balance-positive">
    Saldo a pagar por el ARRENDATARIO: <strong>$ {{ number_format($absBalance, 0, ',', '.') }}</strong>
  </div>
  @elseif($balance < 0)
  <div class="balance-box balance-negative">
    Saldo a devolver al ARRENDATARIO: <strong>$ {{ number_format($absBalance, 0, ',', '.') }}</strong>
  </div>
  @else
  <div class="balance-box balance-zero">
    Saldo en ceros — No hay valores pendientes entre las partes.
  </div>
  @endif

  @if($contentNotes)
  <div style="margin-top:6px;font-size:8.5pt;padding:5px 8px;background:#f8f9fa;border-left:3px solid #7b241c;">
    {{ $contentNotes }}
  </div>
  @endif
</div>

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">Cláusulas de Liquidación</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($clauses && $clauses->isNotEmpty()) ? '5' : '4' }}. Paz y Salvo — Firmas</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:6px;">
    Las partes declaran haber revisado y aceptado la presente liquidación en la ciudad de
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
    La firma de este documento implica que las partes se declaran a paz y salvo por todos los conceptos del
    contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}, salvo los valores
    @if($balance > 0) pendientes de pago señalados arriba. @else devueltos según se indica. @endif
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
