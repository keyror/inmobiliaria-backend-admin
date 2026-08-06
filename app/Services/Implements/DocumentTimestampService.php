<?php

namespace App\Services\Implements;

use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Sello notarial digital RFC 3161 usando FreeTSA.
 *
 * Construye una Time-Stamp Query (TSQ) en DER binario sin dependencias externas,
 * la envía a freetsa.org y persiste el Time-Stamp Response (TSR) en el documento.
 */
class DocumentTimestampService
{
    private const TSA_URL = 'https://freetsa.org/tsr';

    private const TIMEOUT_SECONDS = 8;

    /**
     * Sella el PDF firmado de un documento y persiste el token TSA.
     * Si FreeTSA falla, registra un warning pero NO lanza excepción.
     */
    public function stampDocument(Document $document): void
    {
        if (! $document->file_path) {
            return;
        }

        try {
            $pdfContent = Storage::disk('local')->get($document->file_path);

            if (! $pdfContent) {
                return;
            }

            $result = $this->stamp($pdfContent);

            $document->tsa_token = $result['token'];
            $document->tsa_stamped_at = $result['stamped_at'];
            $document->save();
        } catch (\Throwable $e) {
            Log::warning('tsa.stamp_failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Construye el TSQ, llama a FreeTSA y retorna el token base64 y el timestamp.
     *
     * @return array{token: string, stamped_at: Carbon}
     */
    public function stamp(string $fileContent): array
    {
        $hash = hash('sha256', $fileContent, true);
        $tsq = $this->buildTsq($hash);

        $response = Http::withHeaders(['Content-Type' => 'application/timestamp-query'])
            ->withBody($tsq, 'application/timestamp-query')
            ->timeout(self::TIMEOUT_SECONDS)
            ->post(self::TSA_URL);

        if (! $response->successful()) {
            throw new \RuntimeException('FreeTSA respondió con HTTP '.$response->status());
        }

        $tsrBody = $response->body();

        if (strlen($tsrBody) < 10) {
            throw new \RuntimeException('Respuesta TSA vacía o inválida');
        }

        return [
            'token' => base64_encode($tsrBody),
            'stamped_at' => now(),
        ];
    }

    /**
     * Genera el TSQ para un documento a partir de su pdf_hash almacenado.
     * El resultado es idéntico al TSQ original enviado a FreeTSA.
     */
    public function buildTsqForDocument(Document $document): string
    {
        if (! $document->pdf_hash) {
            throw new \RuntimeException('El documento no tiene hash PDF registrado.');
        }

        return $this->buildTsq(hex2bin($document->pdf_hash));
    }

    /**
     * Construye un Time-Stamp Request (TSQ) mínimo en DER binario.
     *
     * Estructura RFC 3161 TimeStampReq:
     *   SEQUENCE {
     *     version INTEGER 1,
     *     messageImprint SEQUENCE {
     *       AlgorithmIdentifier SEQUENCE { OID sha-256, NULL },
     *       hashedMessage OCTET STRING (32 bytes)
     *     },
     *     certReq BOOLEAN TRUE
     *   }
     */
    private function buildTsq(string $rawHash): string
    {
        // OID 2.16.840.1.101.3.4.2.1 (sha-256) en DER
        $sha256Oid = "\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01";

        // AlgorithmIdentifier: SEQUENCE { OID, NULL }
        $alg = "\x30\x0d".$sha256Oid."\x05\x00";

        // MessageImprint: SEQUENCE { AlgorithmIdentifier, OCTET STRING hash }
        $msgContent = $alg."\x04\x20".$rawHash;
        $msgImprint = "\x30".$this->derLen($msgContent).$msgContent;

        // TimeStampReq: SEQUENCE { version=1, messageImprint, certReq=TRUE }
        $tsqContent = "\x02\x01\x01".$msgImprint."\x01\x01\xff";

        return "\x30".$this->derLen($tsqContent).$tsqContent;
    }

    private function derLen(string $data): string
    {
        $len = strlen($data);

        if ($len < 128) {
            return chr($len);
        }

        if ($len < 256) {
            return "\x81".chr($len);
        }

        return "\x82".chr($len >> 8).chr($len & 0xFF);
    }
}
