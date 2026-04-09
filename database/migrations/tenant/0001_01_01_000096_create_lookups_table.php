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
        Schema::create('lookups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category'); // ej: "document_type", "property_type"
            $table->string('name');     // ej: "Cédula", "Pasaporte", "Apartamento"
            $table->string('alias')->nullable();
            $table->decimal('value')->nullable();
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('lang')->default('ES');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category']);
            $table->index(['code']);
            $table->index(['alias']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lookups');
    }
};
