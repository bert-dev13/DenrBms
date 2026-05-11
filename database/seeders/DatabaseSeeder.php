<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProtectedAreaSeeder::class,
            SiteNameSeeder::class,
            SpeciesSeeder::class,
        ]);

        $this->seedObservationDefaultsWhenEmpty();
    }

    private function seedObservationDefaultsWhenEmpty(): void
    {
        $observationSeeders = [
            BmsSpeciesObservationSeeder::class => ['batanes_tbl'],
            FuyotObservationSeeder::class => ['fuyot_tbl'],
            QuirinoObservationSeeder::class => ['quirino_tbl'],
            PalauiObservationSeeder::class => ['palaui_tbl'],
            BauaObservationSeeder::class => ['baua_tbl'],
            WangagObservationSeeder::class => ['wangag_tbl'],
            MagapitObservationSeeder::class => ['magapit_tbl'],
            MadupapaObservationSeeder::class => ['madupapa_tbl'],
            MarianoObservationSeeder::class => ['mariano_tbl'],
            ToyotaSeeder::class => ['toyota_tbl'],
            SanRoqueSeeder::class => ['roque_tbl'],
            MangaSeeder::class => ['manga_tbl'],
            QuibalSeeder::class => ['quibal_tbl'],
            MadreSeeder::class => ['madre_tbl'],
            TumauiniSeeder::class => ['tumauini_tbl'],
            BanganSeeder::class => ['bangan_tbl'],
            SalinasSeeder::class => ['salinas_tbl'],
            DupaxSeeder::class => ['dupax_tbl'],
            CasecnanSeeder::class => ['casecnan_tbl'],
            DipaniongSeeder::class => ['dipaniong_tbl'],
        ];

        foreach ($observationSeeders as $seeder => $tables) {
            $hasSeedData = collect($tables)->contains(function (string $table): bool {
                return Schema::hasTable($table) && DB::table($table)->exists();
            });

            if (! $hasSeedData) {
                $this->call($seeder);
            }
        }
    }
}
