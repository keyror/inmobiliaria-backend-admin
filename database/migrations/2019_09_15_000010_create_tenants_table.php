<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID o string personalizado
            $table->string('name');
            $table->string('email')->unique();
            $table->string('domain')->unique();
            $table->enum('plan', ['basic', 'premium', 'enterprise'])->default('basic');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('subscription_ends_at')->nullable();
            $table->json('data')->nullable();

            $table->timestamps();

            // Índices para optimizar búsquedas
            $table->index(['status', 'plan']);
            $table->index('subscription_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
