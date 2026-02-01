<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DipaniongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the protected area ID for Dipaniong Natural Park
        $protectedArea = DB::table('protected_areas')->where('code', 'DNP')->first();
        if (!$protectedArea) {
            $protectedAreaId = DB::table('protected_areas')->insertGetId([
                'name' => 'DIPANIONG NATURAL PARK (DNP)',
                'code' => 'DNP',
                'location' => 'San Miguel, Echague, Isabela',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $protectedAreaId = $protectedArea->id;
        }

        // Format per record:
        // [transaction_code, patrol_year, patrol_semester, bio_group, common_name, scientific_name, recorded_count]
        $records = [
            // 2025 1st Semester Flora
            [null, 2025, '1st', 'Flora', 'Red Lauan', 'Shorea negrosensis', 40],
            [null, 2025, '1st', 'Flora', 'White Lauan', 'Shorea contorta', 35],
            [null, 2025, '1st', 'Flora', 'Apitong', 'Dipterocarpus grandiflorus', 21],
            [null, 2025, '1st', 'Flora', 'Mayapis', 'Shorea palosapis', 25],
            [null, 2025, '1st', 'Flora', 'Philippine Oak Tree/Ulayan (Manaring)', 'Lithocaspus celebicus', 4],
            [null, 2025, '1st', 'Flora', 'Lanete', 'Wrightia pubescens', 1],
            [null, 2025, '1st', 'Flora', 'Bagtikan', 'Parashorea malaanonan', 7],
            [null, 2025, '1st', 'Flora', 'Palosapis', 'Anisoptera thurifera', 5],
            [null, 2025, '1st', 'Flora', 'Tanguile', 'Shorea polysperma', 11],
            [null, 2025, '1st', 'Flora', 'Takip Asin', 'Macaranga grandifolia', 5],
            [null, 2025, '1st', 'Flora', 'Bikal', 'Dinochloa acutiflora', 10],
            [null, 2025, '1st', 'Flora', 'Rattan', 'Calamus rotang', 32],
            [null, 2025, '1st', 'Flora', 'Binunga', 'Macaranga tanarius', 5],
            [null, 2025, '1st', 'Flora', 'Jade Vine', 'Strongylodon macrobotrys', 2],
            [null, 2025, '1st', 'Flora', 'Buri Palm (Silag)', 'Corypha utan', 5],
            [null, 2025, '1st', 'Flora', 'Balinghasay', 'Buchanania arborescens', 14],
            [null, 2025, '1st', 'Flora', 'Pakpak Lawin', 'Drynaria quercifloia', 8],
            [null, 2025, '1st', 'Flora', 'Balete', 'Ficus benjamina', 1],
            [null, 2025, '1st', 'Flora', 'Niyog Niyogan', 'Ficus pseudopalma', 5],
            
            // 2025 1st Semester Fauna
            [null, 2025, '1st', 'Fauna', 'Golden- crowned babbler', 'Sterrhoptilus dennistouni', 2],
            [null, 2025, '1st', 'Fauna', 'Brown shrike', 'Lanius cristatus', 25],
            [null, 2025, '1st', 'Fauna', 'Luzon Rufous hornbill', 'Buceros hydrocorax', 4],
            [null, 2025, '1st', 'Fauna', 'Blue-capped Woody kingfisher', 'Actenoides hombroni', 12],
            [null, 2025, '1st', 'Fauna', 'Greater musky fruit bat', 'Ptenochirus jagori', 21],
            [null, 2025, '1st', 'Fauna', 'Bush rat', 'Rattus fuscipes', 15],
            [null, 2025, '1st', 'Fauna', 'Rice field frog', 'Fejervarya limnocharis', 21],
            [null, 2025, '1st', 'Fauna', 'Brahminy Kite', 'Haliastur indus', 2],
            [null, 2025, '1st', 'Fauna', 'Philippine pitviper', 'Trimeresurus flavomaculatus', 1],
            
            // 2025 2nd Semester Flora
            [null, 2025, '2nd', 'Flora', 'Red Lauan', 'Shorea negrosensis', 40],
            [null, 2025, '2nd', 'Flora', 'White Lauan', 'Shorea contorta', 33],
            [null, 2025, '2nd', 'Flora', 'Apitong', 'Dipterocarpus grandiflorus', 24],
            [null, 2025, '2nd', 'Flora', 'Mayapis', 'Shorea palosapis', 25],
            [null, 2025, '2nd', 'Flora', 'Philippine Oak Tree/Ulayan (Manaring)', 'Lithocaspus celebicus', 7],
            [null, 2025, '2nd', 'Flora', 'Lanete', 'Wrightia pubescens', 1],
            [null, 2025, '2nd', 'Flora', 'Bagtikan', 'Parashorea malaanonan', 7],
            
            // 2025 2nd Semester Fauna
            [null, 2025, '2nd', 'Fauna', 'Golden- crowned babbler', 'Sterrhoptilus dennistouni', 2],
            [null, 2025, '2nd', 'Fauna', 'Brown shrike', 'Lanius cristatus', 25],
            [null, 2025, '2nd', 'Fauna', 'Luzon Rufous hornbill', 'Buceros hydrocorax', 4],
            [null, 2025, '2nd', 'Fauna', 'Blue-capped Woody kingfisher', 'Actenoides hombroni', 15],
            [null, 2025, '2nd', 'Fauna', 'Brown-breasted kingfisher', 'Halycon gularis', 1],
            [null, 2025, '2nd', 'Fauna', 'Greater musky fruit bat', 'Ptenochirus jagori', 37],
            [null, 2025, '2nd', 'Fauna', 'Bush rat', 'Rattus fuscipes', 12],
            [null, 2025, '2nd', 'Fauna', 'Rice field frog', 'Fejervarya limnocharis', 23],
            [null, 2025, '2nd', 'Fauna', 'Brahminy Kite', 'Haliastur indus', 2],
            [null, 2025, '2nd', 'Fauna', 'Philippine pit viper', 'Trimeresurus flavomaculatus', 1],
        ];

        foreach ($records as $index => $record) {
            $transactionCode = 'R2-DNP-BMS-T' . (($index % 2) + 1); // Alternate between T1 and T2
            
            DB::table('dipaniong_tbl')->insert([
                'protected_area_id' => $protectedAreaId,
                'transaction_code' => $transactionCode,
                'station_code' => $transactionCode,
                'patrol_year' => $record[1],
                'patrol_semester' => $record[2] === '1st' ? 1 : 2,
                'bio_group' => strtolower($record[3]),
                'common_name' => trim($record[4]),
                'scientific_name' => $record[5] ?: null,
                'recorded_count' => $record[6] ?: 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
