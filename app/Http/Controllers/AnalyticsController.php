<?php

namespace App\Http\Controllers;

use App\Helpers\PatrolYearHelper;
use App\Models\ProtectedArea;
use App\Services\AnalyticsService;
use App\Support\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
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
        $dataset = $this->analyticsService->buildAnalyticsDataset($request);

        return view('pages.analytics.index', [
            'dataset' => $dataset,
            'filterOptions' => [
                'protectedAreas' => ProtectedArea::query()
                    ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('id', $assignedProtectedAreaId))
                    ->orderBy('name')
                    ->get(),
                'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
                'years' => PatrolYearHelper::getYears(),
                'semesters' => [1 => '1st', 2 => '2nd'],
            ],
        ]);
    }

    public function species(Request $request)
    {
        $assignedProtectedAreaId = $this->assignedProtectedAreaId();
        if ($assignedProtectedAreaId !== null) {
            $request->merge(['protected_area_id' => $assignedProtectedAreaId]);
        }
        $dataset = $this->analyticsService->buildSpeciesAnalyticsDataset($request);

        return view('pages.analytics.species', [
            'dataset' => $dataset,
            'filterOptions' => [
                'protectedAreas' => ProtectedArea::query()
                    ->when($assignedProtectedAreaId !== null, fn ($query) => $query->where('id', $assignedProtectedAreaId))
                    ->orderBy('name')
                    ->get(),
                'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
                'years' => PatrolYearHelper::getYears(),
                'semesters' => [1 => '1st', 2 => '2nd'],
            ],
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $assignedProtectedAreaId = $this->assignedProtectedAreaId();
        if ($assignedProtectedAreaId !== null) {
            $request->merge(['protected_area_id' => $assignedProtectedAreaId]);
        }
        $dataset = $this->analyticsService->buildAnalyticsDataset($request);
        $filename = 'analytics-overview-'.date('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = static function () use ($dataset): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['Analytics Overview']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Observations', $dataset['summary']['total_observations'] ?? 0]);
            fputcsv($file, ['Recorded Count', $dataset['summary']['total_recorded_count'] ?? 0]);
            fputcsv($file, ['Total Protected Areas', $dataset['summary']['total_protected_areas'] ?? 0]);
            fputcsv($file, ['Total Species', $dataset['summary']['total_species'] ?? 0]);
            fputcsv($file, ['Total Species Observed', $dataset['summary']['total_species_observed'] ?? 0]);
            fputcsv($file, ['Endemic Observations', $dataset['summary']['endemic_observations'] ?? 0]);
            fputcsv($file, ['Migratory Observations', $dataset['summary']['migratory_observations'] ?? 0]);
            fputcsv($file, []);

            fputcsv($file, ['Trend (Year/Semester)']);
            fputcsv($file, ['Period', 'Observation Count', 'Recorded Count Sum', 'Distinct Species']);
            foreach ($dataset['timeseries'] as $row) {
                fputcsv($file, [
                    $row['label'] ?? '',
                    $row['observation_count'] ?? 0,
                    $row['recorded_count_sum'] ?? 0,
                    $row['species_count'] ?? 0,
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Top Protected Areas']);
            fputcsv($file, ['Protected Area', 'Observation Count', 'Recorded Count Sum']);
            foreach ($dataset['top_areas'] as $row) {
                fputcsv($file, [
                    $row['label'] ?? '',
                    $row['observation_count'] ?? 0,
                    $row['recorded_count_sum'] ?? 0,
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Top Species']);
            fputcsv($file, ['Common Name', 'Scientific Name', 'Observation Count', 'Recorded Count Sum']);
            foreach ($dataset['top_species'] as $row) {
                fputcsv($file, [
                    $row['common_name'] ?? '',
                    $row['scientific_name'] ?? '',
                    $row['observation_count'] ?? 0,
                    $row['recorded_count_sum'] ?? 0,
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Top Species Observation']);
            fputcsv($file, ['Rank', 'Species Name', 'Scientific Name', 'Protected Areas', 'Recorded Count', 'Observation Records']);
            foreach ($dataset['top_species_observation'] ?? [] as $row) {
                fputcsv($file, [
                    $row['rank'] ?? 0,
                    $row['species_name'] ?? '',
                    $row['scientific_name'] ?? '',
                    $row['protected_area_count'] ?? 0,
                    $row['recorded_count_sum'] ?? 0,
                    $row['observation_records'] ?? 0,
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Top 10 Species by Recorded Count']);
            fputcsv($file, ['Species', 'Observation rows', 'Recorded Count', 'Percent of top 10 observation rows']);
            $dist = $dataset['species_observation_distribution'] ?? [];
            foreach ($dist['slices'] ?? [] as $row) {
                fputcsv($file, [
                    $row['species_name'] ?? '',
                    $row['observation_records'] ?? 0,
                    $row['recorded_count_sum'] ?? 0,
                    isset($row['percent_share']) ? round((float) $row['percent_share'], 4) : 0,
                ]);
            }
            fputcsv($file, ['Combined observation rows (top 10 species)', $dist['grand_total_observation_records_top10'] ?? 0]);
            fputcsv($file, ['Combined Σ (top 10 species)', $dist['grand_total_recorded_count'] ?? 0]);
            fputcsv($file, ['Total observation rows (current filters)', $dist['total_observation_rows'] ?? 0]);
            fputcsv($file, ['Total recorded count Σ (current filters, all species)', $dist['total_recorded_count'] ?? ($dataset['summary']['total_recorded_count'] ?? 0)]);
            fputcsv($file, []);

            fputcsv($file, ['Fauna vs Flora Observation Distribution']);
            fputcsv($file, ['Category', 'Recorded Count', 'Percent (Fauna + Flora Σ)']);
            $ff = $dataset['fauna_flora_observation_distribution'] ?? [];
            foreach ($ff['slices'] ?? [] as $row) {
                fputcsv($file, [
                    $row['category'] ?? '',
                    $row['recorded_count_sum'] ?? 0,
                    isset($row['percent_share']) ? round((float) $row['percent_share'], 4) : 0,
                ]);
            }
            fputcsv($file, ['Combined Σ (Fauna + Flora)', $ff['grand_total_recorded_count'] ?? 0]);
            fputcsv($file, ['Total observation rows', $ff['total_observation_rows'] ?? 0]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
