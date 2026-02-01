<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MagapitObservation;
use App\Models\ProtectedArea;

class MagapitObservationSeeder extends Seeder
{
    /**
     * Run database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'MPL')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area MPL not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 3],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Civet cat', 'scientific_name' => 'Paradoxurus philippinensis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 3],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Wild boar', 'scientific_name' => 'Sus scrofa', 'recorded_count' => 1],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 10],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyros pilosanthera', 'recorded_count' => 25],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Sablot', 'scientific_name' => 'Litsea glutinosa', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Balete', 'scientific_name' => 'Ficus benjamina', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea concorta', 'recorded_count' => 3],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S9', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Red Lauan', 'scientific_name' => 'Shorea negrosensis', 'recorded_count' => 5],
        ];

        foreach ($observations as $observation) {
            MagapitObservation::create(array_merge($observation, [
                'protected_area_id' => $protectedArea->id,
            ]));
        }
    }
}
