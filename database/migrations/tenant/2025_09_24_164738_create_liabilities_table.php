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
        //Reponsabilidades como cuota de admin o mantenimiento
        Schema::create('liabilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rent_id')->nullable();
            $table->uuid('liability_type_id')->nullable();
            $table->decimal('fee',5,2)->comment('cuota, pago o precio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('liability_type_id')->references('id')->on('lookups');
            $table->foreign('rent_id')->references('id')->on('rents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liabilities');
    }
};
