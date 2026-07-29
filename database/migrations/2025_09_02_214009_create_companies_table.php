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
            $table->uuid('parent_company_id')->nullable()->comment('null = sede principal; FK = sucursal');
            $table->string('branch_code')->nullable()->comment('Código interno, ej: SEDE, SUC-001');
            $table->boolean('is_active')->default(true);
            $table->boolean('uses_branches')->default(false)->comment('Habilita el módulo de sucursales para este tenant');
            $table->string('company_name'); // Razón social
            $table->string('tradename')->nullable(); // Nombre comercial
            $table->string('nit')->unique();
            $table->uuid('legal_representative_id')->nullable();
            $table->uuid('person_attendant_id')->nullable();
            $table->uuid('fiscal_profile_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles')->onDelete('cascade');
            $table->foreign('legal_representative_id')->references('id')->on('people');
            $table->foreign('person_attendant_id')->references('id')->on('people');

            $table->index('parent_company_id');
        });

        // FK diferida: people.company_id → companies (people se crea antes que companies)
        Schema::table('people', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });

        Schema::create('company_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('user_id');
            $table->boolean('is_default')->default(false)->comment('Sucursal por defecto al hacer login');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['company_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('companies');
    }
};
