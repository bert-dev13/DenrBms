@extends('layouts.app')

@section('title', 'Analytics')
@section('header', 'Analytics')

@section('head')
<meta name="analytics-export-excel" content="{{ route('analytics.export.excel') }}">
@vite(['resources/css/pages/analytics.css', 'resources/js/pages/analytics.js'])
<script id="analytics-payload" type="application/json">{!! json_encode($dataset) !!}</script>
<script>
    (function () {
        var payloadNode = document.getElementById('analytics-payload');
        if (!payloadNode) return;
        try {
            window.analyticsPayload = JSON.parse(payloadNode.textContent || '{}');
        } catch (e) {
            window.analyticsPayload = {};
        }
    })();
</script>
@endsection

@section('content')
    <div class="filter-panel analytics-filter-panel">
        <form method="GET" action="{{ route('analytics.index') }}" id="analytics-filter-form">
            <div class="filter-panel__header">
                <div>
                    <h2 class="filter-panel__title">Analytics Filters</h2>
                    <p class="analytics-filter-subtitle">Filters auto-apply.</p>
                </div>
                <div class="filter-panel__actions">
                    <button type="button" class="btn-filter-clear" onclick="clearAnalyticsFilters()">Clear</button>
                </div>
            </div>
            <div class="filter-panel__grid filter-panel__grid--cols-4">
                <div class="filter-panel__field">
                    <label class="filter-panel__label" for="protected_area_id">Protected Area</label>
                    <select name="protected_area_id" id="protected_area_id" class="filter-panel__select analytics-auto-filter">
                        <option value="">All Areas</option>
                        @foreach ($filterOptions['protectedAreas'] as $area)
                            <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-panel__field">
                    <label class="filter-panel__label" for="bio_group">Bio Group</label>
                    <select name="bio_group" id="bio_group" class="filter-panel__select analytics-auto-filter">
                        <option value="">All Groups</option>
                        @foreach ($filterOptions['bioGroups'] as $key => $group)
                            <option value="{{ $key }}" {{ request('bio_group') == $key ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-panel__field">
                    <label class="filter-panel__label" for="patrol_year">Year</label>
                    <select name="patrol_year" id="patrol_year" class="filter-panel__select analytics-auto-filter">
                        <option value="">All Years</option>
                        @foreach ($filterOptions['years'] as $year)
                            <option value="{{ $year }}" {{ request('patrol_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-panel__field">
                    <label class="filter-panel__label" for="patrol_semester">Semester</label>
                    <select name="patrol_semester" id="patrol_semester" class="filter-panel__select analytics-auto-filter">
                        <option value="">All Semesters</option>
                        @foreach ($filterOptions['semesters'] as $value => $label)
                            <option value="{{ $value }}" {{ request('patrol_semester') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <section class="analytics-yearly-trends">
        <div class="analytics-yearly-trends__header">
            <h2 class="analytics-yearly-trends__title">Yearly Monitoring Trends</h2>
            <p class="analytics-yearly-trends__subtitle">Observation and species tracked trend by year</p>
        </div>

        <div class="analytics-yearly-trends__chart-wrap">
            <canvas id="analytics-yearly-monitoring-chart"></canvas>
            <div id="analytics-yearly-chart-empty" class="analytics-yearly-trends__empty hidden">
                No yearly monitoring data available for selected filters.
            </div>
        </div>

        <div class="analytics-yearly-trends__stats">
            <div class="analytics-yearly-stat">
                <span>Total Years Tracked</span>
                <strong id="analytics-total-years">—</strong>
            </div>
            <div class="analytics-yearly-stat">
                <span>Peak Year Observations</span>
                <strong id="analytics-peak-year-observations">—</strong>
            </div>
            <div class="analytics-yearly-stat">
                <span>Trend Direction</span>
                <strong id="analytics-yearly-direction">—</strong>
            </div>
        </div>
    </section>

@endsection
