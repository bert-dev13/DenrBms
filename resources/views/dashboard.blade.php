@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('head')
@vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
@endsection

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name ?? 'User' }}!</h1>
    <p class="text-gray-600 mt-2">Here's what's happening with your biodiversity management system today.</p>
</div>

<!-- Stats Grid (KPI Cards) -->
<div class="kpi-grid mb-8">
    <!-- Total Species Observations -->
    <div class="kpi-card kpi-card--blue">
        <div class="kpi-card-icon kpi-card-icon--blue">
            <i data-lucide="clipboard-list" class="lucide-icon"></i>
        </div>
        <div class="kpi-card-body">
            <p class="kpi-card-label">Total Observations</p>
            <p class="kpi-card-value" id="total-observations">{{ number_format($stats['total_observations']) }}</p>
            @if(isset($stats['monthly_growth']))
            <span class="kpi-card-meta {{ $stats['monthly_growth'] > 0 ? 'kpi-card-meta--positive' : ($stats['monthly_growth'] < 0 ? 'kpi-card-meta--negative' : 'kpi-card-meta--neutral') }}">
                {{ $stats['monthly_growth'] > 0 ? '+' : '' }}{{ $stats['monthly_growth'] }}% from last month
            </span>
            @endif
        </div>
    </div>

    <!-- Total Areas -->
    <div class="kpi-card kpi-card--green">
        <div class="kpi-card-icon kpi-card-icon--green">
            <i data-lucide="map-pin" class="lucide-icon"></i>
        </div>
        <div class="kpi-card-body">
            <p class="kpi-card-label">Total Areas</p>
            <p class="kpi-card-value">{{ $stats['protected_areas'] }}</p>
        </div>
    </div>

    <!-- Species Count -->
    <div class="kpi-card kpi-card--purple">
        <div class="kpi-card-icon kpi-card-icon--purple">
            <i data-lucide="panda" class="lucide-icon"></i>
        </div>
        <div class="kpi-card-body">
            <p class="kpi-card-label">Species Tracked</p>
            <p class="kpi-card-value" id="total-species">{{ number_format($stats['total_species']) }}</p>
            @if(isset($stats['quarterly_growth']))
            <span class="kpi-card-meta {{ $stats['quarterly_growth'] > 0 ? 'kpi-card-meta--positive' : ($stats['quarterly_growth'] < 0 ? 'kpi-card-meta--negative' : 'kpi-card-meta--neutral') }}">
                {{ $stats['quarterly_growth'] > 0 ? '+' : '' }}{{ $stats['quarterly_growth'] }}% this quarter
            </span>
            @endif
        </div>
    </div>

    <!-- Active Users -->
    <div class="kpi-card kpi-card--orange">
        <div class="kpi-card-icon kpi-card-icon--orange">
            <i data-lucide="users" class="lucide-icon"></i>
        </div>
        <div class="kpi-card-body">
            <p class="kpi-card-label">Active Users</p>
            <p class="kpi-card-value" id="active-users">{{ $stats['active_users'] }}</p>
            <span class="kpi-card-meta kpi-card-meta--neutral">This week</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-widget">
    <header class="quick-actions-widget__header">
        <h2 class="quick-actions-widget__title">Quick Actions</h2>
        <p class="quick-actions-widget__subtitle">Common tasks and quick access to system features</p>
    </header>

    <div class="quick-actions-grid">
        <a href="{{ route('species-observations.index') }}" class="quick-action-card">
            <div class="quick-action-card__icon quick-action-card__icon--green">
                <i data-lucide="plus" class="lucide-icon"></i>
            </div>
            <h3 class="quick-action-card__title">New Observation</h3>
            <p class="quick-action-card__desc">Record species observation data</p>
        </a>

        <a href="{{ route('protected-areas.index') }}" class="quick-action-card">
            <div class="quick-action-card__icon quick-action-card__icon--blue">
                <i data-lucide="map-pin" class="lucide-icon"></i>
            </div>
            <h3 class="quick-action-card__title">Protected Areas</h3>
            <p class="quick-action-card__desc">Browse protected area information</p>
        </a>

        <a href="{{ route('analytics.index') }}" class="quick-action-card">
            <div class="quick-action-card__icon quick-action-card__icon--purple">
                <i data-lucide="bar-chart-3" class="lucide-icon"></i>
            </div>
            <h3 class="quick-action-card__title">Analytics</h3>
            <p class="quick-action-card__desc">View detailed analytics and reports</p>
        </a>

        <a href="{{ route('species-observations.index') }}" class="quick-action-card">
            <div class="quick-action-card__icon quick-action-card__icon--orange">
                <i data-lucide="search" class="lucide-icon"></i>
            </div>
            <h3 class="quick-action-card__title">Search Data</h3>
            <p class="quick-action-card__desc">Search and filter observations</p>
        </a>
    </div>
</div>

<!-- Monitoring Activity Over Time (chart widget) -->
<div class="chart-widget">
    <header class="chart-widget__header">
        <div>
            <h2 class="chart-widget__title">Monitoring Activity Over Time</h2>
            <p class="chart-widget__subtitle">Yearly monitoring activity showing patterns</p>
        </div>
        <span id="trendBadge" class="chart-trend-badge chart-trend-badge--neutral" aria-live="polite">—</span>
    </header>

    <div class="chart-widget__canvas-wrap">
        <canvas id="monitoringChart"></canvas>
        <div id="chartLoading" class="chart-widget__loading">
            <div class="flex items-center gap-2">
                <span class="inline-flex animate-spin"><i data-lucide="loader-2" class="lucide-icon w-5 h-5 text-blue-600"></i></span>
                <span class="text-sm text-gray-600">Loading monitoring data...</span>
            </div>
        </div>
    </div>

    <div class="chart-mini-stats">
        <div class="chart-mini-stat">
            <p class="chart-mini-stat__label">Total Years Tracked</p>
            <p class="chart-mini-stat__value" id="totalYears">—</p>
        </div>
        <div class="chart-mini-stat">
            <p class="chart-mini-stat__label">Average Annual Monitoring</p>
            <p class="chart-mini-stat__value" id="avgMonitoring">—</p>
        </div>
        <div class="chart-mini-stat">
            <p class="chart-mini-stat__label">Trend Direction</p>
            <p class="chart-mini-stat__value chart-mini-stat__value--neutral" id="trendDirection">—</p>
        </div>
    </div>
</div>
@endsection
