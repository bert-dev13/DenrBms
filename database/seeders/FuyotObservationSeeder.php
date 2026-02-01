<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuyotObservation;
use App\Models\ProtectedArea;

class FuyotObservationSeeder extends Seeder
{
    /**
     * Run database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'FSNP')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area FSNP not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Wild duck', 'scientific_name' => 'Anas luzonica', 'recorded_count' => 30],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => 'Hypsipetes philipinus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Martines', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 4],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 6],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 8],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine tailor bird', 'scientific_name' => 'Orthotomus castaneiceps', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 8],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pagao', 'scientific_name' => 'Geopelia Striata', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => 'Hypsipetes philipinus', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine tailor bird', 'scientific_name' => 'Orthotomus castaneiceps', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus Indicus', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lawan', 'scientific_name' => 'Shorea Contorta', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Braminy kyte', 'scientific_name' => 'Haliastur indus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Martines', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Coucal', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pagao', 'scientific_name' => 'Geopelia Striata', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Antipolo', 'scientific_name' => 'Artucarpus blancoi', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus Indicus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White Lawan', 'scientific_name' => 'Shorea Contorta', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine tailor bird', 'scientific_name' => 'Orthotomus castaneiceps', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T1', 'station_code' => 'R2-FNSP-BMS-T1-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => 'Hypsipetes philipinus', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine tailor bird', 'scientific_name' => 'Orthotomus castaneiceps', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Little egret', 'scientific_name' => 'Egreta garzetta', 'recorded_count' => 10],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S2', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'yellow vented bulbul', 'scientific_name' => 'Pycnonoyus urostictus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Little egret', 'scientific_name' => 'Egreta garzetta', 'recorded_count' => 9],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S3', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black nape oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus Indicus', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => 'Hypsipetes philipinus', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S4', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'yellow vented bulbul', 'scientific_name' => 'Pycnonoyus urostictus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black nape oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S5', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Little egret', 'scientific_name' => 'Egreta garzetta', 'recorded_count' => 9],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus Indicus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S6', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Coucal', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pagao', 'scientific_name' => 'Geopelia Striata', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiflets', 'scientific_name' => 'Aerodramus fuciphagus', 'recorded_count' => 5],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Zebra Dove', 'scientific_name' => 'Geopelia Striata', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S7', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Martines', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut munia', 'scientific_name' => 'Estrildid finches', 'recorded_count' => 2],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Pied fantail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 3],
            ['transaction_code' => 'R2-FNSP-BMS-T2', 'station_code' => 'R2-FNSP-BMS-T2-S8', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => 'Hypsipetes philipinus', 'recorded_count' => 2],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            FuyotObservation::create($observation);
        }

        $this->command->info('Fuyot observations seeded successfully!');
    }
}
