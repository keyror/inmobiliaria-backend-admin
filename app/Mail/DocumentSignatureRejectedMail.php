<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\DocumentSignatory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentSignatureRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly DocumentSignatory $signatory,
        public readonly Company $company,
        public readonly ?Address $fromAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->tradename ?: $this->company->company_name;

        return new Envelope(
            from: $this->fromAddress ?? new Address(config('mail.from.address'), $companyName),
            subject: 'Firma rechazada — '.$this->signatory->document->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.document-signature-rejected'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
