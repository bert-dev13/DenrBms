<?php

namespace App\Http\Controllers;

use App\Data\ObservationFactFilter;
use App\Helpers\PatrolYearHelper;
use App\Models\ProtectedArea;
use App\Services\SpeciesObservationFactService;
use App\Support\ObservationRowValue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeciesRankingController extends Controller
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
    ) {}

    public function index(Request $request)
    {
        $filterOptions = [
            'protectedAreas' => ProtectedArea::orderBy('name')->get(),
            'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
            'years' => PatrolYearHelper::getYears(),
            'semesters' => [1 => '1st', 2 => '2nd'],
        ];

        $dataset = $this->resolveRankingDataset($request);
        $summaryStats = $dataset['summaryStats'];

        if ($dataset['ranked']->isEmpty()) {
            $rows = new LengthAwarePaginator([], 0, 20, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        } else {
            $ranked = $dataset['ranked'];
            $perPage = 20;
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

        return view('pages.reports.species_ranking.index', [
            'rows' => $rows,
            'filterOptions' => $filterOptions,
            'summaryStats' => $summaryStats,
        ]);
    }

    public function exportPrint(Request $request)
    {
        $dataset = $this->resolveRankingDataset($request);
        $filterInfo = $this->rankingExportFilterInfo($request);

        return view('pages.reports.species_ranking.print', [
            'rankedRows' => $dataset['ranked'],
            'summaryStats' => $dataset['summaryStats'],
            'filterInfo' => $filterInfo,
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $dataset = $this->resolveRankingDataset($request);
        $ranked = $dataset['ranked'];

        $filename = 'species-ranking-report-'.date('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($ranked): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'Rank',
                'Common Name',
                'Scientific Name',
                'Recorded Count (Σ)',
                'Observation Records',
            ]);
            foreach ($ranked as $row) {
                fputcsv($file, [
                    $row->rank,
                    $row->common_name ?? '',
                    $row->scientific_name ?? '',
                    $row->recorded_count_sum,
                    $row->observation_records,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $dataset = $this->resolveRankingDataset($request);
        $ranked = $dataset['ranked'];
        $maxRecords = 100;
        if ($ranked->count() > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} ranked species. Your dataset has {$ranked->count()} groups. Please use Excel export for larger datasets.");
        }

        $filterInfo = $this->rankingExportFilterInfo($request);
        $filename = 'species-ranking-report-'.date('Y-m-d-H-i-s').'.pdf';

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

        $pdf = Pdf::setOptions($options)->loadView('pages.reports.species_ranking.pdf', [
            'rankedRows' => $ranked,
            'summaryStats' => $dataset['summaryStats'],
            'filterInfo' => $filterInfo,
        ]);

        return $pdf->download($filename);
    }

    /**
     * @return array{ranked: Collection<int, object>, summaryStats: array<string, int>}
     */
    private function resolveRankingDataset(Request $request): array
    {
        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);
        $facts = $this->observationFactService->getFactsWithSummary($filter, $request);
        $allResults = $facts['rows'];
        $summaryStats = $facts['summaryStats'];

        $rankOrder = $this->normalizeRankOrder($request);
        $ranked = $this->rankSpeciesFromObservationRows($allResults, $rankOrder);
        foreach ($ranked as $i => $row) {
            $row->rank = $i + 1;
        }

        return [
            'ranked' => $ranked,
            'summaryStats' => $summaryStats,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function rankingExportFilterInfo(Request $request): array
    {
        $filterInfo = [];

        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea) {
                $filterInfo['protected_area'] = $protectedArea->name;
            }
        }

        if ($request->filled('bio_group')) {
            $filterInfo['bio_group'] = ucfirst((string) $request->bio_group);
        }

        if ($request->filled('patrol_year')) {
            $filterInfo['patrol_year'] = (string) $request->patrol_year;
        }

        if ($request->filled('patrol_semester')) {
            $semesters = [1 => '1st', 2 => '2nd'];
            $filterInfo['patrol_semester'] = $semesters[(int) $request->patrol_semester] ?? (string) $request->patrol_semester;
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $filterInfo['search'] = $search;
        }

        $rankOrder = $this->normalizeRankOrder($request);
        $filterInfo['rank_order'] = $rankOrder === 'asc'
            ? 'Lowest to highest (Σ)'
            : 'Highest to lowest (Σ)';

        return $filterInfo;
    }

    /**
     * @return 'asc'|'desc'
     */
    private function normalizeRankOrder(Request $request): string
    {
        $raw = (string) $request->input('rank_order', 'desc');

        return $raw === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Group union observation rows by species identity (same keys as distinct species in the grid).
     * Order by total recorded_count (Σ): $rankOrder `desc` = highest first, `asc` = lowest first;
     * observation row count uses the same direction as tiebreaker.
     *
     * @param  'asc'|'desc'  $rankOrder
     * @return Collection<int, object>
     */
    private function rankSpeciesFromObservationRows(Collection $allResults, string $rankOrder): Collection
    {
        $groups = [];

        foreach ($allResults as $row) {
            $sci = trim((string) (ObservationRowValue::field($row, 'scientific_name') ?? ''));
            $com = trim((string) (ObservationRowValue::field($row, 'common_name') ?? ''));

            if ($sci === '' && $com === '') {
                $key = "\0unspecified";
            } elseif ($sci !== '') {
                $key = 's:'.mb_strtolower($sci);
            } else {
                $key = 'c:'.mb_strtolower($com);
            }

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'common_name' => $com,
                    'scientific_name' => $sci,
                    'observation_records' => 0,
                    'recorded_count_sum' => 0,
                ];
            }

            $groups[$key]['observation_records']++;
            $groups[$key]['recorded_count_sum'] += ObservationRowValue::recordedCount($row);

            if ($groups[$key]['common_name'] === '' && $com !== '') {
                $groups[$key]['common_name'] = $com;
            }
            if ($groups[$key]['scientific_name'] === '' && $sci !== '') {
                $groups[$key]['scientific_name'] = $sci;
            }
        }

        $items = array_values($groups);
        $ascending = $rankOrder === 'asc';
        usort($items, static function (array $a, array $b) use ($ascending): int {
            $sumA = (int) $a['recorded_count_sum'];
            $sumB = (int) $b['recorded_count_sum'];
            if ($sumA !== $sumB) {
                $cmp = $sumA <=> $sumB;

                return $ascending ? $cmp : -$cmp;
            }

            $obsA = (int) $a['observation_records'];
            $obsB = (int) $b['observation_records'];
            $cmp = $obsA <=> $obsB;

            return $ascending ? $cmp : -$cmp;
        });

        return collect($items)
            ->map(static fn (array $g) => (object) [
                'common_name' => $g['common_name'],
                'scientific_name' => $g['scientific_name'],
                'observation_records' => (int) $g['observation_records'],
                'recorded_count_sum' => (int) $g['recorded_count_sum'],
            ])
            ->values();
    }
}
