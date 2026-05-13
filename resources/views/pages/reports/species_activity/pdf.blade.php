<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Observation Rankings | DENR BMS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        td.num { text-align: right; }
    </style>
</head>
<body>
    <h1>Observation Rankings</h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Species Name</th>
                <th>Scientific Name</th>
                @unless($isPaScoped ?? false)
                    <th>Protected Areas</th>
                @endunless
                <th>Recorded Count</th>
                <th>Observation Records</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankedRows as $row)
                <tr>
                    <td>{{ $row->rank }}</td>
                    <td>{{ $row->species_name ?: '—' }}</td>
                    <td>{{ $row->scientific_name ?: '—' }}</td>
                    @unless($isPaScoped ?? false)
                        <td class="num">{{ number_format($row->protected_area_count) }}</td>
                    @endunless
                    <td class="num">{{ number_format($row->recorded_count_sum) }}</td>
                    <td class="num">{{ number_format($row->observation_frequency) }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ ($isPaScoped ?? false) ? 5 : 6 }}">No activity records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

