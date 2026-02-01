<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            BmsSpeciesObservationSeeder::class,
            FuyotObservationSeeder::class,
            QuirinoObservationSeeder::class,
            PalauiObservationSeeder::class,
            BauaObservationSeeder::class,
            WangagObservationSeeder::class,
            MagapitObservationSeeder::class,
            MadupapaObservationSeeder::class,
            MarianoObservationSeeder::class,
            ToyotaSeeder::class,
            SanRoqueSeeder::class,
            MangaSeeder::class,
            QuibalSeeder::class,
            MadreSeeder::class,
            TumauiniSeeder::class,
            BanganSeeder::class,
            SalinasSeeder::class,
            DupaxSeeder::class,
            CasecnanSeeder::class,
            DipaniongSeeder::class,
        ]);
    }
}
