<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura Canon N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #922b21; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #922b21; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #922b21; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #922b21; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #922b21; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #922b21; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #922b21; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 40%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #fdf0ef; }
  .invoice-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 8px; }
  .invoice-table th { background: #922b21; color: #fff; font-weight: bold; padding: 4px 8px; text-align: left; }
  .invoice-table td { padding: 4px 8px; border-bottom: 1px solid #eee; }
  .invoice-table tr:nth-child(even) td { background: #fdf0ef; }
  .invoice-table tfoot td { font-weight: bold; border-top: 2px solid #922b21; padding: 6px 8px; font-size: 10pt; }
  .total-box { background: #f8d7da; border: 1px solid #f5c6cb; padding: 6px 10px; font-size: 11pt; font-weight: bold; text-align: right; margin-bottom: 8px; }
  .payment-box { background: #e9f7ef; border-left: 3px solid #196f3d; padding: 6px 10px; font-size: 8.5pt; margin-bottom: 6px; }
  .due-date-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 4px 10px; font-size: 9pt; margin-bottom: 8px; }
  .late-fee-note { font-size: 8pt; color: #666; font-style: italic; margin-top: 4px; }
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 18px; page-break-inside: avoid; }
  .sig-table td { width: 50%; padding: 0 12px; vertical-align: bottom; text-align: center; }
  .sig-line { border-top: 1px solid #1a1a1a; margin-top: 46px; padding-top: 3px; }
  .sig-name { font-weight: bold; font-size: 8.5pt; }
  .sig-role { font-size: 7.5pt; color: #555; }
  .sig-doc  { font-size: 7.5pt; color: #555; }
  .legal-note { font-size: 7pt; color: #666; text-align: justify; margin-top: 8px; border-top: 1px solid #ddd; padding-top: 4px; }
  .mt-4 { margin-top: 4px; }
  .text-right { text-align: right; }
</style>
</head>
<body>

@php
  $content         = $document->content ?? [];
  $periodFrom      = $content['period_from'] ?? null;
  $periodTo        = $content['period_to'] ?? null;
  $canonAmount     = $content['canon_amount'] ?? ($rent->canon ?? 0);
  $ivaAmount       = $content['iva_amount'] ?? ($rent->iva ?? 0);
  $adminAmount     = $content['administration_amount'] ?? 0;
  $lateFee         = $content['late_fee'] ?? 0;
  $paymentDueDate  = $content['payment_due_date'] ?? null;
  $total           = $canonAmount + $ivaAmount + $adminAmount + $lateFee;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;

  $periodFromFmt = $periodFrom ? \Carbon\Carbon::parse($periodFrom)->format('d/m/Y') : '---';
  $periodToFmt   = $periodTo   ? \Carbon\Carbon::parse($periodTo)->format('d/m/Y')   : '---';
  $dueDateFmt    = $paymentDueDate ? \Carbon\Carbon::parse($paymentDueDate)->format('d/m/Y') : '---';
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
      <div class="header-doc-title">FACTURA DE CANON</div>
      <div class="header-doc-number">N° {{ $document->number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Factura de Canon de Arrendamiento</div>
<div class="doc-date">
  Emisión: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }} —
  Contrato N° {{ $rent->contract_number ?? '---' }}
</div>

{{-- DATOS ARRENDATARIO --}}
<div class="section">
  <div class="section-title">Cobrar a</div>
  @php $firstTenant2 = $tenantPairs->first()?->tenant; @endphp
  @if($firstTenant2)
  <table class="data-table">
    <tr><td class="label">Nombre:</td><td class="value">{{ $tenantPairs->filter(fn($p) => $p->tenant)->map(fn($p) => $p->tenant->full_name ?? $p->tenant->company_name)->join(' / ') }}</td></tr>
    <tr><td class="label">Documento:</td><td class="value">{{ $firstTenant2->documentType?->alias ?? 'C.C.' }} {{ $firstTenant2->document_number }}</td></tr>
    <tr><td class="label">Inmueble:</td><td class="value">{{ $propertyAddress }}</td></tr>
    @if($periodFrom || $periodTo)
    <tr><td class="label">Período:</td><td class="value">{{ $periodFromFmt }} al {{ $periodToFmt }}</td></tr>
    @endif
  </table>
  @endif
</div>

{{-- DETALLE FACTURA --}}
<div class="section">
  <div class="section-title">Detalle</div>
  <table class="invoice-table">
    <thead>
      <tr>
        <th>Concepto</th>
        <th class="text-right" style="width:30%;text-align:right;">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Canon de arrendamiento
          @if($periodFrom) — período {{ $periodFromFmt }} al {{ $periodToFmt }} @endif
        </td>
        <td class="text-right">$ {{ number_format($canonAmount, 0, ',', '.') }}</td>
      </tr>
      @if($ivaAmount > 0)
      <tr>
        <td>IVA (19%) sobre el canon</td>
        <td class="text-right">$ {{ number_format($ivaAmount, 0, ',', '.') }}</td>
      </tr>
      @endif
      @if($adminAmount > 0)
      <tr>
        <td>Cuota de administración</td>
        <td class="text-right">$ {{ number_format($adminAmount, 0, ',', '.') }}</td>
      </tr>
      @endif
      @if($lateFee > 0)
      <tr>
        <td>Mora / Intereses de mora</td>
        <td class="text-right">$ {{ number_format($lateFee, 0, ',', '.') }}</td>
      </tr>
      @endif
    </tbody>
    <tfoot>
      <tr>
        <td><strong>TOTAL A PAGAR</strong></td>
        <td class="text-right"><strong>$ {{ number_format($total, 0, ',', '.') }}</strong></td>
      </tr>
    </tfoot>
  </table>

  @if($paymentDueDate)
  <div class="due-date-box">
    Fecha límite de pago: <strong>{{ $dueDateFmt }}</strong>
  </div>
  @endif
</div>

{{-- DATOS DE PAGO --}}
@if($rent->consignment_account || $rent->paymentBank)
<div class="section">
  <div class="section-title">Datos para el Pago</div>
  <div class="payment-box">
    @if($rent->paymentBank)
    <strong>Banco:</strong> {{ $rent->paymentBank->name }} &nbsp;|&nbsp;
    @endif
    @if($rent->consignment_account)
    <strong>Cuenta:</strong> {{ $rent->consignment_account }}
    @endif
    <br><span style="font-size:8pt;">Titular: {{ $company->company_name }}</span>
  </div>
  @if($lateFee > 0)
  <p class="late-fee-note">* Se han aplicado intereses de mora. El pago oportuno evita cargos adicionales.</p>
  @endif
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">Condiciones y Cláusulas</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- SELLO / FIRMA --}}
<div class="section">
  <div class="section-title">Expedida por</div>
  <table class="sig-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">ARRENDADOR / ADMINISTRADOR</div>
          @if($company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      <td style="vertical-align:middle; text-align:center;">
        <div style="border: 2px dashed #922b21; border-radius: 50%; width: 80px; height: 80px; margin: 0 auto; display:flex; align-items:center; justify-content:center;">
          <span style="font-size:7pt;color:#922b21;text-align:center;">SELLO<br>INMOBILIARIA</span>
        </div>
      </td>
    </tr>
  </table>
  <p class="legal-note">
    El no pago oportuno del canon causará intereses de mora a la tasa máxima legal permitida.
    Esta factura es válida como soporte contable. Conserve este documento.
    Contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}.
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
