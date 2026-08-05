<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_set')->nullable()->index();
            $table->json('theme')->nullable();
            $table->json('pages')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_site_settings');
    }
};
