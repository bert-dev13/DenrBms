<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>DENR BMS - Protected Area Sites | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/protected-area-sites-modal.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/protected-area-sites-modal.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        // Pass protected areas data to JavaScript for modal dropdowns
        window.protectedAreas = @json(\App\Models\ProtectedArea::orderBy('name')->get(['id', 'name', 'code']));
    </script>
</head>
<body class="antialiased bg-gray-50">
    <!-- Your existing page layout here -->
    <!-- Include sidebar, header, etc. -->

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Protected Area Sites Content -->
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

            <!-- Protected Area Sites Table Card -->
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
                                        id="protected-area-sites-search"
                                        class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Search protected area sites..."
                                        autocomplete="off"
                                        oninput="filterProtectedAreaSitesTable()"
                                    />

                                    <button
                                        id="protected-area-sites-search-clear"
                                        type="button"
                                        class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
                                               text-gray-400 hover:text-gray-600 hidden bg-transparent"
                                        onclick="clearProtectedAreaSitesSearch()"
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

                        <!-- Title and Add Button -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Protected Area Sites ({{ $siteNames->total() }} records)
                            </h2>
                            <button onclick="openAddProtectedAreaSitesModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add Protected Area Site</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="responsive-table-container">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Site Name</th>
                                <th>Protected Area</th>
                                <th>Station Code</th>
                                <th>Observations</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="protected-area-sites-table-body">
                            @forelse ($siteNames as $site)
                                <tr class="hover:bg-gray-50 protected-area-sites-row" data-site-id="{{ $site->id }}">
                                    <td>
                                        <div class="font-medium text-gray-900">
                                            {{ $site->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($site->protectedArea)
                                                <div class="text-sm text-gray-900">
                                                    {{ $site->protectedArea->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $site->protectedArea->code }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">Not assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($site->station_code)
                                                <span class="station-code-badge">
                                                    {{ $site->station_code }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-400">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($site->species_observations_count ?? 0) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                observations
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($site->protectedArea)
                                            <span class="status-badge status-badge-active">
                                                Active
                                            </span>
                                        @else
                                            <span class="status-badge status-badge-unassigned">
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                            <!-- View Button -->
                                            <button type="button" onclick="openViewProtectedAreaSitesModal({{ $site->id }})" 
                                               class="protected-area-sites-action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="View Site">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                             <!-- Edit Button -->
                                            <button type="button" onclick="openEditProtectedAreaSitesModal({{ $site->id }})" 
                                               class="protected-area-sites-action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Edit Site">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" onclick="openDeleteProtectedAreaSitesModal({{ $site->id }})" 
                                               class="protected-area-sites-action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Delete Site">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No Protected Area Sites</h3>
                                            <p class="text-gray-500">Protected area sites will appear here once they are added to the system.</p>
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
                            {{ ($siteNames->currentPage() - 1) * $siteNames->perPage() + 1 }} 
                            to 
                            {{ min($siteNames->currentPage() * $siteNames->perPage(), $siteNames->total()) }} 
                            of {{ $siteNames->total() }} results
                        </div>
                        
                        <!-- Custom Previous/Next Navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($siteNames->onFirstPage())
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    « Previous
                                </button>
                            @else
                                <a href="{{ $siteNames->previousPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    « Previous
                                </a>
                            @endif
                            
                            <!-- Next Button -->
                            @if($siteNames->hasMorePages())
                                <a href="{{ $siteNames->nextPageUrl() }}" 
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
                        {{ $siteNames->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Client-side search over protected area sites table
    function filterProtectedAreaSitesTable() {
        const input = document.getElementById('protected-area-sites-search');
        const clearBtn = document.getElementById('protected-area-sites-search-clear');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('#protected-area-sites-table-body .protected-area-sites-row');

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', filter.length === 0);
        }

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    function clearProtectedAreaSitesSearch() {
        const input = document.getElementById('protected-area-sites-search');
        input.value = '';
        filterProtectedAreaSitesTable();
    }

    // Initialize modal system when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Update the modal system to include protected areas data
        if (window.protectedAreaSitesModalSystem) {
            window.protectedAreaSitesModalSystem.generateProtectedAreaOptions = function(selectedId = null) {
                if (!window.protectedAreas) return '';
                
                return window.protectedAreas.map(area => 
                    `<option value="${area.id}" ${area.id == selectedId ? 'selected' : ''}>${area.name} (${area.code})</option>`
                ).join('');
            };
        }
    });
    </script>
</body>
</html>
