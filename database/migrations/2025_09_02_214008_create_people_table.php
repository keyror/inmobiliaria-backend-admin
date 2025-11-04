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
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('fiscal_profile_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->uuid('document_type_id');
            $table->string('document_number')->unique();
            $table->string('document_from')->comment('Lugar expedición documento');
            $table->string('organization_type_id')->comment('Persona natural o juridica');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles')->onDelete('cascade');
            $table->foreign('organization_type_id')->references('id')->on('lookups');
            $table->foreign('document_type_id')->references('id')->on('lookups');
            $table->foreign('user_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
