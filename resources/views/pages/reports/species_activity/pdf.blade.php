<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Species Activity Ranking | DENR BMS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        td.num { text-align: right; }
    </style>
</head>
<body>
    <h1>Species Activity Ranking</h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Species Name</th>
                <th>Scientific Name</th>
                <th>Total Recorded Count (Σ)</th>
                <th>Protected Areas</th>
                <th>Observation Frequency</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankedRows as $row)
                <tr>
                    <td>{{ $row->rank }}</td>
                    <td>{{ $row->species_name ?: '—' }}</td>
                    <td>{{ $row->scientific_name ?: '—' }}</td>
                    <td class="num">{{ number_format($row->recorded_count_sum) }}</td>
                    <td class="num">{{ number_format($row->protected_area_count) }}</td>
                    <td class="num">{{ number_format($row->observation_frequency) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No activity records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

