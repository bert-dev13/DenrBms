<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class SanRoqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to prevent duplicates
        \DB::table('roque_tbl')->truncate();
        
        $protectedArea = ProtectedArea::where('code', 'PPLS')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area PPLS not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            // 2025 Fauna
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped Oriole', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue naped Monarch', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue throated bee-eater', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brahminy kite', 'scientific_name' => 'Haliastur indus', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown Breasted kingfisher', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Collared Dove', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Koel', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pigmy swiflet', 'scientific_name' => 'Collocalia troglodytes', 'recorded_count' => 17],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 1],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Dove', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Straited Grassbird', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White earned brown dove', 'scientific_name' => '', 'recorded_count' => 7],

            // 2024 Fauna
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian Tree Sparrow', 'scientific_name' => '', 'recorded_count' => 10],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown shrike', 'scientific_name' => 'Lanius cristatus', 'recorded_count' => 12],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow bellied whistler', 'scientific_name' => 'Pachycephala philippinensis', 'recorded_count' => 7],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 1],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Purple throated sunbird', 'scientific_name' => 'Leptocoma sperata', 'recorded_count' => 10],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pigmy swiflet', 'scientific_name' => 'Collocalia troglodytes', 'recorded_count' => 14],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine coucal', 'scientific_name' => 'Centropus viridis', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 10],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brahminy kite', 'scientific_name' => 'Haliastur indus', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Fan tail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 8],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Crested myna/Martinez', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 2],

            // 2023 Fauna
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue throated bee-eater', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Crested myna/Martinez', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Domesticated Chiken', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Fan tail', 'scientific_name' => 'Rhipidura nigritorquis', 'recorded_count' => 6],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Sunbird', 'scientific_name' => '', 'recorded_count' => 7],

            // 2022 Fauna
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brahminy kite', 'scientific_name' => 'Haliastur indus', 'recorded_count' => 3],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Chestnut Munia', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Goliath heron', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted wood kingfisher', 'scientific_name' => '', 'recorded_count' => 1],

            // 2021 Fauna
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2021, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => "Colasisi'", 'scientific_name' => '', 'recorded_count' => 26],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2021, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White earned brown dove', 'scientific_name' => '', 'recorded_count' => 11],

            // 2025 Flora
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Agandung/Ligas', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => 'Melanolepsis multiglandulosa', 'recorded_count' => 4],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Cacao', 'scientific_name' => 'Theobroma cacao', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Duhat', 'scientific_name' => 'Syzygium cumini', 'recorded_count' => 6],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'G-melina', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-ipil', 'scientific_name' => 'Leucaena leucocephala', 'recorded_count' => 15],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus philippinesis', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malatungaw', 'scientific_name' => 'Melastoma malabathricum', 'recorded_count' => 4],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mango', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 12],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 3],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Raintree', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus Nota', 'recorded_count' => 15],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 10],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => 'Harpullia arborea', 'recorded_count' => 10],

            // 2024 Flora
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-ipil', 'scientific_name' => 'Leucaena leucocephala', 'recorded_count' => 12],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Yemane', 'scientific_name' => 'Gmelina arborea', 'recorded_count' => 10],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => 'Polyscias nodosa', 'recorded_count' => 1],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => 'Melanolepsis multiglandulosa', 'recorded_count' => 4],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus philippinesis', 'recorded_count' => 2],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malatungaw', 'scientific_name' => 'Melastoma malabathricum', 'recorded_count' => 4],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Rain tree', 'scientific_name' => 'Samanea saman', 'recorded_count' => 6],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Duhat', 'scientific_name' => 'Syzygium cumini', 'recorded_count' => 5],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus Nota', 'recorded_count' => 15],
            ['transaction_code' => 'SANROQUE', 'station_code' => 'SANROQUE-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => 'Harpullia arborea', 'recorded_count' => 10],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            
            // Create a model instance to insert into roque_tbl
            \DB::table('roque_tbl')->insert($observation);
        }

        $this->command->info('San Roque species observations seeded successfully!');
    }
}
