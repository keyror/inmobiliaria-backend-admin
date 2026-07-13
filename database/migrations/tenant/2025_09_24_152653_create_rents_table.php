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
        Schema::create('rents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->nullable()->comment('Estado del contrato');
            $table->uuid('property_id')->comment('Propiedad');
            $table->date('start_date')->comment('Fecha de inicio');
            $table->date('end_date')->nullable()->comment('Fecha fin');
            $table->uuid('limit_dates_id')->nullable();
            $table->integer('duration')->nullable()->comment('Duración o término del contrato');
            $table->string('destination')->nullable()->comment('Destinaciones: Vivienda, Comercial');
            $table->string('activity')->nullable();
            $table->date('period')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('consignment_account')->nullable();
            $table->string('commissions')->nullable();
            // hereda del propietario $table->uuid('fiscal_profile_id')->nullable();

            // Campos del contrato
            $table->string('contract_number')->nullable()->comment('Número o referencia del contrato');
            $table->uuid('contract_type_id')->nullable()->comment('Lookup: arrendamiento/comodato/colocación');
            $table->decimal('canon', 15, 2)->nullable()->comment('Valor mensual del arrendamiento');
            $table->decimal('iva', 5, 2)->nullable()->comment('Porcentaje de IVA si aplica (ej: 19)');
            $table->boolean('administration_included')->default(false)->comment('¿Incluye cuota de administración?');
            $table->boolean('is_ph')->default(false)->comment('¿Es propiedad horizontal?');
            $table->uuid('increment_type_id')->nullable()->comment('Lookup: IPC / porcentaje manual / IPC+puntos');
            $table->date('adjustment_date')->nullable()->comment('Fecha de ajuste del incremento');
            $table->boolean('is_insured')->default(false)->comment('¿Tiene póliza de seguro?');
            $table->uuid('payment_bank_id')->nullable()->comment('Lookup: banco para consignación del canon');
            $table->string('signed_city')->nullable()->comment('Ciudad donde se firma el contrato');
            $table->date('signed_at')->nullable()->comment('Fecha de firma del contrato');
            $table->json('additional_clauses')->nullable()->comment('Cláusulas adicionales pactadas');
            $table->text('internal_notes')->nullable()->comment('Notas internas de la inmobiliaria');

            $table->timestamps();
            $table->softDeletes();

            // hereda del propietario $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('limit_dates_id')->references('id')->on('limit_dates');
            $table->foreign('contract_type_id')->references('id')->on('lookups');
            $table->foreign('increment_type_id')->references('id')->on('lookups');
            $table->foreign('payment_bank_id')->references('id')->on('lookups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};
