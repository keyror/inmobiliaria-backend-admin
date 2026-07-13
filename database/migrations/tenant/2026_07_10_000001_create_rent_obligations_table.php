<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_obligations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rent_id');
            $table->uuid('obligation_type_id')->nullable()->comment('Lookup: predial, hipoteca, administracion...');
            $table->decimal('amount', 12, 2)->nullable()->comment('Monto de la obligación en el momento del contrato');
            $table->decimal('total', 12, 2)->nullable()->comment('Total de la obligación (ej: saldo hipoteca)');
            $table->uuid('frequency_type_id')->nullable()->comment('Lookup: monthly, yearly, one_time');
            $table->date('expiration_date')->nullable();
            $table->string('paid_by', 20)->nullable()->comment('owner | tenant');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_id')->references('id')->on('rents');
            $table->foreign('obligation_type_id')->references('id')->on('lookups');
            $table->foreign('frequency_type_id')->references('id')->on('lookups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_obligations');
    }
};
