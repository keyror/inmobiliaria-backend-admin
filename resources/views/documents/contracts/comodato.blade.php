<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contrato de Comodato N° {{ $document->number ?? $rent->contract_number }}</title>
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
  .clause { margin-bottom: 7px; font-size: 9pt; text-align: justify; }
  .clause-title { font-weight: bold; color: #4a3500; margin-bottom: 2px; }
  .free-badge { display: inline-block; background: #fff3cd; border: 1px solid #ffc107; border-radius: 3px; padding: 2px 8px; font-weight: bold; font-size: 9pt; margin: 4px 0; }
  .clauses-box { background: #f9f7f2; border: 1px solid #d6cbb0; padding: 6px 10px; font-size: 8.5pt; margin-bottom: 6px; }
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
  $purpose           = $content['comodatario_purpose'] ?? ($rent->activity ?? null);
  $clausesAdditional = $content['clauses_additional'] ?? [];
  $contentNotes      = $content['notes'] ?? null;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;
  $mainCodebtor     = $tenantPairs->first()?->codebtor;
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
      <div class="header-doc-title">CONTRATO DE COMODATO</div>
      <div class="header-doc-number">N° {{ $document->number ?? $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Contrato de Comodato (Préstamo de Uso)</div>
<div class="doc-date">
  Contrato N° {{ $rent->contract_number ?? '---' }} —
  Ciudad: {{ $rent->signed_city ?? '---' }} —
  Fecha: {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
</div>

{{-- PARTES --}}
<div class="section">
  <div class="section-title">PRIMERA — Partes</div>

  <div class="section-subtitle">COMODANTE (Propietario / Administrador)</div>
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
    <div class="section-subtitle mt-4">{{ $tenantCount > 1 ? 'COMODATARIO ' . $tenantIdx : 'COMODATARIO' }}</div>
    <table class="data-table">
      <tr><td class="label">Nombre:</td><td class="value">{{ $t->full_name ?? $t->company_name }}</td></tr>
      <tr><td class="label">Documento:</td><td class="value">{{ $t->documentType?->alias ?? 'C.C.' }} {{ $t->document_number }}</td></tr>
    </table>
    @endif
    @if($pair->codebtor)
    @php $c = $pair->codebtor; @endphp
    <div class="section-subtitle mt-4">{{ $tenantCount > 1 ? 'GARANTE ' . $tenantIdx : 'GARANTE' }}</div>
    <table class="data-table">
      <tr><td class="label">Nombre:</td><td class="value">{{ $c->full_name ?? $c->company_name }}</td></tr>
      <tr><td class="label">Documento:</td><td class="value">{{ $c->documentType?->alias ?? 'C.C.' }} {{ $c->document_number }}</td></tr>
    </table>
    @endif
  @endforeach
</div>

{{-- INMUEBLE --}}
<div class="section">
  <div class="section-title">SEGUNDA — Inmueble</div>
  <table class="data-table">
    <tr>
      <td class="label">Código:</td><td class="value">{{ $rent->property->code }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->property->propertyType?->name ?? '---' }}</td>
    </tr>
    <tr><td class="label">Dirección:</td><td class="value" colspan="3">{{ $propertyAddress }}</td></tr>
    @if($rent->property->registration_number)
    <tr><td class="label">Matrícula:</td><td class="value" colspan="3">{{ $rent->property->registration_number }}</td></tr>
    @endif
  </table>
</div>

{{-- OBJETO --}}
<div class="section">
  <div class="section-title">TERCERA — Objeto del Contrato</div>
  <div class="clause">
    El COMODANTE entrega al COMODATARIO, a título de <span class="free-badge">COMODATO (USO GRATUITO)</span>,
    el inmueble descrito en la cláusula anterior, para ser destinado
    @if($purpose)
    a: <strong>{{ $purpose }}</strong>.
    @else
    exclusivamente al uso acordado entre las partes.
    @endif
    Queda expresamente prohibido el subarriendo total o parcial del inmueble sin autorización escrita del COMODANTE.
  </div>
</div>

{{-- GRATUIDAD --}}
<div class="section">
  <div class="section-title">CUARTA — Gratuidad</div>
  <div class="clause">
    El presente contrato es <strong>esencialmente gratuito</strong>. El COMODATARIO no pagará canon de arrendamiento,
    remuneración ni ningún valor al COMODANTE por el uso del inmueble. No obstante, el COMODATARIO asume a su cargo
    los gastos de servicios públicos, administración (si aplica) y mantenimiento menor durante la vigencia del
    presente contrato.
  </div>
</div>

{{-- DURACION --}}
<div class="section">
  <div class="section-title">QUINTA — Duración</div>
  <table class="data-table">
    <tr>
      <td class="label">Fecha de inicio:</td>
      <td class="value">{{ $rent->start_date?->format('d/m/Y') }}</td>
      <td class="label">Fecha de terminación:</td>
      <td class="value">{{ $rent->end_date?->format('d/m/Y') ?? 'Indefinida' }}</td>
    </tr>
    @if($rent->duration)
    <tr>
      <td class="label">Duración:</td>
      <td class="value" colspan="3">{{ $rent->duration }} meses</td>
    </tr>
    @endif
  </table>
  <div class="clause" style="margin-top:4px;">
    El COMODANTE podrá solicitar la restitución anticipada del inmueble en cualquier momento con
    preaviso de 30 días calendario, de conformidad con el artículo 2218 del Código Civil.
  </div>
</div>

{{-- OBLIGACIONES --}}
<div class="section">
  <div class="section-title">SEXTA — Obligaciones del Comodatario</div>
  <div class="clause">
    El COMODATARIO se obliga a: (a) usar el inmueble exclusivamente para el fin pactado; (b) conservarlo con el
    cuidado de un buen padre de familia; (c) no realizar modificaciones sin autorización escrita del COMODANTE;
    (d) restituirlo al vencimiento del contrato en las mismas condiciones en que lo recibió, salvo el deterioro
    natural por el uso; (e) permitir al COMODANTE o sus delegados visitas periódicas de inspección.
  </div>
</div>

@if($clauses && $clauses->isNotEmpty())
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

@if($contentNotes)
<div class="section">
  <div class="section-title">Observaciones</div>
  <div class="clauses-box">{{ $contentNotes }}</div>
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">{{ ($clauses && $clauses->isNotEmpty()) || count($clausesAdditional) > 0 ? 'OCTAVA' : 'SÉPTIMA' }} — Firma y Aceptación</div>
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
          <div class="sig-role">COMODANTE</div>
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
          <div class="sig-role">{{ $tenantPairs->filter(fn($p) => $p->tenant)->count() > 1 ? 'COMODATARIO 1' : 'COMODATARIO' }}</div>
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
            <div class="sig-role">COMODATARIO {{ $tIdx2 }}</div>
            <div class="sig-doc">{{ $t2->documentType?->alias ?? 'C.C.' }} {{ $t2->document_number }}</div>
          </div>
        </td>
      </tr>
      @endif
    @endforeach
    @php $cIdx2 = 0; $cCount2 = $tenantPairs->filter(fn($p) => $p->codebtor)->count(); @endphp
    @foreach($tenantPairs as $pair)
      @if($pair->codebtor)
      @php $cIdx2++; $c2 = $pair->codebtor; @endphp
      <tr>
        <td style="padding-top:20px;">
          <div class="sig-line">
            <div class="sig-name">{{ $c2->full_name ?? $c2->company_name }}</div>
            <div class="sig-role">{{ $cCount2 > 1 ? 'GARANTE ' . $cIdx2 : 'GARANTE' }}</div>
            <div class="sig-doc">{{ $c2->documentType?->alias ?? 'C.C.' }} {{ $c2->document_number }}</div>
          </div>
        </td>
        <td></td>
      </tr>
      @endif
    @endforeach
  </table>
  <p class="legal-note">
    Contrato regulado por los artículos 2200 y siguientes del Código Civil de Colombia. El COMODATO es
    esencialmente gratuito; cualquier remuneración lo convierte en arrendamiento y le aplica la Ley 820/2003.
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
