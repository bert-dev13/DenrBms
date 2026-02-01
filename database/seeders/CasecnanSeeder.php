<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CasecnanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the protected area ID for CASECNAN PROTECTED LANDSCAPE (CPL)
        $protectedArea = DB::table('protected_areas')->where('code', 'CPL')->first();
        if (!$protectedArea) {
            // Insert the protected area if it doesn't exist
            $protectedAreaId = DB::table('protected_areas')->insertGetId([
                'name' => 'CASECNAN PROTECTED LANDSCAPE',
                'code' => 'CPL',
                'description' => 'Casecnan Protected Landscape (CPL)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $protectedAreaId = $protectedArea->id;
        }

        // Format per record:
        // [transaction_code, station_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            // Flora records
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 7],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 6],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Yemane', 'Gmelina arborea', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Tibig', 'Ficus nota', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 6],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Ligas', 'Semecarpus cuneiformis', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Tibig', 'Ficus nota', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 7],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Tangisang Bayawak', 'Ficus variegata', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Tibig', 'Ficus nota', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Palo santo', 'Bursera graveolens', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Tibig', 'Ficus nota', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Ligas', 'Semecarpus cuneiformis', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Tsaang Gubat', 'Carmona heterophylla Cav.', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Kalumpit', 'Terminalia microcarpa', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Tsaang gubat', 'Carmona heterophylla Cav.', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Hauli', 'Ficus septica', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Tanguile', 'Shorea polysperma', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Tanguile', 'Shorea polysperma', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 6],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Tangisang Bayawak', 'Ficus variegata', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Ligas', 'Semecarpus cuneiformis', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Tangisang Bayawak', 'Ficus variegata', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Hauili', 'Ficus septica', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Ligas', 'Semecarpus cuneiformis', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Tuai', 'Bischofia javanica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Auri', 'Acacia auriculiformis', 2],

            // Fauna records
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Monitor Lizard', 'Varanus', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Brown Rat Snake', 'Ptyas fusca', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Red Jungle Fowl', 'Gallus gallus', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 18],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Brahminy Kite', 'Haliastur indus', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Grey-Faced Buzzard', 'Butastur indicus', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Limenitis Arthemis', 'Limenitis Arthemis', 8],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Chestnut Munia', 'Lonchura atricapilla', 23],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Spotted Buttonquail', 'Turnix ocellatus', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Large Billed Crow', 'Corvus macrorhynchos', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 14],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Chestnut Munia', 'Lonchura atricapilla', 21],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Philippine Pied Fantail', 'Rhipidura nigritorquis', 13],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Cicada Beetle', 'Cicadoidea', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Common Garden Skink', 'Lampropholis guichenoti', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Egret', 'Ardea alba', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Crested Myna', 'Acridotheres cristatellus', 20],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Brown Shrike', 'Lanius cristatus', 14],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Monitor Lizard', 'Varanus', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Brahminy Kite', 'Haliastur indus', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Rufuos Hornbill', 'Buceros hydrocorax', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Spotted Buttonquail', 'Turnix ocellatus', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Luzon Hornbill', 'Penelopides manillae', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Cicada Beetle', 'Cicadoidea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Brown Shrike', 'Lanius cristatus', 13],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Red Jungle Fowl', 'Gallus gallus', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Egret', 'Ardea alba', 8],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 36],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Chestnut Munia', 'Lonchura atricapilla', 14],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Common Garden Skink', 'Lampropholis guichenoti', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Striated Grass Bird', 'Megalurus palustris', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Large Billed Crow', 'Corvus macrorhynchos', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Common Kingfisher', 'Alcedo Atthis', 6],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Grey-Faced Buzzard', 'Butastur indicus', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Crested Myna', 'Acridotheres cristatellus', 17],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Eurasian Tree Sparrow', 'Passer montanus', 15],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Rufous Coucal', 'Centropus unirufus', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Rock pigeon', 'Columba livia', 10],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Fauna', 'Rufous Hornbill', 'Buceros hydrocorax', 3],
        ];

        foreach ($records as $record) {
            DB::table('casecnan_tbl')->insert([
                'protected_area_id' => $protectedAreaId,
                'transaction_code' => $record[0],
                'station_code' => $record[1],
                'patrol_year' => $record[2],
                'patrol_semester' => $record[3] === '1st Semester' ? 1 : 2,
                'bio_group' => strtolower($record[4]),
                'common_name' => trim($record[5]),
                'scientific_name' => $record[6] ?: null,
                'recorded_count' => $record[7] ?: 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
