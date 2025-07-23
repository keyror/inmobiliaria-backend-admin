<div style="background-color: #f5f5f5; padding: 30px;">
    <div style="background-color: #fff; border-radius: 10px; padding: 30px;">
        <h2 style="text-align: center; margin-bottom: 30px;">Restablecimiento de contraseña</h2>

        <p>Hola,</p>

        <p>Recibes este correo electrónico porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}"
               style="background-color: #007bff; color: #fff; padding: 15px 25px; text-decoration: none; border-radius: 5px;">
                Restablecer contraseña
            </a>
        </div>

        <p>Si no has solicitado un restablecimiento de contraseña, puedes omitir este email.</p>

        <p>Gracias,</p>
        <p>Este correo se ha generado automáticamente, por favor no responder este correo.</p>
        <p style="margin: 0; padding: 0; line-height: 1.5;">Atentamente, <b>{{ env('APP_NAME') }}</b></p>
    </div>
</div>

