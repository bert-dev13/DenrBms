<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per–protected area species observation tables (BMS import / patrol rows).
     */
    private array $observationTables = [
        'batanes_tbl',
        'fuyot_tbl',
        'quirino_tbl',
        'palaui_tbl',
        'baua_tbl',
        'wangag_tbl',
        'magapit_tbl',
        'madupapa_tbl',
        'mariano_tbl',
        'toyota_tbl',
        'roque_tbl',
        'manga_tbl',
        'quibal_tbl',
        'madre_tbl',
        'tumauini_tbl',
        'bangan_tbl',
        'salinas_tbl',
        'dupax_tbl',
        'casecnan_tbl',
        'dipaniong_tbl',
    ];

    public function up(): void
    {
        foreach ($this->observationTables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('protected_area_id');
                $table->string('transaction_code', 50)->nullable();
                $table->string('station_code', 60);
                $table->year('patrol_year');
                $table->unsignedTinyInteger('patrol_semester');
                $table->enum('bio_group', ['fauna', 'flora']);
                $table->string('common_name', 150);
                $table->string('scientific_name', 200)->nullable();
                $table->unsignedInteger('recorded_count');
                $table->timestamps();

                $table->foreign('protected_area_id')
                    ->references('id')
                    ->on('protected_areas')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->observationTables) as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
