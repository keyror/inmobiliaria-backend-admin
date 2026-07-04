<x-mail::message>
# Restablecimiento de contraseña

Hola,

Recibiste este correo porque se solicitó restablecer la contraseña de tu cuenta.

<x-mail::button :url="$resetUrl">
Restablecer contraseña
</x-mail::button>

Si no solicitaste este cambio, puedes ignorar este correo — tu contraseña no será modificada.

Este correo se generó automáticamente, por favor no respondas a este mensaje.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
