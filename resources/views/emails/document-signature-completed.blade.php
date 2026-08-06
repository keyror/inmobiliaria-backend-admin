<x-mail::message>
# Documento firmado por todas las partes

El siguiente documento ha sido firmado por todos los participantes:

**{{ $document->title }}**
@if($document->number)
N° {{ $document->number }}
@endif

**Fecha de firma:** {{ now()->format('d/m/Y H:i') }}

Puedes descargar el documento firmado desde el panel de administración de **{{ $company->tradename ?: $company->company_name }}**.

Gracias,<br>
{{ $company->tradename ?: $company->company_name }}
</x-mail::message>
