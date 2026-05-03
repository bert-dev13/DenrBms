<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /**
     * Seed species registry rows aligned with demo observation data (Batanes / BPLS seed).
     */
    public function run(): void
    {
        foreach (config('endemic_species_defaults', []) as $row) {
            Species::updateOrCreate(
                ['scientific_name' => $row['scientific_name']],
                array_merge(['is_migratory' => false], $row)
            );
        }

        foreach (config('migratory_species_defaults', []) as $row) {
            Species::updateOrCreate(
                ['scientific_name' => $row['scientific_name']],
                $row
            );
        }

        $this->command->info('Species registry seeded.');
    }
}
