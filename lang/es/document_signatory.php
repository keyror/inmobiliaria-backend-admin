<?php

declare(strict_types=1);

return [
    'created' => 'Firmante agregado correctamente.',
    'updated' => 'Firmante actualizado correctamente.',
    'deleted' => 'Firmante eliminado correctamente.',
    'sent' => 'Documento enviado a firmar correctamente.',
    'resent' => 'Correo de firma reenviado correctamente.',
    'signed' => 'Firma registrada correctamente.',
    'rejected' => 'Firma rechazada.',
    'must_be_generated' => 'El documento debe estar en estado "generado" para enviarse a firmar.',
    'no_signatories' => 'Agrega al menos un firmante antes de enviar.',
    'cannot_modify' => 'No se pueden modificar los firmantes después de enviar el documento.',
    'cannot_delete' => 'Solo se pueden eliminar firmantes en estado pendiente.',
    'token_invalid' => 'El enlace de firma no es válido.',
    'token_expired' => 'El enlace de firma ha expirado.',
    'already_processed' => 'Esta firma ya fue procesada.',
    'rejection_required' => 'Indica el motivo del rechazo.',
    'signature_required' => 'La imagen de firma es obligatoria.',
    'consent_required' => 'Debes aceptar el consentimiento para firmar.',
    'completed' => 'Todos los firmantes han firmado. Se ha generado el documento firmado.',
    'completion_resent' => 'Correo con documento firmado reenviado correctamente.',
    'certificate_not_available' => 'El certificado de evidencias no está disponible para este documento.',
    'document_not_signed' => 'El documento aún no ha sido firmado por todos los firmantes.',
    'rejection_notified' => 'El administrador ha sido notificado del rechazo.',

    // Etiquetas de auditoría — valores de estado
    'status_labels' => [
        'pending' => 'Pendiente',
        'viewed' => 'Visto',
        'signed' => 'Firmado',
        'rejected' => 'Rechazado',
        'expired' => 'Expirado',
    ],

    // Etiquetas de campos en auditoría
    'field_labels' => [
        'status' => 'Estado',
        'viewed_at' => 'Fecha de visualización',
        'signed_at' => 'Fecha de firma',
        'consent_accepted_at' => 'Consentimiento aceptado',
        'rejection_reason' => 'Motivo de rechazo',
        'sent_at' => 'Correo enviado',
    ],
];
