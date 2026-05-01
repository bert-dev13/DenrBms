<?php

namespace App\Http\Controllers;

use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Services\DynamicTableService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;

class ProtectedAreaController extends Controller
{
    /**
     * Count observations for a site from both site-specific tables and station-coded tables.
     */
    private function countObservationsForSite(SiteName $site, array $allObservationTables): int
    {
        $count = 0;
        $siteTableName = $this->createSafeSiteTableName($site->name, $site->id);

        if (Schema::hasTable($siteTableName)) {
            try {
                $count += DB::table($siteTableName)->count();
            } catch (\Exception $e) {
                Log::error("Error counting observations in site table {$siteTableName}: " . $e->getMessage());
            }
        }

        $stationCodes = $site->all_station_codes ?? [];
        if (empty($stationCodes)) {
            return $count;
        }

        foreach ($allObservationTables as $tableName) {
            if ($tableName === $siteTableName || !Schema::hasColumn($tableName, 'station_code')) {
                continue;
            }

            try {
                $count += DB::table($tableName)
                    ->whereIn('station_code', $stationCodes)
                    ->count();
            } catch (\Exception $e) {
                Log::error("Error counting station-coded observations in {$tableName}: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function index(Request $request)
    {
        // Handle export requests
        if ($request->has('export') || $request->has('print')) {
            return $this->handleExport($request);
        }
        
        // Handle filters
        $statusFilter = $request->input('status'); // active, no_data, or null
        $sort = $request->input('sort', 'name');   // name or code

        // Base query for all protected areas (we'll filter in collection so we can use
        // the computed species_observations_count attribute)
        $allProtectedAreas = ProtectedArea::withTotalObservationsCount()
            ->orderBy($sort === 'code' ? 'code' : 'name')
            ->get();

        // Apply status filter in PHP so we can rely on the computed observation count
        $filteredAreas = $allProtectedAreas->filter(function ($area) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return $area->species_observations_count > 0;
            }
            if ($statusFilter === 'no_data') {
                return $area->species_observations_count == 0;
            }
            return true;
        });

        // Apply search filter if provided (trim, case-insensitive)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim((string) $request->search));
            $filteredAreas = $filteredAreas->filter(function ($area) use ($searchTerm) {
                return strpos(strtolower($area->name ?? ''), $searchTerm) !== false
                    || strpos(strtolower($area->code ?? ''), $searchTerm) !== false;
            })->values();
        }

        // Manual pagination for the filtered collection
        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredAreas->forPage($currentPage, $perPage)->values();

        $protectedAreas = new LengthAwarePaginator(
            $currentItems,
            $filteredAreas->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Calculate total observations across all tables
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

        // Species Tracked: unique scientific_name across all tables (single source of truth)
        $speciesDiversity = DynamicTableService::getUniqueSpeciesCount();

        // Calculate active areas using all protected areas, not just paginated ones
        $allProtectedAreas = ProtectedArea::all();
        $activeAreasCount = 0;
        foreach ($allProtectedAreas as $area) {
            if ($area->getTotalObservationsCount() > 0) {
                $activeAreasCount++;
            }
        }

        $stats = [
            'total_areas' => ProtectedArea::count(),
            'active_areas' => $activeAreasCount,
            'total_observations' => $totalObservations,
            'species_diversity' => $speciesDiversity,
            'total_sites' => SiteName::count(),
        ];

        return view('pages.protected_areas.index', compact('protectedAreas', 'stats', 'statusFilter', 'sort'));
    }

    /**
     * Store a newly created protected area.
     */
    public function store(Request $request)
    {
        // Log request details for debugging
        Log::info('ProtectedArea store called', [
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'expects_json' => $request->expectsJson(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'has_code' => $request->has('code'),
            'has_name' => $request->has('name'),
            'headers' => [
                'x-requested-with' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'x-csrf-token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing'
            ]
        ]);

        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        // Validate the request
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:protected_areas,code',
                'name' => 'required|string|max:255',
            ]);
            
            Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        try {
            // Create the protected area
            $protectedArea = ProtectedArea::create($validated);
            
            // Create a safe table name
            $tableName = $this->createSafeTableName($validated['code']);
            
            // Create the observation table
            $this->createObservationTable($tableName, $protectedArea->id);
            
            // Get observation count for response
            $observationCount = $protectedArea->getTotalObservationsCount();
            $protectedArea->species_observations_count = $observationCount;

            // Return JSON response for AJAX requests
            if ($isAjax) {
                Log::info('Returning JSON response', [
                    'success' => true,
                    'area_id' => $protectedArea->id,
                    'area_code' => $protectedArea->code
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area created successfully with observation table.',
                    'area' => $protectedArea,
                    'table_name' => $tableName
                ]);
            }

            Log::info('Not AJAX, returning redirect');
            // Return redirect for regular form submissions
            return redirect()
                ->route('protected-areas.index')
                ->with('success', 'Protected area created successfully.');
                
        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to create protected area: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->with('error', 'Failed to create protected area.')
                ->withInput();
        }
    }

    /**
     * Create a safe table name from protected area code
     */
    private function createSafeTableName(string $code): string
    {
        // Use DynamicTableService for consistent table naming
        return DynamicTableService::getTableNameForProtectedArea($code);
    }

    /**
     * Create observation table for the protected area
     */
    private function createObservationTable(string $tableName, int $protectedAreaId): void
    {
        try {
            Log::info("Creating table: {$tableName}");
            
            // Check if table already exists
            if (Schema::hasTable($tableName)) {
                Log::info("Table {$tableName} already exists, skipping creation.");
                return;
            }

            // Create the table
            Schema::create($tableName, function (Blueprint $table) use ($protectedAreaId) {
                $table->id();
                
                // Foreign key to protected areas
                $table->unsignedBigInteger('protected_area_id');
                
                // Standard observation columns
                $table->string('station_code', 60);
                $table->year('patrol_year');
                $table->unsignedTinyInteger('patrol_semester'); // 1 or 2
                $table->enum('bio_group', ['fauna', 'flora']);
                $table->string('common_name', 150);
                $table->string('scientific_name', 200)->nullable();
                $table->unsignedInteger('recorded_count');
                
                $table->timestamps();
                
                // Foreign key constraint - make it nullable for now to avoid issues
                $table->foreign('protected_area_id')
                      ->references('id')
                      ->on('protected_areas')
                      ->onDelete('cascade');
            });
            
            Log::info("Successfully created observation table: {$tableName}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create observation table {$tableName}: " . $e->getMessage());
            
            // Don't throw the error - log it and continue
            // The protected area was still created successfully
            return;
        }
    }

    /**
     * Create default site entry for the protected area
     */
    private function createDefaultSite(ProtectedArea $protectedArea): void
    {
        try {
            DB::table('site_names')->insert([
                'name' => $protectedArea->name . ' - Main Site',
                'protected_area_id' => $protectedArea->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info("Created default site for protected area: {$protectedArea->name}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create default site: " . $e->getMessage());
            // Don't throw - the protected area was still created successfully
        }
    }

    public function sites(Request $request)
    {
        // Handle export requests
        if ($request->has('export') || $request->has('print')) {
            return $this->handleSitesExport($request);
        }
        
        // Handle filters
        $statusFilter = $request->input('status'); // active, no_data, or null
        $sort = $request->input('sort', 'name');   // name or code
        
        // Base query for all sites with their protected areas
        $query = SiteName::with('protectedArea');
        $sortDirection = 'asc';
        
        // Apply sorting
        if ($sort === 'protected_area') {
            // Sort by related protected area name, then by site name
            $query->leftJoin('protected_areas', 'site_names.protected_area_id', '=', 'protected_areas.id')
                  ->select('site_names.*')
                  ->orderBy('protected_areas.name', $sortDirection)
                  ->orderBy('site_names.name', $sortDirection);
        } else {
            $query->orderBy($sort, $sortDirection);
        }
        
        // Get all sites for filtering (we'll filter in collection so we can use the computed observation count)
        $allSites = $query->get();

        // Deduplicate by protected area + normalized site name so duplicate records
        // do not render twice in the table/modal flows.
        $allSites = $allSites
            ->unique(function ($site) {
                $protectedAreaId = $site->protected_area_id ?? 'null';
                $normalizedName = strtolower(trim((string) ($site->name ?? '')));

                return $protectedAreaId . '|' . $normalizedName;
            })
            ->values();
        
        // Add observation counts to each site from its site-specific table
        $tables = DynamicTableService::getAllObservationTables();
        
        foreach ($allSites as $site) {
            $siteObservationCount = $this->countObservationsForSite($site, $tables);
            $site->species_observations_count = $siteObservationCount;
        }
        
        // Apply status filter in PHP so we can rely on the computed observation count
        $filteredSites = $allSites->filter(function ($site) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return $site->species_observations_count > 0;
            }
            if ($statusFilter === 'no_data') {
                return $site->species_observations_count == 0;
            }
            return true;
        });

        // Apply search filter if provided (trim, case-insensitive)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim((string) $request->search));
            $filteredSites = $filteredSites->filter(function ($site) use ($searchTerm) {
                $nameMatch = strpos(strtolower($site->name ?? ''), $searchTerm) !== false;
                $areaMatch = $site->protectedArea && strpos(strtolower($site->protectedArea->name ?? ''), $searchTerm) !== false;
                return $nameMatch || $areaMatch;
            })->values();
        }
        
        // Manual pagination for the filtered collection
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredSites->forPage($currentPage, $perPage)->values();
        
        $siteNames = new LengthAwarePaginator(
            $currentItems,
            $filteredSites->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Calculate total observations across all tables
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Species Tracked: unique scientific_name across all tables (single source of truth)
        $speciesDiversity = DynamicTableService::getUniqueSpeciesCount();

        $stats = [
            'total_areas' => ProtectedArea::count(),
            'total_sites' => SiteName::count(),
            'total_observations' => $totalObservations,
            'species_diversity' => $speciesDiversity,
        ];

        return view('pages.protected_area_sites.index', compact('siteNames', 'stats', 'statusFilter', 'sort'));
    }

    /**
     * Display the specified protected area.
     */
    public function show(ProtectedArea $protectedArea)
    {
        $protectedArea->loadCount('speciesObservations');
        return view('pages.protected_areas.show', compact('protectedArea'));
    }

    /**
     * Get protected area data for View modal (AJAX)
     */
    public function getProtectedAreaData(int $id)
    {
        try {
            $protectedArea = ProtectedArea::findOrFail($id);
            
            // Get observation count
            $observationCount = $protectedArea->getTotalObservationsCount();
            
            return response()->json([
                'success' => true,
                'protectedArea' => [
                    'id' => $protectedArea->id,
                    'code' => $protectedArea->code,
                    'name' => $protectedArea->name,
                    'species_observations_count' => $observationCount,
                    'created_at' => $protectedArea->created_at,
                    'updated_at' => $protectedArea->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching protected area data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load protected area data.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified protected area.
     */
    public function edit(ProtectedArea $protectedArea)
    {
        return view('pages.protected_areas.edit', compact('protectedArea'));
    }

    /**
     * Update the specified protected area in storage.
     */
    public function update(Request $request, ProtectedArea $protectedArea)
    {
        // Log request details for debugging
        Log::info('ProtectedArea update called', [
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'expects_json' => $request->expectsJson(),
            'area_id' => $protectedArea->id,
            'current_code' => $protectedArea->code,
            'new_code' => $request->input('code'),
            'has_code' => $request->has('code'),
            'has_name' => $request->has('name'),
            'headers' => [
                'x-requested-with' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'x-csrf-token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing'
            ]
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:protected_areas,code,' . $protectedArea->id,
            'name' => 'required|string|max:255',
        ]);
        
        Log::info('Validation passed', ['validated' => $validated]);

        try {
            $protectedArea->update($validated);
            
            // Get the observation count for the response
            $observationCount = $protectedArea->getTotalObservationsCount();
            $protectedArea->species_observations_count = $observationCount;
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area updated successfully.',
                    'area' => $protectedArea
                ]);
            }
            
            return redirect()->route('protected-areas.index')
                ->with('success', 'Protected area updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in update', ['errors' => $e->errors()]);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating protected area: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to update protected area.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update protected area.')
                ->withInput();
        }
    }

    /**
     * Remove the specified protected area from storage.
     */
    public function destroy(ProtectedArea $protectedArea)
    {
        try {
            // Get observation count for response
            $observationCount = $protectedArea->getTotalObservationsCount();
            
            // Get the table name before deletion
            $tableName = DynamicTableService::getTableNameForProtectedArea($protectedArea->code);
            
            // First drop the observation table to avoid foreign key constraints
            DynamicTableService::dropObservationTable($protectedArea->code);
            
            // Then delete the protected area
            $protectedArea->delete();
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area deleted successfully.',
                    'table_dropped' => $tableName
                ]);
            }
            
            return redirect()->route('protected-areas.index')
                ->with('success', 'Protected area deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a foreign key constraint violation
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                $message = 'Cannot delete this protected area because it has related records in the system. Please delete all related species observations first.';
                
                if (request()->expectsJson() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => $message
                    ], 422);
                }
                
                return back()->with('error', $message);
            }
            
            Log::error('Error deleting protected area: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete protected area.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete protected area.');
        } catch (\Exception $e) {
            Log::error('Error deleting protected area: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete protected area.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete protected area.');
        }
    }

    /**
     * Display the specified protected area site.
     */
    public function showSite(SiteName $siteName)
    {
        $siteName->load('protectedArea');
        return view('pages.protected_area_sites.show', compact('siteName'));
    }

    /**
     * Get site data for View modal (AJAX)
     */
    public function getSiteData(int $id)
    {
        try {
            $siteName = SiteName::with('protectedArea')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'siteName' => [
                    'id' => $siteName->id,
                    'name' => $siteName->name,
                    'protected_area_id' => $siteName->protected_area_id,
                    'protected_area' => $siteName->protectedArea ? [
                        'id' => $siteName->protectedArea->id,
                        'code' => $siteName->protectedArea->code,
                        'name' => $siteName->protectedArea->name,
                    ] : null,
                    'status' => $siteName->protectedArea ? 'Active' : 'Unassigned',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching site data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load site data.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified protected area site.
     */
    public function editSite(SiteName $siteName)
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        return view('pages.protected_area_sites.edit', compact('siteName', 'protectedAreas'));
    }

    /**
     * Update the specified protected area site in storage.
     */
    public function updateSite(Request $request, SiteName $siteName)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('site_names', 'name')
                    ->where(function ($query) use ($request) {
                        $query->where('protected_area_id', $request->input('protected_area_id'));
                    })
                    ->ignore($siteName->id),
            ],
            'protected_area_id' => 'nullable|exists:protected_areas,id',
        ]);

        try {
            $siteName->update($validated);
            
            // Load protected area relationship for response
            $siteName->load('protectedArea');
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site updated successfully.',
                    'siteName' => [
                        'id' => $siteName->id,
                        'name' => $siteName->name,
                        'protected_area_id' => $siteName->protected_area_id,
                        'protected_area' => $siteName->protectedArea ? [
                            'id' => $siteName->protectedArea->id,
                            'name' => $siteName->protectedArea->name,
                            'code' => $siteName->protectedArea->code,
                        ] : null,
                        'created_at' => $siteName->created_at,
                        'updated_at' => $siteName->updated_at,
                    ]
                ]);
            }
            
            return redirect()->route('protected-area-sites.index')
                ->with('success', 'Site updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating site: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to update site.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update site.')
                ->withInput();
        }
    }

    /**
     * Create a safe table name from site name
     */
    private function createSafeSiteTableName(string $siteName, int $siteId): string
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

            // Create the table with complete schema (matching SpeciesObservationController)
            Schema::create($tableName, function (Blueprint $table) use ($siteId) {
                $table->id();
                
                // Foreign key to protected areas (consistent with SpeciesObservationController)
                $table->unsignedBigInteger('protected_area_id');
                
                // Standard observation columns (must stay aligned with SpeciesObservationController)
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
            // The site was still created successfully
            return;
        }
    }

    /**
     * Store a newly created protected area site.
     */
    public function storeSite(Request $request)
    {
        // Log request details for debugging
        Log::info('ProtectedAreaSite store called', [
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'expects_json' => $request->expectsJson(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'has_name' => $request->has('name'),
            'has_protected_area_id' => $request->has('protected_area_id'),
            'headers' => [
                'x-requested-with' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'x-csrf-token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing'
            ]
        ]);

        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        // Validate the request
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('site_names', 'name')
                        ->where(function ($query) use ($request) {
                            $query->where('protected_area_id', $request->input('protected_area_id'));
                        }),
                ],
                'protected_area_id' => 'nullable|exists:protected_areas,id',
            ]);
            
            Log::info('Site validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Site validation failed', ['errors' => $e->errors()]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        try {
            // Create the site
            $siteName = SiteName::create($validated);
            
            // Load protected area relationship for response
            $siteName->load('protectedArea');
            
            // Create a safe table name based on site name
            $tableName = $this->createSafeSiteTableName($siteName->name, $siteName->id);
            
            // Create the observation table for the site
            $this->createSiteObservationTable($tableName, $siteName->id);

            // Return JSON response for AJAX requests
            if ($isAjax) {
                Log::info('Returning site JSON response', [
                    'success' => true,
                    'site_id' => $siteName->id,
                    'site_name' => $siteName->name,
                    'table_name' => $tableName
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Site created successfully with observation table.',
                    'siteName' => [
                        'id' => $siteName->id,
                        'name' => $siteName->name,
                        'protected_area_id' => $siteName->protected_area_id,
                        'protected_area' => $siteName->protectedArea ? [
                            'id' => $siteName->protectedArea->id,
                            'name' => $siteName->protectedArea->name,
                            'code' => $siteName->protectedArea->code,
                        ] : null,
                        'created_at' => $siteName->created_at,
                        'updated_at' => $siteName->updated_at,
                    ],
                    'table_name' => $tableName
                ]);
            }

            Log::info('Not AJAX, returning redirect');
            // Return redirect for regular form submissions
            return redirect()
                ->route('protected-area-sites.index')
                ->with('success', 'Site created successfully with observation table.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create site: ' . $e->getMessage());
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to create site: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->with('error', 'Failed to create site.')
                ->withInput();
        }
    }

    /**
     * Remove the specified protected area site from storage.
     */
    public function destroySite(SiteName $siteName)
    {
        try {
            // Get the site table name before deletion
            $tableName = $this->createSafeSiteTableName($siteName->name, $siteName->id);
            
            // Delete the site
            $siteName->delete();
            
            // Try to drop the observation table
            $this->dropSiteObservationTable($tableName);
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site deleted successfully.',
                    'table_dropped' => $tableName
                ]);
            }
            
            return redirect()->route('protected-area-sites.index')
                ->with('success', 'Site deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting site: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete site.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete site.');
        }
    }

    /**
     * Drop observation table for a deleted protected area
     */
    private function dropObservationTable(string $tableName): void
    {
        try {
            if (Schema::hasTable($tableName)) {
                Schema::dropIfExists($tableName);
                Log::info("Successfully dropped observation table: {$tableName}");
            } else {
                Log::info("Observation table {$tableName} does not exist, skipping drop.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to drop observation table {$tableName}: " . $e->getMessage());
            // Don't throw - the protected area was still deleted successfully
        }
    }

    /**
     * Drop observation table for a deleted site
     */
    private function dropSiteObservationTable(string $tableName): void
    {
        try {
            if (Schema::hasTable($tableName)) {
                Schema::dropIfExists($tableName);
                Log::info("Successfully dropped site observation table: {$tableName}");
            } else {
                Log::info("Site observation table {$tableName} does not exist, skipping drop.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to drop site observation table {$tableName}: " . $e->getMessage());
            // Don't throw - the site was still deleted successfully
        }
    }

    /**
     * Handle export requests for different formats
     */
    private function handleExport(Request $request)
    {
        // Get the same filtered data as the index method but without pagination
        $statusFilter = $request->input('status');
        $sort = $request->input('sort', 'name');

        // Base query for all protected areas
        $allProtectedAreas = ProtectedArea::withTotalObservationsCount()
            ->orderBy($sort === 'code' ? 'code' : 'name')
            ->get();

        // Apply status filter in PHP so we can rely on the computed observation count
        $filteredAreas = $allProtectedAreas->filter(function ($area) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return $area->species_observations_count > 0;
            }
            if ($statusFilter === 'no_data') {
                return $area->species_observations_count == 0;
            }
            return true;
        });

        // Apply search filter if provided (trim, case-insensitive)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim((string) $request->search));
            $filteredAreas = $filteredAreas->filter(function ($area) use ($searchTerm) {
                return strpos(strtolower($area->name ?? ''), $searchTerm) !== false
                    || strpos(strtolower($area->code ?? ''), $searchTerm) !== false;
            })->values();
        } else {
            $filteredAreas = $filteredAreas->values();
        }

        $protectedAreas = $filteredAreas;

        // Handle different export formats
        if ($request->has('print')) {
            return $this->exportPrint($protectedAreas, $request);
        } elseif ($request->has('excel')) {
            return $this->exportExcel($protectedAreas, $request);
        } elseif ($request->has('pdf')) {
            return $this->exportPdf($protectedAreas, $request);
        }
        
        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export to print-friendly view
     */
    private function exportPrint(\Illuminate\Support\Collection $protectedAreas, Request $request)
    {
        // Get filter information for title
        $filterInfo = $this->getFilterInfo($request);
        
        return view('pages.protected_areas.print', compact('protectedAreas', 'filterInfo'));
    }

    /**
     * Export to Excel
     */
    private function exportExcel(\Illuminate\Support\Collection $protectedAreas, Request $request)
    {
        $filename = 'protected-areas-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($protectedAreas) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($file, [
                'Area Code',
                'Name',
                'Observations Count',
                'Status',
                'Created At',
                'Updated At'
            ]);
            
            // Data rows
            foreach ($protectedAreas as $area) {
                fputcsv($file, [
                    $area->code ?? 'N/A',
                    $area->name ?? 'N/A',
                    $area->species_observations_count ?? 0,
                    $area->species_observations_count > 0 ? 'Active' : 'No Data',
                    $area->created_at ? $area->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $area->updated_at ? $area->updated_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    private function exportPdf(\Illuminate\Support\Collection $protectedAreas, Request $request)
    {
        // Limit the number of records for PDF to prevent memory issues
        $maxRecords = 100;
        $totalRecords = $protectedAreas->count();
        
        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }
        
        $filename = 'protected-areas-' . date('Y-m-d-H-i-s') . '.pdf';
        
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
            ->loadView('pages.protected_areas.pdf', compact('protectedAreas', 'filterInfo'));
        
        return $pdf->download($filename);
    }

    /**
     * Get filter information for export titles
     */
    private function getFilterInfo(Request $request)
    {
        $filterInfo = [];
        
        // Status filter
        if ($request->filled('status')) {
            $filterInfo['status'] = ucfirst($request->status);
        }
        
        // Sort filter
        if ($request->filled('sort')) {
            $filterInfo['sort'] = $request->sort === 'code' ? 'Code (A-Z)' : 'Name (A-Z)';
        }
        
        // Search filter
        if ($request->filled('search')) {
            $filterInfo['search'] = $request->search;
        }
        
        return $filterInfo;
    }

    /**
     * Handle export requests for Protected Area Sites
     */
    private function handleSitesExport(Request $request)
    {
        // Get the same filtered data as the sites method but without pagination
        $statusFilter = $request->input('status');
        $sort = $request->input('sort', 'name');
        
        // Base query for all sites with their protected areas
        $query = SiteName::with('protectedArea');
        $sortDirection = 'asc';
        
        // Apply sorting
        if ($sort === 'protected_area') {
            // Sort by related protected area name, then by site name
            $query->leftJoin('protected_areas', 'site_names.protected_area_id', '=', 'protected_areas.id')
                  ->select('site_names.*')
                  ->orderBy('protected_areas.name', $sortDirection)
                  ->orderBy('site_names.name', $sortDirection);
        } else {
            $query->orderBy($sort, $sortDirection);
        }
        
        // Get all sites for filtering
        $allSites = $query->get();

        // Keep export output aligned with table behavior (no duplicate site labels).
        $allSites = $allSites
            ->unique(function ($site) {
                $protectedAreaId = $site->protected_area_id ?? 'null';
                $normalizedName = strtolower(trim((string) ($site->name ?? '')));

                return $protectedAreaId . '|' . $normalizedName;
            })
            ->values();
        
        // Add observation counts to each site from its site-specific table
        $tables = DynamicTableService::getAllObservationTables();
        foreach ($allSites as $site) {
            $site->species_observations_count = $this->countObservationsForSite($site, $tables);
        }
        
        // Apply status filter in PHP
        $filteredSites = $allSites->filter(function ($site) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return $site->species_observations_count > 0;
            }
            if ($statusFilter === 'no_data') {
                return $site->species_observations_count == 0;
            }
            return true;
        });

        // Apply search filter if provided (trim, case-insensitive)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim((string) $request->search));
            $filteredSites = $filteredSites->filter(function ($site) use ($searchTerm) {
                $nameMatch = strpos(strtolower($site->name ?? ''), $searchTerm) !== false;
                $areaMatch = $site->protectedArea && strpos(strtolower($site->protectedArea->name ?? ''), $searchTerm) !== false;
                return $nameMatch || $areaMatch;
            })->values();
        } else {
            $filteredSites = $filteredSites->values();
        }

        $siteNames = $filteredSites;

        // Handle different export formats
        if ($request->has('print')) {
            return $this->exportSitesPrint($siteNames, $request);
        } elseif ($request->has('excel')) {
            return $this->exportSitesExcel($siteNames, $request);
        } elseif ($request->has('pdf')) {
            return $this->exportSitesPdf($siteNames, $request);
        }
        
        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export Protected Area Sites to print-friendly view
     */
    private function exportSitesPrint(\Illuminate\Support\Collection $siteNames, Request $request)
    {
        // Get filter information for title
        $filterInfo = $this->getSitesFilterInfo($request);
        
        return view('pages.protected_area_sites.print', compact('siteNames', 'filterInfo'));
    }

    /**
     * Export Protected Area Sites to Excel
     */
    private function exportSitesExcel(\Illuminate\Support\Collection $siteNames, Request $request)
    {
        $filename = 'protected-area-sites-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($siteNames) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($file, [
                'Site Name',
                'Protected Area',
                'Observations Count',
                'Status',
                'Created At'
            ]);
            
            // Data rows
            foreach ($siteNames as $site) {
                fputcsv($file, [
                    $site->name ?? 'N/A',
                    $site->protectedArea->name ?? 'Not assigned',
                    $site->species_observations_count ?? 0,
                    $site->species_observations_count > 0 ? 'Active' : 'No Data',
                    $site->created_at ? $site->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Protected Area Sites to PDF
     */
    private function exportSitesPdf(\Illuminate\Support\Collection $siteNames, Request $request)
    {
        // Limit the number of records for PDF to prevent memory issues
        $maxRecords = 100;
        $totalRecords = $siteNames->count();
        
        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }
        
        $filename = 'protected-area-sites-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Get filter information for title
        $filterInfo = $this->getSitesFilterInfo($request);
        
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
            ->loadView('pages.protected_area_sites.pdf', compact('siteNames', 'filterInfo'));
        
        return $pdf->download($filename);
    }

    /**
     * Get filter information for Protected Area Sites export titles
     */
    private function getSitesFilterInfo(Request $request)
    {
        $filterInfo = [];
        
        // Status filter
        if ($request->filled('status')) {
            $filterInfo['status'] = ucfirst($request->status);
        }
        
        // Sort filter
        if ($request->filled('sort')) {
            $filterInfo['sort'] = $request->sort === 'protected_area' ? 'Protected Area (A-Z)' : 'Name (A-Z)';
        }
        
        // Search filter
        if ($request->filled('search')) {
            $filterInfo['search'] = $request->search;
        }
        
        return $filterInfo;
    }

}
