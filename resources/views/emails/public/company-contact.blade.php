<x-mail::message>
# Nuevo contacto desde el sitio publico

Recibiste una nueva solicitud para la empresa.

**Empresa:** {{ $company->tradename ?: $company->company_name }}

**Nombre:** {{ $contactData['name'] }}

**Correo:** {{ $contactData['email'] }}

@if (! empty($contactData['phone']))
**Celular:** {{ $contactData['phone'] }}
@endif

**Mensaje:**

{{ $contactData['message'] }}

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
