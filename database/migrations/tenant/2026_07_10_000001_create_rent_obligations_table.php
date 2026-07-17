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
            $table->uuid('obligation_type_id')->comment('Lookup: predial, hipoteca, administracion...');
            $table->decimal('amount', 12, 2)->comment('Monto de la obligación en el momento del contrato');
            $table->decimal('total', 12, 2)->comment('Total de la obligación (ej: saldo hipoteca)');
            $table->uuid('frequency_type_id')->comment('Lookup: monthly, yearly, one_time');
            $table->date('expiration_date')->nullable();
            $table->uuid('status_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_id')->references('id')->on('rents');
            $table->foreign('obligation_type_id')->references('id')->on('lookups');
            $table->foreign('frequency_type_id')->references('id')->on('lookups');
            $table->foreign('status_id')->references('id')->on('lookups');

            $table->index('rent_id');
            $table->index('obligation_type_id');
            $table->index('status_id');
            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_obligations');
    }
};
