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
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->char('company_id', 36)->nullable()->index()->after('batch_uuid');
        });
    }

    public function down(): void
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
