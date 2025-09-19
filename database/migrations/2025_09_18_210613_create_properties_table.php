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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');

            // Estado y activación
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->uuid('status_id');

            // Información básica
            $table->string('title');

            $table->uuid('offer_type_id');
            $table->uuid('property_type_id');

            // Características físicas
            $table->integer('social_strata')->default(0);
            $table->integer('year_built')->nullable();
            $table->integer('rooms')->default(0);
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('garages')->default(0);
            $table->uuid('garage_type_id')->nullable();
            $table->integer('parking_spots')->default(0);
            $table->uuid('parking_type_id')->nullable();

            // Información catastral
            $table->string('cadastral_number')->nullable()->unique();

            // Ubicación gogle map
            $table->string('url_google_map')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('boundaries')->nullable();

            // Descripción
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('lookups');
            $table->foreign('offer_type_id')->references('id')->on('lookups');
            $table->foreign('property_type_id')->references('id')->on('lookups');
            $table->foreign('garage_type_id')->references('id')->on('lookups');
            $table->foreign('parking_type_id')->references('id')->on('lookups');

            $table->index('code');
            $table->index('is_active');
            $table->index('status_id');
            $table->index('offer_type_id');
            $table->index('property_type_id');
            $table->index('cadastral_number');
        });

        Schema::create('property_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('area_type_id'); // Referencia a lookups (total, built, land, private, etc)
            $table->decimal('area_value', 10, 2);
            $table->string('area_unit'); // Referencia a lookups (sqm, sqft, hectares, yards)
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('area_type_id')->references('id')->on('lookups');
            $table->foreign('property_id')->references('id')->on('properties');

            $table->index('area_value');
        });

        Schema::create('property_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('property_id');
            $table->uuid('price_type_id'); // Referencia a lookups (rent, sale, etc)
            $table->decimal('price_min', 15, 2);
            $table->decimal('price_max', 15, 2);
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('COP');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('price_type_id')->references('id')->on('lookups');

            $table->index('price_min');
            $table->index( 'price_max');
        });

        Schema::create('property_publish_channels', function (Blueprint $table) {
            $table->id();
            $table->uuid('property_id');
            $table->uuid('channel_id'); // Referencia a lookups (website, FB, Whatsapp, etc)
            $table->string('external_link')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->datetime('unpublished_at')->nullable();
            $table->json('channel_specific_data')->nullable(); // Para datos específicos del canal
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('channel_id')->references('id')->on('lookups');
            $table->index( 'is_published');
        });

        Schema::create('property_features', function (Blueprint $table) {
            $table->id();
            $table->uuid('property_id');
            $table->uuid('feature_type_id'); // Referencia a lookups (aire acondicionado, internet, terraza, etc)
            $table->boolean('has_feature')->default(true);
            $table->text('feature_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('feature_type_id')->references('id')->on('lookups');
            $table->index( 'has_feature');
        });

        Schema::create('property_obligations', function (Blueprint $table) {
            $table->id();
            $table->uuid('property_id');
            $table->uuid('obligation_type_id'); // Referencia a lookups (impuesto_predial, hipoteca, mantenimiento, etc)
            $table->decimal('amount', 12, 2);
            $table->uuid('frequency_type_id'); // Referencia a lookups (monthly, yearly, one_time, etc)
            $table->date('due_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('obligation_type_id')->references('id')->on('lookups');
            $table->foreign('frequency_type_id')->references('id')->on('lookups');

            $table->index('property_id');
            $table->index('obligation_type_id');
            $table->index('is_active');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_obligations');
        Schema::dropIfExists('property_features');
        Schema::dropIfExists('property_publish_channels');
        Schema::dropIfExists('property_prices');
        Schema::dropIfExists('property_areas');
        Schema::dropIfExists('properties');
    }
};
