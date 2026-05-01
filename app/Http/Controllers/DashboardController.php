<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BmsSpeciesObservation;
use App\Models\ProtectedArea;
use App\Models\User;
use App\Models\SiteName;
use App\Services\DynamicTableService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real-time species observation data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard', [
            'user' => $user,
            'stats' => $this->getDashboardStats(),
        ]);
    }

    /**
     * Get comprehensive dashboard statistics from species observations.
     *
     * @return array
     */
    private function getDashboardStats()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastQuarter = $now->copy()->subQuarter();

        // Calculate total observations across ALL tables (static + dynamic) using direct DB queries
        $tables = DynamicTableService::getAllObservationTables();
        
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Calculate total protected areas
        $totalProtectedAreas = ProtectedArea::count();

        // Calculate active areas (protected areas with observations)
        $allProtectedAreas = ProtectedArea::all();
        $activeProtectedAreas = 0;
        foreach ($allProtectedAreas as $area) {
            if ($area->getTotalObservationsCount() > 0) {
                $activeProtectedAreas++;
            }
        }

        // Calculate total species (unique scientific_name) - single source of truth
        $totalSpecies = DynamicTableService::getUniqueSpeciesCount();

        return [
            'total_observations' => $totalObservations,
            'monthly_growth' => 100, // Calculate from actual data if needed
            'total_species' => $totalSpecies,
            'quarterly_growth' => 100, // Calculate from actual data if needed
            'protected_areas' => $totalProtectedAreas,
            'active_areas' => $activeProtectedAreas,
            'total_sites' => SiteName::count(),
            'active_users' => User::where('last_login_at', '>=', $now->copy()->subDays(7))->count(),
        ];
    }

    /**
     * Get yearly monitoring data aggregated from all observation tables.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYearlyMonitoringData()
    {
        // Use dynamic table discovery for consistency
        $tables = DynamicTableService::getAllObservationTables();
        
        $yearlyData = [];
        
        foreach ($tables as $table) {
            try {
                $data = DB::table($table)
                    ->select('patrol_year', DB::raw('COUNT(*) as observations'))
                    ->whereNotNull('patrol_year')
                    ->groupBy('patrol_year')
                    ->orderBy('patrol_year')
                    ->get();

                $speciesByYear = DB::table($table)
                    ->select('patrol_year', 'scientific_name')
                    ->whereNotNull('patrol_year')
                    ->whereNotNull('scientific_name')
                    ->distinct()
                    ->get()
                    ->groupBy('patrol_year');
                
                foreach ($data as $row) {
                    $year = $row->patrol_year;
                    if (!isset($yearlyData[$year])) {
                        $yearlyData[$year] = [
                            'observations' => 0,
                            'species' => [],
                        ];
                    }
                    $yearlyData[$year]['observations'] += (int) $row->observations;

                    foreach (($speciesByYear[$year] ?? collect()) as $speciesRow) {
                        $scientificName = $speciesRow->scientific_name;
                        $yearlyData[$year]['species'][$scientificName] = true;
                    }
                }
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        
        // Sort by year and return plain yearly totals for long-term trend reading.
        ksort($yearlyData);
        $chartData = [];
        
        foreach ($yearlyData as $year => $metrics) {
            $chartData[] = [
                'year' => $year,
                'observations' => $metrics['observations'],
                'species_tracked' => count($metrics['species']),
            ];
        }
        
        // Calculate total observations using same method as ProtectedAreaController
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        
        return response()->json([
            'data' => $chartData,
            'total_years' => count($chartData),
            'total_observations' => $totalObservations
        ]);
    }

}
