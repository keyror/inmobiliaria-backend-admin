# Correos y Notificaciones — Backend

## Helpers disponibles

### `FrontendUrl` — URL del frontend según contexto

Siempre usar este helper para construir URLs que apunten al frontend. **Nunca construir la URL manualmente.**

```php
use App\Support\FrontendUrl;

// Resuelve la URL base + path según contexto
$url = FrontendUrl::resolve('admin/authentication/reset-password');
```

| Contexto | Variable | URL resultante |
|---|---|---|
| Tenant | `APP_SCHEME` + `tenant.domain` | `{scheme}://{tenant.domain}/admin/...` |
| Central | `APP_FRONTEND_URL` (fallback `APP_URL`) | `{APP_FRONTEND_URL}/admin/...` |

> **`APP_SCHEME`** (en `config/app.php` → `config('app.scheme')`) — por defecto `https`. Usar `APP_SCHEME=http` en `.env` local para evitar redirect HTTPS.  
> La implementación **no** usa `app()->environment('production')` — fue refactorizada para depender solo de la variable de entorno.

**Nota sobre query strings**: usar `http_build_query()` para construir parámetros, nunca concatenación manual:

```php
// ✅ Correcto
$url = FrontendUrl::resolve('admin/authentication/reset-password')
    .'?'.http_build_query(['token' => $token, 'email' => $email]);

// ❌ Incorrecto — no escapa caracteres especiales del email
$url = FrontendUrl::resolve('admin/...') . '?token=' . $token . '&email=' . $email;
```

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
| `Mail\DocumentSignatureMail` | `emails/document-signature-invite` | ✅ — invitación a firmar |
| `Mail\DocumentSignatureCompletedMail` | `emails/document-signature-completed` | ✅ — confirmación cuando todos firmaron |
| `Notifications\ResetPasswordNotification` | `emails/password-reset` | ❌ (plataforma) |
