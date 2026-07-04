<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PublicCompanyContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone?: string|null, message: string, emails: array<int, string>}  $contactData
     */
    public function __construct(
        public readonly Company $company,
        public readonly array $contactData,
        public readonly ?Address $fromAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->tradename ?: $this->company->company_name;

        return new Envelope(
            from: $this->fromAddress ?? new Address(config('mail.from.address'), $companyName),
            replyTo: [$this->contactData['email']],
            subject: 'Nuevo contacto para '.$companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.public.company-contact'
        );
    }
}
