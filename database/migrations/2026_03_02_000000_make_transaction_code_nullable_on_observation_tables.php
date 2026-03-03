<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Observation table names (static model-backed tables).
     */
    private array $observationTables = [
        'batanes_tbl',
        'fuyot_tbl',
        'quirino_tbl',
        'palaui_tbl',
        'buaa_tbl',
        'wangag_tbl',
        'magapit_tbl',
        'madupapa_tbl',
        'mariano_tbl',
        'madre_tbl',
        'tumauini_tbl',
        'bangan_tbl',
        'salinas_tbl',
        'dupax_tbl',
        'casecnan_tbl',
        'dipaniong_tbl',
        'toyota_tbl',
        'roque_tbl',
        'manga_tbl',
        'quibal_tbl',
    ];

    /**
     * Run the migrations.
     * Makes transaction_code nullable so new observations can be created without it.
     */
    public function up(): void
    {
        foreach ($this->observationTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'transaction_code')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('transaction_code', 50)->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->observationTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'transaction_code')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('transaction_code', 50)->nullable(false)->change();
                });
            }
        }
    }
};
