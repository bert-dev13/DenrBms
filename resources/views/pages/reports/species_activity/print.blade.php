<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Observation Rankings | DENR BMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Observation Rankings</h1>
    <p>Generated: {{ now()->format('F j, Y g:i A') }}</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Species Name</th>
                <th>Scientific Name</th>
                <th>Protected Areas</th>
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
                    <td>{{ number_format($row->protected_area_count) }}</td>
                    <td>{{ number_format($row->recorded_count_sum) }}</td>
                    <td>{{ number_format($row->observation_frequency) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No activity records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

