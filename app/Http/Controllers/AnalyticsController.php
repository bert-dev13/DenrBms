<?php

namespace App\Http\Controllers;

use App\Helpers\PatrolYearHelper;
use App\Models\ProtectedArea;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
    ) {}

    public function index(Request $request)
    {
        $dataset = $this->analyticsService->buildAnalyticsDataset($request);

        return view('pages.analytics.index', [
            'dataset' => $dataset,
            'filterOptions' => [
                'protectedAreas' => ProtectedArea::orderBy('name')->get(),
                'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
                'years' => PatrolYearHelper::getYears(),
                'semesters' => [1 => '1st', 2 => '2nd'],
            ],
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
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
            fputcsv($file, ['Total Recorded Count', $dataset['summary']['total_recorded_count'] ?? 0]);
            fputcsv($file, ['Total Protected Areas', $dataset['summary']['total_protected_areas'] ?? 0]);
            fputcsv($file, ['Total Species', $dataset['summary']['total_species'] ?? 0]);
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
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
