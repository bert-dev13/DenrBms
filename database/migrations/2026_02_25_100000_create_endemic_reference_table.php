<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('endemic_reference', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('protected_area_id')->nullable();
            $table->string('scientific_name', 200);
            $table->string('common_name', 150)->nullable();
            $table->string('region_code', 20)->nullable();
            $table->string('source', 50)->nullable();
            $table->timestamps();

            $table->foreign('protected_area_id')
                ->references('id')
                ->on('protected_areas')
                ->onDelete('cascade');

            $table->index(['protected_area_id', 'scientific_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('endemic_reference');
    }
};
