<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MadreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the protected area ID for Northern Sierra Madre Natural Park
        $protectedArea = DB::table('protected_areas')->where('code', 'NSMNP')->first();
        $protectedAreaId = $protectedArea ? $protectedArea->id : 1;

        // Format per record:
        // [transaction_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', ''],
            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'aauna', 'Philippine Bulbul', 'Hypsipetes philippinus', ''],

            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 10],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Flower Pecker', 'Prionochilus olivaceus', ''],
            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'Fauna', 'Flower Pecker', 'Prionochilus olivaceus', ''],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Flower Pecker', 'Prionochilus olivaceus', 6],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Philippine Coucal', 'Centropus viridis', ''],
            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'Fauna', 'Philippine Coucal', 'Centropus viridis', ''],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 3],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Colasisi', 'Loriculus philippinensis', ''],
            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'Fauna', 'Colasisi', 'Loriculus philippinensis', ''],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Colasisi', 'Loriculus philippinensis', 3],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Luzon Bleeding Heart', 'Gollicolumba luzonica', ''],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Philippine Tailorbird', 'Orthotomus castaneiceps', ''],
            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'Fauna', 'Philippine Tailorbird', 'Orthotomus castaneiceps', ''],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Crested Serpent Eagle', 'Spilornis Cheela', ''],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Crested Serpent Eagle', 'Spilornis Cheela', 1],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Yellow Breasted Fruit-Dove', 'Ptinilopus occipitalis', ''],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Yellow Breasted Fruit-Dove', 'Ptinilopus occipitalis', 1],

            ['R2-NSMNP-BMS-T1', 2021, '1st', 'Fauna', 'Black Naped Oriole', 'Oriolus chinensis', ''],

            ['R2-NSMNP-BMS-T1', 2021, '2nd', 'Fauna', 'Flamed Breasted Fruit Dove', 'Ptinilopus Marchei', ''],

            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Luzon Bush Warbler', 'Cettia seebohmi', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Amethyst Brown Dove', 'Phapitreron amethystine', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Common Emerald Dove', 'Chalcophaps indica', 2],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Philippine Hawk Eagle', 'Spizactus cirrhatus', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Philippine Dwarf King Fisher', 'Ceyx melandrus', 3],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Wild Duck', 'Anas luzonica', 2],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Yellow Vented Bulbul', 'Pycnonotus goiavier', 2],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Rufous-Crowned Bee Eater', 'Merops philippinus', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Spotted Dove', 'Streptopelia Chinensis', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'Little Egret', 'Egreta garzetta', 1],
            ['R2-NSMNP-BMS-T1', 2022, '1st', 'Fauna', 'House swift', 'Appus affinis', 2],

            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 12],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 4],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Crimson Sunbird', '', 13],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Colasisi', 'Loriculus philippinensis', 8],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Common Emerald Dove', 'Chalcophaps indica', 20],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Amethyst Brown Dove', 'Phapitreron amethystine', 3],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Philippine Swiftlets', '', 9],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Wild Duck', 'Anas luzonica', 2],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Purple Throated Sunbird', '', 1],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Flower Pecker', 'Prionochilus olivaceus', 2],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Philippine Tailorbird', 'Orthotomus castaneiceps', 1],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'White Collared King Fisher', '', 1],
            ['R2-NSMNP-BMS-T1', 2023, '2nd', 'Fauna', 'Red-keeled Flowerpecker', '', 1],

            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Philippine coucal', 'Centropus viridis', 4],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Colasisi', 'Loriculus philippinensis', 1],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Lovely sunbird', 'Aethopyga shelleyi', 3],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinensis', 2],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Blackish Cuckoo shrike', 'Coracina coerulescens', 3],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Crimson sunbird', 'Aethopyga siparaja', 2],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Philippine tailorbird', 'Orthotomus castaneiceps', 1],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Coleto', 'Sarcops calvus', 1],
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 10],

            // Duplicate row in your data (kept as-is)
            ['R2-NSMNP-BMS-T1', 2024, '1st', 'Fauna', 'Philippine coucal', 'Centropus viridis', 4],

            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Colasisi', 'Loriculus philippinensis', 1],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Lovely sunbird', 'Aethopyga shelleyi', 3],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 2],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Blackish Cuckoo shrike', 'Coracina coerulescens', 3],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Crimson sunbird', 'Aethopyga siparaja', 2],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Philippine tailorbird', 'Orthotomus castaneiceps', 1],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Coleto', 'Sarcops calvus', 1],
            ['R2-NSMNP-BMS-T1', 2024, '2nd', 'Fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 10],

            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'Amethyst brown dove', 'Phapitreron amethystinus', 2],
            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'Common emerald dove', 'Chalcophaps indica', 3],
            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'Olive backed flowerpecker', 'Pachyglossa olivacea', 8],
            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'Philippine tailorbird', 'Orthotomus castaneiceps', 3],
            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'Philippine bulbul', 'Hypsipetes philippinus', 3],
            ['R2-NSMNP-BMS-T1', 2025, '1st', 'Fauna', 'white-browed shamah', 'Copsychus luzoniensis', 2],

            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'Amethyst brown dove', 'Phapitreron amethystinus', 2],
            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'Common emerald dove', 'Chalcophaps indica', 3],
            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'Olive backed flowerpecker', 'Pachyglossa olivacea', 8],
            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'Philippine tailorbird', 'Orthotomus castaneiceps', 3],
            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'Philippine bulbul', 'Hypsipetes philippinus', 3],
            ['R2-NSMNP-BMS-T1', 2025, '2nd', 'Fauna', 'white-browed shamah', 'Copsychus luzoniensis', 2],

            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 5],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Gray-Throated Sunbird', 'Anthreptes griseigularis', 2],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Lovely sunbird', 'Aethopyga shelleyi', 4],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'paradise flycatcher', 'Terpsiphone', 1],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Philippine bulbul', 'Hypsipetes philippinus', 14],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Philippine Dove', 'Streptopelia dusumieri', 1],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Philippine tailorbird', 'Orthotomus castaneiceps', 4],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Red crested malkoha', 'Phaenicophaeus superciliosus', 1],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Rufous Paradise', 'Terpsiphone cinnamomea', 2],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'Tarictic Hornbill', 'Penelopides panini', 1],
            ['R2-NSMNP-BMS-T2', 2025, '2nd', 'Fauna', 'white-browed shamah', 'Copsychus luzoniensis', 5],
        ];

        foreach ($records as $record) {
            DB::table('madre_tbl')->insert([
                'protected_area_id' => $protectedAreaId,
                'transaction_code' => $record[0],
                'station_code' => $record[0], // kept as-is from your code
                'patrol_year' => $record[1],
                'patrol_semester' => $record[2] === '1st' ? 1 : 2,
                'bio_group' => strtolower($record[3]) === 'aauna' ? 'fauna' : strtolower($record[3]),
                'common_name' => trim($record[4]),
                'scientific_name' => $record[5] ?: null,
                'recorded_count' => $record[6] ?: 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
