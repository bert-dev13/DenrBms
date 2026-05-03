<?php

namespace App\Services;

use App\Models\BanganObservation;
use App\Models\BauaObservation;
use App\Models\BmsSpeciesObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\DupaxObservation;
use App\Models\FuyotObservation;
use App\Models\MadreObservation;
use App\Models\MadupapaObservation;
use App\Models\MagapitObservation;
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
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SpeciesObservationUnionService
{
    /**
     * @return array<string, class-string|array<int, class-string>>
     */
    public function observationModelsByProtectedAreaCode(): array
    {
        return [
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
    }

    /**
     * Same union result set as Species Observations index (dynamic tables + filters).
     */
    public function getUnionResults(Request $request): Collection
    {
        return $this->unionTableQueries($this->collectAllTableQueries($request));
    }

    /**
     * @param  array<int, \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder>  $allTableQueries
     */
    public function unionTableQueries(array $allTableQueries): Collection
    {
        if ($allTableQueries === []) {
            return collect();
        }

        $allTableQueries = array_values($allTableQueries);
        $baseQuery = $allTableQueries[0];
        for ($i = 1, $n = count($allTableQueries); $i < $n; $i++) {
            $baseQuery = $baseQuery->union($allTableQueries[$i]);
        }

        return $baseQuery->orderBy('patrol_year', 'desc')
            ->orderBy('patrol_semester', 'desc')
            ->orderBy('station_code')
            ->get();
    }

    /**
     * @return array<int, \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder>
     */
    public function collectAllTableQueries(Request $request): array
    {
        $observationModels = $this->observationModelsByProtectedAreaCode();
        $allTableQueries = [];

        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea) {
                $areaCode = $protectedArea->code;
                if (isset($observationModels[$areaCode])) {
                    $models = $observationModels[$areaCode];
                    if (is_array($models)) {
                        foreach ($models as $modelClass) {
                            try {
                                $allTableQueries[] = $this->buildFilteredQuery($modelClass, $request);
                            } catch (\Exception $e) {
                                Log::warning("Failed to build query for model {$modelClass}: ".$e->getMessage());
                            }
                        }
                    } else {
                        try {
                            $allTableQueries[] = $this->buildFilteredQuery($models, $request);
                        } catch (\Exception $e) {
                            Log::warning("Failed to build query for model {$models}: ".$e->getMessage());
                        }
                    }
                }
            }
        } else {
            $allTables = DynamicTableService::getAllObservationTables();
            foreach ($allTables as $tableName) {
                try {
                    if (str_contains($tableName, '_site_tbl')) {
                        $allTableQueries[] = $this->buildTableQuery($tableName, $request);
                    } else {
                        $model = DynamicTableService::createDynamicModel($tableName);
                        $allTableQueries[] = $this->buildFilteredQuery(get_class($model), $request);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to build query for table {$tableName}: ".$e->getMessage());
                }
            }
        }

        if ($request->filled('site_name') && $request->site_name !== '') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $siteTableName = $this->createSiteTableName($siteName->name, $siteName->id);
                if (Schema::hasTable($siteTableName)) {
                    try {
                        $allTableQueries = [$this->buildTableQuery($siteTableName, $request)];
                    } catch (\Exception $e) {
                        Log::warning("Failed to build site-specific query for {$siteTableName}: ".$e->getMessage());
                    }
                }
            }
        }

        return $allTableQueries;
    }

    public function createSiteTableName(string $siteName, int $siteId): string
    {
        $words = explode(' ', $siteName);
        $safeName = '';
        $wordCount = min(3, count($words));
        for ($i = 0; $i < $wordCount; $i++) {
            $word = preg_replace('/[^a-zA-Z0-9]/', '', $words[$i]);
            $safeName .= strtolower(substr($word, 0, 8));
        }
        if (strlen($safeName) < 3) {
            $safeName = 'site'.$siteId;
        }

        return $safeName.'_site_tbl';
    }

    public function buildTableQuery(string $tableName, Request $request)
    {
        $selectOrNull = static function (string $column) use ($tableName) {
            return Schema::hasColumn($tableName, $column)
                ? $column
                : DB::raw("NULL as {$column}");
        };

        $query = DB::table($tableName)->select(
            $selectOrNull('id'),
            $selectOrNull('species_id'),
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
            DB::raw("'".$tableName."' as table_name")
        );

        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'protected_area_id')) {
                        $q->orWhereExists(function ($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = '.$tableName.'.protected_area_id')
                                ->where('protected_areas.name', 'like', '%'.$searchTerm.'%');
                        });
                    }
                }
            );
        }

        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group',
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester',
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && Schema::hasColumn($tableName, $dbField)) {
                $query->where($dbField, $request->$requestKey);
            }
        }

        if ($request->filled('site_name')) {
            Log::info('Processing site name filter:', [
                'site_name_value' => $request->site_name,
                'site_name_type' => gettype($request->site_name),
                'is_empty_string' => $request->site_name === '',
                'table_name' => $tableName,
            ]);

            $siteName = $request->filled('site_name') && $request->site_name !== '' ? SiteName::find($request->site_name) : null;

            if ($siteName && ($tableName === 'mariano_tbl' || $tableName === 'madupapa_tbl')) {
                if (($tableName === 'mariano_tbl' && strpos($siteName->name, 'San Mariano') !== false) ||
                    ($tableName === 'madupapa_tbl' && strpos($siteName->name, 'Madupapa') !== false)) {
                    Log::info('Correct site selected for '.$tableName.' - skipping station code filtering');

                    return $query;
                }
                Log::info('Wrong site selected for '.$tableName.' - excluding all records');
                $query->whereRaw('1 = 0');

                return $query;
            }

            if ($request->site_name === '') {
                Log::info('Site filter: All Sites selected - no site filtering applied');

                return $query;
            }

            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                Log::info('Site filter: Specific site selected:', [
                    'site_id' => $request->site_name,
                    'site_name' => $siteName->name,
                    'station_code' => $stationCode,
                ]);

                if ($stationCode && Schema::hasColumn($tableName, 'station_code')) {
                    $query->where('station_code', $stationCode);
                    Log::info('Applied station code filter: '.$stationCode);
                } else {
                    Log::warning('No station code found for site: '.$siteName->name);
                }
            } else {
                Log::warning('Site not found for ID: '.$request->site_name);
            }
        } else {
            Log::info('No site name filter present');
        }

        return $query;
    }

    public function buildFilteredQueryWithoutStationCode(string $modelClass, Request $request)
    {
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
            'site_name' => $request->site_name,
        ]);

        $query = $modelClass::select(
            $selectOrNull('id'),
            $selectOrNull('species_id'),
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
            DB::raw("'".$tableName."' as table_name")
        );

        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($hasColumn, $tableName) {
                    if ($hasColumn('protected_area_id')) {
                        $q->orWhereExists(function ($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = '.$tableName.'.protected_area_id')
                                ->where('protected_areas.name', 'like', '%'.$searchTerm.'%');
                        });
                    }
                }
            );
        }

        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group',
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester',
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && $hasColumn($dbField)) {
                $query->where($dbField, $request->$requestKey);
                Log::info("Applied filter {$requestKey}: ".$request->$requestKey);
            }
        }

        Log::info('Skipping site name filter for Mariano/Madupapa table');

        return $query;
    }

    public function buildFilteredQuery(string $modelClass, Request $request)
    {
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
            'site_name' => $request->site_name,
        ]);

        $query = $modelClass::select(
            $selectOrNull('id'),
            $selectOrNull('species_id'),
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
            DB::raw("'".$tableName."' as table_name")
        );

        if ($request->filled('search')) {
            SearchHelper::applySafeColumnSearch(
                $query,
                $tableName,
                (string) $request->search,
                ['common_name', 'scientific_name', 'station_code', 'transaction_code'],
                function ($q, string $searchTerm) use ($hasColumn, $tableName) {
                    if ($hasColumn('protected_area_id')) {
                        $q->orWhereExists(function ($subQuery) use ($searchTerm, $tableName) {
                            $subQuery->select(DB::raw(1))
                                ->from('protected_areas')
                                ->whereRaw('protected_areas.id = '.$tableName.'.protected_area_id')
                                ->where('protected_areas.name', 'like', '%'.$searchTerm.'%');
                        });
                    }
                }
            );
        }

        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group',
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester',
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey) && $hasColumn($dbField)) {
                $query->where($dbField, $request->$requestKey);
                Log::info("Applied filter {$requestKey}: ".$request->$requestKey);
            }
        }

        if ($request->filled('site_name')) {
            Log::info('Processing site name filter for model:', [
                'site_name_value' => $request->site_name,
                'site_name_type' => gettype($request->site_name),
                'is_empty_string' => $request->site_name === '',
                'model_class' => $modelClass,
            ]);

            if ($request->site_name === '') {
                Log::info('Site filter: All Sites selected for model - no site filtering applied');

                return $query;
            }

            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                Log::info('Site filter: Specific site selected for model:', [
                    'site_id' => $request->site_name,
                    'site_name' => $siteName->name,
                    'station_code' => $stationCode,
                ]);

                if ($stationCode && $hasColumn('station_code')) {
                    $query->where('station_code', $stationCode);
                    Log::info('Applied station code filter for model: '.$stationCode);
                } else {
                    Log::warning('No station code found for site in model: '.$siteName->name);
                }
            } else {
                Log::warning('Site not found for model ID: '.$request->site_name);
            }
        } else {
            Log::info('No site name filter present for model');
        }

        return $query;
    }
}
