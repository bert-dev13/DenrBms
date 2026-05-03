<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Species Rankings Report | DENR BMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.2; margin: 0.5cm; }
            .header { text-align: center; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 5px; }
            .header h1 { font-size: 14pt; margin: 0 0 2px 0; color: #000; }
            .header p { margin: 0 0 2px 0; color: #333; font-size: 10pt; }
            .table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 10px; }
            .table th { background-color: #f0f0f0; font-weight: bold; border: 1px solid #000; padding: 4px 6px; text-align: left; }
            .table td { border: 1px solid #000; padding: 3px 5px; font-size: 9pt; }
            .table tr:nth-child(even) { background-color: #f9f9f9; }
            .table td.num { text-align: right; font-variant-numeric: tabular-nums; }
            .summary-stats { margin-top: 15px; padding: 8px; background-color: #f5f5f5; border: 1px solid #000; }
            .footer { text-align: center; margin-top: 15px; font-size: 9pt; color: #666; border-top: 1px solid #000; padding-top: 5px; }
            .no-print { display: none !important; }
            @page { margin: 0.5cm; }
        }
        @media screen {
            body { padding: 20px; }
            .print-header { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="print-header">
            <h1>Species Rankings Report — Print Preview</h1>
            <p class="text-muted">Ranked groups: {{ $rankedRows->count() }}</p>
            <p class="text-muted">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <button type="button" onclick="window.print()" class="btn btn-primary">Print</button>
            <button type="button" onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>

    <div class="header">
        <h1>Species Rankings Report</h1>
        <p>DENR Biodiversity Management System</p>
        @if (! empty($filterInfo))
            <p style="font-size: 9pt; font-weight: bold; margin: 0 0 2px 0;">
                @php
                    $filters = [];
                    if (array_key_exists('protected_area', $filterInfo)) {
                        $filters[] = strtoupper($filterInfo['protected_area']);
                    }
                    if (array_key_exists('bio_group', $filterInfo)) {
                        $filters[] = strtoupper($filterInfo['bio_group']);
                    }
                    if (array_key_exists('patrol_year', $filterInfo)) {
                        $filters[] = strtoupper($filterInfo['patrol_year']);
                    }
                    if (array_key_exists('patrol_semester', $filterInfo)) {
                        $filters[] = strtoupper($filterInfo['patrol_semester']).' SEMESTER';
                    }
                    if (array_key_exists('rank_order', $filterInfo)) {
                        $filters[] = strtoupper($filterInfo['rank_order']);
                    }
                    if (array_key_exists('search', $filterInfo)) {
                        $filters[] = 'SEARCH: '.strtoupper($filterInfo['search']);
                    }
                    echo strtoupper(implode(' | ', $filters));
                @endphp
            </p>
        @endif
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total observation records in view:</strong> {{ number_format($summaryStats['total_observations'] ?? 0) }} |
            <strong>Total recorded count (Σ):</strong> {{ number_format($summaryStats['total_recorded_count'] ?? 0) }} |
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="8%">Rank</th>
                <th width="22%">Common Name</th>
                <th width="30%">Scientific Name</th>
                <th width="14%" class="text-end">Recorded Count (Σ)</th>
                <th width="14%" class="text-end">Observation Records</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankedRows as $row)
                <tr>
                    <td>{{ $row->rank }}</td>
                    <td>{{ $row->common_name ?: '—' }}</td>
                    <td><em>{{ $row->scientific_name ?: '—' }}</em></td>
                    <td class="num">{{ number_format($row->recorded_count_sum) }}</td>
                    <td class="num">{{ number_format($row->observation_records) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; font-style: italic;">No ranked species for these filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($rankedRows->count() > 0)
        <div class="summary-stats">
            <h3 style="margin: 0 0 8px 0; font-size: 11pt;">Summary</h3>
            <table style="width: 100%; border: none; font-size: 9pt;">
                <tr>
                    <td style="width: 50%; border: none; padding: 2px;"><strong>Species groups (ranked):</strong> {{ number_format($rankedRows->count()) }}</td>
                    <td style="width: 50%; border: none; padding: 2px;"><strong>Total recorded count:</strong> {{ number_format($summaryStats['total_recorded_count'] ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;"><strong>Unique protected areas:</strong> {{ number_format($summaryStats['total_protected_areas'] ?? 0) }}</td>
                    <td style="border: none; padding: 2px;"><strong>Unique species (scientific):</strong> {{ number_format($summaryStats['total_species'] ?? 0) }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Report generated from DENR Biodiversity Management System | {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
