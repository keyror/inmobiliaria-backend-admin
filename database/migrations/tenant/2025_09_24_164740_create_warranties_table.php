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
        Schema::create('warranties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rent_id');
            $table->uuid('warranty_type_id');
            $table->string('code')->comment('radicado');
            $table->boolean('is_approved')->nullable();
            $table->string('insurance_policy')->nullable();

            $table->foreign('warranty_type_id')->references('id')->on('lookups');
            $table->foreign('rent_id')->references('id')->on('rents');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
