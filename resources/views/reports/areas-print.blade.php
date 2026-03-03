<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Protected Areas Overview | DENR BMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 10pt;
                line-height: 1.2;
                margin: 0.5cm;
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

            .no-print {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
                size: portrait;
            }
        }

        @media screen {
            body {
                padding: 20px;
            }

            .print-header {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .table-responsive {
                max-height: 600px;
                overflow-y: auto;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="print-header">
            <h1>Protected Areas Overview - Print Preview</h1>
            <p class="text-muted">Total Records: {{ $areas->count() }}</p>
            <p class="text-muted">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <button onclick="window.print()" class="btn btn-primary">
                Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Close
            </button>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>Protected Areas Overview</h1>
        <p>DENR Biodiversity Management System</p>
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total Records:</strong> {{ $areas->count() }} |
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="15%">Area Code</th>
                <th width="40%">Area Name</th>
                <th width="15%">Observations</th>
                <th width="15%">Species Count</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($areas as $area)
                <tr>
                    <td>{{ $area['code'] ?? 'N/A' }}</td>
                    <td>{{ $area['name'] ?? 'N/A' }}</td>
                    <td>{{ number_format($area['observations'] ?? 0) }}</td>
                    <td>{{ number_format($area['species'] ?? 0) }}</td>
                    @php $obs = $area['observations'] ?? 0; @endphp
                    <td>
                        <span style="padding: 2px 6px; border-radius: 3px; font-size: 8pt;
                               {{ $obs > 0 ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
                            {{ $obs > 0 ? 'Active' : 'No Data' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; font-style: italic;">
                        No protected areas found
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

