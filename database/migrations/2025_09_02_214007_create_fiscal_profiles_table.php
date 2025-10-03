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
            $table->string('tax_regime')->comment('regimen fiscal ejm: simplicado, común, contribuyente'); // Simplificado, Común, Gran contribuyente
            $table->string('responsible_for_vat')->default('NO')->comment('responsable de iva'); // sí / no
            $table->decimal('vat_withholding', 5, 2)->nullable()->comment('Retención IVA');
            $table->decimal('income_tax_withholding', 5, 2)->nullable()->comment('Retención Fuente');
            $table->decimal('ica_withholding', 5, 2)->nullable()->comment('Retención ICA');
            $table->string('economic_activity')->nullable()->comment('actividad economica'); // código CIIU
            $table->string('dv')->nullable()->comment('digito de verificación NIT'); // Dígito de verificación NIT
            $table->string('taxe_type_id')->comment('obligaciones tributarias o reponsabilidad fiscal ejm: gran contribuyente, agente de retención regimen simple');
            $table->uuid('ficas_profile_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('taxe_type_id')->references('id')->on('lookups');
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
