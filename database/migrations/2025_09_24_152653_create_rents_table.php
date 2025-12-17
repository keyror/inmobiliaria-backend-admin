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
            $table->date('end_date')->comment('Fecha fin');
            $table->uuid('limit_dates_id')->nullable();
            $table->integer('duration')->comment('Duración o término del contrato');
            $table->string('destination')->nullable()->comment('Destinaciones: Vivienda, Comercial');
            $table->string('activity')->nullable();
            $table->date('period');
            $table->string('interest_rate')->nullable();
            $table->string('consignment_account')->nullable();
            $table->string('commissions')->nullable();
            // hereda del propietario $table->uuid('fiscal_profile_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // hereda del propietario $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('limit_dates_id')->references('id')->on('limit_dates');
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
