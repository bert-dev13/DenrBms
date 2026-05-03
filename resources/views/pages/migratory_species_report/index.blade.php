@extends('layouts.app')

@section('title', 'Migratory Species Report')
@section('header', 'Migratory Species Report')

@section('head')
@vite(['resources/css/pages/protected_areas.css', 'resources/css/pages/migratory_species_report.css', 'resources/js/pages/migratory_species_report.js'])
@endsection

@section('content')
<div class="migratory-report">
    <div class="filter-panel">
        <form method="GET" action="{{ route('reports.migratory-species') }}" id="migratory-filter-form">
            <div class="migratory-filter-row">
                <h2 class="filter-panel__title">Filters</h2>

                <div class="filter-panel__field migratory-filter-field">
                    <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                    <select name="protected_area_id" id="protected_area_id" class="filter-panel__select">
                        <option value="">All Protected Areas</option>
                        @foreach ($protectedAreas as $pa)
                            <option value="{{ $pa->id }}" {{ $filters['protected_area_id'] === (string) $pa->id ? 'selected' : '' }}>
                                {{ $pa->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-panel__field migratory-filter-field">
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

                <div class="filter-panel__actions migratory-filter-actions">
                    <button type="submit" class="btn-filter-apply">Apply</button>
                    <button type="button" class="btn-filter-clear" id="migratory-clear-filters">Clear</button>
                </div>
            </div>
        </form>
    </div>

    <div class="action-bar-card">
        <div class="action-bar-card__header">
            <h2 class="action-bar-card__title">Migratory species</h2>
            <div class="action-bar">
                <form method="GET" action="{{ route('reports.migratory-species') }}" id="migratory-search-form" class="action-bar__search-wrap">
                    <input type="hidden" name="protected_area_id" value="{{ $filters['protected_area_id'] }}">
                    <input type="hidden" name="site_id" value="{{ $filters['site_id'] }}">
                    <div class="action-bar__search action-bar__search--with-submit">
                        <span class="action-bar__search-icon" aria-hidden="true">🔍</span>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            class="action-bar__search-input"
                            placeholder="Search by species name or scientific name"
                            autocomplete="off"
                        />
                    </div>
                    <button type="submit" class="action-bar__search-submit-btn">Search</button>
                </form>

                <div class="action-bar__actions">
                    <div class="action-bar__export-wrap">
                        <button type="button" id="migratory-export-btn" class="action-bar__export-btn">
                            <i data-lucide="download" class="lucide-icon"></i>
                            <span>Export</span>
                            <i data-lucide="chevron-down" class="lucide-icon"></i>
                        </button>
                        <div id="migratory-export-dropdown" class="action-bar__export-dropdown">
                            <button type="button" data-export="pdf">
                                <i data-lucide="file-text" class="lucide-icon"></i>
                                <span>Export as PDF</span>
                            </button>
                            <button type="button" data-export="excel">
                                <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                                <span>Export as Excel</span>
                            </button>
                            <button type="button" data-export="print">
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
                <table class="responsive-table migratory-table">
                    <thead>
                        <tr>
                            <th>Scientific Name</th>
                            <th>Common Name</th>
                            <th>Protected Area</th>
                            <th>Locations</th>
                            <th>Conservation</th>
                            <th class="migratory-col-count">Recorded Count (Σ)</th>
                            <th class="migratory-col-count">Obs. records</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $index => $row)
                            <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }}">
                                <td><em>{{ $row->scientific_name ?? 'N/A' }}</em></td>
                                <td>{{ $row->common_name ?? 'N/A' }}</td>
                                <td>{{ $row->protected_area_name }}</td>
                                <td>{{ $row->site_name }}</td>
                                <td>{{ $row->conservation_status ? ucwords(str_replace('_', ' ', $row->conservation_status)) : '—' }}</td>
                                <td class="migratory-col-count">
                                    <span class="data-table-count-badge">{{ number_format((int) $row->observation_count) }}</span>
                                </td>
                                <td class="migratory-col-count">
                                    <span class="data-table-count-badge">{{ number_format((int) $row->observation_records) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="data-table-empty-cell">
                                    <div class="data-table-empty-state">
                                        <h3 class="data-table-empty-title">No migratory species observations found</h3>
                                        <p class="data-table-empty-text">Try adjusting the filters or search keywords.</p>
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
                    Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ number_format($rows->total()) }} records
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
</div>
@endsection
