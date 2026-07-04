# Correos y Notificaciones — Backend

## Helpers disponibles

### `FrontendUrl` — URL del frontend según contexto

Siempre usar este helper para construir URLs que apunten al frontend. **Nunca construir la URL manualmente.**

```php
use App\Support\FrontendUrl;

// Resuelve la URL base + path según contexto
$url = FrontendUrl::resolve('admin/Authentication/reset-password');
```

| Contexto | Entorno | URL resultante |
|---|---|---|
| Tenant | `local` | `http://{tenant.domain}/admin/...` |
| Tenant | `production` | `https://{tenant.domain}/admin/...` |
| Central | `local` | `{APP_FRONTEND_URL}/admin/...` |
| Central | `production` | `{APP_URL}/admin/...` |

### `TenantMailer` — SMTP según configuración del tenant

Siempre usar este helper para enviar correos desde contexto tenant. Detecta si el tenant tiene SMTP propio configurado; si no, usa el SMTP de la plataforma.

```php
use App\Support\TenantMailer;

$company = Company::with('setting')->first();
['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

$mailer->to($recipients)->send(new MiMailable($data, $from));
```

---

## Plantilla de correo

Todos los correos usan `<x-mail::message>` — la plantilla de Laravel con el logo de la plataforma en el header.

```blade
{{-- resources/views/emails/mi-correo.blade.php --}}
<x-mail::message>
# Título del correo

Contenido del mensaje.

<x-mail::button :url="$url">
Texto del botón
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
```

**Reglas:**
- Usar siempre `<x-mail::message>` — nunca HTML crudo ni plantillas propias
- El logo se inyecta automáticamente desde `public/logo.png` (embebido en base64)
- Para botones usar `<x-mail::button :url="$url">`

---

## Crear un nuevo correo (Mailable)

```php
// app/Mail/MiCorreo.php
class MiCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly array $data,
        public readonly ?Address $fromAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAddress ?? new Address(config('mail.from.address')),
            subject: 'Asunto del correo',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.mi-correo'
        );
    }
}
```

## Crear una nueva notificación

```php
// app/Notifications/MiNotificacion.php
public function toMail($notifiable): MailMessage
{
    $url = FrontendUrl::resolve('admin/ruta-destino');

    return (new MailMessage)
        ->subject('Asunto')
        ->markdown('emails.mi-notificacion', ['url' => $url]);
}
```

---

## Correos existentes

| Clase | Vista | Usa TenantMailer |
|---|---|---|
| `Mail\PublicCompanyContactMail` | `emails/public/company-contact` | ✅ |
| `Mail\PublicPropertyContactMail` | `emails/public/property-contact` | ✅ |
| `Notifications\ResetPasswordNotification` | `emails/password-reset` | ❌ (plataforma) |
