<x-mail::message>
# Firma rechazada — {{ $signatory->document->title }}

Se ha registrado el **rechazo de firma** por parte de un firmante.

**Documento:** {{ $signatory->document->title }}
@if($signatory->document->number)
**N°:** {{ $signatory->document->number }}
@endif

**Firmante:** {{ $signatory->name }} ({{ $signatory->email }})
**Rol:** {{ ucfirst($signatory->role) }}
**Fecha:** {{ $signatory->signed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

@if($signatory->rejection_reason)
**Motivo del rechazo:**

> {{ $signatory->rejection_reason }}
@endif

Por favor revisa el estado del documento en el panel de administración y toma las acciones necesarias.

Gracias,<br>
{{ $company->tradename ?: $company->company_name }}
</x-mail::message>
