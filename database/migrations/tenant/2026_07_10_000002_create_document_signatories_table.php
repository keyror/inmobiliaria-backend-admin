<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signatories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->uuid('person_id')->nullable()->comment('Persona registrada en el sistema');
            $table->string('name')->comment('Snapshot del nombre al momento del envío');
            $table->string('email')->comment('Correo donde se envía el link');
            $table->string('role', 50)->comment('arrendatario|arrendador|codeudor|propietario|custom');
            $table->tinyInteger('order')->default(1)->comment('Orden de firma (1 firma primero)');
            $table->string('token', 100)->unique()->comment('UUID para la URL pública de firma');
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status', 20)->default('pending')->comment('pending|viewed|signed|rejected|expired');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_type', 20)->nullable()->comment('drawn|uploaded');
            $table->string('signature_path')->nullable()->comment('Path en storage a la imagen de firma');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('consent_accepted_at')->nullable()->comment('Cuándo aceptó la firma electrónica');
            $table->text('consent_text')->nullable()->comment('Texto legal aceptado por el firmante');
            $table->string('document_hash_at_signing', 64)->nullable()->comment('SHA-256 del PDF en el momento de firmar');
            $table->string('view_ip_address', 45)->nullable()->comment('IP al abrir el enlace de firma');
            $table->string('view_user_agent')->nullable()->comment('Dispositivo al abrir el enlace');
            $table->timestamp('sent_at')->nullable()->comment('Cuándo se envió el correo de invitación');
            $table->timestamp('document_read_at')->nullable()->comment('Cuándo el firmante llegó al final del documento');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('person_id')->references('id')->on('people');

            $table->index('token');
            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signatories');
    }
};
