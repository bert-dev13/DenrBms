<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Species Observations Report | DENR BMS</title>
    <style>
        @page {
            margin: 0.5cm;
            size: landscape;
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
        <h1>Species Observations Report</h1>
        <p>DENR Biodiversity Management System</p>
        
        @if(!empty($filterInfo))
            <p style="font-size: 9pt; font-weight: bold; margin: 0 0 2px 0;">
                @php
                    $filters = [];
                    if(array_key_exists('protected_area', $filterInfo)) $filters[] = strtoupper($filterInfo['protected_area']);
                    if(array_key_exists('site_name', $filterInfo)) $filters[] = strtoupper($filterInfo['site_name']);
                    if(array_key_exists('bio_group', $filterInfo)) $filters[] = strtoupper($filterInfo['bio_group']);
                    if(array_key_exists('patrol_year', $filterInfo)) $filters[] = strtoupper($filterInfo['patrol_year']);
                    if(array_key_exists('patrol_semester', $filterInfo)) $filters[] = strtoupper($filterInfo['patrol_semester']) . ' SEMESTER';
                    echo strtoupper(implode(' | ', $filters));
                @endphp
            </p>
        @endif
        
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total Records:</strong> {{ $observations->count() }} | 
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>
    
    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="12%">Protected Area</th>
                <th width="8%">Station Code</th>
                <th width="10%">Transaction Code</th>
                <th width="8%">Patrol Period</th>
                <th width="6%">Bio Group</th>
                <th width="14%">Common Name</th>
                <th width="18%">Scientific Name</th>
                <th width="6%">Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($observations as $observation)
                <tr>
                    <td>{{ $observation->protectedArea->name ?? 'N/A' }}</td>
                    <td>{{ $observation->station_code ?? 'N/A' }}</td>
                    <td>{{ $observation->transaction_code ?? 'N/A' }}</td>
                    <td>{{ $observation->patrol_year ?? 'N/A' }} {{ $observation->patrol_semester_text ?? '' }}</td>
                    <td>{{ ucfirst($observation->bio_group ?? 'N/A') }}</td>
                    <td>{{ $observation->common_name ?? 'N/A' }}</td>
                    <td><em>{{ $observation->scientific_name ?? 'N/A' }}</em></td>
                    <td>{{ $observation->recorded_count ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; font-style: italic;">
                        No observations found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Summary Statistics -->
    @if($observations->count() > 0)
        <div style="margin-top: 15px; padding: 8px; background-color: #f5f5f5; border: 1px solid #000;">
            <h3 style="margin: 0 0 8px 0; font-size: 11pt;">Summary Statistics</h3>
            <table style="width: 100%; border: none; font-size: 9pt;">
                <tr>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Observations:</strong> {{ $observations->count() }}
                    </td>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Count:</strong> {{ $observations->sum('recorded_count') }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;">
                        <strong>Fauna Observations:</strong> {{ $observations->where('bio_group', 'fauna')->count() }}
                    </td>
                    <td style="border: none; padding: 2px;">
                        <strong>Flora Observations:</strong> {{ $observations->where('bio_group', 'flora')->count() }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;">
                        <strong>Unique Protected Areas:</strong> {{ $observations->pluck('protectedArea.name')->unique()->filter()->count() }}
                    </td>
                    <td style="border: none; padding: 2px;">
                        <strong>Unique Species:</strong> {{ $observations->pluck('scientific_name')->unique()->filter()->count() }}
                    </td>
                </tr>
            </table>
        </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Report generated from DENR Biodiversity Management System | Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
