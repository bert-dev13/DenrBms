<?php

namespace App\Http\Controllers;

use App\Models\ProtectedArea;
use App\Models\BanganObservation;
use App\Models\BauaObservation;
use App\Models\BmsSpeciesObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\DupaxObservation;
use App\Models\FuyotObservation;
use App\Models\MadreObservation;
use App\Models\MadupapaObservation;
use App\Models\MangaObservation;
use App\Models\MarianoObservation;
use App\Models\PalauiObservation;
use App\Models\QuibalObservation;
use App\Models\QuirinoObservation;
use App\Models\SalinasObservation;
use App\Models\SanRoqueObservation;
use App\Models\ToyotaObservation;
use App\Models\TumauiniObservation;
use App\Models\WangagObservation;
use App\Models\MagapitObservation;
use App\Services\DynamicTableService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        
        // Calculate total observations across all tables (static + dynamic)
        $tables = DynamicTableService::getAllObservationTables();
        
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += \DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Calculate species diversity across all tables (true unique species)
        $allScientificNames = collect();
        foreach ($tables as $table) {
            try {
                $species = \DB::table($table)->pluck('scientific_name')->filter();
                $allScientificNames = $allScientificNames->merge($species);
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        $speciesDiversity = $allScientificNames->unique()->count();

        $stats = [
            'total_areas' => ProtectedArea::count(),
            'total_sites' => \App\Models\SiteName::count(),
            'total_observations' => $totalObservations,
            'species_diversity' => $speciesDiversity,
        ];
        
        return view('analytics', compact('protectedAreas', 'stats'));
    }

    public function getObservationData(Request $request)
    {
        $request->validate([
            'protected_area_id' => 'required|exists:protected_areas,id'
        ]);

        $protectedArea = ProtectedArea::findOrFail($request->protected_area_id);
        
        // Map area codes to their respective observation models
        $observationModels = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => [MagapitObservation::class, MarianoObservation::class, MadupapaObservation::class],
            'PIPLS' => PalauiObservation::class,
            'PPLS' => [ToyotaObservation::class, SanRoqueObservation::class, MangaObservation::class, QuibalObservation::class],
            'QPL' => QuirinoObservation::class,
            'SANMARIANO' => MarianoObservation::class,
            'MADUPAPA' => MadupapaObservation::class,
            'WWFR' => WangagObservation::class,
            'TOYOTA' => ToyotaObservation::class,
            'SANROQUE' => SanRoqueObservation::class,
            'MANGA' => MangaObservation::class,
            'QUIBAL' => QuibalObservation::class,
            'NSMNP' => MadreObservation::class,
            'TWNP' => TumauiniObservation::class,
            'BHNP' => BanganObservation::class,
            'SNM' => SalinasObservation::class,
            'DWFR' => DupaxObservation::class,
            'CPL' => CasecnanObservation::class,
            'DNP' => DipaniongObservation::class,
        ];

        $yearlyData = [];
        $modelClass = $observationModels[$protectedArea->code] ?? null;

        if ($modelClass) {
            try {
                $allObservations = collect();
                
                if (is_array($modelClass)) {
                    // Handle multiple models (like MPL)
                    foreach ($modelClass as $singleModel) {
                        try {
                            $observations = $singleModel::selectRaw('patrol_year as year, COUNT(*) as count, SUM(recorded_count) as total_count')
                                ->whereNotNull('patrol_year')
                                ->groupBy('patrol_year')
                                ->get();
                            $allObservations = $allObservations->merge($observations);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    // Aggregate by year
                    $groupedObservations = $allObservations->groupBy('year');
                    $observations = $groupedObservations->map(function ($yearGroup) {
                        return (object)[
                            'year' => $yearGroup->first()->year,
                            'count' => $yearGroup->sum('count'),
                            'total_count' => $yearGroup->sum('total_count')
                        ];
                    })->values()->sortBy('year')->values();
                } else {
                    // Handle single model
                    $observations = $modelClass::selectRaw('patrol_year as year, COUNT(*) as count, SUM(recorded_count) as total_count')
                        ->whereNotNull('patrol_year')
                        ->groupBy('patrol_year')
                        ->orderBy('patrol_year', 'asc')
                        ->get();
                }

                // Convert to cumulative data for the chart
                $cumulativeCount = 0;
                $yearlyCounts = [];
                
                foreach ($observations as $obs) {
                    $yearlyCounts[$obs->year] = $obs->count;
                    $cumulativeCount += $obs->count;
                    $yearlyData[] = [
                        'year' => $obs->year,
                        'count' => $cumulativeCount, // Cumulative total
                        'yearly_count' => $obs->count, // Individual year count
                        'total_count' => $obs->total_count // Total recorded individuals
                    ];
                }

                // Fill in missing years with zero counts if needed
                if (!empty($yearlyData)) {
                    $minYear = min(array_column($yearlyData, 'year'));
                    $maxYear = max(array_column($yearlyData, 'year'));
                    
                    $completeData = [];
                    $cumulativeCount = 0;
                    
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $yearData = collect($yearlyData)->firstWhere('year', $year);
                        
                        if ($yearData) {
                            $cumulativeCount += $yearData['yearly_count'];
                            $completeData[] = [
                                'year' => $year,
                                'count' => $cumulativeCount,
                                'yearly_count' => $yearData['yearly_count'],
                                'total_count' => $yearData['total_count']
                            ];
                        } else {
                            $completeData[] = [
                                'year' => $year,
                                'count' => $cumulativeCount,
                                'yearly_count' => 0,
                                'total_count' => 0
                            ];
                        }
                    }
                    
                    $yearlyData = $completeData;
                }

            } catch (\Exception $e) {
                // If there's an error querying the table, return empty data
                $yearlyData = [];
            }
        }

        return response()->json([
            'protected_area' => [
                'id' => $protectedArea->id,
                'name' => $protectedArea->name,
                'code' => $protectedArea->code
            ],
            'data' => $yearlyData,
            'total_years' => count($yearlyData),
            'total_observations' => !empty($yearlyData) ? end($yearlyData)['count'] : 0
        ]);
    }

    public function getTopSpeciesTrends(Request $request)
    {
        // Get all observation tables (static + dynamic)
        $tables = DynamicTableService::getAllObservationTables();
        
        $allSpeciesData = collect();
        
        foreach ($tables as $table) {
            try {
                // Check if table exists first
                if (!\Schema::hasTable($table)) {
                    continue;
                }
                
                // Get species observation counts by year for this table
                // Include records with either scientific_name or common_name
                $speciesData = \DB::table($table)
                    ->select('scientific_name', 'common_name', 'patrol_year', \DB::raw('SUM(recorded_count) as total_count'))
                    ->where(function($query) {
                        $query->whereNotNull('scientific_name')
                              ->orWhereNotNull('common_name');
                    })
                    ->where(function($query) {
                        $query->where('scientific_name', '!=', '')
                              ->orWhere('common_name', '!=', '');
                    })
                    ->whereNotNull('patrol_year')
                    ->whereBetween('patrol_year', [2021, 2025])
                    ->groupBy('scientific_name', 'common_name', 'patrol_year')
                    ->get();
                
                $allSpeciesData = $allSpeciesData->merge($speciesData);
                
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        
        // Group by species and calculate total observations across all years
        $speciesTotals = $allSpeciesData
            ->groupBy(function($item) {
                // Use scientific_name if available, otherwise use common_name as the key
                return $item->scientific_name ?: $item->common_name;
            })
            ->map(function ($speciesGroup) {
                $totalCount = $speciesGroup->sum('total_count');
                $commonName = $speciesGroup->first()->common_name;
                $scientificName = $speciesGroup->first()->scientific_name;
                $yearlyData = $speciesGroup->mapWithKeys(function ($item) {
                    return [$item->patrol_year => $item->total_count];
                })->toArray();
                
                return [
                    'scientific_name' => $scientificName ?: $commonName, // Use common_name as fallback
                    'common_name' => $commonName,
                    'total_observations' => $totalCount,
                    'yearly_data' => $yearlyData
                ];
            })
            ->sortByDesc('total_observations')
            ->take(20);
        
        return response()->json([
            'species_list' => $speciesTotals->values()->map(function ($species, $index) {
                return [
                    'rank' => $index + 1,
                    'scientific_name' => $species['scientific_name'],
                    'common_name' => $species['common_name'],
                    'total_observations' => $species['total_observations']
                ];
            })
        ]);
    }

    public function getSpeciesTrendData(Request $request)
    {
        $request->validate([
            'scientific_name' => 'required|string'
        ]);

        $scientificName = $request->scientific_name;
        
        // Get all observation tables (static + dynamic)
        $tables = DynamicTableService::getAllObservationTables();
        
        $allSpeciesData = collect();
        
        foreach ($tables as $table) {
            try {
                // Check if table exists first
                if (!\Schema::hasTable($table)) {
                    continue;
                }
                
                // Get species observation counts by year for this table
                // Match either scientific_name or common_name
                $speciesData = \DB::table($table)
                    ->select('scientific_name', 'common_name', 'patrol_year', \DB::raw('SUM(recorded_count) as total_count'))
                    ->where(function($query) use ($scientificName) {
                        $query->where('scientific_name', $scientificName)
                              ->orWhere('common_name', $scientificName);
                    })
                    ->whereNotNull('patrol_year')
                    ->whereBetween('patrol_year', [2021, 2025])
                    ->groupBy('scientific_name', 'common_name', 'patrol_year')
                    ->get();
                
                $allSpeciesData = $allSpeciesData->merge($speciesData);
                
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        
        if ($allSpeciesData->isEmpty()) {
            return response()->json([
                'error' => 'No data found for the selected species',
                'years' => range(2021, 2025),
                'data' => array_fill(0, 5, 0),
                'species_info' => null
            ]);
        }
        
        // Aggregate data by year
        $yearlyData = $allSpeciesData->groupBy('patrol_year')
            ->map(function ($yearGroup) {
                return $yearGroup->sum('total_count');
            });
        
        // Prepare data for all years 2021-2025
        $years = range(2021, 2025);
        $data = [];
        foreach ($years as $year) {
            $data[] = $yearlyData->get($year, 0);
        }
        
        $firstRecord = $allSpeciesData->first();
        $speciesInfo = [
            'scientific_name' => $firstRecord->scientific_name ?: $firstRecord->common_name,
            'common_name' => $firstRecord->common_name,
            'total_observations' => $allSpeciesData->sum('total_count')
        ];
        
        return response()->json([
            'years' => $years,
            'data' => $data,
            'species_info' => $speciesInfo
        ]);
    }
}
