<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TumauiniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the protected area ID for Tumauini Watershed Natural Park
        $protectedArea = DB::table('protected_areas')->where('code', 'TWNP')->first();
        $protectedAreaId = $protectedArea ? $protectedArea->id : 1;

        // Format per record:
        // [transaction_code, station_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S1', 2024, '2nd Semester', 'Fauna', 'Philippine bulbul, Phil. Coucal,Yellow vented bulbul, Swiftlet', 'Hypsipetes philippinus, Pycnonotus goiavier, Cypsiurus balasiensis', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S2', 2024, '2nd Semester', 'Fauna', 'Emerald Dove, Crested Mania, Bee eater', 'Chalcophaps indica, Acridotheres cristatellus, Merops apiaster', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S3', 2024, '2nd Semester', 'Fauna', 'swiflet, sun bird', 'Cypsiurus balasiensis, Cinnyris jugularis', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S4', 2024, '2nd Semester', 'Fauna', 'none', '', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S5', 2024, '2nd Semester', 'Fauna', 'Philippine duck', 'Anas luzonica', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S6', 2024, '2nd Semester', 'Fauna', 'Sun bird', 'Cinnyris jugularis', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S7', 2024, '2nd Semester', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S8', 2024, '2nd Semester', 'Fauna', 'Egret', 'Ardea alba', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S9', 2024, '2nd Semester', 'Fauna', 'Bee eater', 'Merops apiaster', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S10', 2024, '2nd Semester', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S11', 2024, '2nd Semester', 'Fauna', 'none', '', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S12', 2024, '2nd Semester', 'Fauna', 'Emerald Dove', 'Chalcophaps indica', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S13', 2024, '2nd Semester', 'Fauna', 'Bee eater', 'Merops apiaster', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S14', 2024, '2nd Semester', 'Fauna', 'Philippine Bulbul', 'Hypsipetes philippinus', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S15', 2024, '2nd Semester', 'Fauna', 'Warty pig', 'Sus philippensis', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S16', 2024, '2nd Semester', 'Fauna', 'None', '', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S17', 2024, '2nd Semester', 'Fauna', 'None', '', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S18', 2024, '2nd Semester', 'Fauna', 'Yellow vented bulbul', 'Pycnonotus goiavier', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S19', 2024, '2nd Semester', 'Fauna', 'sunbird', 'Cinnyris jugularis', 1],
            ['R2-TWNP-BMS-T1', 'R2-BPLS-BMS-T1-S20', 2024, '2nd Semester', 'Fauna', 'swiflet', 'Cypsiurus balasiensis', 1],
        ];

        foreach ($records as $record) {
            DB::table('tumauini_tbl')->insert([
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
