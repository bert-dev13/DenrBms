<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds is_endemic column to all species observation tables for endemic species reporting.
     */
    public function up(): void
    {
        $tables = $this->getObservationTables();

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            if (Schema::hasColumn($tableName, 'is_endemic')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                // Omit after() for SQLite compatibility (SQLite does not support column order in ALTER TABLE)
                $table->boolean('is_endemic')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = $this->getObservationTables();

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            if (!Schema::hasColumn($tableName, 'is_endemic')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('is_endemic');
            });
        }
    }

    private function getObservationTables(): array
    {
        $static = [
            'batanes_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'palaui_tbl',
            'quirino_tbl', 'mariano_tbl', 'madupapa_tbl', 'wangag_tbl', 'toyota_tbl',
            'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl',
            'salinas_tbl', 'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl', 'roque_tbl',
        ];

        $dynamic = [];
        try {
            $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();
            if ($driver === 'sqlite') {
                $all = \Illuminate\Support\Facades\DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE '%_tbl'");
                foreach ($all as $row) {
                    $name = $row->name;
                    if (!in_array($name, $static)) {
                        $dynamic[] = $name;
                    }
                }
            } else {
                $all = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                foreach ($all as $row) {
                    $name = array_values((array) $row)[0];
                    if (str_ends_with($name, '_tbl') && !in_array($name, $static)) {
                        $dynamic[] = $name;
                    }
                    if (str_ends_with($name, '_site_tbl')) {
                        $dynamic[] = $name;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return array_unique(array_merge($static, $dynamic));
    }
};
