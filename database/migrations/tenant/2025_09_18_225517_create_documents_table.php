<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relación polimórfica
            $table->uuid('documentable_id');
            $table->string('documentable_type');

            // Información básica
            $table->uuid('document_type_id')->nullable(); // Referencia a lookups
            $table->string('title');
            $table->text('description')->nullable();

            // Archivo
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_extension', 10);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');

            // Metadatos básicos
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->uuid('status_id')->nullable(); // Referencia a lookups

            // Control
            $table->integer('sort_order')->default(0);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_verified')->default(false);

            // Campos adicionales
            $table->uuid('document_category_id')->nullable()->comment('Lookup: contrato/acta/factura/garantía/etc.');
            $table->string('number')->nullable()->comment('Número o referencia del documento');
            $table->string('template_key')->nullable()->comment('Clave de plantilla Blade o personalizada');
            $table->json('content')->nullable()->comment('Variables/contenido del documento generado');
            $table->timestamp('generated_at')->nullable()->comment('Cuándo se generó el PDF');
            $table->timestamp('signed_at')->nullable()->comment('Cuándo se firmó electrónicamente');
            $table->text('notes')->nullable()->comment('Notas internas');
            $table->uuid('created_by')->nullable()->comment('Usuario que subió/generó el documento');
            $table->uuid('parent_document_id')->nullable()->comment('Documento padre para versiones (original → firmado)');

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('document_type_id')->references('id')->on('lookups');
            $table->foreign('status_id')->references('id')->on('lookups');
            $table->foreign('document_category_id')->references('id')->on('lookups');

            // Índices
            $table->index('documentable_type');
            $table->index('documentable_id');
            $table->index('document_type_id');
            $table->index('status_id');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
