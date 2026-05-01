<?php

namespace App\Http\Controllers;

use App\Models\BanganObservation;
use App\Models\BauaObservation;
use App\Models\BaseObservation;
use App\Models\BmsSpeciesObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\DupaxObservation;
use App\Models\FuyotObservation;
use App\Models\MagapitObservation;
use App\Models\MadreObservation;
use App\Models\MadupapaObservation;
use App\Models\MangaObservation;
use App\Models\MarianoObservation;
use App\Models\PalauiObservation;
use App\Models\ProtectedArea;
use App\Models\QuibalObservation;
use App\Models\QuirinoObservation;
use App\Models\SalinasObservation;
use App\Models\SanRoqueObservation;
use App\Models\SiteName;
use App\Models\ToyotaObservation;
use App\Models\TumauiniObservation;
use App\Models\WangagObservation;
use App\Helpers\PatrolYearHelper;
use App\Services\DynamicTableService;
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SpeciesObservationController extends Controller
{
    /**
     * Resolve available sites for a protected area using the same logic as site dropdown APIs.
     */
    private function resolveSitesForProtectedArea(ProtectedArea $protectedArea): \Illuminate\Support\Collection
    {
        $siteNames = SiteName::where('protected_area_id', $protectedArea->id)
            ->orderBy('name')
            ->get();

        // Keep fallback behavior consistent with existing site loading API.
        // Important: only run fallback for known legacy PA codes. Running an
        // empty fallback condition can incorrectly return unrelated sites.
        if ($siteNames->isEmpty()) {
            if ($protectedArea->code === 'PPLS') {
                $siteNames = SiteName::where('name', 'like', 'PPLS Site%')
                    ->orderBy('name')
                    ->get();
            } elseif ($protectedArea->code === 'MPL') {
                $siteNames = SiteName::where(function ($query) {
                    $query->where('name', 'like', 'MPL SITE%')
                        ->orWhere('name', 'like', 'MPL Site%');
                })->orderBy('name')->get();
            }
        }

        return $siteNames;
    }

    /**
     * Enforce PA/Site consistency rules for save operations.
     */
    private function validateProtectedAreaSiteSelection(array $validated, ProtectedArea $protectedArea): ?JsonResponse
    {
        $availableSites = $this->resolveSitesForProtectedArea($protectedArea);
        $hasSites = $availableSites->isNotEmpty();
        $siteId = $validated['site_name_id'] ?? null;

        if ($hasSites && empty($siteId)) {
            return response()->json([
                'success' => false,
                'message' => 'Site Name is required because this Protected Area has available sites.'
            ], 422);
        }

        if (!$hasSites && !empty($siteId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected Protected Area has no sites. Site Name must be empty.'
            ], 422);
        }

        if (!empty($siteId) && !$availableSites->contains(fn ($site) => (int) $site->id === (int) $siteId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected site does not belong to the selected protected area.'
            ], 422);
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle export requests
        if ($request->has('export') || $request->has('print')) {
            return $this->handleExport($request);
        }
        
        // Log request parameters for debugging
        Log::info('Species Observation Index Request:', [
            'protected_area_id' => $request->protected_area_id,
            'site_name' => $request->site_name,
            'bio_group' => $request->bio_group,
            'patrol_year' => $request->patrol_year,
            'patrol_semester' => $request->patrol_semester,
            'search' => $request->search
        ]);
        
        // Get observations from all tables (static + dynamic)
        $query = null; // Initialize query variable
        $allTableQueries = [];
        
        // Get all observation tables dynamically - use the same logic as ProtectedArea model
        $observationModels = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => [MagapitObservation::class, MarianoObservation::class, MadupapaObservation::class],
            'PIPLS' => PalauiObservation::class,
            'QPL' => QuirinoObservation::class,
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
            'PPLS' => [ToyotaObservation::class, SanRoqueObservation::class, MangaObservation::class, QuibalObservation::class],
        ];

        // Build queries based on the selected protected area
        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea) {
                $areaCode = $protectedArea->code;
                if (isset($observationModels[$areaCode])) {
                    $models = $observationModels[$areaCode];
                    if (is_array($models)) {
                        // Handle multiple models (MPL and PPLS)
                        foreach ($models as $modelClass) {
                            try {
                                $allTableQueries[] = $this->buildFilteredQuery($modelClass, $request);
                            } catch (\Exception $e) {
                                Log::warning("Failed to build query for model {$modelClass}: " . $e->getMessage());
                                continue;
                            }
                        }
                    } else {
                        // Single model
                        try {
                            $allTableQueries[] = $this->buildFilteredQuery($models, $request);
                        } catch (\Exception $e) {
                            Log::warning("Failed to build query for model {$models}: " . $e->getMessage());
                        }
                    }
                }
            }
        } else {
            // No protected area filter - get all tables using DynamicTableService
            $allTables = DynamicTableService::getAllObservationTables();
            
            foreach ($allTables as $tableName) {
                try {
                    // Check if this is a site-specific table
                    if (strpos($tableName, '_site_tbl') !== false) {
                        // For site-specific tables, use direct DB query
                        $allTableQueries[] = $this->buildTableQuery($tableName, $request);
                    } else {
                        // For regular tables, use dynamic model
                        $model = DynamicTableService::createDynamicModel($tableName);
                        $allTableQueries[] = $this->buildFilteredQuery(get_class($model), $request);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to build query for table {$tableName}: " . $e->getMessage());
                    continue;
                }
            }
        }
        
        // Special handling for site-specific tables
        if ($request->filled('site_name') && $request->site_name !== '') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $siteTableName = $this->createSiteTableName($siteName->name, $siteName->id);
                
                // Filter queries to only include the specific site table
                $allTableQueries = array_filter($allTableQueries, function($query) use ($siteTableName) {
                    // This is a bit tricky since we're dealing with query objects
                    // For now, we'll handle this in the union logic
                    return true;
                });
                
                // If the site-specific table exists, prioritize it
                if (Schema::hasTable($siteTableName)) {
                    try {
                        $siteQuery = $this->buildTableQuery($siteTableName, $request);
                        // Only use the site-specific table query
                        $allTableQueries = [$siteQuery];
                    } catch (\Exception $e) {
                        Log::warning("Failed to build site-specific query for {$siteTableName}: " . $e->getMessage());
                    }
                }
            }
        }
        
        // Combine all queries with union
        if (empty($allTableQueries)) {
            // No tables found, return empty pagination
            Log::warning('No table queries found, returning empty results');
            $observations = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $summaryStats = [
                'total_observations' => 0,
                'total_recorded_count' => 0,
                'total_protected_areas' => 0,
                'total_species' => 0,
            ];
        } else {
            // Re-index array to avoid issues with union operations
            $allTableQueries = array_values($allTableQueries);
            
            Log::info('Combining queries:', [
                'total_queries' => count($allTableQueries),
                'query_types' => array_map(function($q) { return get_class($q); }, $allTableQueries)
            ]);
            
            $baseQuery = $allTableQueries[0];
            for ($i = 1; $i < count($allTableQueries); $i++) {
                $baseQuery = $baseQuery->union($allTableQueries[$i]);
            }
            
            $baseQuery = $baseQuery->orderBy('patrol_year', 'desc')
                ->orderBy('patrol_semester', 'desc')
                ->orderBy('station_code');

            // Get all filtered results (single query) for summary stats + pagination
            $allResults = $baseQuery->get();

            // Compute summary stats from full filtered dataset (not just current page)
            $summaryStats = [
                'total_observations' => $allResults->count(),
                'total_recorded_count' => $allResults->sum('recorded_count'),
                'total_protected_areas' => $allResults->pluck('protected_area_id')->unique()->count(),
                'total_species' => $allResults->pluck('scientific_name')->filter(fn ($v) => !empty(trim((string) $v)))->unique()->count(),
            ];

            // Load protected area for each observation
            $allResults->each(function ($observation) {
                if (method_exists($observation, 'load')) {
                    $observation->load('protectedArea');
                } else {
                    $observation->protectedArea = ProtectedArea::find($observation->protected_area_id);
                }
            });

            // Paginate for table display
            $currentPage = $request->get('page', 1);
            $perPage = 20;
            $slice = $allResults->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $observations = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $allResults->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
                
            Log::info('Final pagination result:', [
                'total_count' => $observations->total(),
                'current_page' => $observations->currentPage(),
                'per_page' => $observations->perPage(),
                'summary_stats' => $summaryStats
            ]);
        }

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return view('pages.species_observations.index', compact(
            'observations',
            'filterOptions',
            'summaryStats'
        ));
    }

    /**
     * Build a filtered query for database tables
     */
    private function buildTableQuery(string $tableName, Request $request)
    {
        $selectOrNull = static function (string $column) use ($tableName) {
            return Schema::hasColumn($tableName, $column)
                ? $column
                : DB::raw("NULL as {$column}");
        };

        $query = DB::table($tableName)->select(
            $selectOrNull('id'),
            $selectOrNull('protected_area_id'),
            $selectOrNull('transaction_code'),
            $selectOrNull('station_code'),
            $selectOrNull('patrol_year'),
            $selectOrNull('patrol_semester'),
            $selectOrNull('bio_group'),
            $selectOrNull('common_name'),
            $selectOrNull('scientific_name'),
            $selectOrNull('recorded_count'),
            $selectOrNull('created_at'),
            $selectOrNull('updated_at'),
            DB::raw("'" . $tableName . "' as table_name")
        );
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'protected_area_id')) {
                        $q->orWhereExists(function($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = ' . $tableName . '.protected_area_id')
                                ->where('protected_areas.name', 'like', '%' . $searchTerm . '%');
                        });
                    }
                }
            );
        }
        
        // Apply other filters if they exist
        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group', 
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester'
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && Schema::hasColumn($tableName, $dbField)) {
                $query->where($dbField, $request->$requestKey);
            }
        }

        // Handle site name filter separately since it needs to be converted to station_code
        if ($request->filled('site_name')) {
            Log::info('Processing site name filter:', [
                'site_name_value' => $request->site_name,
                'site_name_type' => gettype($request->site_name),
                'is_empty_string' => $request->site_name === '',
                'table_name' => $tableName
            ]);
            
            // Check if this is a Mariano or Madupapa table and if their respective sites are selected
            $siteName = $request->filled('site_name') && $request->site_name !== '' ? SiteName::find($request->site_name) : null;
            
            // Special handling for Mariano and Madupapa tables
            if ($siteName && ($tableName === 'mariano_tbl' || $tableName === 'madupapa_tbl')) {
                if (($tableName === 'mariano_tbl' && strpos($siteName->name, 'San Mariano') !== false) ||
                    ($tableName === 'madupapa_tbl' && strpos($siteName->name, 'Madupapa') !== false)) {
                    // Correct site selected for this table - skip station code filtering
                    Log::info('Correct site selected for ' . $tableName . ' - skipping station code filtering');
                    return $query;
                } else {
                    // Wrong site selected for this table - exclude all records
                    Log::info('Wrong site selected for ' . $tableName . ' - excluding all records');
                    $query->whereRaw('1 = 0'); // Exclude all records
                    return $query;
                }
            }
            
            // If site_name is empty string, it means "All Sites" was selected - show all sites for the selected protected area
            if ($request->site_name === '') {
                Log::info('Site filter: All Sites selected - no site filtering applied');
                // Don't apply any site filtering - just use protected_area_id filter
                // This will show all sites under the selected protected area
                return $query;
            }
            
            // If a specific site is selected, filter by that site's station code
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                Log::info('Site filter: Specific site selected:', [
                    'site_id' => $request->site_name,
                    'site_name' => $siteName->name,
                    'station_code' => $stationCode
                ]);
                
                if ($stationCode && Schema::hasColumn($tableName, 'station_code')) {
                    // Apply exact station code filtering for the specific site
                    $query->where('station_code', $stationCode);
                    Log::info('Applied station code filter: ' . $stationCode);
                } else {
                    Log::warning('No station code found for site: ' . $siteName->name);
                }
            } else {
                Log::warning('Site not found for ID: ' . $request->site_name);
            }
        } else {
            Log::info('No site name filter present');
        }

        return $query;
    }

    /**
     * Build a filtered query for observation models WITHOUT station code filtering
     * Used for Mariano and Madupapa sites where the table contains all records for that site
     */
    private function buildFilteredQueryWithoutStationCode(string $modelClass, Request $request)
    {
        // Get table name from model
        $model = new $modelClass;
        $tableName = $model->getTable();
        $hasColumn = static fn (string $column): bool => Schema::hasColumn($tableName, $column);
        $selectOrNull = static fn (string $column) => Schema::hasColumn($tableName, $column)
            ? $column
            : DB::raw("NULL as {$column}");
        
        Log::info('Building filtered query WITHOUT station code for model:', [
            'model_class' => $modelClass,
            'table_name' => $tableName,
            'protected_area_id' => $request->protected_area_id,
            'site_name' => $request->site_name
        ]);
        
        $query = $modelClass::select(
            $selectOrNull('id'),
            $selectOrNull('protected_area_id'),
            $selectOrNull('transaction_code'),
            $selectOrNull('station_code'),
            $selectOrNull('patrol_year'),
            $selectOrNull('patrol_semester'),
            $selectOrNull('bio_group'),
            $selectOrNull('common_name'),
            $selectOrNull('scientific_name'),
            $selectOrNull('recorded_count'),
            $selectOrNull('created_at'),
            $selectOrNull('updated_at'),
            DB::raw("'" . $tableName . "' as table_name")
        );
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($hasColumn, $tableName) {
                    if ($hasColumn('protected_area_id')) {
                        $q->orWhereExists(function($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = ' . $tableName . '.protected_area_id')
                                ->where('protected_areas.name', 'like', '%' . $searchTerm . '%');
                        });
                    }
                }
            );
        }
        
        // Apply other filters if they exist (but NOT site_name filter)
        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group', 
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester'
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && $hasColumn($dbField)) {
                $query->where($dbField, $request->$requestKey);
                Log::info("Applied filter {$requestKey}: " . $request->$requestKey);
            }
        }

        // IMPORTANT: Do NOT apply site name filter for Mariano/Madupapa
        // These tables contain all records for their respective sites
        Log::info('Skipping site name filter for Mariano/Madupapa table');

        return $query;
    }

    /**
     * Build a filtered query for observation models
     */
    private function buildFilteredQuery(string $modelClass, Request $request)
    {
        // Get table name from model
        $model = new $modelClass;
        $tableName = $model->getTable();
        $hasColumn = static fn (string $column): bool => Schema::hasColumn($tableName, $column);
        $selectOrNull = static fn (string $column) => Schema::hasColumn($tableName, $column)
            ? $column
            : DB::raw("NULL as {$column}");
        
        Log::info('Building filtered query for model:', [
            'model_class' => $modelClass,
            'table_name' => $tableName,
            'protected_area_id' => $request->protected_area_id,
            'site_name' => $request->site_name
        ]);
        
        $query = $modelClass::select(
            $selectOrNull('id'),
            $selectOrNull('protected_area_id'),
            $selectOrNull('transaction_code'),
            $selectOrNull('station_code'),
            $selectOrNull('patrol_year'),
            $selectOrNull('patrol_semester'),
            $selectOrNull('bio_group'),
            $selectOrNull('common_name'),
            $selectOrNull('scientific_name'),
            $selectOrNull('recorded_count'),
            $selectOrNull('created_at'),
            $selectOrNull('updated_at'),
            DB::raw("'" . $tableName . "' as table_name")
        );
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($hasColumn, $tableName) {
                    if ($hasColumn('protected_area_id')) {
                        $q->orWhereExists(function($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = ' . $tableName . '.protected_area_id')
                                ->where('protected_areas.name', 'like', '%' . $searchTerm . '%');
                        });
                    }
                }
            );
        }
        
        // Apply other filters if they exist
        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group', 
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester'
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && $hasColumn($dbField)) {
                $query->where($dbField, $request->$requestKey);
                Log::info("Applied filter {$requestKey}: " . $request->$requestKey);
            }
        }

        // Handle site name filter for models (similar to table queries)
        if ($request->filled('site_name')) {
            Log::info('Processing site name filter for model:', [
                'site_name_value' => $request->site_name,
                'site_name_type' => gettype($request->site_name),
                'is_empty_string' => $request->site_name === '',
                'model_class' => $modelClass
            ]);
            
            // If site_name is empty string, it means "All Sites" was selected
            if ($request->site_name === '') {
                Log::info('Site filter: All Sites selected for model - no site filtering applied');
                // Don't apply any site filtering
                return $query;
            }
            
            // If a specific site is selected, filter by that site's station code
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                Log::info('Site filter: Specific site selected for model:', [
                    'site_id' => $request->site_name,
                    'site_name' => $siteName->name,
                    'station_code' => $stationCode
                ]);
                
                if ($stationCode && $hasColumn('station_code')) {
                    $query->where('station_code', $stationCode);
                    Log::info('Applied station code filter for model: ' . $stationCode);
                } else {
                    Log::warning('No station code found for site in model: ' . $siteName->name);
                }
            } else {
                Log::warning('Site not found for model ID: ' . $request->site_name);
            }
        } else {
            Log::info('No site name filter present for model');
        }

        return $query;
    }

    /**
     * Get filter options for the view
     */
    private function getFilterOptions(): array
    {
        return [
            'protectedAreas' => ProtectedArea::orderBy('name')->get(),
            'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
            'years' => PatrolYearHelper::getYears(),
            'semesters' => [1 => '1st', 2 => '2nd']
        ];
    }

    /**
     * Get site names for a specific protected area (AJAX)
     */
    public function getSiteNames(int $protectedAreaId)
    {
        $protectedArea = ProtectedArea::find($protectedAreaId);
        
        if ($protectedArea) {
            $siteNames = $this->resolveSitesForProtectedArea($protectedArea);
            
            return response()->json([
                'success' => true,
                'site_names' => $siteNames,
                'sites' => $siteNames, // Alias for consistency with GET /protected-areas/{id}/sites
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Protected area not found',
            'site_names' => [],
            'sites' => [],
        ]);
    }


    /**
     * Get observation data for Edit modal
     */
    public function getObservationForEdit(int $id)
    {
        $tableName = request()->query('table_name');
        Log::info('getObservationForEdit called with ID: ' . $id . ', tableName: ' . $tableName);
        
        try {
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            if (!$observation) {
                Log::error('Observation not found for editing with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation not found.'
                ], 404);
            }

            // Check if observation still exists in database before trying to load relationships
            if (!$observation->exists) {
                Log::error('Observation no longer exists for editing with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation no longer exists.'
                ], 410);
            }

            // Load the protected area relationship
            try {
                // For site-specific tables, we need to manually load the protected area
                if (isset($observation->table_name) && strpos($observation->table_name, '_site_tbl') !== false) {
                    // This is a site-specific table result, manually load protected area
                    $protectedArea = ProtectedArea::find($observation->protected_area_id);
                    $observation->protectedArea = $protectedArea;
                } else {
                    // This is an Eloquent model, use the relationship
                    $observation->load('protectedArea');
                }
            } catch (\Exception $e) {
                Log::error('Failed to load protected area relationship for editing: ' . $e->getMessage());
            }

            // Get table name
            $tableName = isset($observation->table_name) ? $observation->table_name : $observation->getTable();
            
            // Prepare response data
            $observationData = [
                'id' => $observation->id,
                'table_name' => $tableName,
                'protected_area_id' => $observation->protected_area_id,
                'transaction_code' => $observation->transaction_code,
                'station_code' => $observation->station_code,
                'patrol_year' => $observation->patrol_year,
                'patrol_semester' => $observation->patrol_semester,
                'bio_group' => $observation->bio_group,
                'common_name' => $observation->common_name,
                'scientific_name' => $observation->scientific_name,
                'recorded_count' => $observation->recorded_count,
                'site_name_id' => null,
            ];

            // Try to get site name ID if applicable
            if (in_array($tableName, ['toyota_tbl', 'roque_tbl', 'manga_tbl', 'quibal_tbl'])) {
                // For PPLS site tables, try to find corresponding site name
                $siteName = SiteName::where('name', 'like', '%PPLS Site%')
                    ->where(function($query) use ($observation) {
                        $query->where('name', 'like', '%Toyota%')
                              ->orWhere('name', 'like', '%San Roque%')
                              ->orWhere('name', 'like', '%Manga%')
                              ->orWhere('name', 'like', '%Quibal%');
                    })
                    ->first();
                
                if ($siteName) {
                    $observationData['site_name_id'] = $siteName->id;
                }
            } elseif (in_array($tableName, ['magapit_tbl', 'mariano_tbl', 'madupapa_tbl'])) {
                // For MPL site tables, try to find corresponding site name
                $siteName = SiteName::where('name', 'like', '%MPL SITE%')
                    ->where(function($query) use ($observation) {
                        $query->where('name', 'like', '%Magapit%')
                              ->orWhere('name', 'like', '%San Mariano%')
                              ->orWhere('name', 'like', '%Madupapa%');
                    })
                    ->first();
                
                if ($siteName) {
                    $observationData['site_name_id'] = $siteName->id;
                }
            }
            
            return response()->json([
                'success' => true,
                'observation' => $observationData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching observation data for editing: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load observation data for editing.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            // Accept site_id alias from frontend and normalize to site_name_id.
            if ($request->has('site_id') && !$request->has('site_name_id')) {
                $request->merge(['site_name_id' => $request->site_id]);
            }

            // Normalize site_name_id to site_name for backward compatibility
            if ($request->has('site_name_id')) {
                $request->merge(['site_name' => $request->site_name_id === '0' || $request->site_name_id === '' ? null : $request->site_name_id]);
            }

            $validated = $request->validate([
                'protected_area_id' => 'required|exists:protected_areas,id',
                'transaction_code' => 'required|string|max:50',
                'station_code' => 'required|string|max:60',
                'patrol_year' => 'required|integer|min:2000|max:2100',
                'patrol_semester' => 'required|integer|in:1,2',
                'bio_group' => 'required|in:fauna,flora',
                'common_name' => 'required|string|max:150',
                'scientific_name' => 'nullable|string|max:200',
                'recorded_count' => 'required|integer|min:0',
                'site_name' => [
                    'nullable',
                    Rule::exists('site_names', 'id')->where('protected_area_id', $request->protected_area_id),
                ],
                'table_name' => 'required|string',
            ], [], [
                'protected_area_id' => 'protected area',
                'transaction_code' => 'transaction code',
                'station_code' => 'station code',
                'patrol_year' => 'patrol year',
                'patrol_semester' => 'patrol semester',
                'bio_group' => 'bio group',
                'common_name' => 'common name',
                'scientific_name' => 'scientific name',
                'recorded_count' => 'recorded count',
                'site_name' => 'site name',
                'table_name' => 'table name',
            ]);

            // Find the observation to update
            $observation = $this->findObservationById($id, $validated['table_name']);
            
            if (!$observation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Observation not found.'
                ], 404);
            }

            // Get the protected area to determine which table to use
            $protectedArea = ProtectedArea::find($validated['protected_area_id']);
            if (!$protectedArea) {
                return response()->json([
                    'success' => false,
                    'message' => 'Protected area not found.'
                ], 422);
            }

            $normalizedSiteId = $validated['site_name'] ?? null;
            $validated['site_name_id'] = $normalizedSiteId;
            $siteValidationError = $this->validateProtectedAreaSiteSelection($validated, $protectedArea);
            if ($siteValidationError) {
                return $siteValidationError;
            }
            if (empty($validated['site_name_id'])) {
                $validated['site_name'] = null;
            }
            
            // Handle table changes if protected area is different
            $currentTableName = $observation->getTable();
            $targetTableName = null;
            
            if ($protectedArea->code === 'PPLS') {
                // For PPLS, determine which site table based on selected site name
                if (!empty($validated['site_name'])) {
                    $siteName = SiteName::find($validated['site_name']);
                    if ($siteName) {
                        // Set station code based on selected site
                        $validated['station_code'] = $siteName->getStationCodeAttribute();
                        
                        // Determine which table to use based on site name
                        if (strpos($siteName->name, 'Toyota') !== false) {
                            $targetTableName = 'toyota_tbl';
                        } elseif (strpos($siteName->name, 'San Roque') !== false) {
                            $targetTableName = 'roque_tbl';
                        } elseif (strpos($siteName->name, 'Manga') !== false) {
                            $targetTableName = 'manga_tbl';
                        } elseif (strpos($siteName->name, 'Quibal') !== false) {
                            $targetTableName = 'quibal_tbl';
                        } else {
                            $targetTableName = 'toyota_tbl';
                        }
                    }
                }
            } else {
                // For other protected areas, use their specific tables
                $tableMap = [
                    'BPLS' => 'batanes_tbl',
                    'FSNP' => 'fuyot_tbl',
                    'QPL' => 'quirino_tbl',
                    'PIPLS' => 'palaui_tbl',
                    'BWFR' => 'buaa_tbl',
                    'WWFR' => 'wangag_tbl',
                    'MPL' => 'magapit_tbl',
                    'MADUPAPA' => 'madupapa_tbl',
                    'SANMARIANO' => 'mariano_tbl',
                    'NSMNP' => 'madre_tbl',
                    'TWNP' => 'tumauini_tbl',
                    'BHNP' => 'bangan_tbl',
                    'SNM' => 'salinas_tbl',
                    'DWFR' => 'dupax_tbl',
                    'CPL' => 'casecnan_tbl',
                    'DNP' => 'dipaniong_tbl',
                ];
                
                $targetTableName = $tableMap[$protectedArea->code] ?? null;
            }

            // If table needs to change, delete from old table and create in new table
            if ($targetTableName && $targetTableName !== $currentTableName) {
                // Delete from current table
                $observation->delete();
                
                // Create in new table
                $targetModel = $this->getModelByTableName($targetTableName);
                if ($targetModel) {
                    $targetModel::create($validated);
                }
            } else {
                // Update in same table
                $observation->update($validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Observation updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating observation: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the observation.'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new observation.
     */
    public function create()
    {
        // Get all protected areas for the dropdown
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        
        // Get other form data
        $bioGroups = ['fauna' => 'Fauna', 'flora' => 'Flora'];
        $semesters = [1 => '1st', 2 => '2nd'];
        $years = PatrolYearHelper::getYears();
        
        return view('pages.species_observations.create', compact(
            'protectedAreas',
            'bioGroups',
            'semesters',
            'years'
        ));
    }

    /**
     * Store a newly created observation with conditional saving logic.
     */
    public function store(Request $request)
    {
        try {
            // Accept site_id alias from frontend and normalize to site_name_id.
            if ($request->has('site_id') && !$request->has('site_name_id')) {
                $request->merge(['site_name_id' => $request->site_id]);
            }

            // Debug: verify payload (remove or comment in production if not needed)
            Log::info('Species observation store payload', $request->only([
                'transaction_code', 'station_code', 'protected_area_id', 'site_name_id', 'site_id',
                'patrol_year', 'patrol_semester', 'bio_group', 'common_name', 'scientific_name', 'recorded_count'
            ]));

            // Normalize site_name_id: treat "0" or empty as null
            if ($request->site_name_id === '0' || $request->site_name_id === '' || $request->site_name_id === null) {
                $request->merge(['site_name_id' => null]);
            }

            $validated = $request->validate([
                'protected_area_id' => 'required|exists:protected_areas,id',
                'site_name_id' => [
                    'nullable',
                    Rule::exists('site_names', 'id')->where('protected_area_id', $request->protected_area_id),
                ],
                'transaction_code' => 'required|string|max:50',
                'station_code' => 'required|string|max:60',
                'patrol_year' => 'required|integer|min:2000|max:2100',
                'patrol_semester' => 'required|integer|in:1,2',
                'bio_group' => 'required|in:fauna,flora',
                'common_name' => 'required|string|max:150',
                'scientific_name' => 'required|string|max:200',
                'recorded_count' => 'required|integer|min:0',
            ], [], [
                'protected_area_id' => 'protected area',
                'site_name_id' => 'site',
                'transaction_code' => 'transaction code',
                'station_code' => 'station code',
                'patrol_year' => 'patrol year',
                'patrol_semester' => 'patrol semester',
                'bio_group' => 'bio group',
                'common_name' => 'common name',
                'scientific_name' => 'scientific name',
                'recorded_count' => 'recorded count',
            ]);

            // Get the protected area
            $protectedArea = ProtectedArea::find($validated['protected_area_id']);
            if (!$protectedArea) {
                return response()->json([
                    'success' => false,
                    'message' => 'Protected area not found.'
                ], 422);
            }

            $siteValidationError = $this->validateProtectedAreaSiteSelection($validated, $protectedArea);
            if ($siteValidationError) {
                return $siteValidationError;
            }
            
            // Enhanced validation: Check if site belongs to selected protected area
            if (!empty($validated['site_name_id'])) {
                $siteName = SiteName::find($validated['site_name_id']);
                
                if (!$siteName) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected site not found.'
                    ], 422);
                }
                
                if ($siteName->protected_area_id != $validated['protected_area_id']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected site does not belong to the selected protected area.'
                    ], 422);
                }

                // Additional validation: Ensure Toyota observations only go to PPLS
                if ($protectedArea->code !== 'PPLS' && strpos($siteName->name, 'Toyota') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Toyota sites can only be assigned to Peñablanca Protected Landscape and Seascape (PPLS).'
                    ], 422);
                }

                // Additional validation: Ensure MPL sites only go to MPL
                if ($protectedArea->code !== 'MPL' && strpos($siteName->name, 'MPL') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'MPL sites can only be assigned to Magapit Protected Landscape (MPL).'
                    ], 422);
                }
            }
            
            // Initialize variables for conditional saving
            $targetModel = null;
            $stationCode = null;
            $tableName = null;

            // Check if a site is selected
            if (!empty($validated['site_name_id'])) {
                // Protected Area + Site: Save to the Site's table
                $siteName = SiteName::find($validated['site_name_id']);
                
                // Keep site linkage deterministic for counts/filters:
                // if site has a known station code mapping, use that station code.
                $stationCode = $siteName?->station_code ?: ($validated['station_code'] ?? null);
                if (!empty($stationCode)) {
                    $validated['station_code'] = $stationCode;
                }
                
                // Determine which table to use based on protected area and site
                if ($protectedArea->code === 'PPLS') {
                    // PPLS sites have specific tables
                    if (strpos($siteName->name, 'Toyota') !== false) {
                        $targetModel = ToyotaObservation::class;
                        $tableName = 'toyota_tbl';
                    } elseif (strpos($siteName->name, 'San Roque') !== false) {
                        $targetModel = SanRoqueObservation::class;
                        $tableName = 'roque_tbl';
                    } elseif (strpos($siteName->name, 'Manga') !== false) {
                        $targetModel = MangaObservation::class;
                        $tableName = 'manga_tbl';
                    } elseif (strpos($siteName->name, 'Quibal') !== false) {
                        $targetModel = QuibalObservation::class;
                        $tableName = 'quibal_tbl';
                    } else {
                        // Default for PPLS
                        $targetModel = ToyotaObservation::class;
                        $tableName = 'toyota_tbl';
                    }
                } elseif ($protectedArea->code === 'MPL') {
                    // MPL sites have specific tables
                    if (strpos($siteName->name, 'San Mariano') !== false) {
                        $targetModel = MarianoObservation::class;
                        $tableName = 'mariano_tbl';
                    } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                        $targetModel = MadupapaObservation::class;
                        $tableName = 'madupapa_tbl';
                    } else {
                        // Default for MPL (main Magapit site)
                        $targetModel = MagapitObservation::class;
                        $tableName = 'magapit_tbl';
                    }
                } else {
                    // For other protected areas, create/use site-specific tables
                    $siteTableName = $this->createSiteTableName($siteName->name, $siteName->id);
                    
                    // Create the table if it doesn't exist
                    $this->createSiteObservationTable($siteTableName, $siteName->id);
                    
                    $tableName = $siteTableName;
                    $targetModel = null; // Site-specific tables use direct DB operations, no model
                }
                
                Log::info('Saving to Site table', [
                    'protected_area' => $protectedArea->code,
                    'site_name' => $siteName->name,
                    'station_code' => $stationCode,
                    'table' => $tableName
                ]);
                
            } else {
                // Protected Area Only: Save to the Protected Area's main table
                $targetModel = $this->getModelByProtectedAreaCode($protectedArea->code);
                $tableName = $this->getTableNameByProtectedAreaCode($protectedArea->code);
                
                // Use a default station code for protected area level observations
                $stationCode = $validated['station_code'] ?? ($protectedArea->code . '-MAIN');
                $validated['station_code'] = $stationCode;
                
                Log::info('Saving to Protected Area table', [
                    'protected_area' => $protectedArea->code,
                    'protected_area_name' => $protectedArea->name,
                    'station_code' => $stationCode,
                    'table' => $tableName,
                    'target_model' => $targetModel
                ]);
            }

            // Enhanced error handling for missing model/table
            if (!$targetModel && !$tableName) {
                Log::warning('No mapping found for protected area: ' . $protectedArea->code . ', using fallback', [
                    'protected_area' => $protectedArea->toArray(),
                    'has_site' => !empty($validated['site_name_id'])
                ]);
                
                // Fallback: Use the main species observations table
                $targetModel = BmsSpeciesObservation::class;
                $tableName = 'species_observations';
                $validated['station_code'] = $validated['station_code'] ?? 'FALLBACK-' . $protectedArea->code;
                
                Log::info('Using fallback table for unmapped protected area', [
                    'protected_area' => $protectedArea->code,
                    'fallback_table' => $tableName,
                    'fallback_model' => $targetModel
                ]);
            }
            
            // Only check for target model if we're not using a site-specific table
            if (!$targetModel && strpos($tableName ?? '', '_site_tbl') === false) {
                Log::error('Target model not found for table: ' . $tableName, [
                    'protected_area' => $protectedArea->code,
                    'table_name' => $tableName,
                    'is_site_table' => false
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Target model not found for table: ' . $tableName . '. Please contact administrator.',
                    'debug_info' => [
                        'protected_area_code' => $protectedArea->code,
                        'table_name' => $tableName,
                        'is_site_table' => false
                    ]
                ], 500);
            }

            // Prepare the data for saving
            $observationData = [
                'protected_area_id' => $validated['protected_area_id'],
                'transaction_code' => $validated['transaction_code'],
                'station_code' => $validated['station_code'],
                'patrol_year' => $validated['patrol_year'],
                'patrol_semester' => $validated['patrol_semester'],
                'bio_group' => $validated['bio_group'],
                'common_name' => $validated['common_name'],
                'scientific_name' => $validated['scientific_name'] ?? null,
                'recorded_count' => $validated['recorded_count'],
            ];

            // Save the observation using direct database operations for dynamic tables
            if (strpos($tableName, '_site_tbl') !== false) {
                // For site-specific tables, use direct DB operations (no model needed)
                try {
                    $observationId = DB::table($tableName)->insertGetId($observationData);
                    
                    Log::info('Observation saved successfully to site table', [
                        'id' => $observationId,
                        'table' => $tableName,
                        'protected_area_id' => $observationData['protected_area_id'],
                        'station_code' => $observationData['station_code']
                    ]);
                    
                    // Create a mock observation object for the response
                    $observation = (object) array_merge($observationData, [
                        'id' => $observationId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save to site table: ' . $e->getMessage());
                    throw new \Exception('Failed to save observation to site table: ' . $e->getMessage());
                }
            } else {
                // For existing model tables, use the model
                try {
                    if (!$targetModel) {
                        throw new \Exception('Target model not found for table: ' . $tableName);
                    }
                    
                    $observation = $targetModel::create($observationData);
                    
                    Log::info('Observation saved successfully', [
                        'id' => $observation->id,
                        'table' => $tableName,
                        'protected_area_id' => $observation->protected_area_id,
                        'station_code' => $observation->station_code
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save to model table: ' . $e->getMessage());
                    throw new \Exception('Failed to save observation: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Observation added successfully!',
                'observation' => [
                    'id' => $observation->id,
                    'table_name' => $tableName,
                    'protected_area_id' => $observation->protected_area_id,
                    'station_code' => $observation->station_code,
                    'common_name' => $observation->common_name,
                    'scientific_name' => $observation->scientific_name,
                    'recorded_count' => $observation->recorded_count,
                    'patrol_year' => $observation->patrol_year,
                    'patrol_semester' => $observation->patrol_semester,
                    'bio_group' => $observation->bio_group,
                    'created_at' => $observation->created_at,
                ],
                'refresh_counts' => true // Flag to trigger count refreshes on frontend
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in store', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saving observation: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's a database/table error
            if (strpos($e->getMessage(), 'Table') !== false || strpos($e->getMessage(), 'SQL') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'debug_info' => [
                        'error_type' => 'database',
                        'original_message' => $e->getMessage()
                    ]
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the observation.',
                'debug_info' => [
                    'error_type' => 'general',
                    'original_message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Create a safe table name from site name
     */
    private function createSiteTableName(string $siteName, int $siteId): string
    {
        // Extract first few words and convert to safe format
        $words = explode(' ', $siteName);
        $safeName = '';
        
        // Take first 2-3 words, max 8 characters each
        $wordCount = min(3, count($words));
        for ($i = 0; $i < $wordCount; $i++) {
            $word = preg_replace('/[^a-zA-Z0-9]/', '', $words[$i]);
            $safeName .= strtolower(substr($word, 0, 8));
        }
        
        // If too short, use site ID
        if (strlen($safeName) < 3) {
            $safeName = 'site' . $siteId;
        }
        
        // Add site_tbl suffix
        return $safeName . '_site_tbl';
    }

    /**
     * Create observation table for the protected area site
     */
    private function createSiteObservationTable(string $tableName, int $siteId): void
    {
        try {
            Log::info("Creating site observation table: {$tableName}");
            
            // Check if table already exists
            if (Schema::hasTable($tableName)) {
                Log::info("Site observation table {$tableName} already exists, checking schema...");
                
                // Check if required columns exist
                $requiredColumns = [
                    'protected_area_id',
                    'transaction_code', 
                    'station_code',
                    'patrol_year',
                    'patrol_semester',
                    'bio_group',
                    'common_name',
                    'scientific_name',
                    'recorded_count'
                ];
                
                $missingColumns = [];
                foreach ($requiredColumns as $column) {
                    if (!Schema::hasColumn($tableName, $column)) {
                        $missingColumns[] = $column;
                    }
                }
                
                if (!empty($missingColumns)) {
                    Log::warning("Table {$tableName} is missing columns: " . implode(', ', $missingColumns));
                    
                    // Drop and recreate the table to ensure proper schema
                    Schema::dropIfExists($tableName);
                    Log::info("Dropped incomplete table {$tableName}, will recreate...");
                } else {
                    Log::info("Table {$tableName} has all required columns, using existing table.");
                    return;
                }
            }

            // Create the table with complete schema
            Schema::create($tableName, function (Blueprint $table) use ($siteId) {
                $table->id();
                
                // Foreign key to protected areas
                $table->unsignedBigInteger('protected_area_id');
                
                // Standard observation columns
                $table->string('transaction_code', 50);
                $table->string('station_code', 60);
                $table->year('patrol_year');
                $table->unsignedTinyInteger('patrol_semester'); // 1 or 2
                $table->enum('bio_group', ['fauna', 'flora']);
                $table->string('common_name', 150);
                $table->string('scientific_name', 200)->nullable();
                $table->unsignedInteger('recorded_count');
                
                $table->timestamps();
                
                // Foreign key constraint
                $table->foreign('protected_area_id')
                      ->references('id')
                      ->on('protected_areas')
                      ->onDelete('cascade');
            });
            
            Log::info("Successfully created site observation table: {$tableName}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create site observation table {$tableName}: " . $e->getMessage());
            
            // Don't throw the error - log it and continue
            // The observation was still created successfully
            return;
        }
    }

    /**
     * Get model class by protected area code
     */
    private function getModelByProtectedAreaCode(string $code): ?string
    {
        $modelMap = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => MagapitObservation::class, // Main MPL table
            'PIPLS' => PalauiObservation::class,
            'QPL' => QuirinoObservation::class,
            'WWFR' => WangagObservation::class,
            'NSMNP' => MadreObservation::class,
            'TWNP' => TumauiniObservation::class,
            'BHNP' => BanganObservation::class,
            'SNM' => SalinasObservation::class,
            'DWFR' => DupaxObservation::class,
            'CPL' => CasecnanObservation::class,
            'DNP' => DipaniongObservation::class,
        ];

        $model = $modelMap[$code] ?? null;
        
        if (!$model) {
            Log::warning('No model mapping found for protected area code: ' . $code, [
                'code' => $code,
                'available_codes' => array_keys($modelMap)
            ]);
        }
        
        return $model;
    }

    /**
     * Get table name by protected area code
     */
    private function getTableNameByProtectedAreaCode(string $code): ?string
    {
        $tableMap = [
            'BPLS' => 'batanes_tbl',
            'BWFR' => 'buaa_tbl',
            'FSNP' => 'fuyot_tbl',
            'MPL' => 'magapit_tbl', // Main MPL table
            'PIPLS' => 'palaui_tbl',
            'QPL' => 'quirino_tbl',
            'WWFR' => 'wangag_tbl',
            'NSMNP' => 'madre_tbl',
            'TWNP' => 'tumauini_tbl',
            'BHNP' => 'bangan_tbl',
            'SNM' => 'salinas_tbl',
            'DWFR' => 'dupax_tbl',
            'CPL' => 'casecnan_tbl',
            'DNP' => 'dipaniong_tbl',
        ];

        $table = $tableMap[$code] ?? null;
        
        if (!$table) {
            Log::warning('No table mapping found for protected area code: ' . $code, [
                'code' => $code,
                'available_codes' => array_keys($tableMap)
            ]);
        }
        
        return $table;
    }

    /**
     * Display the specified resource.
     */
    public function show(BaseObservation $speciesObservation)
    {
        $speciesObservation->load('protectedArea');
        return view('pages.species_observations.show', compact('speciesObservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BaseObservation $speciesObservation)
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        $bioGroups = ['fauna' => 'Fauna', 'flora' => 'Flora'];
        $semesters = [1 => '1st', 2 => '2nd'];

        return view('pages.species_observations.edit', compact(
            'speciesObservation',
            'protectedAreas',
            'bioGroups',
            'semesters'
        ));
    }

    /**
     * Get observation data for View modal
     */
    public function getObservationData(int $id)
    {
        try {
            $tableName = request()->query('table_name');
            
            // Check if table exists first
            if ($tableName && !Schema::hasTable($tableName)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Table does not exist: ' . $tableName,
                    'debug_info' => [
                        'observation_id' => $id,
                        'table_name' => $tableName,
                        'table_exists' => false
                    ]
                ], 404);
            }
            
            Log::info('getObservationData called with ID: ' . $id . ', tableName: ' . $tableName);
            
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            if (!$observation) {
                // Check what's actually in the table for debugging
                $tableInfo = [];
                if ($tableName && Schema::hasTable($tableName)) {
                    try {
                        $allRecords = DB::table($tableName)->limit(5)->get();
                        $tableInfo = [
                            'total_records' => DB::table($tableName)->count(),
                            'sample_records' => $allRecords->toArray()
                        ];
                    } catch (\Exception $e) {
                        $tableInfo = ['error' => $e->getMessage()];
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation not found.',
                    'debug_info' => [
                        'observation_id' => $id,
                        'table_name' => $tableName,
                        'table_exists' => $tableName ? Schema::hasTable($tableName) : null,
                        'table_info' => $tableInfo
                    ]
                ], 404);
            }

            Log::info('getObservationData - findObservationById returned:', [
                'found' => true,
                'class' => get_class($observation),
                'table' => $observation->table_name ?? $observation->getTable(),
                'protected_area_id' => $observation->protected_area_id,
                'common_name' => $observation->common_name,
                'station_code' => $observation->station_code
            ]);

            // Check if observation still exists in database before trying to load relationships
            if (isset($observation->exists) && !$observation->exists) {
                Log::error('Observation no longer exists with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation no longer exists.'
                ], 410);
            }

            // Load the protected area relationship (this might fail if observation was deleted)
            try {
                // For site-specific tables, we need to manually load the protected area
                if (isset($observation->table_name) && strpos($observation->table_name, '_site_tbl') !== false) {
                    // This is a site-specific table result, manually load protected area
                    $protectedArea = ProtectedArea::find($observation->protected_area_id);
                    $observation->protectedArea = $protectedArea;
                } else {
                    // This is an Eloquent model, use the relationship
                    $observation->load('protectedArea');
                }
            } catch (\Exception $e) {
                Log::error('Failed to load protected area relationship: ' . $e->getMessage());
                
                // Continue without the relationship if it fails
                Log::warning('Continuing without protected area data due to error: ' . $e->getMessage());
            }
            
            Log::info('getObservationData - Final observation data:', [
                'id' => $observation->id,
                'table_name' => $observation->table_name ?? $observation->getTable(),
                'protected_area_id' => $observation->protected_area_id,
                'protected_area_name' => $observation->protectedArea->name ?? 'Not loaded',
                'common_name' => $observation->common_name,
                'station_code' => $observation->station_code
            ]);
            
            return response()->json([
                'success' => true,
                'observation' => $observation
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching observation data: ' . $e->getMessage());
            Log::error('Exception details: ' . $e->getTraceAsString());
            Log::error('File: ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load observation data.',
                'debug_info' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        // Debug: Log the ID being deleted
        Log::info('Attempting to delete observation with ID: ' . $id);
        
        // Get the table name from the request if provided
        $tableName = $request->input('table_name');
        Log::info('Table name from request: ' . $tableName);
        
        try {
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            if (!$observation) {
                Log::error('Observation not found with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Species observation not found.'
                    ], 404);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Species observation not found.');
            }

            Log::info('Found observation in table: ' . (isset($observation->table_name) ? $observation->table_name : get_class($observation)));

            // Check if the observation can be deleted (verify it exists)
            if (isset($observation->exists) && !$observation->exists) {
                Log::error('Observation no longer exists with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Observation no longer exists.'
                    ], 410);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Observation no longer exists.');
            }

            // Perform the deletion - handle both model and site-specific table cases
            $deleted = false;
            if (isset($observation->table_name) && strpos($observation->table_name, '_site_tbl') !== false) {
                // Site-specific table - use direct DB deletion
                try {
                    $deleted = DB::table($observation->table_name)->where('id', $id)->delete();
                    Log::info('Deleted observation from site-specific table: ' . $observation->table_name . ' with ID: ' . $id);
                } catch (\Exception $e) {
                    Log::error('Failed to delete from site-specific table: ' . $e->getMessage());
                    $deleted = false;
                }
            } else {
                // Regular model table - use model deletion
                $deleted = $observation->delete();
            }
            
            if (!$deleted) {
                Log::error('Failed to delete observation with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to delete species observation.'
                    ], 500);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Failed to delete species observation.');
            }
            
            Log::info('Successfully deleted observation with ID: ' . $id . ' (table: ' . (isset($observation->table_name) ? $observation->table_name : 'model') . ')');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'deleted_id' => (int) $id,
                    'message' => 'Species observation deleted successfully.'
                ]);
            }
            
            return redirect()->route('species-observations.index')
                           ->with('success', 'Species observation deleted successfully.');
                           
        } catch (\Exception $e) {
            Log::error('Exception during delete: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'An unexpected error occurred while deleting the observation.'
                ], 500);
            }
            
            return redirect()->route('species-observations.index')
                           ->with('error', 'An unexpected error occurred while deleting the observation.');
        }
    }

    /**
     * Find observation by ID across all tables
     */
    private function findObservationById(int $id, ?string $tableName = null)
    {
        Log::info('findObservationById called with ID: ' . $id . ', tableName: ' . $tableName);
        
        // If table name is provided, try to find the specific model for that table FIRST
        if ($tableName) {
            // Check if it's a site-specific table (no model, use direct DB)
            if (strpos($tableName, '_site_tbl') !== false) {
                try {
                    // First check if table exists
                    if (!Schema::hasTable($tableName)) {
                        Log::error('Site-specific table does not exist: ' . $tableName);
                        return null;
                    }
                    
                    $observation = DB::table($tableName)->where('id', $id)->first();
                    if ($observation) {
                        Log::info('Found observation in site-specific table: ' . $tableName . ' with ID: ' . $id);
                        // Convert to object that behaves like a model
                        $observation->table_name = $tableName;
                        $observation->exists = true;
                        return $observation;
                    } else {
                        Log::warning('No observation found in site-specific table: ' . $tableName . ' with ID: ' . $id);
                    }
                } catch (\Exception $e) {
                    Log::error('Error searching site-specific table ' . $tableName . ': ' . $e->getMessage());
                    Log::error('Exception details: ' . $e->getTraceAsString());
                }
            } else {
                // Try to find the model for regular tables
                $model = $this->getModelByTableName($tableName);
                if ($model) {
                    try {
                        $observation = $model::find($id);
                        if ($observation) {
                            Log::info('Found observation in specified table model: ' . $model . ' with ID: ' . $id);
                            return $observation;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error searching model ' . $model . ': ' . $e->getMessage());
                    }
                }
            }
        }
        
        // Only check all models if no specific table was provided or not found in specified table
        Log::info('Table name not provided or not found in specified table, checking all models');
        
        $models = [
            BmsSpeciesObservation::class,
            FuyotObservation::class,
            QuirinoObservation::class,
            PalauiObservation::class,
            BauaObservation::class,
            WangagObservation::class,
            MagapitObservation::class,
            MadupapaObservation::class,
            MarianoObservation::class,
            MadreObservation::class,
            TumauiniObservation::class,
            BanganObservation::class,
            SalinasObservation::class,
            DupaxObservation::class,
            CasecnanObservation::class,
            DipaniongObservation::class,
            ToyotaObservation::class,
            SanRoqueObservation::class,
            MangaObservation::class,
            QuibalObservation::class,
        ];

        foreach ($models as $model) {
            $observation = $model::find($id);
            if ($observation) {
                Log::info('Found observation in model: ' . $model . ' with ID: ' . $id . ' (table: ' . $observation->getTable() . ')');
                return $observation;
            }
        }

        // If still not found, check all site-specific tables as last resort
        try {
            $siteTables = DB::select("SHOW TABLES LIKE '%_site_tbl'");
            foreach ($siteTables as $table) {
                $tableName = array_values((array)$table)[0];
                try {
                    $observation = DB::table($tableName)->where('id', $id)->first();
                    if ($observation) {
                        Log::info('Found observation in site-specific table: ' . $tableName . ' with ID: ' . $id);
                        // Convert to object that behaves like a model
                        $observation->table_name = $tableName;
                        $observation->exists = true;
                        return $observation;
                    }
                } catch (\Exception $e) {
                    Log::error('Error searching site-specific table ' . $tableName . ': ' . $e->getMessage());
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting site-specific tables: ' . $e->getMessage());
        }

        Log::error('Observation not found in any model or site-specific table with ID: ' . $id);
        return null;
    }

    /**
     * Get model class by table name
     */
    private function getModelByTableName(string $tableName): ?string
    {
        $tableModelMap = [
            'batanes_tbl' => BmsSpeciesObservation::class,
            'fuyot_tbl' => FuyotObservation::class,
            'quirino_tbl' => QuirinoObservation::class,
            'palaui_tbl' => PalauiObservation::class,
            'buaa_tbl' => BauaObservation::class,
            'wangag_tbl' => WangagObservation::class,
            'magapit_tbl' => MagapitObservation::class,
            'madupapa_tbl' => MadupapaObservation::class,
            'mariano_tbl' => MarianoObservation::class,
            'madre_tbl' => MadreObservation::class,
            'tumauini_tbl' => TumauiniObservation::class,
            'bangan_tbl' => BanganObservation::class,
            'salinas_tbl' => SalinasObservation::class,
            'dupax_tbl' => DupaxObservation::class,
            'casecnan_tbl' => CasecnanObservation::class,
            'dipaniong_tbl' => DipaniongObservation::class,
            'toyota_tbl' => ToyotaObservation::class,
            'roque_tbl' => SanRoqueObservation::class,
            'manga_tbl' => MangaObservation::class,
            'quibal_tbl' => QuibalObservation::class,
        ];

        return $tableModelMap[$tableName] ?? null;
    }

    /**
     * Handle export requests for different formats
     */
    private function handleExport(Request $request)
    {
        // Get the same filtered data as the index method but without pagination
        $query = null;
        $batanesQuery = $this->buildFilteredQuery(BmsSpeciesObservation::class, $request);
        $fuyotQuery = $this->buildFilteredQuery(FuyotObservation::class, $request);
        $quirinoQuery = $this->buildFilteredQuery(QuirinoObservation::class, $request);
        $palauiQuery = $this->buildFilteredQuery(PalauiObservation::class, $request);
        $buaaQuery = $this->buildFilteredQuery(BauaObservation::class, $request);
        $wangagQuery = $this->buildFilteredQuery(WangagObservation::class, $request);
        
        // For MPL, include all sub-tables
        $magapitQuery = $this->buildFilteredQuery(MagapitObservation::class, $request);
        $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
        $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
        
        // Check if we need to filter Mariano and Madupapa based on site selection
        if ($request->filled('protected_area_id') && $request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                if (strpos($siteName->name, 'San Mariano') !== false) {
                    $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    $madupapaQuery = null;
                } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                    $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    $marianoQuery = null;
                }
            }
        }
        
        // Handle "no_specific_site" selection for MPL
        if ($request->filled('protected_area_id') && $request->site_name === 'no_specific_site') {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea && $protectedArea->code === 'MPL') {
                $marianoQuery = null;
                $madupapaQuery = null;
            }
        }
        
        // Special handling for San Mariano and Madupapa sites
        if ($request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                if ($stationCode) {
                    if (strpos($siteName->name, 'San Mariano') !== false) {
                        $query = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                        $query = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    } else {
                        $query = null;
                    }
                }
            }
        } else {
            $query = null;
        }
        
        $madreQuery = $this->buildFilteredQuery(MadreObservation::class, $request);
        $tumauiniQuery = $this->buildFilteredQuery(TumauiniObservation::class, $request);
        $banganQuery = $this->buildFilteredQuery(BanganObservation::class, $request);
        $salinasQuery = $this->buildFilteredQuery(SalinasObservation::class, $request);
        $dupaxQuery = $this->buildFilteredQuery(DupaxObservation::class, $request);
        $casecnanQuery = $this->buildFilteredQuery(CasecnanObservation::class, $request);
        $dipaniongQuery = $this->buildFilteredQuery(DipaniongObservation::class, $request);
        
        // Add PPLS site-specific tables
        $toyotaQuery = $this->buildTableQuery('toyota_tbl', $request);
        $sanRoqueQuery = $this->buildTableQuery('roque_tbl', $request);
        $mangaQuery = $this->buildTableQuery('manga_tbl', $request);
        $quibalQuery = $this->buildTableQuery('quibal_tbl', $request);
        
        // Combine all queries with union
        if ($query) {
            $observations = $query->orderBy('patrol_year', 'desc')
                ->orderBy('patrol_semester', 'desc')
                ->orderBy('station_code')
                ->get();
        } else {
            $allQueries = [
                $batanesQuery, $fuyotQuery, $quirinoQuery, $palauiQuery, 
                $buaaQuery, $wangagQuery, $magapitQuery, $marianoQuery, 
                $madupapaQuery, $madreQuery, $tumauiniQuery, $banganQuery, 
                $salinasQuery, $dupaxQuery, $casecnanQuery, $dipaniongQuery, 
                $toyotaQuery, $sanRoqueQuery, $mangaQuery, $quibalQuery
            ];
            
            $allQueries = array_filter($allQueries, function($query) {
                return $query !== null;
            });
            
            $allQueries = array_values($allQueries);
            
            if (empty($allQueries)) {
                $observations = collect();
            } else {
                $observations = $allQueries[0];
                for ($i = 1; $i < count($allQueries); $i++) {
                    $observations = $observations->union($allQueries[$i]);
                }
                $observations = $observations->orderBy('patrol_year', 'desc')
                    ->orderBy('patrol_semester', 'desc')
                    ->orderBy('station_code')
                    ->get();
            }
        }

        // Load protected area relationships
        $observations->each(function ($observation) {
            $observation->load('protectedArea');
        });

        // Handle different export formats
        if ($request->has('print')) {
            return $this->exportPrint($observations, $request);
        } elseif ($request->has('excel')) {
            return $this->exportExcel($observations, $request);
        } elseif ($request->has('pdf')) {
            return $this->exportPdf($observations, $request);
        }
        
        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export to print-friendly view
     */
    private function exportPrint(\Illuminate\Support\Collection $observations, Request $request)
    {
        // Get filter information for title
        $filterInfo = $this->getFilterInfo($request);
        
        return view('pages.species_observations.print', compact('observations', 'filterInfo'));
    }

    /**
     * Export to Excel
     */
    private function exportExcel(\Illuminate\Support\Collection $observations, Request $request)
    {
        $filename = 'species-observations-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($observations) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($file, [
                'Protected Area',
                'Station Code', 
                'Transaction Code',
                'Patrol Year',
                'Patrol Semester',
                'Bio Group',
                'Common Name',
                'Scientific Name',
                'Count'
            ]);
            
            // Data rows
            foreach ($observations as $observation) {
                fputcsv($file, [
                    $observation->protectedArea->name ?? 'N/A',
                    $observation->station_code ?? 'N/A',
                    $observation->transaction_code ?? 'N/A',
                    $observation->patrol_year ?? 'N/A',
                    $observation->patrol_semester_text ?? 'N/A',
                    ucfirst($observation->bio_group ?? 'N/A'),
                    $observation->common_name ?? 'N/A',
                    $observation->scientific_name ?? 'N/A',
                    $observation->recorded_count ?? 0
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    private function exportPdf(\Illuminate\Support\Collection $observations, Request $request)
    {
        // Limit the number of records for PDF to prevent memory issues
        $maxRecords = 100;
        $totalRecords = $observations->count();
        
        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }
        
        $filename = 'species-observations-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Get filter information for title
        $filterInfo = $this->getFilterInfo($request);
        
        // Configure DomPDF for memory efficiency
        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions($options)
            ->loadView('pages.species_observations.pdf', compact('observations', 'filterInfo'));
        
        return $pdf->download($filename);
    }

    /**
     * Get filter information for export titles
     */
    private function getFilterInfo(Request $request)
    {
        $filterInfo = [];
        
        // Protected Area filter
        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea) {
                $filterInfo['protected_area'] = $protectedArea->name;
            }
        }
        
        // Site Name filter
        if ($request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $filterInfo['site_name'] = $siteName->name;
            }
        } elseif ($request->site_name === 'no_specific_site') {
            $filterInfo['site_name'] = 'No Specific Site';
        }
        
        // Bio Group filter
        if ($request->filled('bio_group')) {
            $filterInfo['bio_group'] = ucfirst($request->bio_group);
        }
        
        // Year filter
        if ($request->filled('patrol_year')) {
            $filterInfo['patrol_year'] = $request->patrol_year;
        }
        
        // Semester filter
        if ($request->filled('patrol_semester')) {
            $semesters = [1 => '1st', 2 => '2nd'];
            $filterInfo['patrol_semester'] = $semesters[$request->patrol_semester] ?? $request->patrol_semester;
        }
        
        return $filterInfo;
    }
}
