<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificado de Evidencias de Firma Electrónica</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; line-height: 1.45; margin: 14mm 16mm 20mm 16mm; }

  /* Encabezado — misma estructura que actas/contratos */
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .header-company { font-size: 12pt; font-weight: bold; color: #1a3a5c; }
  .header-subtitle { font-size: 7.5pt; color: #666; margin-top: 2px; }
  .header-nit { font-size: 7.5pt; color: #444; }
  .header-doc-box { text-align: right; }
  .header-doc-title { font-size: 9pt; font-weight: bold; background: #1a3a5c; color: #fff; padding: 3px 8px; text-align: center; }
  .header-doc-subtitle { font-size: 7pt; color: #555; text-align: center; margin-top: 2px; }

  .divider { border: none; border-top: 2px solid #1a3a5c; margin: 6px 0; }

  /* Título del documento */
  .doc-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8px 0 2px 0; color: #1a3a5c; letter-spacing: 0.5px; }

  /* Sección */
  .section { margin-bottom: 8px; }
  .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #fff; background: #1a3a5c; padding: 2px 7px; margin-bottom: 5px; page-break-after: avoid; }

  /* Tabla de info del documento */
  .data-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 5px; }
  .data-table td { padding: 2px 5px; vertical-align: top; }
  .data-table td.label { font-weight: bold; width: 30%; color: #444; white-space: nowrap; }
  .data-table td.value { color: #1a1a1a; }
  .data-table tr:nth-child(even) td { background: #f4f6f9; }

  /* Hash box */
  .hash-box { background: #eef2f8; border-left: 3px solid #1a3a5c; padding: 4px 8px; font-size: 7.5pt; margin-bottom: 8px; }
  .hash-box .hash-label { font-weight: bold; color: #444; }
  .hash-value { font-family: Courier New, monospace; font-size: 7pt; color: #1a3a5c; word-break: break-all; }

  /* Tabla de firmantes */
  .sig-ev-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-bottom: 8px; }
  .sig-ev-table th { background: #1a3a5c; color: #fff; padding: 4px 5px; text-align: left; font-weight: bold; font-size: 7pt; }
  .sig-ev-table td { padding: 4px 5px; border-bottom: 1px solid #e0e4ea; vertical-align: top; }
  .sig-ev-table tr:nth-child(even) td { background: #f4f6f9; }
  .status-signed { color: #155724; font-weight: bold; }
  .status-rejected { color: #721c24; font-weight: bold; }
  .mono { font-family: Courier New, monospace; font-size: 6.5pt; word-break: break-all; }
  .badge-yes { background: #d4edda; color: #155724; padding: 1px 5px; border-radius: 3px; font-size: 7pt; }

  /* Nota legal */
  .legal-note { font-size: 7pt; color: #444; text-align: justify; border-top: 1px solid #ccc; padding-top: 5px; margin-top: 6px; line-height: 1.5; }

  /* Pie de página */
  .footer-table { width: 100%; border-collapse: collapse; margin-top: 10px; border-top: 1px solid #bbb; padding-top: 5px; }
  .footer-table td { font-size: 7pt; color: #666; padding-top: 4px; }
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<table class="header-table">
  <tr>
    <td style="width:60%;">
      @if(!empty($logoDataUri))
        <img src="{{ $logoDataUri }}" alt="" style="display:block;max-height:45px;max-width:160px;margin-bottom:4px;">
      @endif
      <div class="header-company">{{ $company->company_name }}</div>
      @if($company->tradename)
        <div class="header-subtitle">{{ $company->tradename }}</div>
      @endif
      @if($company->nit)
        <div class="header-nit">NIT: {{ $company->nit }}</div>
      @endif
    </td>
    <td style="width:40%;" class="header-doc-box">
      <div class="header-doc-title">CERTIFICADO DE EVIDENCIAS</div>
      <div class="header-doc-subtitle">Firma Electrónica — Ley 527 de 1999 (Colombia)</div>
      @if($document->number)
        <div style="font-size:9pt;font-weight:bold;color:#1a3a5c;text-align:center;margin-top:3px;">N° {{ $document->number }}</div>
      @endif
    </td>
  </tr>
</table>

<hr class="divider">

<div class="doc-title">{{ $document->title }}</div>

<div style="text-align:center;font-size:8pt;color:#555;margin-bottom:10px;">
  @if($document->signed_at)
    Firmado el {{ $document->signed_at->format('d/m/Y \a \l\a\s H:i:s') }}
  @endif
</div>

{{-- INFORMACIÓN DEL DOCUMENTO --}}
<div class="section">
  <div class="section-title">Datos del documento</div>
  <table class="data-table">
    <tr>
      <td class="label">Título:</td>
      <td class="value">{{ $document->title }}</td>
      <td class="label">N° Documento:</td>
      <td class="value">{{ $document->number ?? '—' }}</td>
    </tr>
    <tr>
      <td class="label">Empresa:</td>
      <td class="value">{{ $company->tradename ?: $company->company_name }}</td>
      <td class="label">Fecha de firma:</td>
      <td class="value">{{ $document->signed_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
    </tr>
  </table>
</div>

{{-- HASH DEL DOCUMENTO FIRMADO --}}
@if($document->pdf_hash)
<div class="hash-box">
  <div class="hash-label">Integridad del documento firmado (SHA-256):</div>
  <div class="hash-value">{{ $document->pdf_hash }}</div>
</div>
@endif

{{-- REGISTRO DE FIRMANTES --}}
<div class="section">
  <div class="section-title">Registro de firmantes y evidencias</div>

  <table class="sig-ev-table">
    <thead>
      <tr>
        <th style="width:14%">Firmante</th>
        <th style="width:14%">Correo</th>
        <th style="width:7%">Rol</th>
        <th style="width:11%">Invitación</th>
        <th style="width:13%">Apertura del enlace</th>
        <th style="width:13%">Firma</th>
        <th style="width:7%">Tipo</th>
        <th style="width:21%">Hash al firmar</th>
      </tr>
    </thead>
    <tbody>
      @foreach($signatories as $sig)
      <tr>
        <td>
          <strong>{{ $sig->name }}</strong>
          @if($sig->consent_accepted_at)
            <br><span class="badge-yes">Aceptó términos {{ $sig->consent_accepted_at->format('d/m/Y H:i') }}</span>
          @endif
        </td>
        <td>{{ $sig->email }}</td>
        <td style="text-align:center;">{{ ucfirst($sig->role) }}</td>
        <td>
          @if($sig->sent_at)
            {{ $sig->sent_at->format('d/m/Y') }}<br>
            <span style="color:#555;">{{ $sig->sent_at->format('H:i:s') }}</span>
          @else
            <span style="color:#888;">—</span>
          @endif
        </td>
        <td>
          @if($sig->view_ip_address)
            <span class="mono">{{ $sig->view_ip_address }}</span>
            @if($sig->viewed_at)
              <br><span style="color:#555;">{{ $sig->viewed_at->format('d/m/Y H:i:s') }}</span>
            @endif
          @else
            <span style="color:#888;">—</span>
          @endif
        </td>
        <td>
          @if($sig->status === 'signed' && $sig->signed_at)
            <span class="status-signed">Firmado</span><br>
            <span class="mono">{{ $sig->ip_address ?? '—' }}</span><br>
            <span style="color:#555;">{{ $sig->signed_at->format('d/m/Y H:i:s') }}</span>
          @elseif($sig->status === 'rejected')
            <span class="status-rejected">Rechazado</span>
            @if($sig->rejection_reason)
              <br><em style="color:#888;font-size:6.5pt;">{{ mb_substr($sig->rejection_reason, 0, 60) }}{{ mb_strlen($sig->rejection_reason) > 60 ? '...' : '' }}</em>
            @endif
          @elseif($sig->status === 'expired')
            <span style="color:#856404;font-weight:bold;">Expirado</span>
          @else
            <span style="color:#888;">{{ ucfirst($sig->status) }}</span>
          @endif
        </td>
        <td style="text-align:center;">{{ $sig->signature_type ?? '—' }}</td>
        <td style="max-width:120px;overflow-wrap:break-word;word-break:break-all;">
          @if($sig->document_hash_at_signing)
            <span class="mono">{{ substr($sig->document_hash_at_signing, 0, 32) }}<br>{{ substr($sig->document_hash_at_signing, 32) }}</span>
          @else
            <span style="color:#888;">—</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- SELLO NOTARIAL RFC 3161 --}}
@if($document->tsa_token)
@php
  $tokenLines = implode('<br>', str_split($document->tsa_token, 76));
@endphp
<div class="section">
  <div class="section-title">Sello notarial digital (RFC 3161)</div>
  <table class="data-table">
    <tr>
      <td class="label">Servidor TSA:</td>
      <td class="value">FreeTSA — freetsa.org (RFC 3161)</td>
      <td class="label">Timestamp:</td>
      <td class="value">{{ $document->tsa_stamped_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
    </tr>
    <tr>
      <td class="label" style="vertical-align:top;">Token TSR (base64):</td>
      <td class="value" colspan="3" style="word-break:break-all;overflow-wrap:break-word;">
        <span class="mono">{!! $tokenLines !!}</span>
      </td>
    </tr>
  </table>
</div>

@endif

{{-- NOTA LEGAL --}}
<div class="legal-note">
  <strong>Validez legal:</strong>
  Las firmas registradas en este certificado son firmas electrónicas simples conforme a la <strong>Ley 527 de 1999</strong> de Colombia.
  Cada firmante fue identificado mediante correo electrónico y token de sesión único de 30 días.
  Se registraron la dirección IP de apertura del enlace y de firma, el agente de usuario, la fecha y hora exacta de cada acción,
  y la aceptación explícita del consentimiento de firma electrónica.
  El hash SHA-256 garantiza la integridad e inalterabilidad del documento firmado.
  @if($document->tsa_token)
  El sello notarial digital RFC 3161 de FreeTSA certifica la fecha y hora del proceso con una fuente de tiempo independiente y verificable.
  @endif
  Este certificado fue generado el {{ $generatedAt->format('d/m/Y \a \l\a\s H:i:s') }}.
</div>

{{-- PIE --}}
<table class="footer-table">
  <tr>
    <td>Emitido por: <strong>{{ $company->tradename ?: $company->company_name }}</strong></td>
    <td style="text-align:right;">Generado: {{ $generatedAt->format('d/m/Y H:i:s') }}</td>
  </tr>
</table>

</body>
</html>
