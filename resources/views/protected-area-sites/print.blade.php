<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Protected Area Sites Report | DENR BMS</title>
    
    <!-- Bootstrap CSS for print styling -->
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
            
            .summary-stats {
                margin-top: 15px;
                padding: 8px;
                background-color: #f5f5f5;
                border: 1px solid #000;
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
            <h1>Protected Area Sites Report - Print Preview</h1>
            <p class="text-muted">Total Records: {{ $siteNames->count() }}</p>
            <p class="text-muted">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
    
    <!-- Header -->
    <div class="header">
        <h1>Protected Area Sites Report</h1>
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
            <strong>Total Records:</strong> {{ $siteNames->count() }} | 
            <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>
    
    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="25%">Site Name</th>
                <th width="20%">Protected Area</th>
                <th width="15%">Station Code</th>
                <th width="15%">Observations Count</th>
                <th width="10%">Status</th>
                <th width="15%">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siteNames as $site)
                <tr>
                    <td>{{ $site->name ?? 'N/A' }}</td>
                    <td>{{ $site->protectedArea->name ?? 'Not assigned' }}</td>
                    <td>{{ $site->station_code ?? 'N/A' }}</td>
                    <td>{{ number_format($site->species_observations_count ?? 0) }}</td>
                    <td>
                        <span style="padding: 2px 6px; border-radius: 3px; font-size: 8pt; 
                               {{ $site->species_observations_count > 0 ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
                            {{ $site->species_observations_count > 0 ? 'Active' : 'No Data' }}
                        </span>
                    </td>
                    <td>{{ $site->created_at ? $site->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">
                        No protected area sites found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Summary Statistics -->
    @if($siteNames->count() > 0)
        <div class="summary-stats">
            <h3 style="margin: 0 0 8px 0; font-size: 11pt;">Summary Statistics</h3>
            <table style="width: 100%; border: none; font-size: 9pt;">
                <tr>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Sites:</strong> {{ $siteNames->count() }}
                    </td>
                    <td style="width: 50%; border: none; padding: 2px;">
                        <strong>Total Observations:</strong> {{ number_format($siteNames->sum('species_observations_count')) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;">
                        <strong>Active Sites:</strong> {{ $siteNames->where('species_observations_count', '>', 0)->count() }}
                    </td>
                    <td style="border: none; padding: 2px;">
                        <strong>Sites with No Data:</strong> {{ $siteNames->where('species_observations_count', '=', 0)->count() }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px;">
                        <strong>Unique Protected Areas:</strong> {{ $siteNames->pluck('protectedArea.name')->unique()->filter()->count() }}
                    </td>
                    <td style="border: none; padding: 2px;">
                        <strong>Sites with Assigned Areas:</strong> {{ $siteNames->where('protectedArea', '!=', null)->count() }}
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
