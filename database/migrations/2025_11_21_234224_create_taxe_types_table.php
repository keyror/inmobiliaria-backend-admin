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
        Schema::create('taxe_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('description')->nullable();
            $table->uuid('taxe_type_id');
            $table->boolean('is_principal')->nullable();
            $table->uuid('fiscal_profile_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles');
            $table->foreign('taxe_type_id')->references('id')->on('lookups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxe_types');
    }
};
