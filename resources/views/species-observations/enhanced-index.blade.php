@extends('layouts.app')

@section('title', 'Species Observations')
@section('header', 'Species Observations')

@section('head')
@vite(['resources/css/species-observations.css', 'resources/css/species-observation-modal.css', 'resources/js/species-observation.js'])
<script>
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        speciesObservationsShow: '{{ route("species-observations.show", ":id") }}',
        speciesObservationsUpdate: '{{ route("species-observations.update", ":id") }}',
        speciesObservationsDestroy: '{{ route("species-observations.destroy", ":id") }}'
    };
</script>
@endsection

@section('content')
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div id="error-message" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button type="button" onclick="dismissErrorMessage()" class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="species-obs-summary-cards mb-6">
                <div class="kpi-grid">
                    <div class="kpi-card kpi-card--blue">
                        <div class="kpi-card-icon kpi-card-icon--blue">
                            <i data-lucide="clipboard-list" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Observations</p>
                            <p class="kpi-card-value" id="summary-total-observations">{{ number_format($summaryStats['total_observations'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">records in view</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--green">
                        <div class="kpi-card-icon kpi-card-icon--green">
                            <i data-lucide="bar-chart-3" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Recorded Count</p>
                            <p class="kpi-card-value" id="summary-total-recorded">{{ number_format($summaryStats['total_recorded_count'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">total count</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--purple">
                        <div class="kpi-card-icon kpi-card-icon--purple">
                            <i data-lucide="map-pin" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Protected Areas</p>
                            <p class="kpi-card-value" id="summary-total-areas">{{ number_format($summaryStats['total_protected_areas'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">unique areas</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-card--orange">
                        <div class="kpi-card-icon kpi-card-icon--orange">
                            <i data-lucide="panda" class="lucide-icon"></i>
                        </div>
                        <div class="kpi-card-body">
                            <p class="kpi-card-label">Total Species Recorded</p>
                            <p class="kpi-card-value" id="summary-total-species">{{ number_format($summaryStats['total_species'] ?? 0) }}</p>
                            <span class="kpi-card-meta kpi-card-meta--neutral">by scientific name</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filter-panel">
                <form method="GET" action="{{ route('species-observations.index') }}">
                    <div class="filter-panel__header">
                        <h2 class="filter-panel__title">Filters</h2>
                        <div class="filter-panel__actions">
                            <button type="submit" class="btn-filter-apply">Apply</button>
                            <button type="button" onclick="clearFilters()" class="btn-filter-clear">Clear</button>
                        </div>
                    </div>
                    <div class="filter-panel__grid filter-panel__grid--cols-5">
                        <!-- Protected Area Filter -->
                        <div class="filter-panel__field">
                            <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                            <select
                                name="protected_area_id"
                                id="protected_area_id"
                                class="filter-panel__select"
                                onchange="toggleSiteNameFilter()"
                            >
                                <option value="">All Areas</option>
                                @foreach($filterOptions['protectedAreas'] as $area)
                                    <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }} data-code="{{ $area->code }}">
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bio Group Filter -->
                        <div class="filter-panel__field">
                            <label for="bio_group" class="filter-panel__label">Bio Group</label>
                            <select
                                name="bio_group"
                                id="bio_group"
                                class="filter-panel__select"
                            >
                                <option value="">All Groups</option>
                                @foreach($filterOptions['bioGroups'] as $key => $group)
                                    <option value="{{ $key }}" {{ request('bio_group') == $key ? 'selected' : '' }}>
                                        {{ $group }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="filter-panel__field">
                            <label for="patrol_year" class="filter-panel__label">Patrol Year</label>
                            <select
                                name="patrol_year"
                                id="patrol_year"
                                class="filter-panel__select"
                            >
                                <option value="">All Years</option>
                                @foreach($filterOptions['years'] as $year)
                                    <option value="{{ $year }}" {{ request('patrol_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Semester Filter -->
                        <div class="filter-panel__field">
                            <label for="patrol_semester" class="filter-panel__label">Semester</label>
                            <select
                                name="patrol_semester"
                                id="patrol_semester"
                                class="filter-panel__select"
                            >
                                <option value="">All Semesters</option>
                                @foreach($filterOptions['semesters'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('patrol_semester') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Site Name Filter -->
                        <div class="filter-panel__field">
                            <label for="site_name" class="filter-panel__label">Site Name</label>
                            <select
                                name="site_name"
                                id="site_name"
                                class="filter-panel__select"
                                disabled
                            >
                                <option value="">All Sites</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Observations Table Card with Action Bar -->
            <div class="action-bar-card">
                <div class="action-bar-card__header">
                    <h2 class="action-bar-card__title">Species Observations ({{ $observations->total() }} records)</h2>
                    <div class="action-bar">
                        <!-- Search -->
                        <div class="action-bar__search-wrap">
                            <div class="action-bar__search">
                                <span class="action-bar__search-icon" aria-hidden="true">
                                    <i data-lucide="search" class="lucide-icon"></i>
                                </span>
                                <input
                                    type="text"
                                    id="species-observations-search"
                                    name="search"
                                    class="action-bar__search-input"
                                    placeholder="Search observations..."
                                    autocomplete="off"
                                    aria-label="Search observations"
                                />
                                <span class="action-bar__search-loading hidden" data-search-loading aria-hidden="true">
                                    <i data-lucide="loader-2" class="lucide-icon spin"></i>
                                </span>
                                <button
                                    type="button"
                                    id="species-observations-search-clear"
                                    class="action-bar__search-clear hidden"
                                    aria-label="Clear search"
                                    onclick="clearSearch()"
                                >
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
                                <span>Add Observation</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="data-table-wrap">
                    <div class="responsive-table-container data-table-container">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>
                                    Protected Area
                                </th>
                                <th>
                                    Station Code
                                </th>
                                <th>
                                    Patrol Period
                                </th>
                                <th>
                                    Bio Group
                                </th>
                                <th>
                                    Common Name
                                </th>
                                <th>
                                    Scientific Name
                                </th>
                                <th>
                                    Count
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($observations as $index => $observation)
                                <tr class="data-table-row {{ $index % 2 === 0 ? 'data-table-row--even' : 'data-table-row--odd' }} observation-row">
                                    <td>
                                        <span class="data-table-cell-truncate font-medium" title="{{ $observation->protectedArea->name ?? 'N/A' }}">{{ $observation->protectedArea->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500 block mt-0.5">{{ $observation->transaction_code }}</span>
                                    </td>
                                    <td>
                                        {{ $observation->station_code }}
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-900">{{ $observation->patrol_year }}</span>
                                        <span class="text-xs text-gray-500">{{ $observation->patrol_semester_text }} Semester</span>
                                    </td>
                                    <td>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $observation->bio_group == 'fauna' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($observation->bio_group) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="data-table-cell-truncate" title="{{ $observation->common_name }}">{{ $observation->common_name }}</span>
                                    </td>
                                    <td>
                                        <em class="data-table-cell-truncate" title="{{ $observation->scientific_name }}">{{ $observation->scientific_name }}</em>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                            {{ $observation->recorded_count }}
                                        </span>
                                    </td>
                                    <td class="data-table-col-actions">
                                        <div class="species-observations-actions">
                                            <!-- View Button -->
                                            <button
                                               type="button"
                                               class="species-observation-action-btn view"
                                               title="View Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openViewModal(this.dataset.observationId, this.dataset.tableName)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"/><path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"/><circle cx="12" cy="12" r="1"/><path d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0"/></svg>
                                            </button>

                                            <!-- Edit Button -->
                                            <button
                                               type="button"
                                               class="species-observation-action-btn edit"
                                               title="Edit Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openEditModal(this.dataset.observationId, this.dataset.tableName)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 1 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <button
                                               type="button"
                                               class="species-observation-action-btn delete"
                                               title="Delete Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openDeleteModal(this.dataset.observationId, this.dataset.tableName)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="data-table-empty-cell">
                                        <div class="data-table-empty-state">
                                            <i data-lucide="file-search" class="lucide-icon data-table-empty-icon"></i>
                                            <h3 class="data-table-empty-title">No observations found</h3>
                                            <p class="data-table-empty-text">
                                                @if(request('search'))
                                                    No species observations match "{{ request('search') }}". Try different keywords or clear the search.
                                                @else
                                                    Species observations will appear here once data has been added.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="species-observations-no-results" class="hidden">
                                <td colspan="8" class="data-table-empty-cell text-gray-500">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($observations->total() > 0)
                <div class="data-table-pagination">
                    <div class="data-table-pagination__info">
                        Showing {{ $observations->firstItem() }} to {{ $observations->lastItem() }} of {{ number_format($observations->total()) }} results
                    </div>
                    @if($observations->hasPages())
                    <nav class="data-table-pagination__nav" aria-label="Pagination">
                        @if($observations->onFirstPage())
                            <button type="button" disabled class="cursor-not-allowed">&lsaquo; Previous</button>
                        @else
                            <a href="{{ $observations->previousPageUrl() }}" rel="prev">&lsaquo; Previous</a>
                        @endif
                        @if($observations->hasMorePages())
                            <a href="{{ $observations->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
                        @else
                            <button type="button" disabled class="cursor-not-allowed">Next &rsaquo;</button>
                        @endif
                    </nav>
                    @endif
                </div>
                @endif
            </div>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Simple notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 10000;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                font-weight: 500;
                max-width: 400px;
                color: white;
                transition: opacity 0.3s ease-in-out;
            `;
            
            if (type === 'success') {
                notification.style.backgroundColor = '#10b981';
            } else if (type === 'error') {
                notification.style.backgroundColor = '#ef4444';
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target) && !event.target.closest('a')) {
                sidebar.classList.add('-translate-x-full');
                document.querySelector('.sidebar-overlay').classList.add('hidden');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Toggle site name filter based on protected area selection
        function toggleSiteNameFilter() {
            const protectedAreaSelect = document.getElementById('protected_area_id');
            const siteNameSelect = document.getElementById('site_name');
            const selectedOption = protectedAreaSelect.options[protectedAreaSelect.selectedIndex];
            const selectedAreaId = protectedAreaSelect.value;
            
            if (selectedAreaId) {
                // Enable site name filter for any selected protected area
                if (siteNameSelect._loading) {
                    // Currently loading, don't do anything
                    return;
                }
                
                // Check if this is the same area that's already loaded
                if (siteNameSelect._lastLoadedArea === selectedAreaId && siteNameSelect.options.length > 1) {
                    // Same area already loaded, just enable the dropdown
                    siteNameSelect.disabled = false;
                    return;
                }
                
                if (siteNameSelect._lastLoadedArea !== selectedAreaId) {
                    // Need to load site names for this area
                    loadSiteNames(selectedAreaId);
                } else {
                    // Already loaded for this area, just enable the dropdown
                    siteNameSelect.disabled = false;
                }
            } else {
                // Disable site name filter when no protected area is selected
                siteNameSelect.disabled = true;
                
                // Store current selection before clearing
                const currentSelection = siteNameSelect.value;
                
                // Clear selection but keep it in memory for potential restoration
                if (currentSelection) {
                    siteNameSelect._clearedSelection = currentSelection;
                }
                siteNameSelect.value = '';
                
                // Reset to default options
                siteNameSelect.innerHTML = '<option value="">All Sites</option>';
                
                // Clear any loading state and last loaded area
                siteNameSelect._loading = false;
                siteNameSelect._lastLoadedArea = null;
            }
        }

        // Load site names via AJAX
        function loadSiteNames(protectedAreaId) {
            const siteNameSelect = document.getElementById('site_name');
            // Get current selection from the dropdown (which should reflect URL parameters on page load)
            const currentSelection = siteNameSelect.value;
            const urlSelection = siteNameSelect._urlSelection;
            
            // Prevent multiple simultaneous requests for the same area
            if (siteNameSelect._loading) {
                return;
            }
            
            // Skip if already loaded for this area and has options
            if (siteNameSelect._lastLoadedArea === protectedAreaId && siteNameSelect.options.length > 1) {
                siteNameSelect.disabled = false;
                // Still try to restore the selection if needed
                if (urlSelection && siteNameSelect.value !== urlSelection) {
                    const optionExists = Array.from(siteNameSelect.options).some(opt => opt.value == urlSelection);
                    if (optionExists) {
                        siteNameSelect.value = urlSelection;
                    }
                }
                return;
            }
            
            // Set loading state
            siteNameSelect._loading = true;
            
            // Store the current selection to preserve it during the update
            const preservedSelection = currentSelection || urlSelection;
            
            // Only disable if we don't already have the correct options loaded
            if (siteNameSelect._lastLoadedArea !== protectedAreaId) {
                siteNameSelect.disabled = true;
            }
            
            // Use the proper Laravel route
            const url = `{{ route('species-observations.site-names', ':id') }}`.replace(':id', protectedAreaId);
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(siteNames => {
                    // Store the current selection before clearing
                    const currentSelection = preservedSelection || siteNameSelect.value;
                    
                    // Create a document fragment for efficient DOM manipulation
                    const fragment = document.createDocumentFragment();
                    
                    // Add "All Sites" option first (this allows filtering by all sites within the protected area)
                    const allSitesOption = document.createElement('option');
                    allSitesOption.value = '';
                    allSitesOption.textContent = 'All Sites';
                    fragment.appendChild(allSitesOption);
                    
                    // Add site name options only if sites exist
                    // Fix: The backend returns { success: true, site_names: [...] }
                    // So we need to access siteNames.site_names
                    if (siteNames && siteNames.success && siteNames.site_names && siteNames.site_names.length > 0) {
                        siteNames.site_names.forEach(siteName => {
                            const option = document.createElement('option');
                            option.value = siteName.id;
                            option.textContent = siteName.name;
                            fragment.appendChild(option);
                        });
                    }
                    
                    // Store the current value to check if it needs to be restored
                    const needsRestoration = currentSelection && siteNameSelect.value !== currentSelection;
                    
                    // Clear and append new options
                    siteNameSelect.innerHTML = '';
                    siteNameSelect.appendChild(fragment);
                    
                    // Restore the selection immediately if it exists and is valid
                    if (needsRestoration) {
                        const optionExists = Array.from(siteNameSelect.options).some(opt => opt.value == currentSelection);
                        if (optionExists) {
                            siteNameSelect.value = currentSelection;
                        }
                    }
                    
                    // Mark this area as loaded
                    siteNameSelect._lastLoadedArea = protectedAreaId;
                })
                .catch(error => {
                    console.error('Error loading site names:', error);
                    // Keep only "All Sites" option on error
                    siteNameSelect.innerHTML = '<option value="">All Sites</option>';
                    // Clear the last loaded area on error
                    siteNameSelect._lastLoadedArea = null;
                })
                .finally(() => {
                    // Always clear loading state and enable dropdown when done
                    siteNameSelect._loading = false;
                    siteNameSelect.disabled = false;
                });
        }


        // Initialize dropdown state from URL parameters on page load
        function initializeDropdownState() {
            const protectedAreaSelect = document.getElementById('protected_area_id');
            const siteNameSelect = document.getElementById('site_name');
            
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const protectedAreaId = urlParams.get('protected_area_id');
            const siteName = urlParams.get('site_name');
            
            // Check sessionStorage for immediate restoration (priority over URL params for faster UI)
            const lastSiteName = sessionStorage.getItem('lastSiteName');
            const lastProtectedArea = sessionStorage.getItem('lastProtectedArea');
            
            // Store the target site name for restoration
            const targetSiteName = lastSiteName || siteName;
            
            if (targetSiteName) {
                siteNameSelect._urlSelection = targetSiteName;
            }
            
            // Clear sessionStorage after use to prevent stale data
            sessionStorage.removeItem('lastSiteName');
            sessionStorage.removeItem('lastProtectedArea');
            
            // If we have a protected area selected, trigger the site name loading
            if (protectedAreaId) {
                // Check if we already have the correct selection in the HTML from Laravel
                const currentSelection = siteNameSelect.value;
                const needsLoading = siteNameSelect.options.length <= 1 || 
                                   (targetSiteName && currentSelection !== targetSiteName);
                
                if (needsLoading) {
                    // Store the current state to prevent flickering
                    const originalHTML = siteNameSelect.innerHTML;
                    const originalDisabled = siteNameSelect.disabled;
                    
                    // The static HTML doesn't have the correct selection, need to load sites
                    setTimeout(() => {
                        // Only disable if we actually need to load new sites
                        if (siteNameSelect._lastLoadedArea !== protectedAreaId) {
                            siteNameSelect.disabled = true;
                        }
                        
                        // Pre-set the target selection to reduce flickering
                        if (targetSiteName) {
                            siteNameSelect.value = targetSiteName;
                        }
                        
                        toggleSiteNameFilter();
                        
                        // After loading sites, restore the target selection if it exists
                        if (targetSiteName) {
                            // Use a more reliable method to wait for the AJAX completion
                            const waitForOptions = () => {
                                if (siteNameSelect.options.length > 1) {
                                    const optionExists = Array.from(siteNameSelect.options).some(opt => opt.value == targetSiteName);
                                    if (optionExists) {
                                        siteNameSelect.value = targetSiteName;
                                    } else {
                                        console.warn('Target site name option not found:', targetSiteName);
                                    }
                                } else {
                                    // Options not loaded yet, wait a bit more
                                    setTimeout(waitForOptions, 50);
                                }
                            };
                            setTimeout(waitForOptions, 50);
                        }
                    }, 25); // Reduced initial delay for faster response
                } else {
                    // The HTML already has the correct selection, just enable the dropdown
                    siteNameSelect.disabled = false;
                    siteNameSelect._lastLoadedArea = protectedAreaId;
                    
                    // Ensure the target selection is set if available
                    if (targetSiteName) {
                        const optionExists = Array.from(siteNameSelect.options).some(opt => opt.value == targetSiteName);
                        if (optionExists) {
                            siteNameSelect.value = targetSiteName;
                        }
                    }
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeDropdownState();
            initializeSearch();
            
            // Add form submit handler to preserve dropdown state
            const filterForm = document.querySelector('form[method="GET"]');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    const siteNameSelect = document.getElementById('site_name');
                    const protectedAreaSelect = document.getElementById('protected_area_id');
                    
                    // Store the current values in session storage for immediate restoration after page reload
                    if (siteNameSelect.value) {
                        sessionStorage.setItem('lastSiteName', siteNameSelect.value);
                    }
                    if (protectedAreaSelect.value) {
                        sessionStorage.setItem('lastProtectedArea', protectedAreaSelect.value);
                    }
                });
            }
            
            // Auto-dismiss error message after 5 seconds
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(() => {
                    dismissErrorMessage();
                }, 5000);
            }
            
            // Populate search input from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            if (searchQuery) {
                const searchInput = document.getElementById('species-observations-search');
                const searchClear = document.getElementById('species-observations-search-clear');
                if (searchInput) {
                    searchInput.value = searchQuery;
                }
                if (searchClear) {
                    searchClear.classList.remove('hidden');
                }
            }
        });

        // Dismiss error message function
        function dismissErrorMessage() {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.3s ease-in-out';
                errorMessage.style.opacity = '0';
                setTimeout(() => {
                    errorMessage.remove();
                }, 300);
            }
        }

        // Search functionality
        let searchTimeout;
        
        function initializeSearch() {
            const searchInput = document.getElementById('species-observations-search');
            const searchClear = document.getElementById('species-observations-search-clear');
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                // Show/hide clear button
                if (query) {
                    searchClear.classList.remove('hidden');
                } else {
                    searchClear.classList.add('hidden');
                }
                
                // Debounce search
                searchTimeout = setTimeout(() => {
                    performServerSearch(query);
                }, 500);
            });
            
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                }
            });
        }
        
        function performServerSearch(query) {
            console.log('=== PERFORMING SEARCH ===');
            console.log('Search query:', query);
            
            // Get current form parameters
            const form = document.querySelector('form[method="GET"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            console.log('Current form params:', Object.fromEntries(params));
            
            // Add or update search parameter
            if (query) {
                params.set('search', query);
                params.delete('page');
            } else {
                params.delete('search');
            }
            
            console.log('Updated params:', Object.fromEntries(params));
            
            // Navigate to new URL
            const newUrl = window.location.pathname + '?' + params.toString();
            console.log('Navigating to:', newUrl);
            window.location.href = newUrl;
        }
        
        function clearSearch() {
            const searchInput = document.getElementById('species-observations-search');
            const searchClear = document.getElementById('species-observations-search-clear');
            
            searchInput.value = '';
            searchClear.classList.add('hidden');
            performServerSearch('');
        }

        // Clear all filters
        function clearFilters() {
            // Reset all form fields
            document.getElementById('protected_area_id').value = '';
            document.getElementById('bio_group').value = '';
            document.getElementById('patrol_year').value = '';
            document.getElementById('patrol_semester').value = '';
            document.getElementById('site_name').value = ''; // This will default to No Specific Site on page reload
            
            // Submit the form to reload page with cleared filters
            const form = document.querySelector('form[method="GET"]');
            form.submit();
        }

        // Initialize pagination when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize pagination enhancement
            initializePagination();
            
            const siteNameSelect = document.getElementById('site_name');
            
            // Initialize loading state and tracking
            siteNameSelect._loading = false;
            siteNameSelect._lastLoadedArea = null;
        });

        // Export dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const exportDropdownBtn = document.getElementById('export-dropdown-btn');
            const exportDropdown = document.getElementById('export-dropdown');
            
            if (exportDropdownBtn && exportDropdown) {
                // Toggle dropdown
                exportDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    exportDropdown.classList.toggle('is-open');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!exportDropdown.contains(e.target) && e.target !== exportDropdownBtn) {
                        exportDropdown.classList.remove('is-open');
                    }
                });
            }
        });

        // Export functionality
        function exportTable(format) {
            // Get current filter parameters
            const form = document.querySelector('form[method="GET"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Add export format parameter
            params.set('export', format);
            
            // Get current search query if exists
            const searchInput = document.getElementById('species-observations-search');
            if (searchInput && searchInput.value.trim()) {
                params.set('search', searchInput.value.trim());
            }
            
            // Construct export URL
            const exportUrl = window.location.pathname + '?' + params.toString();
            
            switch(format) {
                case 'print':
                    // Open print-friendly version in new window
                    const printWindow = window.open(exportUrl + '&print=1', '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            printWindow.print();
                        };
                    }
                    break;
                    
                case 'excel':
                    // Download Excel file
                    window.location.href = exportUrl + '&excel=1';
                    showNotification('Excel export started. Download will begin shortly.', 'success');
                    break;
                    
                case 'pdf':
                    // Download PDF file
                    window.location.href = exportUrl + '&pdf=1';
                    showNotification('PDF export started. Download will begin shortly.', 'success');
                    break;
                    
                default:
                    showNotification('Invalid export format', 'error');
            }
            
            // Close dropdown
            const exportDropdown = document.getElementById('export-dropdown');
            if (exportDropdown) {
                exportDropdown.classList.remove('is-open');
            }
        }

    </script>
    
    <!-- Debug: Test if onclick works -->
    <script>
        console.log('Inline script loaded');
        window.testAddModal = function() {
            console.log('Test add modal function called');
            alert('Test function works!');
        };
        
        // Fallback function in case modal system isn't loaded yet
        window.openAddModal = function() {
            console.log('Fallback openAddModal called');
            if (window.modalSystem) {
                console.log('ModalSystem available, opening modal...');
                modalSystem.open('add', {});
            } else {
                console.log('ModalSystem not ready yet, initializing...');
                // Try to initialize and then open
                setTimeout(() => {
                    if (window.modalSystem) {
                        modalSystem.open('add', {});
                    } else {
                        alert('Modal system is still loading. Please try again in a moment.');
                    }
                }, 500);
            }
        };
        
        // Global functions for modal operations
        window.openDeleteModal = function(observationId, tableName) {
            console.log('openDeleteModal called with ID:', observationId, 'tableName:', tableName);
            if (window.modalSystem) {
                modalSystem.open('delete', {
                    observationId: observationId,
                    tableName: tableName
                });
            } else {
                alert('Modal system is not ready. Please try again.');
            }
        };
        
        window.openEditModal = function(observationId, tableName) {
            console.log('openEditModal called with ID:', observationId, 'tableName:', tableName);
            if (window.modalSystem) {
                modalSystem.open('edit', {
                    observationId: observationId,
                    tableName: tableName
                });
            } else {
                alert('Modal system is not ready. Please try again.');
            }
        };
        
        window.closeModal = function() {
            console.log('closeModal called');
            if (window.modalSystem) {
                modalSystem.close();
            }
        };
    </script>
@endsection
