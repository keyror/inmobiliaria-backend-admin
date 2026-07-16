<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carta de Garantía Codeudor N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #4a3500; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #4a3500; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #4a3500; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #4a3500; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #4a3500; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 8px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #4a3500; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #4a3500; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f9f7f2; }
  .guarantee-box { background: #fdf3e7; border: 2px solid #4a3500; border-radius: 4px; padding: 8px 12px; font-size: 9pt; margin-bottom: 8px; text-align: justify; }
  .solidarity-badge { display: inline-block; background: #4a3500; color: #fff; padding: 2px 10px; border-radius: 3px; font-size: 8pt; font-weight: bold; margin-bottom: 4px; }
  .observations-box { background: #f9f7f2; border-left: 3px solid #4a3500; padding: 5px 8px; font-size: 8.5pt; margin-bottom: 6px; }
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
  $content         = $document->content ?? [];
  $guaranteeType   = $content['guarantee_type'] ?? 'solidaria';
  $observations    = $content['guarantee_observations'] ?? ($content['obligations_notes'] ?? null);

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;
  $codebtors        = $tenantPairs->filter(fn($p) => $p->codebtor !== null)->map(fn($p) => $p->codebtor);
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
      <div class="header-doc-title">GARANTÍA CODEUDOR</div>
      <div class="header-doc-number">Contrato N° {{ $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Carta de Garantía y Aval de Codeudor</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Ciudad: {{ $rent->signed_city ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- CONTRATO DE REFERENCIA --}}
<div class="section">
  <div class="section-title">1. Contrato de Referencia</div>
  <table class="data-table">
    <tr>
      <td class="label">N° Contrato:</td>
      <td class="value">{{ $rent->contract_number ?? '---' }}</td>
      <td class="label">Tipo:</td>
      <td class="value">{{ $rent->contractType?->name ?? 'Arrendamiento' }}</td>
    </tr>
    <tr>
      <td class="label">Inmueble:</td>
      <td class="value" colspan="3">{{ $propertyAddress }}</td>
    </tr>
    <tr>
      <td class="label">Canon mensual:</td>
      <td class="value">${{ number_format($rent->canon ?? 0, 0, ',', '.') }}</td>
      <td class="label">Vigencia:</td>
      <td class="value">{{ $rent->start_date?->format('d/m/Y') }} al {{ $rent->end_date?->format('d/m/Y') ?? 'Indefinido' }}</td>
    </tr>
    @foreach($tenantPairs->filter(fn($p) => $p->tenant) as $pair)
    @php $t = $pair->tenant; @endphp
    <tr>
      <td class="label">{{ $loop->first ? 'Arrendatario:' : '' }}</td>
      <td class="value" colspan="3">{{ $t->full_name ?? $t->company_name }} — {{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td>
    </tr>
    @endforeach
  </table>
</div>

{{-- CODEUDORES --}}
<div class="section">
  <div class="section-title">2. Codeudor(es) Garante(s)</div>
  @if($codebtors->count() > 0)
    @foreach($codebtors as $codebtor)
    <table class="data-table" style="margin-bottom:6px;">
      <tr><td class="label">Nombre:</td><td class="value">{{ $codebtor->full_name ?? $codebtor->company_name }}</td></tr>
      <tr><td class="label">Documento:</td><td class="value">{{ $codebtor->documentType?->alias ?? 'C.C.' }} {{ $codebtor->document_number }}</td></tr>
    </table>
    @endforeach
  @else
  <p style="color:#888;font-size:8pt;">Sin codeudores registrados en el contrato.</p>
  @endif
</div>

{{-- GARANTIA --}}
<div class="section">
  <div class="section-title">3. Alcance de la Garantía</div>
  <div class="guarantee-box">
    <div style="margin-bottom:4px;">Tipo de garantía: <span class="solidarity-badge">{{ strtoupper($guaranteeType) }}</span></div>
    <p style="margin-top:6px;">
      El/Los CODEUDOR(ES) se constituye(n) en garante(s) <strong>solidario(s)</strong> de todas las obligaciones
      del ARRENDATARIO derivadas del contrato de arrendamiento referenciado, incluyendo pero sin limitarse a:
      el pago oportuno de los cánones de arrendamiento, los servicios públicos a cargo del arrendatario,
      los daños al inmueble, las costas procesales en caso de demanda y cualquier otra obligación pactada en
      el contrato.
    </p>
    <p style="margin-top:6px;">
      La responsabilidad del CODEUDOR es <strong>solidaria e ilimitada</strong> frente al ARRENDADOR:
      el ARRENDADOR podrá exigir el cumplimiento total de las obligaciones indistintamente al ARRENDATARIO
      o al CODEUDOR, sin necesidad de constituir en mora al uno antes que al otro.
    </p>
  </div>
</div>

{{-- OBLIGACIONES DEL CODEUDOR --}}
<div class="section">
  <div class="section-title">4. Obligaciones del Codeudor</div>
  <p style="font-size:9pt;text-align:justify;margin-bottom:5px;line-height:1.5;">
    El CODEUDOR se obliga expresamente a: (a) pagar cualquier suma adeudada por el ARRENDATARIO cuando sea
    requerido por el ARRENDADOR; (b) notificar al ARRENDADOR cualquier cambio de domicilio o situación financiera
    relevante; (c) no trasladar ni ceder su condición de garante sin autorización escrita del ARRENDADOR;
    (d) mantener vigente su capacidad de pago durante toda la vigencia del contrato.
  </p>
</div>

@if($observations)
<div class="section">
  <div class="section-title">5. Observaciones</div>
  <div class="observations-box">{{ $observations }}</div>
</div>
@endif

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">{{ $observations ? '6' : '5' }}. Cláusulas Adicionales de la Garantía</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($observations ? 6 : 5) + ($clauses && $clauses->isNotEmpty() ? 1 : 0) }}. Firmas</div>
  <p style="font-size:8pt;text-align:center;margin-bottom:4px;">
    Las partes suscriben el presente documento en la ciudad de
    <strong>{{ $rent->signed_city ?? '_______________' }}</strong>,
    el {{ $document->document_date?->format('d \d\e F \d\e Y') ?? 'día ___ de ____________ de ______' }}.
  </p>
  <table class="sig-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">ARRENDADOR — ACREEDOR</div>
          @if($company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      <td>
        <div class="sig-line">
          @if($codebtors->count() > 0)
          <div class="sig-name">{{ $codebtors->first()->full_name ?? $codebtors->first()->company_name }}</div>
          <div class="sig-doc">{{ $codebtors->first()->documentType?->alias ?? 'C.C.' }} {{ $codebtors->first()->document_number }}</div>
          @else
          <div class="sig-name">___________________________</div>
          @endif
          <div class="sig-role">CODEUDOR — GARANTE</div>
        </div>
      </td>
    </tr>
    @foreach($codebtors->skip(1) as $codebtor)
    <tr>
      <td style="padding-top:20px;"></td>
      <td style="padding-top:20px;">
        <div class="sig-line">
          <div class="sig-name">{{ $codebtor->full_name ?? $codebtor->company_name }}</div>
          <div class="sig-role">CODEUDOR — GARANTE</div>
          <div class="sig-doc">{{ $codebtor->documentType?->alias ?? 'C.C.' }} {{ $codebtor->document_number }}</div>
        </div>
      </td>
    </tr>
    @endforeach
  </table>
  <p class="legal-note">
    La garantía solidaria aquí constituida tiene validez legal al amparo del artículo 1568 del Código Civil
    colombiano. El ARRENDADOR queda facultado para adelantar acciones legales directamente contra el CODEUDOR
    sin necesidad de agotar previamente las acciones contra el arrendatario principal.
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
