<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentSignatureCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly Company $company,
        public readonly ?Address $fromAddress = null,
        public readonly ?string $certificatePdfContent = null,
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->tradename ?: $this->company->company_name;

        return new Envelope(
            from: $this->fromAddress ?? new Address(config('mail.from.address'), $companyName),
            subject: 'Documento firmado — '.$this->document->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.document-signature-completed'
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->document->file_path) {
            $attachments[] = Attachment::fromStorageDisk('local', $this->document->file_path)
                ->as($this->document->file_name ?? 'documento_firmado.pdf')
                ->withMime('application/pdf');
        }

        if ($this->certificatePdfContent) {
            $content = $this->certificatePdfContent;
            $attachments[] = Attachment::fromData(fn () => $content, 'certificado_evidencias.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
