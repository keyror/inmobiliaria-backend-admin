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
        //actividades economicas ciuu
        Schema::create('economic_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('description');
            $table->uuid('economic_activity_type_id');
            $table->boolean('is_principal');
            $table->uuid('fiscal_profile_id');
            $table->timestamps();

            $table->foreign('fiscal_profile_id')->references('id')->on('fiscal_profiles');
            $table->foreign('economic_activity_type_id')->references('id')->on('lookups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_activities');
    }
};
