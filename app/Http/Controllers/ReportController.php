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
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Handle export requests for specific report tables
        if ($request->has('export') || $request->has('print')) {
            return $this->handleExport($request);
        }

        $reportData = $this->buildReportData();

        return view('report', $reportData);
    }

    /**
     * Build all data required for the Reports page.
     */
    private function buildReportData(): array
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

                $hasProtectedAreaId = Schema::hasColumn($table, 'protected_area_id');
                $selects = ['scientific_name', 'common_name', \DB::raw('SUM(recorded_count) as total_count')];
                if ($hasProtectedAreaId) {
                    $selects[] = 'protected_area_id';
                }
                $groupBy = $hasProtectedAreaId ? ['scientific_name', 'common_name', 'protected_area_id'] : ['scientific_name', 'common_name'];
                $speciesObs = \DB::table($table)
                    ->select($selects)
                    ->where(function ($query) {
                        $query->whereNotNull('scientific_name')
                            ->orWhereNotNull('common_name');
                    })
                    ->where(function ($query) {
                        $query->where('scientific_name', '!=', '')
                            ->orWhere('common_name', '!=', '');
                    })
                    ->groupBy($groupBy)
                    ->get();

                foreach ($speciesObs as $species) {
                    $key = ($species->scientific_name ?? '') . '|' . ($species->common_name ?? '');
                    if (!isset($speciesData[$key])) {
                        $speciesData[$key] = [
                            'scientific_name' => $species->scientific_name,
                            'common_name' => $species->common_name,
                            'total_count' => 0,
                        ];
                    }
                    $speciesData[$key]['total_count'] += $species->total_count;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Sort yearly data
        ksort($yearlyData);

        // Sort species data by count (for Top Species)
        uasort($speciesData, function ($a, $b) {
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

        // Calculate summary statistics (Species Tracked = unique scientific_name, same as Dashboard/Analytics)
        $stats = [
            'total_areas' => ProtectedArea::count(),
            'total_sites' => \App\Models\SiteName::count(),
            'total_observations' => $totalObservations,
            'species_diversity' => DynamicTableService::getUniqueSpeciesCount(),
            'avg_observations_per_area' => $totalObservations / max(ProtectedArea::count(), 1),
            'most_active_year' => !empty($yearlyData) ? array_keys($yearlyData, max($yearlyData))[0] : null,
            'yearly_totals' => $yearlyData,
        ];

        // Get top species (limit to 20 for display)
        $topSpecies = array_slice($speciesData, 0, 20, true);

        return compact(
            'protectedAreas',
            'stats',
            'topSpecies',
            'areaData',
            'yearlyData'
        );
    }

    /**
     * Handle export requests for the Reports page tables.
     */
    private function handleExport(Request $request)
    {
        $data = $this->buildReportData();
        $table = $request->input('table', 'areas'); // areas | species

        if ($table === 'species') {
            $species = collect($data['topSpecies']);

            if ($request->has('print')) {
                return $this->exportSpeciesPrint($species);
            }

            if ($request->has('excel')) {
                return $this->exportSpeciesExcel($species);
            }

            if ($request->has('pdf')) {
                return $this->exportSpeciesPdf($species);
            }
        } else {
            $areas = collect($data['areaData']);

            if ($request->has('print')) {
                return $this->exportAreasPrint($areas);
            }

            if ($request->has('excel')) {
                return $this->exportAreasExcel($areas);
            }

            if ($request->has('pdf')) {
                return $this->exportAreasPdf($areas);
            }
        }

        return back()->with('error', 'Invalid export format');
    }
    
    private function exportAreasPrint($areas)
    {
        return view('reports.areas-print', compact('areas'));
    }

    private function exportAreasExcel($areas)
    {
        $filename = 'reports-protected-areas-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($areas) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // CSV header
            fputcsv($file, [
                'Area Code',
                'Area Name',
                'Observations Count',
                'Species Count',
                'Status',
            ]);

            foreach ($areas as $area) {
                $observations = $area['observations'] ?? 0;

                fputcsv($file, [
                    $area['code'] ?? 'N/A',
                    $area['name'] ?? 'N/A',
                    $observations,
                    $area['species'] ?? 0,
                    $observations > 0 ? 'Active' : 'No Data',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAreasPdf($areas)
    {
        $maxRecords = 200;
        $totalRecords = $areas->count();

        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }

        $filename = 'reports-protected-areas-' . date('Y-m-d-H-i-s') . '.pdf';

        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions($options)
            ->loadView('reports.areas-pdf', compact('areas'));

        return $pdf->download($filename);
    }

    private function exportSpeciesPrint($species)
    {
        return view('reports.species-print', compact('species'));
    }

    private function exportSpeciesExcel($species)
    {
        $filename = 'reports-top-species-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($species) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // CSV header
            fputcsv($file, [
                'Rank',
                'Scientific Name',
                'Common Name',
                'Total Count',
            ]);

            $rank = 1;
            foreach ($species as $item) {
                fputcsv($file, [
                    $rank,
                    $item['scientific_name'] ?: 'N/A',
                    $item['common_name'] ?: 'N/A',
                    $item['total_count'] ?? 0,
                ]);
                $rank++;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportSpeciesPdf($species)
    {
        $maxRecords = 200;
        $totalRecords = $species->count();

        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }

        $filename = 'reports-top-species-' . date('Y-m-d-H-i-s') . '.pdf';

        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions($options)
            ->loadView('reports.species-pdf', compact('species'));

        return $pdf->download($filename);
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
