<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the protected area ID for BANGAN HILL NATIONAL PARK (BHNP)
        $protectedArea = DB::table('protected_areas')->where('code', 'BHNP')->first();
        $protectedAreaId = $protectedArea ? $protectedArea->id : 1;

        // Format per record:
        // [transaction_code, station_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Narra', 'Pterocarpus indicus', 7],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Molave', 'Vitex parviflora', 4],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Bugauak', 'Clerodendrum quadriloculare (Blanco) Merr.', 8],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Flora', 'Banato', 'Mallotus philippensis (Lam.)', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Bugauak', 'Clerodendrum quadriloculare (Blanco) Merr.', 10],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Binunga', 'Macaranga tanarius', 4],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S2', 2024, '2nd Semester', 'Flora', 'Narra', 'Pterocarpus indicus', 2],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Narra', 'Pterocarpus indicus', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Dita', 'Alstonia scholaris', 2],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S3', 2024, '2nd Semester', 'Flora', 'Binunga', 'Macaranga tanarius', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 13],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Binunga', 'Macaranga tanarius', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Kahoy Dalaga', 'Mussaenda philippica', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Flora', 'Bugauak', 'Clerodendrum quadriloculare (Blanco) Merr.', 10],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Binunga', 'Macaranga tanarius', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Alim', 'Melanolepis multiglandulosa', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Narra', 'Pterocarpus indicus', 10],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Flora', 'Molave', 'Vitex parviflora', 1],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'White Lauan', 'Shorea contorta', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Banato', 'Mallotus philippensis (Lam.)', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S6', 2024, '2nd Semester', 'Flora', 'Tindalo', 'Afzelia rhomboidea', 1],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Kahoy Dalaga', 'Mussaenda philippica', 4],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Anabiong', 'Trema orientalis', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'White Lauan', 'Shorea contorta', 2],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Flora', 'Bugauak', 'Clerodendrum quadriloculare (Blanco) Merr.', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Banato', 'Mallotus philippensis (Lam.)', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Bugauak', 'Clerodendrum quadriloculare (Blanco) Merr.', 3],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Narra', 'Pterocarpus indicus', 2],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Flora', 'Bignai Pugo', 'Antidesma pentandrum', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Olive-backed Sunbird', 'Cinnyris jugularis', 10],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 22],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Philippine Swiftlet', 'Aerodramus mearnsi', 8],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 8],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Brown Shrike', 'Lanius cristatus', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Eurasian tree Sparrow', 'Passer montanus', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'White-breasted wood swallow', 'Artamus leucorynchus', 7],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'Philippine Pygmy Swiftlet', 'Collocalia troglodytes', 6],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Barred buttonquail', 'Turnix suscitator', 4],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 4],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Barred buttonquail', 'Turnix suscitator', 2],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 10],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Olive-backed Sunbird', 'Cinnyris jugularis', 5],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Eurasian tree Sparrow', 'Passer montanus', 15],
            ['R2-BHNP-BMS-T1', 'R2-BHNP-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Yellow-vented Bulbul', 'Pycnonotus goiavier', 6],
        ];

        foreach ($records as $record) {
            DB::table('bangan_tbl')->insert([
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
