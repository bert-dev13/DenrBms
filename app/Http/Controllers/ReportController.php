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

class ReportController extends Controller
{
    public function index()
    {
        // Get basic statistics
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        
        // Calculate total observations across all tables (static + dynamic)
        $tables = DynamicTableService::getAllObservationTables();
        
        $totalObservations = 0;
        $yearlyData = [];
        $speciesData = [];
        $areaData = [];
        
        foreach ($tables as $table) {
            try {
                $totalObservations += \DB::table($table)->count();
                
                // Get yearly data
                $yearlyObs = \DB::table($table)
                    ->select('patrol_year', \DB::raw('COUNT(*) as count'))
                    ->whereNotNull('patrol_year')
                    ->groupBy('patrol_year')
                    ->get();
                
                foreach ($yearlyObs as $year) {
                    if (!isset($yearlyData[$year->patrol_year])) {
                        $yearlyData[$year->patrol_year] = 0;
                    }
                    $yearlyData[$year->patrol_year] += $year->count;
                }
                
                // Get species data
                $speciesObs = \DB::table($table)
                    ->select('scientific_name', 'common_name', \DB::raw('SUM(recorded_count) as total_count'))
                    ->where(function($query) {
                        $query->whereNotNull('scientific_name')
                              ->orWhereNotNull('common_name');
                    })
                    ->where(function($query) {
                        $query->where('scientific_name', '!=', '')
                              ->orWhere('common_name', '!=', '');
                    })
                    ->groupBy('scientific_name', 'common_name')
                    ->get();
                
                foreach ($speciesObs as $species) {
                    $key = $species->scientific_name ?: $species->common_name;
                    if (!isset($speciesData[$key])) {
                        $speciesData[$key] = [
                            'scientific_name' => $species->scientific_name,
                            'common_name' => $species->common_name,
                            'total_count' => 0
                        ];
                    }
                    $speciesData[$key]['total_count'] += $species->total_count;
                }
                
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        
        // Sort yearly data
        ksort($yearlyData);
        
        // Sort species data by count
        uasort($speciesData, function($a, $b) {
            return $b['total_count'] - $a['total_count'];
        });
        
        // Get area-specific data
        foreach ($protectedAreas as $area) {
            $areaData[$area->id] = [
                'name' => $area->name,
                'code' => $area->code,
                'observations' => $this->getAreaObservationCount($area->code),
                'species' => $this->getAreaSpeciesCount($area->code)
            ];
        }
        
        // Calculate summary statistics
        $stats = [
            'total_areas' => ProtectedArea::count(),
            'total_sites' => \App\Models\SiteName::count(),
            'total_observations' => $totalObservations,
            'species_diversity' => count($speciesData),
            'avg_observations_per_area' => $totalObservations / max(ProtectedArea::count(), 1),
            'most_active_year' => !empty($yearlyData) ? array_keys($yearlyData, max($yearlyData))[0] : null,
            'yearly_totals' => $yearlyData
        ];
        
        // Get top species (limit to 20 for display)
        $topSpecies = array_slice($speciesData, 0, 20, true);
        
        return view('report', compact('protectedAreas', 'stats', 'topSpecies', 'areaData', 'yearlyData'));
    }
    
    private function getAreaObservationCount($areaCode)
    {
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
        
        $modelClass = $observationModels[$areaCode] ?? null;
        
        if (!$modelClass) {
            return 0;
        }
        
        try {
            if (is_array($modelClass)) {
                $count = 0;
                foreach ($modelClass as $singleModel) {
                    try {
                        $count += $singleModel::count();
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                return $count;
            } else {
                return $modelClass::count();
            }
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getAreaSpeciesCount($areaCode)
    {
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
        
        $modelClass = $observationModels[$areaCode] ?? null;
        
        if (!$modelClass) {
            return 0;
        }
        
        try {
            if (is_array($modelClass)) {
                $allSpecies = collect();
                foreach ($modelClass as $singleModel) {
                    try {
                        $species = $singleModel::pluck('scientific_name')->filter();
                        $allSpecies = $allSpecies->merge($species);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                return $allSpecies->unique()->count();
            } else {
                return $modelClass::pluck('scientific_name')->filter()->unique()->count();
            }
        } catch (\Exception $e) {
            return 0;
        }
    }
}
