<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_banks', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('account_type_id');
            $table->uuid('bank_id');
            $table->string('account_number');
            $table->boolean('is_principal')->nullable();
            $table->nullableUuidMorphs('accountable');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_type_id')->references('id')->on('lookups');
            $table->foreign('bank_id')->references('id')->on('lookups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_banks');
    }
};
