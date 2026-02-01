<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class MangaSeeder extends Seeder
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
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Black napped oriole', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue tailed bee eater', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown Breasted Kingfisher', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Green Necked Pigeon', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Malkoha', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Phil Coucal', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red Kelled Flowerpecker', 'scientific_name' => '', 'recorded_count' => 2],

            // 2024 Fauna
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Common emerald dove', 'scientific_name' => 'Chalcophaps indica', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 20],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Fan tail', 'scientific_name' => 'Rhipidura', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Green back whistler', 'scientific_name' => 'Pachycephala albiventris', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Brown shrike', 'scientific_name' => 'Lanius cristatus', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blue throated bee eater', 'scientific_name' => 'Merops viridis', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Purple throated sunbird', 'scientific_name' => 'Leptocoma sperata', 'recorded_count' => 15],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pygmy swiflet', 'scientific_name' => 'Collocalia troglodytes', 'recorded_count' => 18],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine dwarf kingfisher', 'scientific_name' => 'Ceyx melanurus', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Yellow vented bulbul', 'scientific_name' => 'Pycnonotus goiavier', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Shrike', 'scientific_name' => '', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Sunbird', 'scientific_name' => '', 'recorded_count' => 15],

            // 2023 Fauna
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Fan tail', 'scientific_name' => 'Rhipidura', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Cuckoo Dove', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Eurasian tree sparrow', 'scientific_name' => 'Passer montanus', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Green back whistler', 'scientific_name' => 'Pachycephala albiventris', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pygmy swiflet', 'scientific_name' => 'Collocalia troglodytes', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Red jungle fowl', 'scientific_name' => 'Gallus gallus', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Shrike', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Dove', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Wood Kingfisher', 'scientific_name' => '', 'recorded_count' => 9],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Turtle Dove', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2023, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 5],

            // 2022 Fauna
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Blackfaced munia', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Philippine bulbul', 'scientific_name' => 'Hypsipetes philippinus', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Pygmy swiflet', 'scientific_name' => 'Collocalia troglodytes', 'recorded_count' => 5],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'Spotted Wood Kingfisher', 'scientific_name' => '', 'recorded_count' => 2],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2022, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 2],

            // 2021 Fauna
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2021, 'patrol_semester' => 1, 'bio_group' => 'fauna', 'common_name' => 'White eared brown dove', 'scientific_name' => 'Phapitreron leucotis', 'recorded_count' => 4],

            // 2025 Flora
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Alim', 'scientific_name' => '', 'recorded_count' => 5],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientalis', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anchoan dilaw', 'scientific_name' => 'Cassia spectabilis', 'recorded_count' => 7],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Cacao', 'scientific_name' => 'Theobroma cacao', 'recorded_count' => 36],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 3],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Hauili', 'scientific_name' => '', 'recorded_count' => 6],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Ipil-ipil', 'scientific_name' => '', 'recorded_count' => 15],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kakawate', 'scientific_name' => '', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalumpit', 'scientific_name' => 'Terminalia microcarpa', 'recorded_count' => 11],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus micranthus', 'recorded_count' => 1],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Katmon', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => '', 'recorded_count' => 3],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Mango', 'scientific_name' => '', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 5],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Niyog-niyogan', 'scientific_name' => 'Combretum indicum', 'recorded_count' => 15],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Philippine teak', 'scientific_name' => '', 'recorded_count' => 20],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2025, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => 'Harpullia arborea', 'recorded_count' => 10],

            // 2024 Flora
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anchoan dilaw', 'scientific_name' => 'Cassia spectabilis', 'recorded_count' => 7],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Anabiong', 'scientific_name' => 'Trema orientalis', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Binunga', 'scientific_name' => 'Macaranga tanarius', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Cacao', 'scientific_name' => 'Theobroma cacao', 'recorded_count' => 36],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Dita', 'scientific_name' => 'Alstonia scholaris', 'recorded_count' => 1],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kamiring', 'scientific_name' => 'Semecarpus micranthus', 'recorded_count' => 1],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Kalumpit', 'scientific_name' => 'Terminalia microcarpa', 'recorded_count' => 11],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Madre de cacao', 'scientific_name' => 'Gliricidia sepium', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Manga', 'scientific_name' => 'Magifera indica', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Malapapaya', 'scientific_name' => 'Polyscias nodosa', 'recorded_count' => 1],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Molave', 'scientific_name' => 'Vitex parviflora', 'recorded_count' => 5],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Narra', 'scientific_name' => 'Pterocarpus indicus', 'recorded_count' => 8],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Niyog-niyogan', 'scientific_name' => 'Combretum indicum', 'recorded_count' => 15],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Uas', 'scientific_name' => 'Harpullia arborea', 'recorded_count' => 10],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Tuai', 'scientific_name' => 'Bischofia javanica', 'recorded_count' => 4],
            ['transaction_code' => 'MANGA', 'station_code' => 'MANGA-S1', 'patrol_year' => 2024, 'patrol_semester' => 1, 'bio_group' => 'flora', 'common_name' => 'Yemana/Gmelina', 'scientific_name' => 'Gmelina arborea', 'recorded_count' => 15],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            
            // Create a model instance to insert into manga_tbl
            \DB::table('manga_tbl')->insert($observation);
        }

        $this->command->info('Manga species observations seeded successfully!');
    }
}
