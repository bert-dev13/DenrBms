@extends('layouts.app')

@section('title', 'Analytics')
@section('header', 'Observation Analytics')

@section('head')
@vite(['resources/css/dashboard.css', 'resources/css/analytics.css', 'resources/js/analytics.js'])
@endsection

@section('content')
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Observation Analytics</h1>
                <p class="text-gray-600 mt-2">Analyze biodiversity observation trends over time for protected areas.</p>
            </div>

            <!-- Stats Grid (KPI Cards) -->
            <div class="kpi-grid mb-6">
                <!-- Total Areas -->
                <div class="kpi-card kpi-card--green">
                    <div class="kpi-card-icon kpi-card-icon--green">
                        <i data-lucide="map-pin" class="lucide-icon"></i>
                    </div>
                    <div class="kpi-card-body">
                        <p class="kpi-card-label">Total Areas</p>
                        <p class="kpi-card-value">{{ number_format($stats['total_areas']) }}</p>
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

            <!-- Protected Area Observation Trends Section -->
            <div class="chart-widget">
                <div class="analytics-filter-row">
                    <div>
                        <h2 class="chart-widget__title">Protected Area Observation Trends</h2>
                        <p class="chart-widget__subtitle">Choose a protected area to view observation trends</p>
                    </div>
                    <div class="analytics-select-wrap">
                        <select id="protectedAreaSelect" class="form-select form-select-sm" style="max-width: 20rem;">
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
                <div id="chartContainer" style="display: none;">
                    <header class="chart-widget__header">
                        <div>
                            <h2 class="chart-widget__title" id="chartTitle">Observation Trends</h2>
                            <p class="chart-widget__subtitle" id="chartSubtitle">Yearly observation patterns for selected area</p>
                        </div>
                        <span id="paTrendBadge" class="chart-trend-badge chart-trend-badge--neutral" aria-live="polite">—</span>
                    </header>

                    <div class="chart-widget__canvas-wrap">
                        <canvas id="analyticsChart"></canvas>
                        <div id="chartLoading" class="chart-widget__loading" style="display: none;">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex animate-spin"><i data-lucide="loader-2" class="lucide-icon w-5 h-5 text-primary"></i></span>
                                <span class="text-sm text-secondary">Loading analytics data...</span>
                            </div>
                        </div>
                        <div id="noDataMessage" class="chart-widget__loading" style="display: none;">
                            <div class="text-center text-secondary">
                                <i data-lucide="bar-chart-3" class="lucide-icon w-12 h-12 mx-auto mb-4 d-block"></i>
                                <p class="mb-0">No observation data available for this protected area</p>
                            </div>
                        </div>
                    </div>

                    <div class="chart-mini-stats" id="chartStats" style="display: none;">
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Total Years Tracked</p>
                            <p class="chart-mini-stat__value" id="totalYears">—</p>
                        </div>
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Total Observations</p>
                            <p class="chart-mini-stat__value" id="totalObservations">—</p>
                        </div>
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Trend Direction</p>
                            <p class="chart-mini-stat__value chart-mini-stat__value--neutral" id="trendDirection">—</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Species Observation Trends Section -->
            <div class="chart-widget">
                <header class="chart-widget__header">
                    <div>
                        <h2 class="chart-widget__title">Species Observation Trends (2021–2025)</h2>
                        <p class="chart-widget__subtitle">Select a species to view its observation trends across all protected areas</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select id="speciesSelect" class="form-select form-select-sm" style="max-width: 18rem;">
                            <option value="">Choose a species...</option>
                        </select>
                        <button type="button" onclick="refreshSpeciesTrends()" class="btn btn-outline-secondary btn-sm p-2" id="speciesRefreshBtn" title="Refresh species list">
                            <i data-lucide="refresh-cw" class="lucide-icon" style="width: 1rem; height: 1rem;"></i>
                        </button>
                    </div>
                </header>

                <!-- Species ranking (shown when species selected) -->
                <div id="speciesRankCard" class="analytics-species-rank-card mb-3" style="display: none;">
                    <span class="analytics-species-rank-num" id="speciesRankNum">#1</span>
                    <div class="analytics-species-rank-info">
                        <div class="analytics-species-rank-name" id="speciesRankName">—</div>
                        <div class="analytics-species-rank-obs"><span id="speciesTotalObsCard">0</span> total observations</div>
                    </div>
                    <span class="analytics-species-rank-badge" id="speciesRankBadge">716 obs</span>
                </div>

                <!-- Species Trends Chart Container -->
                <div id="speciesChartContainer" style="display: none;">
                    <header class="chart-widget__header mb-2">
                        <div>
                            <h3 class="chart-widget__title mb-0" id="speciesChartTitle">Observation Trends</h3>
                        </div>
                        <span id="speciesTrendBadge" class="chart-trend-badge chart-trend-badge--neutral" aria-live="polite">—</span>
                    </header>

                    <div class="chart-widget__canvas-wrap">
                        <canvas id="speciesTrendsChart"></canvas>
                        <div id="speciesTrendsLoading" class="chart-widget__loading" style="display: none;">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex animate-spin"><i data-lucide="loader-2" class="lucide-icon w-5 h-5 text-primary"></i></span>
                                <span class="text-sm text-secondary">Loading species trend data...</span>
                            </div>
                        </div>
                        <div id="speciesTrendsNoData" class="chart-widget__loading" style="display: none;">
                            <div class="text-center text-secondary">
                                <i data-lucide="bar-chart-3" class="lucide-icon w-12 h-12 mx-auto mb-4 d-block"></i>
                                <p class="mb-0">No observation data available for this species</p>
                            </div>
                        </div>
                    </div>

                    <div class="chart-mini-stats" id="speciesChartStats" style="display: none;">
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Years Tracked</p>
                            <p class="chart-mini-stat__value" id="speciesTotalYears">—</p>
                        </div>
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Total Observations</p>
                            <p class="chart-mini-stat__value" id="speciesTotalObs">—</p>
                        </div>
                        <div class="chart-mini-stat">
                            <p class="chart-mini-stat__label">Trend Direction</p>
                            <p class="chart-mini-stat__value chart-mini-stat__value--neutral" id="speciesTrendDirection">—</p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="speciesEmptyState" class="text-center py-12" style="display: none;">
                    <i data-lucide="bar-chart-3" class="lucide-icon w-16 h-16 mx-auto mb-4 text-secondary d-block"></i>
                    <p class="text-secondary mb-0">Select a species from the dropdown to view its observation trends</p>
                </div>

                <!-- Loading State -->
                <div id="speciesLoadingState" class="text-center py-12">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="inline-flex animate-spin"><i data-lucide="loader-2" class="lucide-icon w-6 h-6 text-primary"></i></span>
                        <span class="text-secondary">Loading species data...</span>
                    </div>
                </div>
            </div>
@endsection
