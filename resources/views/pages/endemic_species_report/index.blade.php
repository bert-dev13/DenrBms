@extends('layouts.app')

@section('title', 'Endemic Species Report')
@section('header', 'Endemic Species Report')

@section('head')
@vite([
    'resources/css/pages/protected_areas.css',
    'resources/css/pages/endemic_species_report.css',
    'resources/js/pages/endemic_species_report.js',
])
@endsection

@section('content')
<div class="endemic-report-page">
    <div class="filter-panel">
        <form method="GET" action="{{ route('reports.endemic-species') }}" id="endemic-report-filter-form">
            <div class="endemic-filter-row">
                <h2 class="filter-panel__title">Filters</h2>

                <div class="filter-panel__field endemic-filter-field">
                    <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                    <select name="protected_area_id" id="protected_area_id" class="filter-panel__select">
                        <option value="">All Protected Areas</option>
                        @foreach ($protectedAreas as $protectedArea)
                            <option value="{{ $protectedArea->id }}" {{ $filters['protected_area_id'] === (string) $protectedArea->id ? 'selected' : '' }}>
                                {{ $protectedArea->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-panel__field endemic-filter-field">
                    <label for="site_id" class="filter-panel__label">Site</label>
                    <select name="site_id" id="site_id" class="filter-panel__select">
                        <option value="">All Sites</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ $filters['site_id'] === (string) $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-panel__field endemic-filter-field">
                    <label for="conservation_status" class="filter-panel__label">Conservation Status</label>
                    <select name="conservation_status" id="conservation_status" class="filter-panel__select">
                        <option value="">All Statuses</option>
                        @foreach ($conservationOptions as $status)
                            <option value="{{ $status }}" {{ $filters['conservation_status'] === $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-panel__actions endemic-filter-actions">
                    <button type="submit" class="btn-filter-apply">Apply</button>
                    <a href="{{ route('reports.endemic-species') }}" class="btn-filter-clear">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <div class="action-bar-card">
        <div class="action-bar-card__header">
            <h2 class="action-bar-card__title">Endemic Species Report ({{ number_format($rows->total()) }} grouped rows)</h2>
            <div class="action-bar">
                <form method="GET" action="{{ route('reports.endemic-species') }}" class="action-bar__search-wrap">
                    <input type="hidden" name="protected_area_id" value="{{ $filters['protected_area_id'] }}">
                    <input type="hidden" name="site_id" value="{{ $filters['site_id'] }}">
                    <input type="hidden" name="conservation_status" value="{{ $filters['conservation_status'] }}">
                    <div class="action-bar__search action-bar__search--with-submit">
                        <span class="action-bar__search-icon" aria-hidden="true">🔍</span>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            class="action-bar__search-input"
                            placeholder="Search species name or scientific name..."
                            autocomplete="off"
                            aria-label="Search endemic species"
                        />
                    </div>
                    <button type="submit" class="action-bar__search-submit-btn">
                        <span>Search</span>
                    </button>
                </form>

                <div class="action-bar__actions">
                    <div class="action-bar__export-wrap">
                        <button type="button" id="endemic-export-dropdown-btn" class="action-bar__export-btn endemic-export-btn">
                            <i data-lucide="download" class="lucide-icon"></i>
                            <span>Export</span>
                            <i data-lucide="chevron-down" class="lucide-icon"></i>
                        </button>
                        <div id="endemic-export-dropdown" class="action-bar__export-dropdown">
                            <button type="button" onclick="exportEndemicTable('pdf')">
                                <i data-lucide="file-text" class="lucide-icon"></i>
                                <span>Export as PDF</span>
                            </button>
                            <button type="button" onclick="exportEndemicTable('excel')">
                                <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                                <span>Export as Excel</span>
                            </button>
                            <button type="button" onclick="exportEndemicTable('print')">
                                <i data-lucide="printer" class="lucide-icon"></i>
                                <span>Print</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="data-table-wrap">
            <div class="responsive-table-container data-table-container">
                <table class="responsive-table endemic-report-table">
                    <thead>
                        <tr>
                            <th>Protected Area</th>
                            <th>Site</th>
                            <th>Species Name</th>
                            <th>Scientific Name</th>
                            <th>Conservation Status</th>
                            <th class="endemic-col-count">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $index => $row)
                            @php
                                $statusKey = strtolower((string) ($row->conservation_status ?? ''));
                                $statusClass = match ($statusKey) {
                                    'critically_endangered' => 'status-chip--critical',
                                    'endangered' => 'status-chip--endangered',
                                    'vulnerable' => 'status-chip--vulnerable',
                                    'near_threatened' => 'status-chip--near-threatened',
                                    'least_concern' => 'status-chip--least-concern',
                                    default => 'status-chip--unknown',
                                };
                            @endphp
                            <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }}">
                                <td><span class="data-table-cell-truncate" title="{{ e($row->protected_area_name) }}">{{ $row->protected_area_name }}</span></td>
                                <td><span class="data-table-cell-truncate" title="{{ e($row->site_name) }}">{{ $row->site_name }}</span></td>
                                <td><span class="font-medium data-table-cell-truncate" title="{{ e($row->species_name) }}">{{ $row->species_name }}</span></td>
                                <td><span class="data-table-cell-truncate" title="{{ e($row->scientific_name ?? 'N/A') }}">{{ $row->scientific_name ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="status-chip {{ $statusClass }}">
                                        {{ ucwords(str_replace('_', ' ', (string) $row->conservation_status)) }}
                                    </span>
                                </td>
                                <td class="endemic-col-count">
                                    <span class="data-table-count-badge">{{ number_format((int) $row->observation_count) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="data-table-empty-cell">
                                    <div class="data-table-empty-state">
                                        <h3 class="data-table-empty-title">No endemic species records found</h3>
                                        <p class="data-table-empty-text">Try different filters or clear the current filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($rows->total() > 0)
            <div class="data-table-pagination">
                <div class="data-table-pagination__info">
                    Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ number_format($rows->total()) }} grouped rows
                </div>
                @if($rows->hasPages())
                    <nav class="data-table-pagination__nav" aria-label="Pagination">
                        @if($rows->onFirstPage())
                            <button type="button" disabled class="cursor-not-allowed">&lsaquo; Previous</button>
                        @else
                            <a href="{{ $rows->previousPageUrl() }}" rel="prev">&lsaquo; Previous</a>
                        @endif
                        @if($rows->hasMorePages())
                            <a href="{{ $rows->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
                        @else
                            <button type="button" disabled class="cursor-not-allowed">Next &rsaquo;</button>
                        @endif
                    </nav>
                @endif
            </div>
        @endif
    </div>

    <div id="endemic-export-routes" class="hidden"
        data-pdf-url="{{ route('reports.endemic-species.export.pdf') }}"
        data-excel-url="{{ route('reports.endemic-species.export.excel') }}"
        data-print-url="{{ route('reports.endemic-species.export.print') }}"
    ></div>
</div>
@endsection
