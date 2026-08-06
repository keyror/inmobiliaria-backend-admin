@php
  use App\Support\FrontendUrl;
  $verifyUrl = $document->number ? FrontendUrl::resolve('admin/verificar/' . $document->number) : null;
@endphp
<x-mail::message>
# Documento firmado por todas las partes

El siguiente documento ha sido firmado por todos los participantes:

**{{ $document->title }}**
@if($document->number)
N° {{ $document->number }}
@endif

**Fecha de firma:** {{ now()->format('d/m/Y H:i') }}

El documento firmado se adjunta a este correo en formato PDF.
@if($certificatePdfContent)
Se incluye también el **certificado de evidencias de firma electrónica** con el registro completo del proceso.
@endif

@if($verifyUrl)
<x-mail::button :url="$verifyUrl" color="green">
Verificar autenticidad del documento
</x-mail::button>

Puedes usar ese enlace en cualquier momento para confirmar que el documento es auténtico y no ha sido modificado.
@endif

Gracias,<br>
{{ $company->tradename ?: $company->company_name }}
</x-mail::message>
