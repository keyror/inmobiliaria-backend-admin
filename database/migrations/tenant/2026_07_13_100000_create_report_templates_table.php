<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable()->comment('Sucursal dueña de esta plantilla; null = compartida');
            $table->string('name');
            $table->json('columns'); // [{ key: 'rent.canon', label: 'Canon' }, ...]
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
