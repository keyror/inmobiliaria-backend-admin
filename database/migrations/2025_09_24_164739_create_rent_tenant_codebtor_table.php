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
        Schema::create('rent_tenant_codebtor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rent_id')->nullable('renta');
            $table->uuid('tenant_id')->nullable('inquilino');
            //TODO:: CREAR TABLA CODEUDORES Y AGREGAR LA RELACIÓN CON LA RENTA
            $table->uuid('codebtor_id')->nullable('Codeudor');
            $table->integer('percentage')->comment('Porcentaje que me le pertenece');
            $table->timestamps();

            $table->foreign('rent_id')->references('id')->on('rents');
            $table->foreign('tenant_id')->references('id')->on('people');
            $table->foreign('codebtor_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_tenant_codebtor');
    }
};
