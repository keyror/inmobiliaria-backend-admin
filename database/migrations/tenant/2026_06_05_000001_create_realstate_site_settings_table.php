<?php

use App\Support\RealstateSiteTemplates;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realstate_site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_set')->default(RealstateSiteTemplates::DEFAULT_TEMPLATE_SET)->index();
            $table->string('backup_template_set')->nullable();
            $table->json('theme')->nullable();
            $table->json('backup_theme')->nullable();
            $table->json('pages')->nullable();
            $table->json('backup_pages')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realstate_site_settings');
    }
};
