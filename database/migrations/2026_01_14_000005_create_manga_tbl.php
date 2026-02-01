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
        Schema::create('manga_tbl', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to protected areas
            $table->unsignedBigInteger('protected_area_id');
            
            // Original BMS columns (cleaned names)
            $table->string('transaction_code', 50);
            $table->string('station_code', 60);
            $table->year('patrol_year');
            $table->unsignedTinyInteger('patrol_semester'); // store 1 or 2
            $table->enum('bio_group', ['fauna', 'flora']);
            $table->string('common_name', 150);
            $table->string('scientific_name', 200)->nullable();
            $table->unsignedInteger('recorded_count');
            
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('protected_area_id')
                  ->references('id')
                  ->on('protected_areas')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_tbl');
    }
};
