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
    
    <title>DENR BMS - Analytics | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/theme.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/theme.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- Main Content Area -->
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Observation Analytics</h1>
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

        <!-- Analytics Content -->
        <main class="p-6">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Observation Analytics</h1>
                <p class="text-gray-600 mt-2">Analyze biodiversity observation trends over time for protected areas.</p>
            </div>

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
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_areas']) }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
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
                        <div class="p-3 bg-blue-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

            <!-- Protected Area Observation Trends Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Select Protected Area</h2>
                        <p class="text-sm text-gray-600 mt-1">Choose a protected area to view observation trends</p>
                    </div>
                    <div class="w-96">
                        <select id="protectedAreaSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Select a protected area...</option>
                            @foreach($protectedAreas as $area)
                                @if(preg_match('/\([A-Z]+\)/', $area->name))
                                    <option value="{{ $area->id }}" {{ $area->code === 'BHNP' ? 'selected' : '' }}>{{ $area->name }}</option>
                                @else
                                    <option value="{{ $area->id }}" {{ $area->code === 'BHNP' ? 'selected' : '' }}>{{ $area->name }} ({{ $area->code }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="relative" id="chartContainer" style="display: none;">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900" id="chartTitle">Observation Trends</h2>
                            <p class="text-sm text-gray-600 mt-1" id="chartSubtitle">Yearly observation patterns for selected area</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Increasing
                            </span>
                            <span class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Decreasing
                            </span>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <canvas id="analyticsChart" class="w-full" style="height: 250px !important; max-height: 250px !important; min-height: 250px !important;"></canvas>
                        <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                            <div class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Loading analytics data...</span>
                            </div>
                        </div>
                        <div id="noDataMessage" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75" style="display: none;">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-gray-600">No observation data available for this protected area</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200" id="chartStats" style="display: none;">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Total Years Tracked</p>
                        <p class="text-lg font-semibold text-gray-900" id="totalYears">-</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Total Observations</p>
                        <p class="text-lg font-semibold text-gray-900" id="totalObservations">-</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Trend Direction</p>
                        <p class="text-lg font-semibold" id="trendDirection">-</p>
                    </div>
                </div>
            </div>

            <!-- Top Species Trends Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Species Observation Trends (2021-2025)</h2>
                        <p class="text-sm text-gray-600 mt-1">Select a species to view its observation trends across all protected areas</p>
                    </div>
                    <button onclick="refreshSpeciesTrends()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                
                <!-- Species Selector -->
                <div class="mb-6">
                    <label for="speciesSelect" class="block text-sm font-medium text-gray-700 mb-2">Select Species</label>
                    <div class="flex items-center space-x-4">
                        <select id="speciesSelect" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Choose a species...</option>
                        </select>
                        <div id="speciesInfo" class="text-sm text-gray-600 hidden">
                            <span id="speciesTotalObs"></span> total observations
                        </div>
                    </div>
                </div>
                
                <!-- Species Trends Chart Container -->
                <div class="relative" id="speciesChartContainer" style="display: none;">
                    <div class="relative">
                        <canvas id="speciesTrendsChart" class="w-full" style="height: 250px !important; max-height: 250px !important; min-height: 250px !important;"></canvas>
                        <div id="speciesTrendsLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                            <div class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Loading species trend data...</span>
                            </div>
                        </div>
                        <div id="speciesTrendsNoData" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75" style="display: none;">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-gray-600">No observation data available for this species</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div id="speciesEmptyState" class="text-center py-12" style="display: none;">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-600">Select a species from the dropdown to view its observation trends</p>
                </div>
                
                <!-- Loading State -->
                <div id="speciesLoadingState" class="text-center py-12">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600">Loading species data...</span>
                    </div>
                </div>
            </div>
        </main>

    <!-- JavaScript -->
    <script>
        let analyticsChart = null;
        let speciesTrendsChart = null;
        
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

        // Handle protected area selection
        document.getElementById('protectedAreaSelect').addEventListener('change', function() {
            const protectedAreaId = this.value;
            
            if (protectedAreaId) {
                loadAnalyticsData(protectedAreaId);
            } else {
                document.getElementById('chartContainer').style.display = 'none';
            }
        });

        // Auto-load Bangan Hill National Park data on page load
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('protectedAreaSelect');
            const banganOption = select.querySelector('option[value][selected]');
            
            if (banganOption) {
                loadAnalyticsData(banganOption.value);
            }
            
            // Load species trends data on page load
            loadSpeciesTrendsData();
        });

        // Load analytics data for selected protected area
        async function loadAnalyticsData(protectedAreaId) {
            console.log('Loading analytics data for protected area:', protectedAreaId);
            
            // Show chart container and loading state
            document.getElementById('chartContainer').style.display = 'block';
            document.getElementById('chartLoading').style.display = 'flex';
            document.getElementById('noDataMessage').style.display = 'none';
            
            try {
                const response = await fetch(`/analytics/data?protected_area_id=${protectedAreaId}`);
                const data = await response.json();
                console.log('Received analytics data:', data);
                
                // Hide loading indicator
                document.getElementById('chartLoading').style.display = 'none';
                
                // Update chart title and subtitle
                document.getElementById('chartTitle').textContent = `Observation Trends - ${data.protected_area.name}`;
                document.getElementById('chartSubtitle').textContent = `Yearly observation patterns for ${data.protected_area.name} (${data.protected_area.code})`;
                
                // Check if there's data
                if (data.data.length === 0) {
                    document.getElementById('noDataMessage').style.display = 'flex';
                    document.getElementById('totalYears').textContent = '0';
                    document.getElementById('totalObservations').textContent = '0';
                    document.getElementById('trendDirection').innerHTML = '<span class="text-gray-600">→ No Data</span>';
                    document.getElementById('chartStats').style.display = 'none';
                    return;
                }
                
                // Update statistics
                document.getElementById('totalYears').textContent = data.total_years;
                document.getElementById('totalObservations').textContent = data.total_observations.toLocaleString();
                
                // Show chart statistics
                document.getElementById('chartStats').style.display = 'grid';
                
                // Calculate trend direction
                if (data.data.length >= 2) {
                    const recent = data.data.slice(-3);
                    const older = data.data.slice(-6, -3);
                    
                    if (recent.length > 0 && older.length > 0) {
                        const recentAvg = recent.reduce((sum, item) => sum + item.yearly_count, 0) / recent.length;
                        const olderAvg = older.reduce((sum, item) => sum + item.yearly_count, 0) / older.length;
                        
                        const trendElement = document.getElementById('trendDirection');
                        if (recentAvg > olderAvg) {
                            trendElement.innerHTML = '<span class="text-green-600">↑ Increasing</span>';
                        } else if (recentAvg < olderAvg) {
                            trendElement.innerHTML = '<span class="text-red-600">↓ Decreasing</span>';
                        } else {
                            trendElement.innerHTML = '<span class="text-gray-600">→ Stable</span>';
                        }
                    } else {
                        document.getElementById('trendDirection').innerHTML = '<span class="text-gray-600">→ Stable</span>';
                    }
                } else {
                    document.getElementById('trendDirection').innerHTML = '<span class="text-gray-600">→ Insufficient Data</span>';
                }
                
                // Prepare chart data
                const labels = data.data.map(item => item.year.toString());
                const counts = data.data.map(item => item.count); // Cumulative totals
                const yearlyCounts = data.data.map(item => item.yearly_count); // Individual year counts
                
                // Create zigzag effect by alternating colors based on trend
                const backgroundColors = counts.map((count, index) => {
                    if (index === 0) return 'rgba(59, 130, 246, 0.1)';
                    const trend = count - counts[index - 1];
                    return trend >= 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                });
                
                const borderColors = counts.map((count, index) => {
                    if (index === 0) return 'rgba(59, 130, 246, 1)';
                    const trend = count - counts[index - 1];
                    return trend >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                });
                
                // Destroy existing chart if it exists
                if (analyticsChart) {
                    analyticsChart.destroy();
                }
                
                // Create new chart
                const ctx = document.getElementById('analyticsChart').getContext('2d');
                analyticsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Observation Count',
                            data: counts,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: borderColors,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            segment: {
                                borderColor: function(context) {
                                    const index = context.p0DataIndex;
                                    if (index === 0) return 'rgba(59, 130, 246, 1)';
                                    const trend = counts[index] - counts[index - 1];
                                    return trend >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                                }
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: false,
                        layout: {
                            padding: {
                                top: 5,
                                right: 10,
                                bottom: 5,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return `Year: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        const cumulativeTotal = context.parsed.y.toLocaleString();
                                        const yearlyCount = yearlyCounts[context.dataIndex].toLocaleString();
                                        let label = `Cumulative Total: ${cumulativeTotal}`;
                                        
                                        if (context.dataIndex > 0) {
                                            const prevCumulative = counts[context.dataIndex - 1];
                                            const currentCumulative = context.parsed.y;
                                            const change = currentCumulative - prevCumulative;
                                            const changePercent = prevCumulative > 0 ? ((change / prevCumulative) * 100).toFixed(1) : 0;
                                            const changeSymbol = change >= 0 ? '+' : '';
                                            label += ` (${changeSymbol}${yearlyCount} this year / ${changeSymbol}${changePercent}%)`;
                                        } else {
                                            label += ` (${yearlyCount} this year)`;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    padding: 2
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    padding: 2,
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
                
            } catch (error) {
                console.error('Error loading analytics data:', error);
                document.getElementById('chartLoading').innerHTML = 
                    '<div class="text-center text-red-600">Error loading analytics data</div>';
            }
        }

        // Load species trends data
        async function loadSpeciesTrendsData() {
            console.log('Loading species trends data...');
            
            // Show loading state
            document.getElementById('speciesLoadingState').style.display = 'block';
            document.getElementById('speciesEmptyState').style.display = 'none';
            
            try {
                const response = await fetch('/analytics/species-trends');
                const data = await response.json();
                console.log('Received species list data:', data);
                
                // Hide loading state
                document.getElementById('speciesLoadingState').style.display = 'none';
                
                if (data.species_list && data.species_list.length > 0) {
                    populateSpeciesDropdown(data.species_list);
                } else {
                    console.log('No species data available');
                    // Show empty state when no species data is available
                    document.getElementById('speciesEmptyState').style.display = 'block';
                }
                
            } catch (error) {
                console.error('Error loading species trends data:', error);
                // Hide loading state and show empty state on error
                document.getElementById('speciesLoadingState').style.display = 'none';
                document.getElementById('speciesEmptyState').style.display = 'block';
            }
        }

        // Populate species dropdown
        function populateSpeciesDropdown(speciesList) {
            const select = document.getElementById('speciesSelect');
            select.innerHTML = '<option value="">Choose a species...</option>';
            
            speciesList.forEach(species => {
                const option = document.createElement('option');
                option.value = species.scientific_name;
                const displayName = species.common_name ? species.common_name : species.scientific_name;
                option.textContent = `#${species.rank} ${displayName} (${species.total_observations.toLocaleString()} obs)`;
                option.setAttribute('data-total-obs', species.total_observations);
                select.appendChild(option);
            });
            
            // Add change event listener
            select.addEventListener('change', function() {
                const scientificName = this.value;
                if (scientificName) {
                    const selectedOption = this.options[this.selectedIndex];
                    const totalObs = selectedOption.getAttribute('data-total-obs');
                    showSpeciesInfo(totalObs);
                    loadSpeciesTrendData(scientificName);
                } else {
                    hideSpeciesChart();
                }
            });
            
            // Auto-select the first species (rank #1) when dropdown is populated
            if (speciesList.length > 0) {
                const firstSpecies = speciesList[0];
                select.value = firstSpecies.scientific_name;
                
                // Trigger the change event to load the chart
                const event = new Event('change', { bubbles: true });
                select.dispatchEvent(event);
            }
        }

        // Show species info
        function showSpeciesInfo(totalObservations) {
            const speciesInfo = document.getElementById('speciesInfo');
            const speciesTotalObs = document.getElementById('speciesTotalObs');
            speciesTotalObs.textContent = parseInt(totalObservations).toLocaleString();
            speciesInfo.classList.remove('hidden');
        }

        // Hide species info
        function hideSpeciesInfo() {
            const speciesInfo = document.getElementById('speciesInfo');
            speciesInfo.classList.add('hidden');
        }

        // Load individual species trend data
        async function loadSpeciesTrendData(scientificName) {
            console.log('Loading trend data for species:', scientificName);
            
            // Show chart container and loading state
            document.getElementById('speciesChartContainer').style.display = 'block';
            document.getElementById('speciesEmptyState').style.display = 'none';
            document.getElementById('speciesTrendsLoading').style.display = 'flex';
            document.getElementById('speciesTrendsNoData').style.display = 'none';
            
            try {
                const response = await fetch(`/analytics/species-trend-data?scientific_name=${encodeURIComponent(scientificName)}`);
                const data = await response.json();
                console.log('Received species trend data:', data);
                
                // Hide loading indicator
                document.getElementById('speciesTrendsLoading').style.display = 'none';
                
                // Check if there's an error
                if (data.error) {
                    document.getElementById('speciesTrendsNoData').style.display = 'flex';
                    return;
                }
                
                // Destroy existing chart if it exists
                if (speciesTrendsChart) {
                    speciesTrendsChart.destroy();
                }
                
                // Create new species trend chart
                const ctx = document.getElementById('speciesTrendsChart').getContext('2d');
                speciesTrendsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.years,
                        datasets: [{
                            label: 'Observation Count',
                            data: data.data,
                            borderColor: '#3B82F6',
                            backgroundColor: '#3B82F620',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: false,
                        layout: {
                            padding: {
                                top: 5,
                                right: 10,
                                bottom: 5,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: data.species_info ? 
                                    `${data.species_info.common_name || data.species_info.scientific_name} Observation Trends` : 
                                    'Species Observation Trends',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#1F2937',
                                padding: {
                                    bottom: 10
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return `Year: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        const value = context.parsed.y.toLocaleString();
                                        return `Observations: ${value}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    padding: 2
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    padding: 2,
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Observations',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    },
                                    color: '#6b7280'
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
                
            } catch (error) {
                console.error('Error loading species trend data:', error);
                document.getElementById('speciesTrendsLoading').innerHTML = 
                    '<div class="text-center text-red-600">Error loading species trend data</div>';
            }
        }

        // Hide species chart
        function hideSpeciesChart() {
            document.getElementById('speciesChartContainer').style.display = 'none';
            document.getElementById('speciesEmptyState').style.display = 'block';
            document.getElementById('speciesLoadingState').style.display = 'none';
            hideSpeciesInfo();
        }

        // Refresh species trends data
        function refreshSpeciesTrends() {
            loadSpeciesTrendsData();
        }
    </script>
</body>
</html>
