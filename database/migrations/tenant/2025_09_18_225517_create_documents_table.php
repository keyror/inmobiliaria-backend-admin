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

            // Clasificación
            $table->uuid('document_type_id')->nullable();   // Tipo de plantilla (document_template_type)
            $table->uuid('document_category_id')->nullable(); // Categoría (contrato, acta, factura…)
            $table->string('title');
            $table->string('number')->nullable();           // Número del documento
            $table->string('template_key')->nullable();     // Clave de la plantilla PDF
            $table->text('description')->nullable();
            $table->json('content')->nullable();            // Campos específicos por tipo (actas, etc.)

            // Archivo
            $table->string('file_name')->default('pending');
            $table->string('file_path')->default('');
            $table->string('file_extension', 10)->default('');
            $table->string('mime_type', 100)->default('application/octet-stream');
            $table->bigInteger('file_size')->default(0);

            // Metadatos
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->date('signed_at')->nullable();
            $table->uuid('status_id')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('parent_document_id')->nullable();

            // Control
            $table->integer('sort_order')->default(0);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('document_type_id')->references('id')->on('lookups');
            $table->foreign('document_category_id')->references('id')->on('lookups');
            $table->foreign('status_id')->references('id')->on('lookups');
            $table->foreign('created_by')->references('id')->on('users');

            // Índices
            $table->index('documentable_type');
            $table->index('documentable_id');
            $table->index('document_type_id');
            $table->index('document_category_id');
            $table->index('status_id');
            $table->index('generated_at');
            $table->index('expiry_date');
        });

        // FK auto-referencial se añade después de crear la tabla
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('parent_document_id')->references('id')->on('documents')->nullOnDelete();
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
