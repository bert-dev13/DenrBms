<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Protected Areas Report | DENR BMS</title>
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
        <h1>Protected Areas Report</h1>
        <p>DENR Biodiversity Management System</p>
        
        @if(!empty($filterInfo))
            <p style="font-size: 9pt; font-weight: bold; margin: 0 0 2px 0;">
                @php
                    $filters = [];
                    if(array_key_exists('status', $filterInfo)) $filters[] = 'STATUS: ' . $filterInfo['status'];
                    if(array_key_exists('sort', $filterInfo)) $filters[] = 'SORT: ' . $filterInfo['sort'];
                    if(array_key_exists('search', $filterInfo)) $filters[] = 'SEARCH: ' . $filterInfo['search'];
                    echo strtoupper(implode(' | ', $filters));
                @endphp
            </p>
        @endif
        
        <p style="font-size: 9pt; margin: 0;">
            <strong>Total Records:</strong> {{ $protectedAreas->count() }} | 
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>
    
    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="15%">Area Code</th>
                <th width="35%">Name</th>
                <th width="15%">Observations Count</th>
                <th width="15%">Status</th>
                <th width="20%">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($protectedAreas as $area)
                <tr>
                    <td>{{ $area->code ?? 'N/A' }}</td>
                    <td>{{ $area->name ?? 'N/A' }}</td>
                    <td>{{ number_format($area->species_observations_count ?? 0) }}</td>
                    <td>
                        <span style="padding: 2px 6px; border-radius: 3px; font-size: 8pt; 
                               {{ $area->species_observations_count > 0 ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
                            {{ $area->species_observations_count > 0 ? 'Active' : 'No Data' }}
                        </span>
                    </td>
                    <td>{{ $area->created_at ? $area->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
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
    
    <!-- Summary Statistics -->
    @if($protectedAreas->count() > 0)
        <div style="margin-top: 15px; padding: 8px; background-color: #f5f5f5; border: 1px solid #000;">
            <h3 style="margin: 0 0 8px 0; font-size: 11pt;">Summary Statistics</h3>
            <table style="width: 100%; border: none; font-size: 9pt;">
                <tr>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Protected Areas:</strong> {{ $protectedAreas->count() }}
                    </td>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Observations:</strong> {{ number_format($protectedAreas->sum('species_observations_count')) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;">
                        <strong>Active Areas:</strong> {{ $protectedAreas->where('species_observations_count', '>', 0)->count() }}
                    </td>
                    <td style="border: none; padding: 2px;">
                        <strong>Areas with No Data:</strong> {{ $protectedAreas->where('species_observations_count', '=', 0)->count() }}
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
