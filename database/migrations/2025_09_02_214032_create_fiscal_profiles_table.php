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
        Schema::create('fiscal_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('tax_regime')->comment('regimen fiscal ejm: simplicado, común, contribuyente'); // Simplificado, Común, Gran contribuyente
            $table->string('responsible_for_vat')->default('NO')->comment('responsable de iva'); // sí / no
            $table->decimal('vat_withholding', 5, 2)->nullable()->comment('Retención IVA');
            $table->decimal('income_tax_withholding', 5, 2)->nullable()->comment('Retención Fuente');
            $table->decimal('ica_withholding', 5, 2)->nullable()->comment('Retención ICA');
            $table->string('economic_activity')->nullable()->comment('actividad economica'); // código CIIU
            $table->string('dv')->nullable()->comment('digito de verificación NIT'); // Dígito de verificación NIT
            $table->string('liability_type')->comment('obligaciones tributarias');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_profiles');
    }
};
