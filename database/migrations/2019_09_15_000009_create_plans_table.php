<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->uuid('frequency_type_id')->nullable();
            $table->unsignedTinyInteger('discount')->nullable();
            $table->unsignedSmallInteger('max_users');
            $table->unsignedSmallInteger('max_properties');
            $table->unsignedTinyInteger('max_images_per_property');
            $table->boolean('is_active')->default(true);
            $table->json('data')->nullable();
            $table->timestamps();

            $table->foreign('frequency_type_id')->references('id')->on('lookups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
