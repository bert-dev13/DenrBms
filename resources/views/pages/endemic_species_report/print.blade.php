<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Endemic Species Report | DENR BMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/shared/icons.css', 'resources/js/shared/icons.js'])
    <style>
        @media print {
            body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.2; margin: 0.5cm; }
            .header { text-align: center; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 5px; }
            .header h1 { font-size: 14pt; margin: 0 0 2px 0; color: #000; }
            .header p { margin: 0 0 2px 0; color: #333; font-size: 10pt; }
            .table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 10px; }
            .table th { background-color: #f0f0f0; font-weight: bold; border: 1px solid #000; padding: 4px 6px; text-align: left; font-size: 9pt; }
            .table td { border: 1px solid #000; padding: 3px 5px; font-size: 9pt; line-height: 1.2; }
            .table tr:nth-child(even) { background-color: #f9f9f9; }
            .summary-stats { margin-top: 15px; padding: 8px; background-color: #f5f5f5; border: 1px solid #000; }
            .footer { text-align: center; margin-top: 15px; font-size: 9pt; color: #666; border-top: 1px solid #000; padding-top: 5px; }
            .no-print { display: none !important; }
            @page { margin: 0.5cm; size: portrait; }
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
            <h1>Endemic Species Report - Print Preview</h1>
            <p class="text-muted">Total Records: {{ $reportRows->count() }}</p>
            <p class="text-muted">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <button onclick="window.print()" class="btn btn-primary">
                <i data-lucide="printer" class="lucide-icon"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i data-lucide="x" class="lucide-icon"></i> Close
            </button>
        </div>
    </div>

    <div class="header">
        <h1>Endemic Species Report</h1>
        <p>DENR Biodiversity Management System</p>
        @if(!empty($filterInfo))
            <p style="font-size: 9pt; font-weight: bold; margin: 0 0 2px 0;">
                @php
                    $parts = [];
                    foreach ($filterInfo as $label => $value) {
                        $parts[] = strtoupper(str_replace('_', ' ', $label)).': '.$value;
                    }
                @endphp
                {{ implode(' | ', $parts) }}
            </p>
        @endif
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total Records:</strong> {{ $reportRows->count() }} |
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="22%">Protected Area</th>
                <th width="22%">Site</th>
                <th width="22%">Species</th>
                <th width="18%">Scientific Name</th>
                <th width="10%">Status</th>
                <th width="6%">Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportRows as $row)
                @php
                    $sk = (string) ($row->conservation_status ?? '');
                    $label = $conservationOptions[$sk] ?? ucfirst(str_replace('_', ' ', $sk));
                @endphp
                <tr>
                    <td>{{ $row->protected_area_name ?? 'N/A' }}</td>
                    <td>{{ $row->site_name ?? '—' }}</td>
                    <td>{{ $row->species_name ?? 'N/A' }}</td>
                    <td><em>{{ $row->scientific_name ?? 'N/A' }}</em></td>
                    <td>{{ $label }}</td>
                    <td>{{ number_format((int) ($row->observation_count ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">No endemic species found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($reportRows->count() > 0)
        <div class="summary-stats">
            <strong>Total Rows:</strong> {{ $reportRows->count() }} |
            <strong>Total Count:</strong> {{ number_format($reportRows->sum('observation_count')) }}
        </div>
    @endif

    <div class="footer">
        <p>Report generated from DENR Biodiversity Management System | Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
