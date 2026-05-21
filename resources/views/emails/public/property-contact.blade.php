<x-mail::message>
# Nuevo contacto por propiedad

Recibiste una nueva solicitud desde el sitio publico.

**Propiedad:** {{ $property->title }}

**Codigo:** {{ $property->code }}

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
