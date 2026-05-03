<?php

namespace App\Http\Controllers;

use App\Data\ObservationFactFilter;
use App\Models\ProtectedArea;
use App\Models\Site;
use App\Models\SiteName;
use App\Models\Species;
use App\Services\EndemicSpeciesObservationMatcher;
use App\Services\SpeciesCanonicalResolver;
use App\Services\SpeciesObservationFactService;
use App\Support\ObservationRowValue;
use App\Support\ObservationSiteLabel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EndemicSpeciesReportController extends Controller
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
        private EndemicSpeciesObservationMatcher $endemicMatcher,
        private SpeciesCanonicalResolver $speciesCanonicalResolver,
    ) {}

    public function exportPdf(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $rows = $this->buildRowsCollectionFromRequest($request);
        $maxRecords = 100;
        if ($rows->count() > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Please use Excel export for larger datasets.");
        }

        $filename = 'endemic-species-report-'.date('Y-m-d-H-i-s').'.pdf';
        $filterInfo = $this->filterInfo($request);
        $conservationOptions = $this->conservationOptionLabels();

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

        $pdf = Pdf::setOptions($options)->loadView('pages.endemic_species_report.pdf', [
            'reportRows' => $rows,
            'filterInfo' => $filterInfo,
            'conservationOptions' => $conservationOptions,
        ]);

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $rows = $this->buildRowsCollectionFromRequest($request);
        $filename = 'endemic-species-report-'.date('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Protected Area',
                'Site',
                'Species Name',
                'Scientific Name',
                'Conservation Status',
                'Count',
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->protected_area_name,
                    $row->site_name,
                    $row->species_name,
                    $row->scientific_name,
                    $row->conservation_status,
                    $row->observation_count,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function print(Request $request): \Illuminate\Contracts\View\View
    {
        $rows = $this->buildRowsCollectionFromRequest($request);
        $filterInfo = $this->filterInfo($request);
        $conservationOptions = $this->conservationOptionLabels();

        return view('pages.endemic_species_report.print', [
            'reportRows' => $rows,
            'filterInfo' => $filterInfo,
            'conservationOptions' => $conservationOptions,
        ]);
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|StreamedResponse
    {
        $protectedAreaId = $request->integer('protected_area_id');
        $siteId = $request->integer('site_id');
        $conservationStatus = $request->string('conservation_status')->toString();
        $search = trim($request->string('search')->toString());

        $protectedAreas = ProtectedArea::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $sites = Site::query()
            ->when($protectedAreaId > 0, fn ($query) => $query->where('protected_area_id', $protectedAreaId))
            ->orderBy('name')
            ->get(['id', 'name', 'protected_area_id']);

        $conservationOptions = Species::query()
            ->where('is_endemic', true)
            ->whereNotNull('conservation_status')
            ->where('conservation_status', '!=', '')
            ->distinct()
            ->orderBy('conservation_status')
            ->pluck('conservation_status');

        $rowsCollection = $this->buildRowsCollectionFromRequest($request);

        if ($request->query('export') === 'csv') {
            $filename = 'endemic-species-report-'.now()->format('Y-m-d-H-i-s').'.csv';
            $rows = $rowsCollection;

            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'Protected Area',
                    'Site',
                    'Species Name',
                    'Scientific Name',
                    'Conservation Status',
                    'Count',
                ]);

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->protected_area_name,
                        $row->site_name,
                        $row->species_name,
                        $row->scientific_name,
                        $row->conservation_status,
                        $row->observation_count,
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
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

        return view('pages.endemic_species_report.index', [
            'rows' => $rows,
            'protectedAreas' => $protectedAreas,
            'sites' => $sites,
            'conservationOptions' => $conservationOptions,
            'filters' => [
                'protected_area_id' => $protectedAreaId > 0 ? (string) $protectedAreaId : '',
                'site_id' => $siteId > 0 ? (string) $siteId : '',
                'conservation_status' => $conservationStatus,
                'search' => $search,
            ],
        ]);
    }

    private function buildRowsCollectionFromRequest(Request $request): Collection
    {
        return $this->aggregateEndemicRows($request);
    }

    private function conservationOptionLabels(): array
    {
        return [
            'critically_endangered' => 'Critically Endangered',
            'endangered' => 'Endangered',
            'vulnerable' => 'Vulnerable',
            'near_threatened' => 'Near Threatened',
            'least_concern' => 'Least Concern',
        ];
    }

    private function filterInfo(Request $request): array
    {
        $info = [];
        if ($request->filled('protected_area_id')) {
            $pa = ProtectedArea::query()->find((int) $request->protected_area_id);
            $info['protected_area'] = $pa?->name ?? (string) $request->protected_area_id;
        }
        if ($request->filled('site_id')) {
            $site = SiteName::query()->find((int) $request->site_id);
            $info['site'] = $site?->name ?? (string) $request->site_id;
        }
        if ($request->filled('conservation_status')) {
            $status = (string) $request->conservation_status;
            $info['conservation_status'] = $this->conservationOptionLabels()[$status] ?? ucfirst(str_replace('_', ' ', $status));
        }
        if ($request->filled('search')) {
            $info['search'] = (string) $request->search;
        }

        return $info;
    }

    private function aggregateEndemicRows(Request $request): Collection
    {
        $conservationStatus = $request->string('conservation_status')->toString();
        $conservationStatus = $conservationStatus !== '' ? $conservationStatus : null;

        $siteId = $request->integer('site_id');
        $selectedSite = $siteId > 0 ? SiteName::query()->find($siteId) : null;
        $groupBySite = $selectedSite !== null;
        if ($siteId > 0 && ! $selectedSite) {
            return collect();
        }

        $endemicSpecies = Species::query()
            ->where('is_endemic', true)
            ->get(['id', 'name', 'scientific_name', 'conservation_status']);

        if ($endemicSpecies->isEmpty()) {
            return collect();
        }

        $maps = $this->endemicMatcher->buildLookupMaps($endemicSpecies);
        $byScientific = $maps['byScientific'];
        $byCommon = $maps['byCommon'];
        $endemicSpeciesById = $endemicSpecies->keyBy('id');

        $filter = ObservationFactFilter::fromEndemicReportRequest($request);
        $observationRows = $this->observationFactService->getFactRows($filter, $request);

        $pas = ProtectedArea::query()->get(['id', 'name'])->keyBy('id');
        $grouped = [];

        foreach ($observationRows as $row) {
            $species = null;
            $resolved = $this->speciesCanonicalResolver->resolve(
                trim((string) ($row->scientific_name ?? '')),
                trim((string) ($row->common_name ?? '')),
                is_numeric($row->species_id ?? null) ? (int) $row->species_id : null
            );
            $resolvedSpecies = $resolved['species'] ?? null;
            if ($resolvedSpecies !== null && $endemicSpeciesById->has((int) $resolvedSpecies->id)) {
                $species = $endemicSpeciesById->get((int) $resolvedSpecies->id);
            }
            if ($species === null) {
                $species = $this->endemicMatcher->resolveSpecies($row, $byScientific, $byCommon);
            }
            if ($species === null) {
                continue;
            }

            if ($conservationStatus !== null && $species->conservation_status !== $conservationStatus) {
                continue;
            }

            $tableName = (string) ($row->table_name ?? '');
            $siteLabel = $groupBySite
                ? ObservationSiteLabel::label($selectedSite, (string) ($row->station_code ?? ''), $tableName)
                : 'All Sites';
            $paId = (int) ($row->protected_area_id ?? 0);
            $key = $groupBySite
                ? $paId.'|'.$siteLabel.'|'.$species->id
                : $paId.'|'.$species->id;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'protected_area_id' => $paId,
                    'site_name' => $siteLabel,
                    'species_id' => (int) $species->id,
                    'species_name' => (string) $species->name,
                    'scientific_name' => $species->scientific_name,
                    'conservation_status' => $species->conservation_status,
                    'observation_count' => 0,
                ];
            }

            $grouped[$key]['observation_count'] += ObservationRowValue::recordedCount($row);
        }

        return collect($grouped)
            ->map(function (array $row) use ($pas) {
                return (object) [
                    'protected_area_name' => $pas->get($row['protected_area_id'])->name ?? 'N/A',
                    'site_name' => $row['site_name'],
                    'species_name' => $row['species_name'],
                    'scientific_name' => $row['scientific_name'],
                    'conservation_status' => $row['conservation_status'],
                    'observation_count' => $row['observation_count'],
                ];
            })
            ->sortBy([
                ['protected_area_name', 'asc'],
                ['site_name', 'asc'],
                ['species_name', 'asc'],
            ])
            ->values();
    }
}
