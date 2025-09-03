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
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->uuid('document_type_id');
            $table->string('document_number')->unique();
            $table->string('person_type');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_type_id')->references('id')->on('lookups')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
