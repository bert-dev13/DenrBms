<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuirinoObservation;
use App\Models\ProtectedArea;

class QuirinoObservationSeeder extends Seeder
{
    /**
     * Run database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'QPL')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area QPL not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 12],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 76],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Red Lauan', 'scientific_name' => 'Shorea negrosensis', 'recorded_count' => 15],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tangisang Bayawak', 'scientific_name' => 'Ficus variegata Blume', 'recorded_count' => 8],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibbig', 'scientific_name' => 'Ficus nota', 'recorded_count' => 25],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea contorta', 'recorded_count' => 13],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Balubo', 'scientific_name' => 'Diplodiscus paniculatus Turcz.', 'recorded_count' => 55],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hagemit', 'scientific_name' => 'Ficus minahassae (Teijsm. & Vriese) Miq.', 'recorded_count' => 16],
            ['transaction_code' => 'R2-QPL-BMS-T1', 'station_code' => 'R2-QPL-BMS-T1-S9', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kawayan Kiling', 'scientific_name' => 'Bambusa vulgaris Schrad.', 'recorded_count' => 8],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            QuirinoObservation::create($observation);
        }

        $this->command->info('Quirino observations seeded successfully!');
    }
}
