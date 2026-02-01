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
    
    <title>DENR BMS - Protected Areas | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/protected-areas.css', 'resources/css/protected-area-modal.css', 'resources/css/theme.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/protected-area-modal.js', 'resources/js/theme.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
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
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Protected Areas</h1>
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

        <!-- Protected Areas Content -->
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

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Areas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Areas</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-areas-count">{{ number_format($stats['total_areas']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Sites -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Sites</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sites']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Observations -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Observations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_observations']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Species Diversity -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Species Diversity</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['species_diversity']) }}</p>
                            <p class="text-xs text-gray-600">unique species</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('protected-areas.index') }}">
                    <div class="flex items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex-1">Filters</h2>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Apply
                            </button>
                            <button type="button" onclick="clearProtectedAreaFilters()" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium border border-gray-300">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Status Dropdown -->
                        <div class="w-full">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                                <option value="">All</option>
                                <option value="active" {{ (isset($statusFilter) && $statusFilter === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="no_data" {{ (isset($statusFilter) && $statusFilter === 'no_data') ? 'selected' : '' }}>No Data</option>
                            </select>
                        </div>

                        <!-- Sort By Dropdown -->
                        <div class="w-full">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" id="sort" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                                <option value="name" {{ (!isset($sort) || $sort === 'name') ? 'selected' : '' }}>Name (A–Z)</option>
                                <option value="code" {{ (isset($sort) && $sort === 'code') ? 'selected' : '' }}>Code (A–Z)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Protected Areas Table Card (with search + add button) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Search + Header -->
                <div class="px-6 py-4 border-b border-gray-200 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Search -->
                        <div class="flex-1 max-w-lg">
                            <div class="relative w-full">
                                <div class="relative flex items-center h-10">
                                    <span class="absolute left-3 flex items-center pointer-events-none z-10">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </span>

                                    <input
                                        type="text"
                                        id="protected-area-search"
                                        class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Search protected areas..."
                                        autocomplete="off"
                                        oninput="filterProtectedAreasTable()"
                                    />

                                    <button
                                        id="protected-area-search-clear"
                                        type="button"
                                        class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
                                               text-gray-400 hover:text-gray-600 hidden bg-transparent"
                                        onclick="clearProtectedAreaSearch()"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Title and Export Dropdown -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Protected Areas ({{ $protectedAreas->total() }} records)
                            </h2>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                                                </svg>
                                                <span>Excel</span>
                                            </button>
                                            <button type="button" onclick="exportTable('pdf')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>PDF</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Add Protected Area Button -->
                                <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Add Protected Area</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="responsive-table-container">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>
                                    Area Code
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Observations
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="protected-area-table-body">
                            @forelse ($protectedAreas as $area)
                                <tr class="hover:bg-gray-50 protected-area-row" data-area-id="{{ $area->id }}">
                                    <td>
                                        <div class="font-medium text-gray-900">
                                            {{ $area->code }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-gray-900">
                                            {{ $area->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($area->species_observations_count) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                observations
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($area->species_observations_count > 0)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No Data
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                            <!-- View Button -->
                                            <button type="button" onclick="openViewModal({{ $area->id }})" 
                                               class="action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="View Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                             <!-- Edit Button -->
                                            <button type="button" onclick="openEditModal({{ $area->id }})" 
                                               class="action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Edit Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" onclick="openDeleteModal({{ $area->id }})" 
                                               class="action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Delete Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No protected areas</h3>
                                            <p class="text-gray-500">Protected areas will appear here once they are added to the system.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Results Information -->
                        <div class="text-sm text-gray-700">
                            Showing 
                            {{ ($protectedAreas->currentPage() - 1) * $protectedAreas->perPage() + 1 }} 
                            to 
                            {{ min($protectedAreas->currentPage() * $protectedAreas->perPage(), $protectedAreas->total()) }} 
                            of {{ $protectedAreas->total() }} results
                        </div>
                        
                        <!-- Custom Previous/Next Navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($protectedAreas->onFirstPage())
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    « Previous
                                </button>
                            @else
                                <a href="{{ $protectedAreas->previousPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    « Previous
                                </a>
                            @endif
                            
                            <!-- Next Button -->
                            @if($protectedAreas->hasMorePages())
                                <a href="{{ $protectedAreas->nextPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    Next »
                                </a>
                            @else
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    Next »
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Laravel Pagination Links (hidden but kept for functionality) -->
                    <div class="hidden">
                        {{ $protectedAreas->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
// Ensure modal system is initialized
function ensureModalSystem() {
    // Only initialize if the class exists (for existing modal system)
    if (typeof ProtectedAreaModalSystem !== 'undefined') {
        if (!window.protectedAreaModalSystem) {
            window.protectedAreaModalSystem = new ProtectedAreaModalSystem();
        }
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
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

// Clear filters (status + sort)
function clearProtectedAreaFilters() {
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    if (status) status.value = '';
    if (sort) sort.value = 'name';

    // Submit the form to reset filters
    status.closest('form').submit();
}

// Client-side search over protected areas table
function filterProtectedAreasTable() {
    const input = document.getElementById('protected-area-search');
    const clearBtn = document.getElementById('protected-area-search-clear');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#protected-area-table-body .protected-area-row');

    if (clearBtn) {
        clearBtn.classList.toggle('hidden', filter.length === 0);
    }

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function clearProtectedAreaSearch() {
    const input = document.getElementById('protected-area-search');
    input.value = '';
    filterProtectedAreasTable();
}

// Initialize modal system when page loads
document.addEventListener('DOMContentLoaded', function() {
    ensureModalSystem();
    
    // Initialize export dropdown
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
    const searchInput = document.getElementById('protected-area-search');
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
    document.getElementById('export-dropdown').classList.add('hidden');
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

</script>

</body>
</html>
