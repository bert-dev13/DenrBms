@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('head')
@vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
@endsection

@section('content')
<!-- Dashboard Summary -->
<section class="dashboard-summary mb-8">
    <header class="dashboard-summary__welcome">
        <div class="dashboard-summary__identity">
            <h1 class="dashboard-summary__title">Welcome back, {{ auth()->user()->name ?? 'User' }}</h1>
            <p class="dashboard-summary__subtitle">
                Monitoring {{ number_format((int) ($stats['total_species'] ?? 0)) }} species across {{ number_format((int) ($stats['protected_areas'] ?? 0)) }} protected areas
            </p>
        </div>

        <div class="dashboard-summary__spacer" aria-hidden="true"></div>

        <div class="dashboard-summary__utility">
            <span class="dashboard-summary__status">
                <span class="dashboard-summary__status-dot" aria-hidden="true"></span>
                <span>Active Monitoring</span>
            </span>
            <span class="dashboard-summary__date">{{ now()->format('M j, Y') }}</span>
        </div>
    </header>

    <div class="dashboard-summary__grid">
        <article class="summary-card summary-card--observations">
            <div class="summary-card__head">
                <div class="summary-card__icon" aria-hidden="true"><i data-lucide="eye" class="lucide-icon"></i></div>
                <p class="summary-card__label">Total Observations</p>
            </div>
            <p class="summary-card__value" id="total-observations" data-countup="{{ (int) $stats['total_observations'] }}">0</p>
            <div class="summary-card__divider"></div>
            @if(isset($stats['monthly_growth']))
            <p class="summary-card__meta {{ $stats['monthly_growth'] > 0 ? 'summary-card__meta--up' : ($stats['monthly_growth'] < 0 ? 'summary-card__meta--down' : 'summary-card__meta--neutral') }}">
                <i data-lucide="{{ $stats['monthly_growth'] >= 0 ? 'trending-up' : 'trending-down' }}" class="lucide-icon"></i>
                <span>{{ $stats['monthly_growth'] > 0 ? '+' : '' }}{{ $stats['monthly_growth'] }}% from last month</span>
            </p>
            @endif
        </article>

        <article class="summary-card summary-card--areas">
            <div class="summary-card__head">
                <div class="summary-card__icon" aria-hidden="true"><i data-lucide="map-pin" class="lucide-icon"></i></div>
                <p class="summary-card__label">Total Areas</p>
            </div>
            <p class="summary-card__value" data-countup="{{ (int) $stats['protected_areas'] }}">0</p>
            <div class="summary-card__divider"></div>
            <p class="summary-card__meta summary-card__meta--neutral">
                <i data-lucide="activity" class="lucide-icon"></i>
                <span>{{ number_format((int) ($stats['active_areas'] ?? 0)) }} active monitoring areas</span>
            </p>
        </article>

        <article class="summary-card summary-card--species">
            <div class="summary-card__head">
                <div class="summary-card__icon" aria-hidden="true"><i data-lucide="panda" class="lucide-icon"></i></div>
                <p class="summary-card__label">Species Tracked</p>
            </div>
            <p class="summary-card__value" id="total-species" data-countup="{{ (int) $stats['total_species'] }}">0</p>
            <div class="summary-card__divider"></div>
            @if(isset($stats['quarterly_growth']))
            <p class="summary-card__meta {{ $stats['quarterly_growth'] > 0 ? 'summary-card__meta--up' : ($stats['quarterly_growth'] < 0 ? 'summary-card__meta--down' : 'summary-card__meta--neutral') }}">
                <i data-lucide="{{ $stats['quarterly_growth'] >= 0 ? 'trending-up' : 'trending-down' }}" class="lucide-icon"></i>
                <span>{{ $stats['quarterly_growth'] > 0 ? '+' : '' }}{{ $stats['quarterly_growth'] }}% this quarter</span>
            </p>
            @endif
        </article>

        <article class="summary-card summary-card--users">
            <div class="summary-card__head">
                <div class="summary-card__icon" aria-hidden="true"><i data-lucide="users" class="lucide-icon"></i></div>
                <p class="summary-card__label">Active Users</p>
            </div>
            <p class="summary-card__value" id="active-users" data-countup="{{ (int) $stats['active_users'] }}">0</p>
            <div class="summary-card__divider"></div>
            <p class="summary-card__meta summary-card__meta--neutral">
                <i data-lucide="clock-3" class="lucide-icon"></i>
                <span>Active within the last 7 days</span>
            </p>
        </article>
    </div>
</section>

<!-- Quick Actions -->
<div class="quick-actions-widget">
    <header class="quick-actions-widget__header">
        <div class="quick-actions-widget__heading">
            <h2 class="quick-actions-widget__title">Quick Actions</h2>
            <p class="quick-actions-widget__subtitle">Common workflows</p>
        </div>
    </header>

    <div class="quick-actions-toolbar-scroll">
        <div class="quick-actions-toolbar" role="toolbar" aria-label="Quick Actions">
            <a href="{{ route('species-observations.index') }}" class="quick-action-btn quick-action-btn--observation {{ request()->routeIs('species-observations.*') ? 'is-active' : '' }}" title="Create a new observation">
                <span class="quick-action-btn__icon-wrap">
                    <i data-lucide="eye" class="lucide-icon quick-action-btn__icon" aria-hidden="true"></i>
                </span>
                <span class="quick-action-btn__content">
                    <span class="quick-action-btn__label">Add Observation</span>
                    <span class="quick-action-btn__meta">Record species data</span>
                </span>
                <i data-lucide="arrow-up-right" class="lucide-icon quick-action-btn__arrow" aria-hidden="true"></i>
            </a>

            <a href="{{ route('protected-areas.index') }}" class="quick-action-btn quick-action-btn--areas {{ request()->routeIs('protected-areas.*') ? 'is-active' : '' }}" title="Open protected area records">
                <span class="quick-action-btn__icon-wrap">
                    <i data-lucide="map-pin" class="lucide-icon quick-action-btn__icon" aria-hidden="true"></i>
                </span>
                <span class="quick-action-btn__content">
                    <span class="quick-action-btn__label">Areas</span>
                    <span class="quick-action-btn__meta">Manage locations</span>
                </span>
                <i data-lucide="arrow-up-right" class="lucide-icon quick-action-btn__arrow" aria-hidden="true"></i>
            </a>

            <a href="{{ route('species-observations.index') }}" class="quick-action-btn quick-action-btn--search {{ request()->routeIs('species-observations.*') ? 'is-active' : '' }}" title="Search and filter observation data">
                <span class="quick-action-btn__icon-wrap">
                    <i data-lucide="search" class="lucide-icon quick-action-btn__icon" aria-hidden="true"></i>
                </span>
                <span class="quick-action-btn__content">
                    <span class="quick-action-btn__label">Search</span>
                    <span class="quick-action-btn__meta">Find observations</span>
                </span>
                <i data-lucide="arrow-up-right" class="lucide-icon quick-action-btn__arrow" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>

<!-- Yearly Monitoring Trends -->
<section class="monitoring-trends">
    <div class="monitoring-trends__left">
        <header class="monitoring-trends__header">
            <div class="monitoring-trends__header-icon" aria-hidden="true">
                <i data-lucide="bar-chart-3" class="lucide-icon"></i>
            </div>
            <div class="monitoring-trends__header-copy">
                <h2 class="monitoring-trends__title">Yearly Monitoring Trends</h2>
                <p class="monitoring-trends__subtitle">Long-term biodiversity monitoring patterns by year</p>
            </div>
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
    </div>

    <aside class="monitoring-trends__metrics" aria-label="Yearly monitoring highlights">
        <article class="trend-metric trend-metric--blue" title="Coverage period of available yearly records">
            <div class="trend-metric__icon-wrap" aria-hidden="true">
                <i data-lucide="calendar" class="lucide-icon"></i>
            </div>
            <div class="trend-metric__content">
                <p class="trend-metric__label">Total Years Tracked</p>
                <p class="trend-metric__value" id="totalYears">—</p>
            </div>
        </article>

        <article class="trend-metric trend-metric--green" title="Highest yearly observation total in the tracking period">
            <div class="trend-metric__icon-wrap" aria-hidden="true">
                <i data-lucide="trending-up" class="lucide-icon"></i>
            </div>
            <div class="trend-metric__content">
                <p class="trend-metric__label">Peak Year Observations</p>
                <p class="trend-metric__value" id="peakYearObservations">—</p>
            </div>
        </article>

        <article class="trend-metric trend-metric--gradient" title="Direction of recent monitoring movement versus historical values">
            <div class="trend-metric__icon-wrap" aria-hidden="true">
                <i id="trendMetricIcon" data-lucide="trending-up" class="lucide-icon"></i>
            </div>
            <div class="trend-metric__content">
                <p class="trend-metric__label">Trend Direction</p>
                <p class="trend-metric__value trend-metric__value--neutral" id="trendDirection">—</p>
            </div>
        </article>
    </aside>
</section>
@endsection
