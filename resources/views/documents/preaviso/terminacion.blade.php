<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Preaviso de Terminación N° {{ $document->number ?? $rent->contract_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.55; margin: 18mm 18mm 24mm 18mm; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #5c3317; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #5c3317; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-number { font-size: 10pt; font-weight: bold; color: #5c3317; text-align: center; margin-top: 3px; }
  .divider { border: none; border-top: 2px solid #5c3317; margin: 6px 0; }
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #5c3317; letter-spacing: 0.5px; }
  .doc-date { text-align: center; font-size: 8pt; color: #555; margin-bottom: 12px; }
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #5c3317; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }
  .section-subtitle { font-size: 8.5pt; font-weight: bold; color: #5c3317; margin: 4px 0 2px 0; }
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 38%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #fdf3e7; }
  .body-text { font-size: 9.5pt; text-align: justify; margin-bottom: 8px; line-height: 1.6; }
  .legal-box { background: #fdf3e7; border-left: 3px solid #5c3317; padding: 6px 10px; font-size: 8.5pt; margin: 8px 0; }
  .reason-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 6px 10px; font-size: 8.5pt; margin: 6px 0; }
  .notice-badge { display: inline-block; background: #5c3317; color: #fff; padding: 2px 10px; border-radius: 3px; font-size: 9pt; font-weight: bold; margin: 2px 0; }
  .effects-list { padding-left: 18px; margin: 5px 0; }
  .effects-list li { font-size: 9pt; margin-bottom: 3px; line-height: 1.5; }
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid; }
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
  $senderRole       = $content['sender_role'] ?? 'arrendador';
  $terminationDate  = $content['termination_date'] ?? $rent->end_date?->format('Y-m-d');
  $daysNotice       = $content['days_notice'] ?? 90;
  $reason           = $content['reason'] ?? null;

  $principalAddress = $rent->property->addresses->where('is_principal', true)->first()
      ?? $rent->property->addresses->first();
  $propertyAddress  = $principalAddress?->address ?? 'Sin dirección registrada';
  $tenantPairs      = $rent->rentTenantCodebtors;
  $mainTenant       = $tenantPairs->first()?->tenant;

  if ($senderRole === 'arrendatario') {
    $senderName      = $mainTenant?->full_name ?? $mainTenant?->company_name ?? 'El Arrendatario';
    $senderLabel     = 'ARRENDATARIO — REMITENTE';
    $recipientName   = $company->company_name;
    $recipientLabel  = 'ARRENDADOR — DESTINATARIO';
  } else {
    $senderName      = $company->company_name;
    $senderLabel     = 'ARRENDADOR — REMITENTE';
    $recipientName   = $mainTenant?->full_name ?? $mainTenant?->company_name ?? '_______________';
    $recipientLabel  = 'ARRENDATARIO — DESTINATARIO';
  }

  $terminationFmt  = $terminationDate
    ? \Carbon\Carbon::parse($terminationDate)->format('d \d\e F \d\e Y')
    : 'día ___ de ____________ de ______';
  $documentDateFmt = $document->document_date?->format('d \d\e F \d\e Y')
    ?? now()->format('d \d\e F \d\e Y');
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
      <div class="header-doc-title">PREAVISO DE TERMINACIÓN</div>
      <div class="header-doc-number">Ref. Contrato N° {{ $rent->contract_number ?? '---' }}</div>
    </td>
  </tr>
</table>
<hr class="divider">

<div class="doc-title">Preaviso de No Renovación de Contrato de Arrendamiento</div>
<div class="doc-date">
  {{ $rent->signed_city ?? '_______________' }}, {{ $documentDateFmt }}
</div>

{{-- DESTINATARIO --}}
<div class="section">
  <div class="section-title">Señores</div>
  <p style="font-size:9.5pt;margin:4px 0 2px 0;"><strong>{{ $recipientName }}</strong></p>
  <p style="font-size:8.5pt;color:#666;">{{ $recipientLabel }}</p>
  @if($senderRole === 'arrendador' && $mainTenant)
  <p style="font-size:8.5pt;color:#444;">
    {{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}
  </p>
  @endif
  <p style="font-size:8.5pt;margin-top:4px;">
    <strong>Ref:</strong> Contrato de arrendamiento N° <strong>{{ $rent->contract_number ?? '---' }}</strong> —
    Inmueble: {{ $propertyAddress }}
  </p>
</div>

{{-- NOTIFICACION --}}
<div class="section">
  <div class="section-title">Notificación de No Renovación</div>
  <p class="body-text">
    Por medio del presente escrito, <strong>{{ $senderName }}</strong>, en calidad de
    <strong>{{ strtoupper($senderRole === 'arrendador' ? 'ARRENDADOR' : 'ARRENDATARIO') }}</strong>
    del contrato de arrendamiento identificado arriba, de conformidad con lo establecido en los
    artículos 22 y 23 de la Ley 820 de 2003 y las cláusulas del contrato, le comunico mi
    <strong>voluntad de NO RENOVAR</strong> el presente contrato a su vencimiento.
  </p>

  <div class="legal-box">
    El presente preaviso se da con
    <span class="notice-badge">{{ $daysNotice }} días de anticipación</span>
    a la fecha de terminación del contrato.
    <br>
    <strong>Fecha de terminación notificada: {{ $terminationFmt }}</strong>
  </div>
</div>

{{-- MOTIVACION --}}
@if($reason)
<div class="section">
  <div class="section-title">Motivación</div>
  <div class="reason-box">{{ $reason }}</div>
</div>
@endif

{{-- EFECTOS --}}
<div class="section">
  <div class="section-title">Compromisos y Efectos</div>
  <p class="body-text">
    El <strong>ARRENDATARIO</strong> queda notificado de que deberá desocupar y restituir el inmueble
    a más tardar el <strong>{{ $terminationFmt }}</strong>. Para tal efecto, el ARRENDATARIO deberá:
  </p>
  <ul class="effects-list">
    <li>Desocupar completamente el inmueble y entregar las llaves al ARRENDADOR.</li>
    <li>Cancelar todos los cánones pendientes, servicios públicos y demás obligaciones derivadas del contrato.</li>
    <li>Dejar el inmueble en las mismas condiciones en que fue entregado al inicio del arriendo, salvo el deterioro natural por el uso.</li>
    <li>Suscribir el acta de devolución del inmueble con el inventario de entrega.</li>
  </ul>
  <p class="body-text" style="margin-top:6px;">
    El incumplimiento de la restitución en la fecha indicada generará las acciones legales correspondientes,
    incluyendo el proceso de lanzamiento por vencimiento de término, sin perjuicio de la liquidación de
    los perjuicios causados por cada día de mora en la restitución.
  </p>
</div>

{{-- DATOS CONTRATO --}}
<div class="section">
  <div class="section-title">Datos del Contrato</div>
  <table class="data-table">
    <tr>
      <td class="label">N° Contrato:</td><td class="value">{{ $rent->contract_number ?? '---' }}</td>
      <td class="label">Tipo:</td><td class="value">{{ $rent->contractType?->name ?? 'Arrendamiento' }}</td>
    </tr>
    <tr>
      <td class="label">Inicio:</td><td class="value">{{ $rent->start_date?->format('d/m/Y') }}</td>
      <td class="label">Terminación:</td>
      <td class="value">{{ $rent->end_date?->format('d/m/Y') ?? 'Indefinido' }}</td>
    </tr>
    <tr>
      <td class="label">Inmueble:</td><td class="value" colspan="3">{{ $propertyAddress }}</td>
    </tr>
    <tr>
      <td class="label">Canon mensual:</td>
      <td class="value" colspan="3">${{ number_format($rent->canon ?? 0, 0, ',', '.') }}</td>
    </tr>
  </table>
</div>

@if($clauses && $clauses->isNotEmpty())
<div class="section">
  <div class="section-title">Cláusulas y Compromisos Adicionales</div>
  @foreach($clauses as $clause)
  <div style="margin-bottom:5px;font-size:8.5pt;text-align:justify;">
    <strong>{{ $clause->heading }}:</strong> {!! $clause->rendered_body !!}
  </div>
  @endforeach
</div>
@endif

{{-- FIRMAS --}}
<div class="section">
  <div class="section-title">Firma del Remitente y Acuse de Recibo</div>
  <table class="sig-table">
    <tr>
      @if($senderRole === 'arrendador')
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">ARRENDADOR — REMITENTE</div>
          @if($company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      @if($mainTenant)
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
          <div class="sig-role">ARRENDATARIO — RECIBE Y ACEPTA</div>
          <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
        </div>
      </td>
      @endif
      @else
      @if($mainTenant)
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $mainTenant->full_name ?? $mainTenant->company_name }}</div>
          <div class="sig-role">ARRENDATARIO — REMITENTE</div>
          <div class="sig-doc">{{ $mainTenant->documentType?->alias ?? 'C.C.' }} {{ $mainTenant->document_number }}</div>
        </div>
      </td>
      @endif
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $company->company_name }}</div>
          <div class="sig-role">ARRENDADOR — RECIBE Y ACEPTA</div>
          @if($company->legalRepresentative)
          <div class="sig-doc">Rep. Legal: {{ $company->legalRepresentative->full_name }}</div>
          @endif
        </div>
      </td>
      @endif
    </tr>
  </table>
  <p class="legal-note">
    Preaviso expedido de conformidad con los artículos 22 y 23 de la Ley 820 de 2003.
    Contrato de arrendamiento N° {{ $rent->contract_number ?? '---' }}.
    Ciudad de {{ $rent->signed_city ?? '---' }}, {{ $document->document_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}.
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
