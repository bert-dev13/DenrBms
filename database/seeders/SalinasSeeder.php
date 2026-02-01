<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the protected area ID for Salinas Natural Monument
        $protectedArea = DB::table('protected_areas')->where('code', 'SNM')->first();
        $protectedAreaId = $protectedArea ? $protectedArea->id : 1;

        // Format per record:
        // [transaction_code, station_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Large-billed Crow', 'Corvus macrorhyncos', 4],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Giant Land Snail', 'Lissachatina fulica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Cicada', 'Cicadoidea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Brahminy Kite', 'Haliastur indus', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 10],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Cicada', 'Cicadoidea', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Chesnut Munia', 'Lonchura atricapilla', 9],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 11],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Brown Tussock Moth', 'Olene mendosa', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Common Kingfisher', 'Alcedo Atthis', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Pygmy Swiftlet', 'Collocalia troglodytes', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Cicada', 'Cicadoidea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Cicada', 'Cicadoidea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Cicada', 'Cicadoidea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Fauna', 'Philippine Coucal', 'Centropus viridis', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Mahogany', 'Swietenia', 17],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Antipolo', 'Artocarpus blancoi', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Pagsahingin', 'Canarium luzonicum', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Mahogany', 'Swietenia', 8],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Teak', 'Tectona grandis', 8],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Molave', 'Vitex parviflora', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Salisi', 'Ficus benjamina', 3],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Takip Asin', 'Macaranga grandifolia', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Mango', 'Mangifera', 5],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Takip Asin', 'Macaranga grandifolia', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Mango', 'Mangifera', 6],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Benguet Pine', 'Pinus kesiya', 9],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Benguet Pine', 'Pinus kesiya', 20],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Yemane', 'Gmelina arborea', 2],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Hauli', 'Ficus septica', 1],
            ['R2-SNM-BMS-T1', 'R2-SNM-BMS-T1-S9', 2024, '2nd Semester', 'Flora', 'Benguet Pine', 'Pinus kesiya', 2],
        ];

        foreach ($records as $record) {
            DB::table('salinas_tbl')->insert([
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
