<?php

namespace App\Http\Controllers;

use App\Data\ObservationFactFilter;
use App\Helpers\PatrolYearHelper;
use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Services\SpeciesObservationActivityRankingService;
use App\Services\SpeciesObservationFactService;
use App\Support\UserAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeciesActivityController extends Controller
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
        private SpeciesObservationActivityRankingService $activityRankingService,
    ) {}

    private function assignedProtectedAreaId(): ?int
    {
        return UserAccess::assignedProtectedAreaId(Auth::user());
    }

    public function index(Request $request)
    {
        $assignedProtectedAreaId = $this->assignedProtectedAreaId();
        if ($assignedProtectedAreaId !== null) {
            $request->merge(['protected_area_id' => $assignedProtectedAreaId]);
        }

        $filterOptions = [
            'protectedAreas' => ProtectedArea::query()
                ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('id', $assignedProtectedAreaId))
                ->orderBy('name')
                ->get(),
            'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
            'years' => PatrolYearHelper::getYears(),
            'semesters' => [1 => '1st', 2 => '2nd'],
            'sites' => SiteName::query()
                ->when($request->filled('protected_area_id'), fn ($query) => $query->where('protected_area_id', (int) $request->protected_area_id))
                ->orderBy('name')
                ->get(['id', 'name', 'protected_area_id']),
        ];

        $dataset = $this->resolveActivityDataset($request);
        $summaryStats = $dataset['summaryStats'];

        $perPageInput = strtolower((string) $request->input('per_page', '20'));
        $allowedPerPage = [20, 50, 100, 200];
        $perPage = in_array((int) $perPageInput, $allowedPerPage, true) ? (int) $perPageInput : 20;
        if ($perPageInput === 'all') {
            $perPage = max($dataset['ranked']->count(), 1);
        }

        if ($dataset['ranked']->isEmpty()) {
            $rows = new LengthAwarePaginator([], 0, 20, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        } else {
            $ranked = $dataset['ranked'];
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $rankedSlice = $ranked->forPage($currentPage, $perPage)->values();
            $offset = ($currentPage - 1) * $perPage;
            foreach ($rankedSlice as $i => $row) {
                $row->rank = $offset + (int) $i + 1;
            }
            $rows = new LengthAwarePaginator(
                $rankedSlice,
                $ranked->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return view('pages.reports.species_activity.index', [
            'rows' => $rows,
            'filterOptions' => $filterOptions,
            'summaryStats' => $summaryStats,
            'isPaScoped' => $assignedProtectedAreaId !== null,
            'assignedProtectedAreaId' => $assignedProtectedAreaId,
        ]);
    }

    public function exportPrint(Request $request)
    {
        $dataset = $this->resolveActivityDataset($request);

        return view('pages.reports.species_activity.print', [
            'rankedRows' => $dataset['ranked'],
            'summaryStats' => $dataset['summaryStats'],
            'filterInfo' => $this->exportFilterInfo($request),
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $dataset = $this->resolveActivityDataset($request);
        $ranked = $dataset['ranked'];

        $filename = 'species-activity-ranking-'.date('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($ranked): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'Rank',
                'Species Name',
                'Scientific Name',
                'Total Recorded Count (Σ)',
                'Protected Areas',
                'Observation Frequency',
            ]);
            foreach ($ranked as $row) {
                fputcsv($file, [
                    $row->rank,
                    $row->species_name ?? '',
                    $row->scientific_name ?? '',
                    $row->recorded_count_sum,
                    $row->protected_area_count,
                    $row->observation_frequency,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $dataset = $this->resolveActivityDataset($request);
        $ranked = $dataset['ranked'];
        if ($ranked->count() > 100) {
            return back()->with('error', 'PDF export is limited to 100 ranked species. Please use Excel for larger datasets.');
        }

        $pdf = Pdf::setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ])->loadView('pages.reports.species_activity.pdf', [
            'rankedRows' => $ranked,
            'summaryStats' => $dataset['summaryStats'],
            'filterInfo' => $this->exportFilterInfo($request),
        ]);

        return $pdf->download('species-activity-ranking-'.date('Y-m-d-H-i-s').'.pdf');
    }

    /**
     * @return array{ranked: Collection<int, object>, summaryStats: array<string, int>}
     */
    private function resolveActivityDataset(Request $request): array
    {
        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);
        $facts = $this->observationFactService->getFactsWithSummary($filter, $request);
        $ranked = $this->activityRankingService->rankForSpeciesActivity($facts['rows'], $this->normalizeRankOrder($request));
        $facts['summaryStats']['total_species'] = $ranked->count();
        foreach ($ranked as $i => $row) {
            $row->rank = $i + 1;
        }

        return [
            'ranked' => $ranked,
            'summaryStats' => $facts['summaryStats'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function exportFilterInfo(Request $request): array
    {
        $filterInfo = [];
        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find((int) $request->protected_area_id);
            if ($protectedArea) {
                $filterInfo['protected_area'] = $protectedArea->name;
            }
        }
        if ($request->filled('site_name')) {
            $site = SiteName::find((int) $request->site_name);
            if ($site) {
                $filterInfo['site_name'] = $site->name;
            }
        }
        if ($request->filled('bio_group')) {
            $filterInfo['bio_group'] = ucfirst((string) $request->bio_group);
        }
        if ($request->filled('patrol_year')) {
            $filterInfo['patrol_year'] = (string) $request->patrol_year;
        }
        if ($request->filled('patrol_semester')) {
            $filterInfo['patrol_semester'] = ((int) $request->patrol_semester) === 1 ? '1st' : '2nd';
        }
        if ($request->filled('search')) {
            $filterInfo['search'] = (string) $request->search;
        }
        $filterInfo['rank_order'] = $this->normalizeRankOrder($request) === 'asc'
            ? 'Lowest to highest (Σ)'
            : 'Highest to lowest (Σ)';

        return $filterInfo;
    }

    /**
     * @return 'asc'|'desc'
     */
    private function normalizeRankOrder(Request $request): string
    {
        return (string) $request->input('rank_order', 'desc') === 'asc' ? 'asc' : 'desc';
    }

}

