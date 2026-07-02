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
        Schema::table('realstate_site_settings', function (Blueprint $table) {
            $table->string('backup_template_set')->nullable()->after('template_set');
            $table->json('backup_theme')->nullable()->after('theme');
            $table->json('backup_pages')->nullable()->after('pages');
        });
    }

    public function down(): void
    {
        Schema::table('realstate_site_settings', function (Blueprint $table) {
            $table->dropColumn(['backup_template_set', 'backup_theme', 'backup_pages']);
        });
    }
};
