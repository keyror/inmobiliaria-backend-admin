<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de soporte para Company en la base de datos central.
 * Schema idéntico al de tenant; se mantienen separadas porque cada DB es independiente.
 * Omite FK a `properties` en publish_channels (tabla inexistente en central).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tax_regime')->nullable();
            $table->uuid('responsible_for_vat_type_id')->nullable();
            $table->decimal('vat_withholding', 5, 2)->nullable();
            $table->decimal('income_tax_withholding', 5, 2)->nullable();
            $table->decimal('ica_withholding', 5, 2)->nullable();
            $table->decimal('rental_fee', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('responsible_for_vat_type_id')->references('id')->on('lookups');
        });

        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('fiscal_profile_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->uuid('document_type_id');
            $table->string('document_number')->unique();
            $table->string('dv')->nullable();
            $table->uuid('document_from_id');
            $table->uuid('organization_type_id');
            $table->date('birth_date')->nullable();
            $table->uuid('gender_type_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles')->onDelete('cascade');
            $table->foreign('organization_type_id')->references('id')->on('lookups');
            $table->foreign('document_type_id')->references('id')->on('lookups');
            $table->foreign('gender_type_id')->references('id')->on('lookups');
            $table->foreign('document_from_id')->references('id')->on('lookups');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_principal')->nullable();
            $table->nullableUuidMorphs('contactable');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name')->nullable();
            $table->nullableUuidMorphs('addressable');
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

        Schema::create('account_banks', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('account_type_id');
            $table->uuid('bank_id');
            $table->string('account_number');
            $table->boolean('is_principal')->nullable();
            $table->nullableUuidMorphs('accountable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_type_id')->references('id')->on('lookups');
            $table->foreign('bank_id')->references('id')->on('lookups');
        });

        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('imageable_id')->nullable();
            $table->string('imageable_type')->nullable();
            $table->uuid('image_type_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->string('file_extension', 10);
            $table->string('mime_type', 50);
            $table->bigInteger('file_size');
            $table->string('disk')->default('public');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('image_type_id')->references('id')->on('lookups');
            $table->index('imageable_type');
            $table->index('imageable_id');
            $table->index('image_type_id');
            $table->index('is_cover');
            $table->index('is_public');
            $table->index('sort_order');
        });

        Schema::create('publish_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->uuid('person_id')->nullable();
            $table->uuid('property_id')->nullable(); // sin FK — properties no existe en central
            $table->uuid('channel_id')->nullable();
            $table->string('external_link', 500)->nullable();
            $table->uuid('status_id')->nullable();
            $table->date('published_at')->nullable();
            $table->date('unpublished_at')->nullable();
            $table->json('channel_specific_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('channel_id')->references('id')->on('lookups');
            $table->foreign('status_id')->references('id')->on('lookups');
        });

        Schema::create('company_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->string('text_case_mode')->nullable()->default(null);
            $table->boolean('has_custom_smtp')->default(false);
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_from_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('publish_channels');
        Schema::dropIfExists('images');
        Schema::dropIfExists('account_banks');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('people');
        Schema::dropIfExists('fiscal_profiles');
    }
};
