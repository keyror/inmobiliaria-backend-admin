<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('domain')->unique();
            $table->uuid('plan_id')->nullable();
            $table->uuid('status_id');
            $table->timestamp('subscription_ends_at')->nullable();
            $table->json('data')->nullable();
            $table->foreign('status_id')->references('id')->on('lookups');
            $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete();
            $table->timestamps();

            $table->index(['status_id', 'plan_id']);
            $table->index('subscription_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
