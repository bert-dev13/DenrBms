<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Migratory Species Report | DENR BMS</title>
    <style>
        @page { margin: 0.5cm; size: portrait; }
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 6px; }
        .table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
        .table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">Migratory Species Report</h2>
        <p style="margin:2px 0;">Generated: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Scientific Name</th>
                <th>Common Name</th>
                <th>Protected Area</th>
                <th>Locations</th>
                <th>Conservation</th>
                <th>Recorded Count (Σ)</th>
                <th>Obs. records</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportRows as $row)
                <tr>
                    <td><em>{{ $row->scientific_name ?? 'N/A' }}</em></td>
                    <td>{{ $row->common_name ?? 'N/A' }}</td>
                    <td>{{ $row->protected_area_name }}</td>
                    <td>{{ $row->site_name }}</td>
                    <td>{{ $row->conservation_status ? ucwords(str_replace('_', ' ', $row->conservation_status)) : '—' }}</td>
                    <td>{{ number_format((int) $row->observation_count) }}</td>
                    <td>{{ number_format((int) $row->observation_records) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
