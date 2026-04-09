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
        //canon de arrendamiento
        Schema::create('lease_fees', function (Blueprint $table) {
            $table->id();
            $table->uuid('rent_id');
            $table->uuid('lease_fee_type_id');
            $table->string('description')->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lease_fee_type_id')->references('id')->on('lookups');
            $table->foreign('rent_id')->references('id')->on('rents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_fees');
    }
};
