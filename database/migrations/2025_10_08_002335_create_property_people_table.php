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
        Schema::create('property_people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id')->comment('Propiedad');
            $table->uuid('person_id')->nullable()->comment('Propietario');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('person_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_people');
    }
};
