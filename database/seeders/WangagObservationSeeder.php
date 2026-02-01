<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WangagObservation;
use App\Models\ProtectedArea;

class WangagObservationSeeder extends Seeder
{
    /**
     * Run database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'WWFR')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area WWFR not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            // 2024 1st Semester
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mayapis', 'scientific_name' => 'Shorea palosapis', 'recorded_count' => 40],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 10],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientalis', 'recorded_count' => 11],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White lauan', 'scientific_name' => 'Shorea contorta', 'recorded_count' => 55],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Red lauan', 'scientific_name' => 'Shorea negrosensis', 'recorded_count' => 65],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-ipil', 'scientific_name' => 'Leucaena leucocephala', 'recorded_count' => 15],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyros pilosanthera', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine tailorbird', 'scientific_name' => 'Orthotomus castaneiceps', 'recorded_count' => 3],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Northern Roufous Hornbill', 'scientific_name' => 'Buceros hydrocorax', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine coucal', 'scientific_name' => 'Centripus viridis', 'recorded_count' => 4],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White breasted swallow', 'scientific_name' => 'Artamus leucorynchus', 'recorded_count' => 13],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White-eared brown -dove', 'scientific_name' => 'Papiteron leucotis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pygmy Flower pecker', 'scientific_name' => 'Dicaeum pygmaeum', 'recorded_count' => 9],

            // 2024 2nd Semester
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => 'Ficus septica', 'recorded_count' => 7],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Antipolo', 'scientific_name' => 'Artocarpus blancoi', 'recorded_count' => 10],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius', 'recorded_count' => 12],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Tangisang-bayawak', 'scientific_name' => 'Ficus variegate', 'recorded_count' => 7],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => 'Dillenia philippinensis', 'recorded_count' => 6],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine coucal', 'scientific_name' => 'Centripus viridis', 'recorded_count' => 3],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Hanging parrot', 'scientific_name' => 'Loriculus philippensis', 'recorded_count' => 6],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Pygmy Flower pecker', 'scientific_name' => 'Dicaeum pygmaeum', 'recorded_count' => 14],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Serpent Eagle', 'scientific_name' => 'Spilornis holospilus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Common Emerald Dove', 'scientific_name' => 'Chalcophaps indica', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Grasshoppers', 'scientific_name' => '', 'recorded_count' => 20],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Common sun skink (Bubuli)', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Dragonflies', 'scientific_name' => '', 'recorded_count' => 14],

            // 2025 1st Semester - Part 1
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Red Lauan', 'scientific_name' => 'Shorea negrosensis', 'recorded_count' => 65],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mayapis', 'scientific_name' => 'Shorea palosapis', 'recorded_count' => 40],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tangile', 'scientific_name' => 'Shorea polysperma', 'recorded_count' => 20],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 11],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea concorta', 'recorded_count' => 55],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyrus pilosanthera', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Antiplolo', 'scientific_name' => 'Artocarpus blancoi', 'recorded_count' => 13],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuia', 'scientific_name' => 'Biscofia javanica', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalantas', 'scientific_name' => 'Toona calantas', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea concorta', 'recorded_count' => 55],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Red Lauan', 'scientific_name' => 'Shorea negrosensis', 'recorded_count' => 65],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mayapis', 'scientific_name' => 'Shorea palosapis', 'recorded_count' => 40],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 10],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Antiplolo', 'scientific_name' => 'Artocarpus blancoi', 'recorded_count' => 10],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuia', 'scientific_name' => 'Biscofia javanica', 'recorded_count' => 1],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyrus pilosanthera', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientalis', 'recorded_count' => 11],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius', 'recorded_count' => 12],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hinlaumo', 'scientific_name' => 'Mallotus mollisimus', 'recorded_count' => 9],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => 'Melanolepis multiglandulosa', 'recorded_count' => 7],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => 'Ficus septica', 'recorded_count' => 8],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus nota', 'recorded_count' => 7],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tangisang-bayawak', 'scientific_name' => 'Ficus variegata', 'recorded_count' => 5],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Balite', 'scientific_name' => 'Ficus balete', 'recorded_count' => 2],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => 'Dilenia philiphinensis', 'recorded_count' => 6],
            ['transaction_code' => 'R2-WWFR-BMS', 'station_code' => 'R2-WWFR-BMS-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Pahutan', 'scientific_name' => 'Mangifera altisima', 'recorded_count' => 1],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            WangagObservation::create($observation);
        }

        $this->command->info('Wangag observations seeded successfully!');
    }
}
