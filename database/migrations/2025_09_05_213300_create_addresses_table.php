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
            $table->string('city');
            $table->string('department');
            $table->string('country');
            $table->string('zip_code')->nullable();
            $table->string('sector')->nullable();
            $table->string('stratum')->nullable();
            $table->string('complement')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('property_id')->references('id')->on('lookups');
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
