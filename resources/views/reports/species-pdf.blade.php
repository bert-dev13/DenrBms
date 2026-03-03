<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Top Observed Species | DENR BMS</title>
    <style>
        @page {
            margin: 0.5cm;
            size: portrait;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .header h1 {
            font-size: 14pt;
            margin: 0 0 2px 0;
            color: #000;
        }

        .header p {
            margin: 0 0 2px 0;
            color: #333;
            font-size: 10pt;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 10px;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-size: 9pt;
        }

        .table td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 9pt;
            line-height: 1.2;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Top Observed Species</h1>
        <p>DENR Biodiversity Management System</p>
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total Records:</strong> {{ count($species) }} |
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="35%">Scientific Name</th>
                <th width="35%">Common Name</th>
                <th width="20%">Total Count</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @forelse($species as $item)
                <tr>
                    <td>{{ $rank }}</td>
                    <td>{{ $item['scientific_name'] ?: 'N/A' }}</td>
                    <td>{{ $item['common_name'] ?: 'N/A' }}</td>
                    <td>{{ number_format($item['total_count'] ?? 0) }}</td>
                </tr>
                @php $rank++; @endphp
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic;">
                        No species data found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Report generated from DENR Biodiversity Management System | Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>

