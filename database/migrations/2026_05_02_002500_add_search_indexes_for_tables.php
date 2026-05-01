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
        $this->addIndexIfPossible('protected_areas', ['name'], 'idx_protected_areas_name');
        $this->addIndexIfPossible('protected_areas', ['code'], 'idx_protected_areas_code');
        $this->addIndexIfPossible('site_names', ['name'], 'idx_site_names_name');

        $observationTables = [
            'batanes_tbl', 'buaa_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'mariano_tbl',
            'madupapa_tbl', 'palaui_tbl', 'quirino_tbl', 'wangag_tbl', 'toyota_tbl', 'roque_tbl',
            'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl', 'salinas_tbl',
            'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl',
        ];

        foreach ($observationTables as $table) {
            $this->addIndexIfPossible($table, ['station_code'], "idx_{$table}_station_code");
            $this->addIndexIfPossible($table, ['transaction_code'], "idx_{$table}_transaction_code");
            $this->addIndexIfPossible($table, ['common_name'], "idx_{$table}_common_name");
            $this->addIndexIfPossible($table, ['scientific_name'], "idx_{$table}_scientific_name");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('protected_areas', 'idx_protected_areas_name');
        $this->dropIndexIfExists('protected_areas', 'idx_protected_areas_code');
        $this->dropIndexIfExists('site_names', 'idx_site_names_name');

        $observationTables = [
            'batanes_tbl', 'buaa_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'mariano_tbl',
            'madupapa_tbl', 'palaui_tbl', 'quirino_tbl', 'wangag_tbl', 'toyota_tbl', 'roque_tbl',
            'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl', 'salinas_tbl',
            'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl',
        ];

        foreach ($observationTables as $table) {
            $this->dropIndexIfExists($table, "idx_{$table}_station_code");
            $this->dropIndexIfExists($table, "idx_{$table}_transaction_code");
            $this->dropIndexIfExists($table, "idx_{$table}_common_name");
            $this->dropIndexIfExists($table, "idx_{$table}_scientific_name");
        }
    }

    private function addIndexIfPossible(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            try {
                $blueprint->dropIndex($indexName);
            } catch (\Throwable $e) {
                // Ignore missing index errors to keep rollback resilient.
            }
        });
    }
};

