<?php

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PublicPropertyContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone?: string|null, message: string, emails: array<int, string>}  $contactData
     */
    public function __construct(
        public readonly Property $property,
        public readonly array $contactData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->contactData['email']],
            subject: 'Nuevo contacto por la propiedad '.$this->property->code
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.public.property-contact'
        );
    }
}
