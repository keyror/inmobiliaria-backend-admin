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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name'); // Razón social
            $table->string('tradename')->nullable(); // Nombre comercial
            $table->string('nit')->unique();
            $table->string('logo_url')->nullable();
            $table->uuid('legal_representative_id')->nullable();
            $table->uuid('person_attendant_id')->nullable();
            $table->uuid('fiscal_profile_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles')->onDelete('cascade');
            $table->foreign('legal_representative_id')->references('id')->on('people');
            $table->foreign('person_attendant_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
