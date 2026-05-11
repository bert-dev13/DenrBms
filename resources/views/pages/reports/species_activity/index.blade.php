@extends('layouts.app')

@section('title', 'Species Activity Ranking')
@section('header', 'Species Activity Ranking')

@section('head')
<meta name="species-activity-export-print" content="{{ route('reports.species-activity.export.print') }}">
<meta name="species-activity-export-excel" content="{{ route('reports.species-activity.export.excel') }}">
<meta name="species-activity-export-pdf" content="{{ route('reports.species-activity.export.pdf') }}">
@vite(['resources/css/pages/species_observations.css', 'resources/css/pages/species_activity.css', 'resources/js/pages/species_activity.js'])
@endsection

@section('content')
@if (session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
@endif

<div class="species-obs-summary-cards mb-6">
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue"><div class="kpi-card-icon kpi-card-icon--blue"><i data-lucide="clipboard-list" class="lucide-icon"></i></div><div class="kpi-card-body"><p class="kpi-card-label">Total Observations</p><p class="kpi-card-value">{{ number_format($summaryStats['total_observations'] ?? 0) }}</p><span class="kpi-card-meta kpi-card-meta--neutral">records in view</span></div></div>
        <div class="kpi-card kpi-card--green"><div class="kpi-card-icon kpi-card-icon--green"><i data-lucide="bar-chart-3" class="lucide-icon"></i></div><div class="kpi-card-body"><p class="kpi-card-label">Total Recorded Count</p><p class="kpi-card-value">{{ number_format($summaryStats['total_recorded_count'] ?? 0) }}</p><span class="kpi-card-meta kpi-card-meta--neutral">total count</span></div></div>
        <div class="kpi-card kpi-card--purple"><div class="kpi-card-icon kpi-card-icon--purple"><i data-lucide="map-pin" class="lucide-icon"></i></div><div class="kpi-card-body"><p class="kpi-card-label">Total Protected Areas</p><p class="kpi-card-value">{{ number_format($summaryStats['total_protected_areas'] ?? 0) }}</p><span class="kpi-card-meta kpi-card-meta--neutral">unique areas</span></div></div>
        <div class="kpi-card kpi-card--orange"><div class="kpi-card-icon kpi-card-icon--orange"><i data-lucide="panda" class="lucide-icon"></i></div><div class="kpi-card-body"><p class="kpi-card-label">Total Species Recorded</p><p class="kpi-card-value">{{ number_format($summaryStats['total_species'] ?? 0) }}</p><span class="kpi-card-meta kpi-card-meta--neutral">active species</span></div></div>
    </div>
</div>

<div class="filter-panel">
    <form method="GET" action="{{ route('reports.species-activity') }}" id="species-activity-filter-form">
        <input type="hidden" name="search" id="activity-filters-search-hidden" value="{{ request('search') }}">
        <div class="filter-panel__header">
            <h2 class="filter-panel__title">Filters</h2>
            <div class="filter-panel__actions">
                <button type="submit" class="btn-filter-apply">Apply</button>
                <button type="button" onclick="clearActivityFilters()" class="btn-filter-clear">Clear</button>
            </div>
        </div>
        <div class="filter-panel__grid filter-panel__grid--cols-5">
            <div class="filter-panel__field">
                <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                <select
                    name="protected_area_id"
                    id="protected_area_id"
                    class="filter-panel__select"
                    {{ !empty($isPaScoped) ? 'disabled' : '' }}
                >
                    <option value="">All Areas</option>
                    @foreach ($filterOptions['protectedAreas'] as $area)
                        <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }} data-code="{{ $area->code }}">
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
                @if (!empty($isPaScoped) && !empty($assignedProtectedAreaId))
                    <input type="hidden" name="protected_area_id" value="{{ $assignedProtectedAreaId }}">
                @endif
            </div>
            <div class="filter-panel__field">
                <label for="bio_group" class="filter-panel__label">Bio Group</label>
                <select name="bio_group" id="bio_group" class="filter-panel__select">
                    <option value="">All Groups</option>
                    @foreach ($filterOptions['bioGroups'] as $key => $group)
                        <option value="{{ $key }}" {{ request('bio_group') == $key ? 'selected' : '' }}>{{ $group }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-panel__field">
                <label for="patrol_year" class="filter-panel__label">Patrol Year</label>
                <select name="patrol_year" id="patrol_year" class="filter-panel__select">
                    <option value="">All Years</option>
                    @foreach ($filterOptions['years'] as $year)
                        <option value="{{ $year }}" {{ request('patrol_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-panel__field">
                <label for="patrol_semester" class="filter-panel__label">Semester</label>
                <select name="patrol_semester" id="patrol_semester" class="filter-panel__select">
                    <option value="">All Semesters</option>
                    @foreach ($filterOptions['semesters'] as $value => $label)
                        <option value="{{ $value }}" {{ request('patrol_semester') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-panel__field">
                <label for="rank_order" class="filter-panel__label">Rank by Observation Frequency</label>
                <select name="rank_order" id="rank_order" class="filter-panel__select">
                    <option value="desc" {{ request('rank_order', 'desc') === 'desc' ? 'selected' : '' }}>Highest to lowest</option>
                    <option value="asc" {{ request('rank_order') === 'asc' ? 'selected' : '' }}>Lowest to highest</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="action-bar-card">
    <div class="action-bar-card__header">
        <h2 class="action-bar-card__title">Species activity ranking ({{ number_format($rows->total()) }} groups)</h2>
        <div class="action-bar">
            <form method="GET" action="{{ route('reports.species-activity') }}" class="action-bar__search-wrap" id="species-activity-search-form">
                <input type="hidden" name="protected_area_id" value="{{ request('protected_area_id') }}"><input type="hidden" name="bio_group" value="{{ request('bio_group') }}"><input type="hidden" name="patrol_year" value="{{ request('patrol_year') }}"><input type="hidden" name="patrol_semester" value="{{ request('patrol_semester') }}"><input type="hidden" name="rank_order" value="{{ request('rank_order', 'desc') }}"><input type="hidden" name="per_page" value="{{ request('per_page', '20') }}">
                <div class="action-bar__search action-bar__search--with-submit"><span class="action-bar__search-icon"><i data-lucide="search" class="lucide-icon"></i></span><input type="text" id="species-activity-search" name="search" value="{{ request('search') }}" class="action-bar__search-input" placeholder="Search species name..." autocomplete="off"></div>
                <button type="submit" class="action-bar__search-submit-btn"><i data-lucide="search" class="lucide-icon"></i><span>Search</span></button>
            </form>
            <div class="action-bar__actions">
                <form method="GET" action="{{ route('reports.species-activity') }}" class="inline-flex items-center gap-2">
                    <input type="hidden" name="protected_area_id" value="{{ request('protected_area_id') }}">
                    <input type="hidden" name="bio_group" value="{{ request('bio_group') }}">
                    <input type="hidden" name="patrol_year" value="{{ request('patrol_year') }}">
                    <input type="hidden" name="patrol_semester" value="{{ request('patrol_semester') }}">
                    <input type="hidden" name="rank_order" value="{{ request('rank_order', 'desc') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label for="per_page" class="text-sm text-gray-600">Rows</label>
                    <select name="per_page" id="per_page" class="filter-panel__select filter-panel__select--compact" onchange="this.form.submit()">
                        <option value="20" {{ request('per_page', '20') === '20' ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') === '100' ? 'selected' : '' }}>100</option>
                        <option value="200" {{ request('per_page') === '200' ? 'selected' : '' }}>200</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </form>
                <div class="action-bar__export-wrap"><button type="button" id="species-activity-export-btn" class="action-bar__export-btn"><i data-lucide="download" class="lucide-icon"></i><span>Export</span><i data-lucide="chevron-down" class="lucide-icon"></i></button><div id="species-activity-export-dropdown" class="action-bar__export-dropdown"><button type="button" data-species-activity-export="pdf"><i data-lucide="file-text" class="lucide-icon"></i><span>Export as PDF</span></button><button type="button" data-species-activity-export="excel"><i data-lucide="file-spreadsheet" class="lucide-icon"></i><span>Export as Excel</span></button><button type="button" data-species-activity-export="print"><i data-lucide="printer" class="lucide-icon"></i><span>Print</span></button></div></div>
            </div>
        </div>
    </div>
    <div class="data-table-wrap"><div class="responsive-table-container data-table-container"><table class="responsive-table species-activity-table"><thead><tr><th class="species-activity-col-rank">Rank</th><th>Species Name</th><th>Scientific Name</th><th class="species-activity-col-num">Total Recorded Count (Σ)</th><th class="species-activity-col-num">Protected Areas</th><th class="species-activity-col-num">Observation Frequency</th></tr></thead><tbody>
    @forelse($rows as $index => $row)
        <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }}"><td class="species-activity-col-rank"><span class="species-activity-rank-badge">{{ $row->rank }}</span></td><td><span class="data-table-cell-truncate font-medium">{{ $row->species_name ?: '—' }}</span></td><td><em class="data-table-cell-truncate">{{ $row->scientific_name ?: '—' }}</em></td><td class="species-activity-col-num">{{ number_format($row->recorded_count_sum) }}</td><td class="species-activity-col-num">{{ number_format($row->protected_area_count) }}</td><td class="species-activity-col-num">{{ number_format($row->observation_frequency) }}</td></tr>
    @empty
        <tr><td colspan="6" class="data-table-empty-cell"><div class="data-table-empty-state"><i data-lucide="clipboard-list" class="lucide-icon data-table-empty-icon"></i><h3 class="data-table-empty-title">No activity records found</h3><p class="data-table-empty-text">Adjust filters or add observations from the Species Observations page.</p></div></td></tr>
    @endforelse
    </tbody></table></div></div>
    @if ($rows->total() > 0)
        <div class="data-table-pagination">
            <div class="data-table-pagination__info">
                Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ number_format($rows->total()) }} results
            </div>
            @if ($rows->hasPages())
                <nav class="data-table-pagination__nav" aria-label="Pagination">
                    @if ($rows->onFirstPage())
                        <button type="button" disabled class="cursor-not-allowed">&lsaquo; Previous</button>
                    @else
                        <a href="{{ $rows->previousPageUrl() }}" rel="prev">&lsaquo; Previous</a>
                    @endif
                    @if ($rows->hasMorePages())
                        <a href="{{ $rows->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
                    @else
                        <button type="button" disabled class="cursor-not-allowed">Next &rsaquo;</button>
                    @endif
                </nav>
            @endif
        </div>
    @endif
</div>
@endsection

