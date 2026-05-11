<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Models\User;
use App\Services\DynamicTableService;
use App\Support\UserAccess;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function assignedProtectedAreaId(): ?int
    {
        return UserAccess::assignedProtectedAreaId(Auth::user());
    }

    private function scopedTableQuery(string $table)
    {
        $query = DB::table($table);
        $assignedProtectedAreaId = $this->assignedProtectedAreaId();

        if ($assignedProtectedAreaId !== null && Schema::hasColumn($table, 'protected_area_id')) {
            $query->where('protected_area_id', $assignedProtectedAreaId);
        }

        return $query;
    }

    /**
     * Display the dashboard with real-time species observation data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        return view('pages.dashboard', [
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
        $assignedProtectedAreaId = $this->assignedProtectedAreaId();
        $user = Auth::user();

        // Calculate total observations across ALL tables (static + dynamic) using direct DB queries
        $tables = DynamicTableService::getAllObservationTables();
        
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += $this->scopedTableQuery($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Calculate total protected areas
        $totalProtectedAreas = ProtectedArea::query()
            ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('id', $assignedProtectedAreaId))
            ->count();

        // Calculate active areas (protected areas with observations)
        $allProtectedAreas = ProtectedArea::query()
            ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('id', $assignedProtectedAreaId))
            ->get();
        $activeProtectedAreas = 0;
        foreach ($allProtectedAreas as $area) {
            if ($area->getTotalObservationsCount() > 0) {
                $activeProtectedAreas++;
            }
        }

        // Calculate total species (unique scientific_name) - single source of truth
        $totalSpecies = 0;
        $scientificNames = collect();
        foreach ($tables as $table) {
            try {
                if (! Schema::hasColumn($table, 'scientific_name')) {
                    continue;
                }

                $scientificNames = $scientificNames->merge(
                    $this->scopedTableQuery($table)
                        ->pluck('scientific_name')
                        ->filter(fn ($name) => $name !== null && trim((string) $name) !== '')
                        ->map(fn ($name) => strtolower(trim((string) $name)))
                );
            } catch (\Exception $e) {
                continue;
            }
        }
        $totalSpecies = $scientificNames->unique()->count();

        $activeUsersQuery = User::query()
            ->where('last_login_at', '>=', $now->copy()->subDays(7));

        if (UserAccess::isPaUser($user)) {
            $cardValue = SiteName::query()
                ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('protected_area_id', $assignedProtectedAreaId))
                ->count();
            $cardLabel = 'Total Sites';
            $cardSubtitle = 'Sites in your assigned protected area';
        } else {
            $cardValue = $activeUsersQuery->count();
            $cardLabel = 'Active Users';
            $cardSubtitle = 'Active within the last 7 days';
        }

        return [
            'total_observations' => $totalObservations,
            'monthly_growth' => 100, // Calculate from actual data if needed
            'total_species' => $totalSpecies,
            'quarterly_growth' => 100, // Calculate from actual data if needed
            'protected_areas' => $totalProtectedAreas,
            'active_areas' => $activeProtectedAreas,
            'total_sites' => SiteName::query()
                ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('protected_area_id', $assignedProtectedAreaId))
                ->count(),
            'active_users' => $cardValue,
            'active_users_label' => $cardLabel,
            'active_users_subtitle' => $cardSubtitle,
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
                $data = $this->scopedTableQuery($table)
                    ->select('patrol_year', DB::raw('COUNT(*) as observations'))
                    ->whereNotNull('patrol_year')
                    ->where('patrol_year', '>', 0)
                    ->groupBy('patrol_year')
                    ->orderBy('patrol_year')
                    ->get();

                $speciesByYear = $this->scopedTableQuery($table)
                    ->select('patrol_year', DB::raw('LOWER(TRIM(scientific_name)) as scientific_name'))
                    ->whereNotNull('patrol_year')
                    ->where('patrol_year', '>', 0)
                    ->whereNotNull('scientific_name')
                    ->whereRaw("TRIM(scientific_name) <> ''")
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
                $totalObservations += $this->scopedTableQuery($table)->count();
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
