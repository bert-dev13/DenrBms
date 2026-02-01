<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- FOUC Prevention Script - Apply theme immediately -->
    <script>
        (function() {
            try {
                // Get stored theme with fallback to system preference
                var storedTheme = localStorage.getItem('denr-bms-theme');
                var theme = storedTheme || 
                          (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                
                // Apply theme immediately to prevent FOUC
                if (theme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark-theme');
                }
                
                // Store the initial theme for later use
                window.__initialTheme = theme;
            } catch (e) {
                // Silently fail to prevent script errors
            }
        })();
    </script>
    
    <title>Species Observations | DENR BMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/theme.css', 'resources/css/species-observation-modal.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/theme.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.routes = {
            speciesObservationsShow: '{{ route("species-observations.show", ":id") }}',
            speciesObservationsUpdate: '{{ route("species-observations.update", ":id") }}',
            speciesObservationsDestroy: '{{ route("species-observations.destroy", ":id") }}'
        };
        
        // Debug: Log the routes
        // console.log('Routes defined:', window.routes);
        
    </script>
</head>
<body class="antialiased bg-gray-50">
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle lg:hidden fixed top-4 left-4 z-40 bg-blue-600 text-white p-3 rounded-lg shadow-lg" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        <div class="w-full">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Species Observations</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        @include('components.theme-toggle')
                        
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            {{ now()->format('M j, Y g:i A') }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Species Observations Content -->
        <main class="p-6">
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

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('species-observations.index') }}">
                    <div class="flex items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex-1">Filters</h2>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Apply
                            </button>
                            <button type="button" onclick="clearFilters()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Clear
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <!-- Protected Area Filter -->
                    <div class="flex-1 min-w-40">
                        <label for="protected_area_id" class="block text-sm font-medium text-gray-700 mb-1">Protected Area</label>
                        <select name="protected_area_id" id="protected_area_id" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" onchange="toggleSiteNameFilter()">
                            <option value="">All Areas</option>
                            @foreach($filterOptions['protectedAreas'] as $area)
                                <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }} data-code="{{ $area->code }}">
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bio Group Filter -->
                    <div class="flex-1 min-w-24">
                        <label for="bio_group" class="block text-sm font-medium text-gray-700 mb-1">Bio Group</label>
                        <select name="bio_group" id="bio_group" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Groups</option>
                            @foreach($filterOptions['bioGroups'] as $key => $group)
                                <option value="{{ $key }}" {{ request('bio_group') == $key ? 'selected' : '' }}>
                                    {{ $group }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div class="flex-1 min-w-20">
                        <label for="patrol_year" class="block text-sm font-medium text-gray-700 mb-1">Patrol Year</label>
                        <select name="patrol_year" id="patrol_year" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Years</option>
                            @foreach($filterOptions['years'] as $year)
                                <option value="{{ $year }}" {{ request('patrol_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Filter -->
                    <div class="flex-1 min-w-20">
                        <label for="patrol_semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="patrol_semester" id="patrol_semester" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Semesters</option>
                            @foreach($filterOptions['semesters'] as $value => $label)
                                <option value="{{ $value }}" {{ request('patrol_semester') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Site Name Filter -->
                    <div class="flex-1 min-w-32">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                        <select name="site_name" id="site_name" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" disabled>
                            <option value="">All Sites</option>
                        </select>
                    </div>
                    </div>
                </form>
            </div>

            <!-- Observations Table Search -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 relative">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 max-w-lg">
                <div class="relative w-full">
                    <div class="relative flex items-center h-10">

                        <!-- 🔍 Search Icon (LEFT) -->
                        <span class="absolute left-3 flex items-center pointer-events-none z-10">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>

                        <!-- INPUT -->
                        <input
                            type="text"
                            id="table-search"
                            class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
                                   focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Search observations..."
                            autocomplete="off"
                        />

                        <!-- ❌ Clear Button (FAR RIGHT) -->
                        <button
                            id="search-clear"
                            type="button"
                            class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
                                   text-gray-400 hover:text-gray-600 hidden bg-transparent"
                            onclick="clearSearch()"
                            style="right: 2px;"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Left: Table Title -->
                <div class="flex-shrink-0">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Observations ({{ $observations->total() }} records)
                    </h2>
                </div>
                
                <!-- Right: Export and Add Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <!-- Export Dropdown -->
                    <div class="relative">
                        <button type="button" id="export-dropdown-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors w-full sm:w-auto justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 sm:right-0 right-auto left-0 sm:left-auto">
                            <div class="py-1">
                                <button type="button" onclick="exportTable('print')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                                <button type="button" onclick="exportTable('excel')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Excel</span>
                                </button>
                                <button type="button" onclick="exportTable('pdf')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0112.586 3H7.586A1 1 0 016 3.414l5.414 5.414a1 1 0 001.414.707V19a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add New Observation Button -->
                    <button type="button" onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add New Observation</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>

                <div class="responsive-table-container">
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
                            @forelse($observations as $observation)
                                <tr class="hover:bg-gray-50 observation-row">
                                    <td>
                                        <span class="font-medium text-gray-900">{{ $observation->protectedArea->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $observation->transaction_code }}</span>
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
                                        {{ $observation->common_name }}
                                    </td>
                                    <td>
                                        <em>{{ $observation->scientific_name }}</em>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                            {{ $observation->recorded_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                            <!-- View Button -->
                                            <button type="button" 
                                               class="action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="View Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openViewModal(this.dataset.observationId, this.dataset.tableName)">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                             <!-- Edit Button -->
                                            <button type="button" 
                                               class="action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Edit Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openEditModal(this.dataset.observationId, this.dataset.tableName)">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" 
                                               class="action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Delete Observation"
                                               data-observation-id="{{ $observation->id }}"
                                               data-table-name="{{ e($observation->table_name ?? $observation->getTable()) }}"
                                               onclick="openDeleteModal(this.dataset.observationId, this.dataset.tableName)">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No observations found</h3>
                                            <p class="text-gray-500">No species observations match the current filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="species-observations-no-results" class="hidden">
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($observations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ $observations->firstItem() }} to {{ $observations->lastItem() }} of {{ $observations->total() }} results
                            </div>
                            <div id="pagination-container">
                                {{ $observations->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
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
                document.getElementById('table-search').value = searchQuery;
                document.getElementById('search-clear').classList.remove('hidden');
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
            const searchInput = document.getElementById('table-search');
            const searchClear = document.getElementById('search-clear');
            
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
            const searchInput = document.getElementById('table-search');
            const searchClear = document.getElementById('search-clear');
            
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
            
            // Toggle dropdown
            exportDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!exportDropdown.contains(e.target) && e.target !== exportDropdownBtn) {
                    exportDropdown.classList.add('hidden');
                }
            });
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
            const searchInput = document.getElementById('table-search');
            if (searchInput.value.trim()) {
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
            document.getElementById('export-dropdown').classList.add('hidden');
        }

    </script>
    
    @vite(['resources/js/species-observation-modal.js'])
    
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
</body>
</html>
