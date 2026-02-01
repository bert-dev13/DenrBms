<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DupaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to prevent duplicates
        DB::table('dupax_tbl')->truncate();
        
        // Get the protected area ID for Dupax Watershed Forest Reserve
        $protectedArea = DB::table('protected_areas')->where('name', 'LIKE', '%DUPAX%')->first();
        $protectedAreaId = $protectedArea ? $protectedArea->id : 1;

        $records = [
            // Fauna records
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'fauna', 'Asian millipede', 'Trigoniulus corallinus', 8],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'fauna', 'Emerald Tree Skink', 'Lamprolepis smaragdina', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'fauna', 'Red Jungle Fowl', 'Gallus gallus', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'fauna', 'Philippine nightjar', 'Caprimulgus manillensis', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'fauna', 'Crested Serpent Eagle', 'Spilornis cheela', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'fauna', 'Philippine marbled mantis', 'Hierodula parviceps', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S3', 2024, 2, 'fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 7],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S3', 2024, 2, 'fauna', 'Bicolored flowerpecker', 'Dicaeum bicolor', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Pale Green Awlet Butterfly', 'Bibasis gomata', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Bicolored flowerpecker', 'Dicaeum bicolor', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Asian millipede', 'Trigoniulus corallinus', 4],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'fauna', 'Chesnut Munia', 'Lonchura atricapilla', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'fauna', 'Philippine Bulbul', 'Rhipidura cyaniceps', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'fauna', 'Blue Headed Fantail', 'Rhipidura cyaniceps', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'fauna', 'Asian millipede', 'Trigoniulus corallinus', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'fauna', 'Green Crested Lizard', 'Bronchocela cristatella', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'fauna', 'Philippine Bulbul', 'Rhipidura cyaniceps', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'fauna', 'Amethyst Brown Dove', 'Phapitreron amethystinus', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'fauna', 'Fresh Water Crab', 'Geothelphusa dehaani', 5],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'fauna', 'Terrestrial Snail', 'Heterobranchia', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S8', 2024, 2, 'fauna', 'Chesnut Munia', 'Lonchura atricapilla', 6],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S8', 2024, 2, 'fauna', 'Tawny grassbird', 'Megalurus timoriensis', 2],
            
            // Flora records
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'flora', 'Benguet Pine', 'Pinus kesiya', 6],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S1', 2024, 2, 'flora', 'Teak', 'Tectona grandis', 11],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S2', 2024, 2, 'flora', 'Teak', 'Tectona grandis', 7],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S3', 2024, 2, 'flora', 'Benguet Pine', 'Pinus kesiya', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S3', 2024, 2, 'flora', 'Teak', 'Tectona grandis', 10],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'flora', 'Benguet Pine', 'Pinus kesiya', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S4', 2024, 2, 'flora', 'Teak', 'Tectona grandis', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Bagna', 'Terminalia catappa L.', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Lumbang', 'Aleurites moluccanus', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Piling Laiitan', 'Canarium luzonicum', 8],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Teak', 'Tectona grandis', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Banaba', 'Lagerstroemia speciosa', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S5', 2024, 2, 'flora', 'Kalomata', 'Clausena anisum-olens', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Narra', 'Pterocarpus indicus', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Benguet Pine', 'Pinus kesiya', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Mahogany', 'Swietenia', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Pagsahingin', 'Canarium luzonicum', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Kalomata', 'Clausena anisum-olens', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S6', 2024, 2, 'flora', 'Katmon', 'Dillenia philippinensis', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'flora', 'Narra', 'Pterocarpus indicus', 4],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'flora', 'Pagsahingin', 'Canarium luzonicum', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'flora', 'Antipolo', 'Artocarpus blancoi', 2],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S7', 2024, 2, 'flora', 'Antsoan Dilau', 'Cassia spectabilis', 6],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S8', 2024, 2, 'flora', 'Banaba', 'Lagerstroemia speciosa', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S8', 2024, 2, 'flora', 'Malapapaya', 'Polyscias nodosa', 3],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S8', 2024, 2, 'flora', 'Antipolo', 'Artocarpus blancoi', 1],
            ['R2-DWFR-BMS-T1', 'R2-DWFR-BMS-T1-S9', 2024, 2, 'flora', 'Grassland', 'none', 0],
        ];

        foreach ($records as $record) {
            DB::table('dupax_tbl')->insert([
                'protected_area_id' => $protectedAreaId,
                'transaction_code' => $record[0],
                'station_code' => $record[1],
                'patrol_year' => $record[2],
                'patrol_semester' => $record[3],
                'bio_group' => $record[4],
                'common_name' => $record[5],
                'scientific_name' => $record[6],
                'recorded_count' => $record[7],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
