<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publish_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->uuid('person_id')->nullable();
            $table->uuid('property_id')->nullable();
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
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('channel_id')->references('id')->on('lookups');
            $table->foreign('status_id')->references('id')->on('lookups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publish_channels');
    }
};
