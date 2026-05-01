@extends('layouts.app')

@section('title', 'Protected Areas')
@section('header', 'Protected Areas')

@section('head')
@vite(['resources/css/pages/protected_areas.css', 'resources/css/pages/species_observation_modal.css', 'resources/css/pages/protected_area_modal.css', 'resources/js/pages/protected_area_modal.js', 'resources/js/pages/protected_areas.js'])
@endsection

@section('content')
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="lucide-icon w-5 h-5 mr-2 flex-shrink-0"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Stats Grid (KPI Cards) -->
            <div class="kpi-grid mb-6">
                <!-- Total Areas -->
                <div class="kpi-card kpi-card--green">
                    <div class="kpi-card-icon kpi-card-icon--green">
                        <i data-lucide="map-pin" class="lucide-icon"></i>
                    </div>
                    <div class="kpi-card-body">
                        <p class="kpi-card-label">Total Areas</p>
                        <p class="kpi-card-value" id="total-areas-count">{{ number_format($stats['total_areas']) }}</p>
                    </div>
                </div>

                <!-- Total Sites -->
                <div class="kpi-card kpi-card--orange">
                    <div class="kpi-card-icon kpi-card-icon--orange">
                        <i data-lucide="building-2" class="lucide-icon"></i>
                    </div>
                    <div class="kpi-card-body">
                        <p class="kpi-card-label">Total Sites</p>
                        <p class="kpi-card-value">{{ number_format($stats['total_sites']) }}</p>
                    </div>
                </div>

                <!-- Total Observations -->
                <div class="kpi-card kpi-card--purple">
                    <div class="kpi-card-icon kpi-card-icon--purple">
                        <i data-lucide="clipboard-list" class="lucide-icon"></i>
                    </div>
                    <div class="kpi-card-body">
                        <p class="kpi-card-label">Total Observations</p>
                        <p class="kpi-card-value">{{ number_format($stats['total_observations']) }}</p>
                    </div>
                </div>

                <!-- Species Tracked -->
                <div class="kpi-card kpi-card--blue">
                    <div class="kpi-card-icon kpi-card-icon--blue">
                        <i data-lucide="panda" class="lucide-icon"></i>
                    </div>
                    <div class="kpi-card-body">
                        <p class="kpi-card-label">Species Tracked</p>
                        <p class="kpi-card-value">{{ number_format($stats['species_diversity']) }}</p>
                        <span class="kpi-card-meta kpi-card-meta--neutral">unique species</span>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filter-panel">
                <form method="GET" action="{{ route('protected-areas.index') }}" id="protected-areas-filter-form">
                    <div class="pa-filter-row">
                        <h2 class="filter-panel__title">Filters</h2>
                        <div class="filter-panel__field pa-filter-field">
                            <label for="status" class="filter-panel__label">Status</label>
                            <select name="status" id="status" class="filter-panel__select">
                                <option value="">All</option>
                                <option value="active" {{ (isset($statusFilter) && $statusFilter === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="no_data" {{ (isset($statusFilter) && $statusFilter === 'no_data') ? 'selected' : '' }}>No Data</option>
                            </select>
                        </div>
                        <div class="filter-panel__field pa-filter-field">
                            <label for="sort" class="filter-panel__label">Sort By</label>
                            <select name="sort" id="sort" class="filter-panel__select">
                                <option value="name" {{ (!isset($sort) || $sort === 'name') ? 'selected' : '' }}>Name (A–Z)</option>
                                <option value="code" {{ (isset($sort) && $sort === 'code') ? 'selected' : '' }}>Code (A–Z)</option>
                            </select>
                        </div>
                        <div class="filter-panel__actions pa-filter-actions">
                            <button type="submit" class="btn-filter-apply">Apply</button>
                            <button type="button" onclick="clearProtectedAreaFilters()" class="btn-filter-clear">Clear</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Protected Areas Table Card with Action Bar -->
            <div class="action-bar-card">
                <div class="action-bar-card__header">
                    <h2 class="action-bar-card__title">Protected Areas ({{ $protectedAreas->total() }} records)</h2>
                    <div class="action-bar">
                        <!-- Search -->
                        <div class="action-bar__search-wrap">
                            <div class="action-bar__search">
                                <span class="action-bar__search-icon" aria-hidden="true">
                                    <i data-lucide="search" class="lucide-icon"></i>
                                </span>
                                <input
                                    type="text"
                                    id="protected-area-search"
                                    name="search"
                                    value="{{ request('search', '') }}"
                                    class="action-bar__search-input"
                                    placeholder="Search protected areas..."
                                    autocomplete="off"
                                    aria-label="Search protected areas"
                                />
                                <span class="action-bar__search-loading hidden" data-search-loading aria-hidden="true">
                                    <i data-lucide="loader-2" class="lucide-icon spin"></i>
                                </span>
                                <button type="button" id="protected-area-search-clear" class="action-bar__search-clear hidden" data-search-clear aria-label="Clear search">
                                    <i data-lucide="x" class="lucide-icon"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Export & Add -->
                        <div class="action-bar__actions">
                            <div class="action-bar__export-wrap">
                                <button type="button" id="export-dropdown-btn" class="action-bar__export-btn">
                                    <i data-lucide="download" class="lucide-icon"></i>
                                    <span>Export</span>
                                    <i data-lucide="chevron-down" class="lucide-icon"></i>
                                </button>
                                <div id="export-dropdown" class="action-bar__export-dropdown">
                                    <button type="button" onclick="exportTable('pdf')">
                                        <i data-lucide="file-text" class="lucide-icon"></i>
                                        <span>Export as PDF</span>
                                    </button>
                                    <button type="button" onclick="exportTable('excel')">
                                        <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                                        <span>Export as Excel</span>
                                    </button>
                                    <button type="button" onclick="exportTable('print')">
                                        <i data-lucide="printer" class="lucide-icon"></i>
                                        <span>Print</span>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="openAddModal()" class="action-bar__add-btn">
                                <i data-lucide="plus" class="lucide-icon"></i>
                                <span>Add Protected Area</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="data-table-wrap">
                    <div class="responsive-table-container data-table-container">
                        <table class="responsive-table protected-areas-table">
                            <thead>
                                <tr>
                                    <th class="pa-col-code">Area Code</th>
                                    <th class="pa-col-name">Name</th>
                                    <th class="pa-col-obs">Observations</th>
                                    <th class="pa-col-status">Status</th>
                                    <th class="pa-col-actions data-table-col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="protected-area-table-body">
                                @forelse ($protectedAreas as $index => $area)
                                    <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }}" data-area-id="{{ $area->id }}">
                                        <td class="pa-col-code">
                                            <span class="data-table-cell-truncate font-medium" title="{{ e($area->code) }}">{{ $area->code }}</span>
                                        </td>
                                        <td class="pa-col-name">
                                            <span class="data-table-cell-truncate font-medium" title="{{ e($area->name) }}">{{ $area->name }}</span>
                                        </td>
                                        <td class="pa-col-obs">
                                            <span class="data-table-count-badge">{{ number_format($area->species_observations_count) }}</span>
                                            <span class="text-xs text-gray-500 ml-1">obs</span>
                                        </td>
                                        <td class="pa-col-status">
                                            @if($area->species_observations_count > 0)
                                                <span class="data-table-status-badge data-table-status-badge--active">Active</span>
                                            @else
                                                <span class="data-table-status-badge data-table-status-badge--no-data">No Data</span>
                                            @endif
                                        </td>
                                        <td class="pa-col-actions data-table-col-actions">
                                            <div class="species-observations-actions">
                                            <!-- View Button -->
                                            <button type="button"
                                               class="species-observation-action-btn view"
                                               title="View Protected Area"
                                               data-area-id="{{ $area->id }}"
                                               onclick="openViewModal(Number(this.dataset.areaId))">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"/><path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"/><circle cx="12" cy="12" r="1"/><path d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0"/></svg>
                                            </button>
                                            <!-- Edit Button -->
                                            <button type="button"
                                               class="species-observation-action-btn edit"
                                               title="Edit Protected Area"
                                               data-area-id="{{ $area->id }}"
                                               onclick="openEditModal(Number(this.dataset.areaId))">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                            </button>
                                            <!-- Delete Button -->
                                            <button type="button"
                                               class="species-observation-action-btn delete"
                                               title="Delete Protected Area"
                                               data-area-id="{{ $area->id }}"
                                               onclick="openDeleteModal(Number(this.dataset.areaId))">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="data-table-empty-cell">
                                            <div class="data-table-empty-state">
                                                <i data-lucide="map-pin" class="lucide-icon data-table-empty-icon"></i>
                                                <h3 class="data-table-empty-title">No results found</h3>
                                                <p class="data-table-empty-text">
                                                    @if(request('search'))
                                                        No protected areas match "{{ request('search') }}". Try different keywords or clear the search.
                                                    @else
                                                        Protected areas will appear here once they are added to the system.
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

                <!-- Pagination -->
                @if($protectedAreas->total() > 0)
                <div class="data-table-pagination">
                    <div class="data-table-pagination__info">
                        Showing {{ $protectedAreas->firstItem() }} to {{ $protectedAreas->lastItem() }} of {{ number_format($protectedAreas->total()) }} results
                    </div>
                    @if($protectedAreas->hasPages())
                    <nav class="data-table-pagination__nav" aria-label="Pagination">
                        @if($protectedAreas->onFirstPage())
                            <button type="button" disabled class="cursor-not-allowed">&lsaquo; Previous</button>
                        @else
                            <a href="{{ $protectedAreas->previousPageUrl() }}" rel="prev">&lsaquo; Previous</a>
                        @endif
                        @if($protectedAreas->hasMorePages())
                            <a href="{{ $protectedAreas->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
                        @else
                            <button type="button" disabled class="cursor-not-allowed">Next &rsaquo;</button>
                        @endif
                    </nav>
                    @endif
                </div>
                @endif
            </div>
@endsection

