<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_sections', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 60)->index();
            $table->string('section_key', 60)->nullable();
            $table->string('section_type', 32)->default('clause');
            $table->string('heading', 255)->nullable();
            $table->text('body')->nullable();
            $table->json('content_json')->nullable();
            $table->json('section_config')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_sections');
    }
};
