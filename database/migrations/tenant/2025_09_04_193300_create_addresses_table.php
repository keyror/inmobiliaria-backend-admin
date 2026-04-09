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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('property_id')->nullable();
            $table->uuid('person_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('address');
            $table->uuid('city_id');
            $table->uuid('department_id');
            $table->uuid('country_id');
            $table->boolean('is_principal')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('sector')->nullable();
            $table->uuid('stratum_id')->nullable();
            $table->string('complement')->nullable();
            $table->uuid('via_type_id')->nullable();
            $table->string('via_number')->nullable();
            $table->uuid('letra1_id')->nullable();
            $table->uuid('orientation1_id')->nullable();
            $table->string('number2')->nullable();
            $table->uuid('letra2_id')->nullable();
            $table->uuid('orientation2_id')->nullable();
            $table->string('number3')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('stratum_id')->references('id')->on('lookups');
            $table->foreign('city_id')->references('id')->on('lookups');
            $table->foreign('department_id')->references('id')->on('lookups');
            $table->foreign('country_id')->references('id')->on('lookups');
            $table->foreign('via_type_id')->references('id')->on('lookups');
            $table->foreign('letra1_id')->references('id')->on('lookups');
            $table->foreign('orientation1_id')->references('id')->on('lookups');
            $table->foreign('letra2_id')->references('id')->on('lookups');
            $table->foreign('orientation2_id')->references('id')->on('lookups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
