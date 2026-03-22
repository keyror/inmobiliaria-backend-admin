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
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relación polimórfica
            $table->uuid('imageable_id')->nullable();
            $table->string('imageable_type')->nullable();

            // Información básica
            $table->uuid('image_type_id')->nullable(); // Referencia a lookups
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Archivo
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->string('file_extension', 10);
            $table->string('mime_type', 50);
            $table->bigInteger('file_size');
            $table->string('disk')->default('public');

            // Dimensiones
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            // Control y presentación
            $table->integer('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->boolean('is_public')->default(true);
            ///$table->boolean('is_processed')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('image_type_id')->references('id')->on('lookups');

            // Índices
            $table->index('imageable_type');
            $table->index('imageable_id');
            $table->index('image_type_id');
            $table->index('is_cover');
            $table->index( 'is_public');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
