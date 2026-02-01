<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarianoObservation;
use App\Models\ProtectedArea;

class MarianoObservationSeeder extends Seeder
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
            // 1st Semester Data
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientale)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Antipolo', 'scientific_name' => 'Artocarpus blancoi)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bamboo', 'scientific_name' => 'Bambusa)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyros pilosanthera Blanco var. pilosanthera)', 'recorded_count' => 10],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bignay-pugo', 'scientific_name' => 'Antidesma ghaesembilla)', 'recorded_count' => 7],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius)', 'recorded_count' => 7],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dao', 'scientific_name' => 'Dracontomelon dao)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => 'Ficus septica)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Himbabao', 'scientific_name' => 'Broussonetia luzonica)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Jade Vine', 'scientific_name' => 'Strongylodon elmeri)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamagong', 'scientific_name' => 'Diospyros discolor)', 'recorded_count' => 9],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kahoy Dalaga', 'scientific_name' => 'Mussaenda philippica)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalantas', 'scientific_name' => 'Toona calantas)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => 'Dillenia philippinensis)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ligas', 'scientific_name' => 'Semecarpus cuneiformis)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mayapis', 'scientific_name' => 'Shorea palosapis)', 'recorded_count' => 10],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => 'Polyscias nodosa)', 'recorded_count' => 9],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus)', 'recorded_count' => 11],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Palosapis', 'scientific_name' => 'Anisoptera thurifera)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S4', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Philippine Teak', 'scientific_name' => 'Tectona philippinensis)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S5', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ratan', 'scientific_name' => 'Calamus rotang)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S5', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Sablot', 'scientific_name' => 'Litsea glutinosa)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S6', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tangisang bayawak', 'scientific_name' => 'Ficus variegata Blume)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S7', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tanguile', 'scientific_name' => 'Shorea polysperma)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S8', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus nota)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S9', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea contorta)', 'recorded_count' => 4],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S9', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Yakal', 'scientific_name' => 'Shorea astylosa)', 'recorded_count' => 4],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 1],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Civet cat', 'scientific_name' => 'Paradoxurus philippinensis', 'recorded_count' => 1],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Wild boar', 'scientific_name' => 'Sus scrofa', 'recorded_count' => 1],

            // 2nd Semester Data
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientale)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Antipolo', 'scientific_name' => 'Artocarpus blancoi)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Apitong', 'scientific_name' => 'Dipterocarpus grandiflorus)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Bamboo', 'scientific_name' => 'Bambusa)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Bolong-eta', 'scientific_name' => 'Diospyros pilosanthera Blanco var. pilosanthera)', 'recorded_count' => 10],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Bignay-pugo', 'scientific_name' => 'Antidesma ghaesembilla)', 'recorded_count' => 7],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius)', 'recorded_count' => 7],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Dao', 'scientific_name' => 'Dracontomelon dao)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => 'Ficus septica)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Himbabao', 'scientific_name' => 'Broussonetia luzonica)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Jade Vine', 'scientific_name' => 'Strongylodon elmeri)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Kamagong', 'scientific_name' => 'Diospyros discolor)', 'recorded_count' => 9],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Kahoy Dalaga', 'scientific_name' => 'Mussaenda philippica)', 'recorded_count' => 5],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Kalantas', 'scientific_name' => 'Toona calantas)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => 'Dillenia philippinensis)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Ligas', 'scientific_name' => 'Semecarpus cuneiformis)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Mayapis', 'scientific_name' => 'Shorea palosapis)', 'recorded_count' => 10],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => 'Polyscias nodosa)', 'recorded_count' => 9],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus)', 'recorded_count' => 11],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Palosapis', 'scientific_name' => 'Anisoptera thurifera)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S4', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Philippine Teak', 'scientific_name' => 'Tectona philippinensis)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S5', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Ratan', 'scientific_name' => 'Calamus rotang)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S5', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Sablot', 'scientific_name' => 'Litsea glutinosa)', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S6', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Tangisang bayawak', 'scientific_name' => 'Ficus variegata Blume)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S7', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Tanguile', 'scientific_name' => 'Shorea polysperma)', 'recorded_count' => 8],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S8', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus nota)', 'recorded_count' => 6],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S9', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'White Lauan', 'scientific_name' => 'Shorea contorta)', 'recorded_count' => 4],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S9', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'flora', 'common_name' => 'Yakal', 'scientific_name' => 'Shorea astylosa)', 'recorded_count' => 4],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 4],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Civet cat', 'scientific_name' => 'Paradoxurus philippinensis', 'recorded_count' => 2],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 3],
            ['transaction_code' => 'R2-MPL-BMS-T1', 'station_code' => 'R2-MPL-BMS-T - S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Wild boar', 'scientific_name' => 'Sus scrofa', 'recorded_count' => 2],
        ];

        foreach ($observations as $observation) {
            MarianoObservation::create(array_merge($observation, [
                'protected_area_id' => $protectedArea->id,
            ]));
        }
    }
}
