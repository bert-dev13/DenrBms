<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class QuibalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'PPLS')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area PPLS not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            // 2025 Fauna
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Balicassiao', 'scientific_name' => '', 'recorded_count' => 7],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black chin fruit Dove', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blackish Cuckoo shrike', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Braminy Kite', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Elegant Tit', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Emerald Dove', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Greater Coucal', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Nightjar', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Bulbul', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine gree pigeon', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pink neck green pigeon', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Purple throated bee eater', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Purple tjroated sun bird', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red keeled flowerpecker', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Rough Crested Malkoha', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Swiftlet', 'scientific_name' => '', 'recorded_count' => 10],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White Eared bron dove', 'scientific_name' => '', 'recorded_count' => 2],

            // 2025 Flora
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Agandung/Ligas', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Amamali', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Banato', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Batino', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binayuyu', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Emerald Tree/China Doll', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'G-Melina', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipip-Ipil', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalios', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalumpit', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring/Ligas', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kahoy-dalaga', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mabolo/Kamagong', 'scientific_name' => '', 'recorded_count' => 10],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Manga', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Manggachapui', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Pakak', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Palosanto', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Wisak', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'QUIBAL', 'station_code' => 'QUIBAL-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Yakal Dongonon', 'scientific_name' => '', 'recorded_count' => 5],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            
            // Create a model instance to insert into quibal_tbl
            \DB::table('quibal_tbl')->insert($observation);
        }

        $this->command->info('Quibal species observations seeded successfully!');
    }
}
