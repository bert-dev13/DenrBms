<?php

namespace App\Http\Controllers;

use App\Data\ObservationFactFilter;
use App\Models\ProtectedArea;
use App\Models\Site;
use App\Models\SiteName;
use App\Models\Species;
use App\Services\MigratorySpeciesObservationMatcher;
use App\Services\SpeciesObservationFactService;
use App\Support\ObservationRowValue;
use App\Support\ObservationSiteLabel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MigratorySpeciesReportController extends Controller
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
        private MigratorySpeciesObservationMatcher $migratoryMatcher,
    ) {}

    public function index(Request $request)
    {
        $protectedAreaId = $request->integer('protected_area_id');
        $siteId = $request->integer('site_id');
        $search = trim((string) $request->input('search', ''));

        $rowsCollection = $this->aggregateMigratoryRows($request);

        if ($request->query('export') === 'excel') {
            return $this->exportExcel($rowsCollection);
        }

        if ($request->query('export') === 'pdf') {
            return $this->exportPdf($rowsCollection, $protectedAreaId, $siteId, $search);
        }

        if ($request->query('export') === 'print') {
            return view('pages.migratory_species_report.print', [
                'reportRows' => $rowsCollection,
                'filters' => [
                    'protected_area_id' => $protectedAreaId > 0 ? (string) $protectedAreaId : '',
                    'site_id' => $siteId > 0 ? (string) $siteId : '',
                    'search' => $search,
                ],
            ]);
        }

        $perPage = 25;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $slice = $rowsCollection->forPage($currentPage, $perPage)->values();
        $rows = new LengthAwarePaginator(
            $slice,
            $rowsCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $protectedAreas = ProtectedArea::query()->orderBy('name')->get(['id', 'name']);
        $sites = Site::query()
            ->when($protectedAreaId > 0, fn ($q) => $q->where('protected_area_id', $protectedAreaId))
            ->orderBy('name')
            ->get(['id', 'name', 'protected_area_id']);

        return view('pages.migratory_species_report.index', [
            'rows' => $rows,
            'protectedAreas' => $protectedAreas,
            'sites' => $sites,
            'filters' => [
                'protected_area_id' => $protectedAreaId > 0 ? (string) $protectedAreaId : '',
                'site_id' => $siteId > 0 ? (string) $siteId : '',
                'search' => $search,
            ],
        ]);
    }

    private function aggregateMigratoryRows(Request $request): Collection
    {
        $siteId = $request->integer('site_id');
        $selectedSite = $siteId > 0 ? SiteName::query()->find($siteId) : null;
        if ($siteId > 0 && ! $selectedSite) {
            return collect();
        }

        $migratorySpecies = Species::query()
            ->where('is_migratory', true)
            ->get(['id', 'name', 'scientific_name', 'conservation_status']);

        if ($migratorySpecies->isEmpty()) {
            return collect();
        }

        $maps = $this->migratoryMatcher->buildLookupMaps($migratorySpecies);
        $byScientific = $maps['byScientific'];
        $byCommon = $maps['byCommon'];

        $filter = ObservationFactFilter::fromMigratoryReportRequest($request);
        $observationRows = $this->observationFactService->getFactRows($filter, $request);

        $pas = ProtectedArea::query()->get(['id', 'name'])->keyBy('id');
        $grouped = [];

        foreach ($observationRows as $row) {
            $species = $this->migratoryMatcher->resolveSpecies($row, $byScientific, $byCommon);
            if ($species === null) {
                continue;
            }

            $tableName = (string) ($row->table_name ?? '');
            $siteLabel = ObservationSiteLabel::label($selectedSite, (string) ($row->station_code ?? ''), $tableName);
            $paId = (int) ($row->protected_area_id ?? 0);
            // One row per species per PA; locations are summarized (avoids repeating the same species per station).
            $key = $paId.'|'.$species->id;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'protected_area_id' => $paId,
                    'species_id' => (int) $species->id,
                    'common_name' => (string) $species->name,
                    'scientific_name' => $species->scientific_name,
                    'conservation_status' => $species->conservation_status,
                    'observation_count' => 0,
                    'observation_records' => 0,
                    'location_labels' => [],
                ];
            }

            $grouped[$key]['observation_count'] += ObservationRowValue::recordedCount($row);
            $grouped[$key]['observation_records']++;
            if ($siteLabel !== '' && ! in_array($siteLabel, $grouped[$key]['location_labels'], true)) {
                $grouped[$key]['location_labels'][] = $siteLabel;
            }
        }

        return collect($grouped)
            ->map(function (array $row) use ($pas) {
                sort($row['location_labels']);

                return (object) [
                    'protected_area_name' => $pas->get($row['protected_area_id'])->name ?? 'N/A',
                    'site_name' => $this->formatLocationSummary($row['location_labels']),
                    'common_name' => $row['common_name'],
                    'scientific_name' => $row['scientific_name'],
                    'conservation_status' => $row['conservation_status'],
                    'observation_count' => $row['observation_count'],
                    'observation_records' => $row['observation_records'],
                ];
            })
            ->sortBy([
                ['scientific_name', 'asc'],
                ['protected_area_name', 'asc'],
            ])
            ->values();
    }

    /**
     * @param  list<string>  $labels
     */
    private function formatLocationSummary(array $labels): string
    {
        $labels = array_values(array_filter($labels, static fn ($s) => $s !== '' && $s !== '—'));
        if ($labels === []) {
            return '—';
        }
        if (count($labels) === 1) {
            return $labels[0];
        }
        if (count($labels) <= 4) {
            return implode(', ', $labels);
        }

        $shown = array_slice($labels, 0, 3);
        $more = count($labels) - 3;

        return implode(', ', $shown).' (+ '.$more.' more)';
    }

    private function exportExcel(Collection $rows): StreamedResponse
    {
        $filename = 'migratory-species-report-'.date('Y-m-d-H-i-s').'.csv';

        return response()->stream(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Scientific Name',
                'Common Name',
                'Protected Area',
                'Locations',
                'Conservation Status',
                'Recorded Count (Σ)',
                'Observation Records',
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->scientific_name,
                    $row->common_name,
                    $row->protected_area_name,
                    $row->site_name,
                    $row->conservation_status ?? '',
                    $row->observation_count,
                    $row->observation_records,
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function exportPdf(Collection $rows, int $protectedAreaId, int $siteId, string $search): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pages.migratory_species_report.pdf', [
            'reportRows' => $rows,
            'filters' => [
                'protected_area_id' => $protectedAreaId > 0 ? (string) $protectedAreaId : '',
                'site_id' => $siteId > 0 ? (string) $siteId : '',
                'search' => $search,
            ],
        ]);

        return $pdf->download('migratory-species-report-'.date('Y-m-d-H-i-s').'.pdf');
    }
}
