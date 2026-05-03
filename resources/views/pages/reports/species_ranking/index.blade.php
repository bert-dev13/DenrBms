@extends('layouts.app')

@section('title', 'Species Rankings Report')
@section('header', 'Species Rankings Report')

@section('head')
<meta name="species-ranking-export-print" content="{{ route('reports.species-ranking.export.print') }}">
<meta name="species-ranking-export-excel" content="{{ route('reports.species-ranking.export.excel') }}">
<meta name="species-ranking-export-pdf" content="{{ route('reports.species-ranking.export.pdf') }}">
@vite(['resources/css/pages/species_observations.css', 'resources/css/pages/species_ranking.css', 'resources/js/pages/species_ranking.js'])
@endsection

@section('content')
            @if (session('error'))
                <div id="species-ranking-error-message" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Summary Cards (same layout as Species Observations) -->
            <div class="species-obs-summary-cards mb-6">
                <div class="kpi-grid">
                    <div class="kpi-card kpi-card--blue">
                        <div class="kpi-card-icon kpi-card-icon--blue">
                            <i data-lucide="clipboard-list" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Observations</p>
                            <p class="kpi-card-value">{{ number_format($summaryStats['total_observations'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">records in view</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--green">
                        <div class="kpi-card-icon kpi-card-icon--green">
                            <i data-lucide="bar-chart-3" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Recorded Count</p>
                            <p class="kpi-card-value">{{ number_format($summaryStats['total_recorded_count'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">total count</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--purple">
                        <div class="kpi-card-icon kpi-card-icon--purple">
                            <i data-lucide="map-pin" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Protected Areas</p>
                            <p class="kpi-card-value">{{ number_format($summaryStats['total_protected_areas'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">unique areas</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--orange">
                        <div class="kpi-card-icon kpi-card-icon--orange">
                            <i data-lucide="panda" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Species Recorded</p>
                            <p class="kpi-card-value">{{ number_format($summaryStats['total_species'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">by scientific name</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-panel">
                <form method="GET" action="{{ route('reports.species-ranking') }}" id="species-ranking-filter-form">
                    <input type="hidden" name="search" id="ranking-filters-search-hidden" value="{{ request('search') }}">
                    <div class="filter-panel__header">
                        <h2 class="filter-panel__title">Filters</h2>
                        <div class="filter-panel__actions">
                            <button type="submit" class="btn-filter-apply">Apply</button>
                            <button type="button" onclick="clearRankingFilters()" class="btn-filter-clear">Clear</button>
                        </div>
                    </div>
                    <div class="filter-panel__grid filter-panel__grid--cols-5">
                        <div class="filter-panel__field">
                            <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                            <select
                                name="protected_area_id"
                                id="protected_area_id"
                                class="filter-panel__select"
                            >
                                <option value="">All Areas</option>
                                @foreach ($filterOptions['protectedAreas'] as $area)
                                    <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }} data-code="{{ $area->code }}">
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
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
                            <label for="rank_order" class="filter-panel__label">Rank by Σ</label>
                            <select name="rank_order" id="rank_order" class="filter-panel__select">
                                <option value="desc" {{ request('rank_order', 'desc') === 'desc' ? 'selected' : '' }}>Highest to lowest</option>
                                <option value="asc" {{ request('rank_order') === 'asc' ? 'selected' : '' }}>Lowest to highest</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Ranked table -->
            <div class="action-bar-card">
                <div class="action-bar-card__header">
                    @php
                        $rankOrderCurrent = request('rank_order', 'desc') === 'asc' ? 'lowest first' : 'highest first';
                    @endphp
                    <h2 class="action-bar-card__title">
                        Ranked species ({{ number_format($rows->total()) }} groups) — by total recorded count, {{ $rankOrderCurrent }}
                    </h2>
                    <div class="action-bar">
                        <form method="GET" action="{{ route('reports.species-ranking') }}" class="action-bar__search-wrap" id="species-ranking-search-form">
                            <input type="hidden" name="protected_area_id" value="{{ request('protected_area_id') }}">
                            <input type="hidden" name="bio_group" value="{{ request('bio_group') }}">
                            <input type="hidden" name="patrol_year" value="{{ request('patrol_year') }}">
                            <input type="hidden" name="patrol_semester" value="{{ request('patrol_semester') }}">
                            <input type="hidden" name="rank_order" value="{{ request('rank_order', 'desc') }}">
                            <div class="action-bar__search action-bar__search--with-submit">
                                <span class="action-bar__search-icon" aria-hidden="true">
                                    <i data-lucide="search" class="lucide-icon"></i>
                                </span>
                                <input
                                    type="text"
                                    id="species-ranking-search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="action-bar__search-input"
                                    placeholder="Search common or scientific name…"
                                    autocomplete="off"
                                    aria-label="Search ranked species"
                                />
                            </div>
                            <button type="submit" class="action-bar__search-submit-btn" aria-label="Search">
                                <i data-lucide="search" class="lucide-icon"></i>
                                <span>Search</span>
                            </button>
                        </form>
                        <div class="action-bar__actions">
                            <div class="action-bar__export-wrap">
                                <button type="button" id="species-ranking-export-btn" class="action-bar__export-btn" aria-expanded="false" aria-haspopup="true">
                                    <i data-lucide="download" class="lucide-icon"></i>
                                    <span>Export</span>
                                    <i data-lucide="chevron-down" class="lucide-icon"></i>
                                </button>
                                <div id="species-ranking-export-dropdown" class="action-bar__export-dropdown" role="menu">
                                    <button type="button" role="menuitem" data-species-ranking-export="pdf">
                                        <i data-lucide="file-text" class="lucide-icon"></i>
                                        <span>Export as PDF</span>
                                    </button>
                                    <button type="button" role="menuitem" data-species-ranking-export="excel">
                                        <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                                        <span>Export as Excel</span>
                                    </button>
                                    <button type="button" role="menuitem" data-species-ranking-export="print">
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
                        <table class="responsive-table species-ranking-table">
                            <thead>
                                <tr>
                                    <th class="species-ranking-col-rank">Rank</th>
                                    <th>Common Name</th>
                                    <th>Scientific Name</th>
                                    <th class="species-ranking-col-num">Recorded Count (Σ)</th>
                                    <th class="species-ranking-col-num">Observation Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $index => $row)
                                    <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }}">
                                        <td class="species-ranking-col-rank">
                                            <span class="species-ranking-rank-badge">{{ $row->rank }}</span>
                                        </td>
                                        <td>
                                            <span class="data-table-cell-truncate font-medium" title="{{ e($row->common_name ?: '—') }}">{{ $row->common_name ?: '—' }}</span>
                                        </td>
                                        <td>
                                            <em class="data-table-cell-truncate" title="{{ e($row->scientific_name ?: '—') }}">{{ $row->scientific_name ?: '—' }}</em>
                                        </td>
                                        <td class="species-ranking-col-num">
                                            <span class="inline-flex items-center px-2 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                                {{ number_format($row->recorded_count_sum) }}
                                            </span>
                                        </td>
                                        <td class="species-ranking-col-num">
                                            <span class="inline-flex items-center px-2 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                                {{ number_format($row->observation_records) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="data-table-empty-cell">
                                            <div class="data-table-empty-state">
                                                <i data-lucide="clipboard-list" class="lucide-icon data-table-empty-icon"></i>
                                                <h3 class="data-table-empty-title">No ranked species for these filters</h3>
                                                <p class="data-table-empty-text">
                                                    @if (request('search'))
                                                        No groups match "{{ request('search') }}". Try different keywords or clear the search.
                                                    @else
                                                        Adjust filters or add observations from the Species Observations page.
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

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
