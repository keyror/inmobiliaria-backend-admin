<x-mail::message>
# Solicitud de firma electrónica

Hola **{{ $signatory->name }}**,

**{{ $company->tradename ?: $company->company_name }}** te ha enviado el siguiente documento para que lo firmes electrónicamente:

**{{ $document->title }}**
@if($document->number)
N° {{ $document->number }}
@endif

Por favor revisa el documento completo y firma usando el botón de abajo. Tu firma tiene validez legal en Colombia de acuerdo con la Ley 527 de 1999.

<x-mail::button :url="$signUrl">
Revisar y firmar documento
</x-mail::button>

Este enlace es válido por **30 días** a partir de la fecha de envío. Si el enlace ya no funciona, comunícate con la inmobiliaria.

---

Si no reconoces esta solicitud, ignora este correo.

Gracias,<br>
{{ $company->tradename ?: $company->company_name }}
</x-mail::message>
