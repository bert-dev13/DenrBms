<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BmsSpeciesObservation;
use App\Models\ProtectedArea;

class BmsSpeciesObservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $protectedArea = ProtectedArea::where('code', 'BPLS')->first();

        if (!$protectedArea) {
            $this->command->error('Protected area BPLS not found. Please run ProtectedAreaSeeder first.');
            return;
        }

        $observations = [
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S1', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Barn Swallow', 'scientific_name' => 'Hirundo rustica', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 6],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 8],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S2', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Zitting Cisticola', 'scientific_name' => 'Cisticola juncidis', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Zitting Cisticola', 'scientific_name' => 'Cisticola juncidis', 'recorded_count' => 8],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Paddyfield Pipit', 'scientific_name' => 'Anthus rufulus', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 5],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S3', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 6],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2024, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Barn Swallow', 'scientific_name' => 'Hirundo rustica', 'recorded_count' => 7],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Paddyfield Pipit', 'scientific_name' => 'Anthus rufulus', 'recorded_count' => 4],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => 'Macropygia tenuirostris', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Cow', 'scientific_name' => 'Bos sp.', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S4', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Goat', 'scientific_name' => 'Capra sp.', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Cow', 'scientific_name' => 'Bos sp.', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Goat', 'scientific_name' => 'Capra sp.', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Zitting Cisticola', 'scientific_name' => 'Cisticola juncidis', 'recorded_count' => 5],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => 'Macropygia tenuirostris', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Whistling Green Pigeon', 'scientific_name' => 'Treron formosae', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 12],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Coucal', 'scientific_name' => 'Centropus viridis', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S5', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Paddyfield Pipit', 'scientific_name' => 'Anthus rufulus', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lesser Coucal', 'scientific_name' => 'Centropus bengalensis', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Coucal', 'scientific_name' => 'Centropus viridis', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 9],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 11],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Paddyfield Pipit', 'scientific_name' => 'Anthus rufulus', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S6', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Zitting Cisticola', 'scientific_name' => 'Cisticola juncidis', 'recorded_count' => 4],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S7', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => 'Macropygia tenuirostris', 'recorded_count' => 5],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S7', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Whistling Green Pigeon', 'scientific_name' => 'Treron formosae', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S7', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 3],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S7', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 5],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S8', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => 'Macropygia tenuirostris', 'recorded_count' => 1],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S8', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 6],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S8', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 4],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S9', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 7],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S9', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 6],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S9', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Plain Bush Hen', 'scientific_name' => 'Amaurornis olivacea', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S10', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 8],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S10', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Lowland White-eye', 'scientific_name' => 'Zosterops meyeni', 'recorded_count' => 9],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S10', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Philippine Cuckoo Dove', 'scientific_name' => 'Macropygia tenuirostris', 'recorded_count' => 2],
            ['transaction_code' => 'BPLS', 'station_code' => 'BPLS-S11', 'patrol_year' => 2025, 'patrol_semester' => 2, 'bio_group' => 'fauna', 'common_name' => 'Brown-eared Bulbul', 'scientific_name' => 'Hypsipetes amaurotis', 'recorded_count' => 2],
        ];

        foreach ($observations as $observation) {
            $observation['protected_area_id'] = $protectedArea->id;
            BmsSpeciesObservation::create($observation);
        }

        $this->command->info('Species observations seeded successfully!');
    }
}
