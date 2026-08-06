<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentSignatory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentSignatureMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly DocumentSignatory $signatory,
        public readonly Company $company,
        public readonly string $signUrl,
        public readonly ?Address $fromAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->tradename ?: $this->company->company_name;

        return new Envelope(
            from: $this->fromAddress ?? new Address(config('mail.from.address'), $companyName),
            subject: 'Solicitud de firma — '.$this->document->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.document-signature-invite'
        );
    }
}
