<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class ToyotaSeeder extends Seeder
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
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue-throated bee-eater', 'scientific_name' => 'Merops viridis', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow Wattled Bulbul', 'scientific_name' => 'Pcycnonotus urostictus', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red killed flowerpecker', 'scientific_name' => 'Dicaeum austral', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine Magpie robin', 'scientific_name' => 'Copsycgus mindanensis', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pied bushchat', 'scientific_name' => 'Saxicola caprata', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Braminy Kite', 'scientific_name' => 'Haliastur indus', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Coppersmith barbets', 'scientific_name' => 'Megalaima haemacephala', 'recorded_count' => 3],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Shrike', 'scientific_name' => 'lanius cristatus', 'recorded_count' => 7],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Dove', 'scientific_name' => 'Spilopelia Chinensis', 'recorded_count' => 3],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow vented bulbul', 'scientific_name' => 'Pycnonotus goiavier', 'recorded_count' => 8],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Crested myna/Martinez', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 15],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 6],

            // 2024 Fauna
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Shrike', 'scientific_name' => 'lanius cristatus', 'recorded_count' => 7],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Coppersmith barbets', 'scientific_name' => 'Megalaima haemacephala', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Zebra dove', 'scientific_name' => 'Geopelia striata', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red collared dove', 'scientific_name' => 'Streptopelia tranquebarica', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow vented bulbul', 'scientific_name' => 'Pycnonotus goiavier', 'recorded_count' => 8],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Purple throated sunbird', 'scientific_name' => 'Leptocoma sperata', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown shrike', 'scientific_name' => 'Lanius cristatus', 'recorded_count' => 7],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine coucal', 'scientific_name' => 'Centropus viridis', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Crested myna/Martinez', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Plain martin', 'scientific_name' => 'Riparia paludicola', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Tawny grassbird', 'scientific_name' => 'Megalurus timoriensis', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 15],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 2],

            // 2023 Fauna
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Shrike', 'scientific_name' => 'lanius cristatus', 'recorded_count' => 6],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Crested myna/Martinez', 'scientific_name' => 'Acridotheres cristatellus', 'recorded_count' => 6],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Dove', 'scientific_name' => 'Streptopelia Chinensis', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White-eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 4],

            // 2022 Fauna
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow vented bulbul', 'scientific_name' => 'Pycnonotus goiavier', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Wood Kingfisher', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black naped oriole', 'scientific_name' => 'Oriolus chinensis', 'recorded_count' => 4],

            // 2025 Flora
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alibangbang', 'scientific_name' => 'Bauhinia monandra', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => 'Melanolepsis multiglandulosa', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Banato', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bayag usa', 'scientific_name' => 'Voacanga globosa', 'recorded_count' => 7],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binayuyo', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'G-melina', 'scientific_name' => '', 'recorded_count' => 11],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-Ipil', 'scientific_name' => 'Leucaena leucocephala', 'recorded_count' => 20],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kakawate', 'scientific_name' => '', 'recorded_count' => 11],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalios', 'scientific_name' => '', 'recorded_count' => 14],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalumpit', 'scientific_name' => '', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus micranthus', 'recorded_count' => 3],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malatungaw', 'scientific_name' => 'Melastoma malabathricum', 'recorded_count' => 6],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Manga', 'scientific_name' => 'Magifera indica', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Rain tree', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tibig', 'scientific_name' => 'Ficus Nota', 'recorded_count' => 5],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 20],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => 'Harpullia arborea', 'recorded_count' => 15],

            // 2024 Flora
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalios', 'scientific_name' => 'Streblus asper', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-Ipil', 'scientific_name' => 'Leucaena leucocephala', 'recorded_count' => 20],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => 'Melanolepsis multiglandulosa', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Vulala', 'scientific_name' => 'Euphoria ramboutan-ake', 'recorded_count' => 0],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Yemane', 'scientific_name' => 'Gmelina arborea', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus micranthus', 'recorded_count' => 1],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Manga', 'scientific_name' => 'Magifera indica', 'recorded_count' => 8],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alibangbang', 'scientific_name' => 'Bauhinia monandra', 'recorded_count' => 10],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Arusit', 'scientific_name' => 'Acidoton virosus', 'recorded_count' => 0],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malatungaw', 'scientific_name' => 'Melastoma malabathricum', 'recorded_count' => 6],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Madre de cacao', 'scientific_name' => 'Gliricidia sepium', 'recorded_count' => 0],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 0],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Bayag usa', 'scientific_name' => 'Voacanga globosa', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Agandung', 'scientific_name' => 'Trema orientalis', 'recorded_count' => 0],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Wisak', 'scientific_name' => 'Neonauclea media', 'recorded_count' => 15],

            // Additional Fauna records
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2021, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Colasisi', 'scientific_name' => '', 'recorded_count' => 19],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Buff banded rail', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown Dove', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Bee Eater', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Maya', 'scientific_name' => '', 'recorded_count' => 8],
            ['transaction_code' => 'TOYOTA', 'station_code' => 'TOYOTA-S1', 'patrol_year' => 2021, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Guaia bero', 'scientific_name' => '', 'recorded_count' => 1],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            
            // Create a model instance to insert into toyota_tbl
            \DB::table('toyota_tbl')->insert($observation);
        }

        $this->command->info('Toyota species observations seeded successfully!');
    }
}
