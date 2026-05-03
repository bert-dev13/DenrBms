@extends('layouts.app')

@section('title', 'Species Analytics')
@section('header', 'Species Analytics')

@section('head')
<meta name="analytics-species-page" content="1">
@vite(['resources/css/pages/analytics-species.css', 'resources/js/pages/analytics-species.js'])
<script id="analytics-species-payload" type="application/json">{!! json_encode($dataset) !!}</script>
<script>
    (function () {
        var payloadNode = document.getElementById('analytics-species-payload');
        if (!payloadNode) return;
        try {
            window.analyticsSpeciesPayload = JSON.parse(payloadNode.textContent || '{}');
        } catch (e) {
            window.analyticsSpeciesPayload = {};
        }
    })();
</script>
@endsection

@section('content')
    <div class="filter-panel analytics-filter-panel">
        <form method="GET" action="{{ route('analytics.species.index') }}" id="analytics-species-filter-form">
            <div class="filter-panel__header">
                <div>
                    <h2 class="filter-panel__title">Species Trend Filters</h2>
                    <p class="analytics-filter-subtitle">Filters auto-apply.</p>
                </div>
                <div class="filter-panel__actions">
                    <button type="button" class="btn-filter-clear" onclick="clearSpeciesAnalyticsFilters()">Clear</button>
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

    <section class="species-trend-card">
        <div class="species-trend-card__header">
            <h2 class="species-trend-card__title">Top 20 Species Trend by Year</h2>
            <p class="species-trend-card__subtitle">
                Select a species to view if yearly observations are increasing or decreasing
                @if (!empty($dataset['filters']['protected_area_name']))
                    in {{ $dataset['filters']['protected_area_name'] }}.
                @else
                    across all protected areas.
                @endif
            </p>
        </div>

        <div class="species-trend-card__controls">
            <div class="species-trend-card__control">
                <label class="filter-panel__label" for="species-trend-selector">Species (Top 20)</label>
                <select id="species-trend-selector" class="filter-panel__select">
                    @foreach (($dataset['top_species_options'] ?? []) as $species)
                        <option value="{{ $species['species_key'] }}" {{ ($dataset['selected_species_key'] ?? null) === $species['species_key'] ? 'selected' : '' }}>
                            {{ $species['label'] }} ({{ number_format($species['total_recorded_count']) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="species-trend-direction" class="species-trend-badge species-trend-badge--neutral">
                Trend: {{ ucfirst(str_replace('_', ' ', (string) ($dataset['selected_species_direction'] ?? 'no_data'))) }}
            </div>
        </div>

        <div class="species-trend-card__chart-wrap">
            <canvas id="species-yearly-trend-chart"></canvas>
            <div id="species-yearly-trend-empty" class="species-trend-card__empty hidden">
                No species trend data available for selected filters.
            </div>
        </div>
    </section>

    <section class="species-chart-grid">
        <article class="species-trend-card">
            <div class="species-trend-card__header">
                <h2 class="species-trend-card__title">Top 10 Increasing Species</h2>
                <p class="species-trend-card__subtitle">Highest growth from earliest to latest observed year.</p>
            </div>
            <div class="species-trend-card__chart-wrap species-trend-card__chart-wrap--compact">
                <canvas id="species-top-increasing-chart"></canvas>
                <div id="species-top-increasing-empty" class="species-trend-card__empty hidden">
                    No increasing species data available.
                </div>
            </div>
        </article>

        <article class="species-trend-card">
            <div class="species-trend-card__header">
                <h2 class="species-trend-card__title">Top 10 Decreasing Species</h2>
                <p class="species-trend-card__subtitle">Largest declines from earliest to latest observed year.</p>
            </div>
            <div class="species-trend-card__chart-wrap species-trend-card__chart-wrap--compact">
                <canvas id="species-top-decreasing-chart"></canvas>
                <div id="species-top-decreasing-empty" class="species-trend-card__empty hidden">
                    No decreasing species data available.
                </div>
            </div>
        </article>
    </section>

    <section class="species-trend-card">
        <div class="species-trend-card__header">
            <h2 class="species-trend-card__title">Species Total Count per Year</h2>
            <p class="species-trend-card__subtitle">Yearly comparison of total observations and total recorded count (Σ).</p>
        </div>
        <div class="species-trend-card__chart-wrap species-trend-card__chart-wrap--compact">
            <canvas id="species-yearly-total-chart"></canvas>
            <div id="species-yearly-total-empty" class="species-trend-card__empty hidden">
                No yearly total count data available for selected filters.
            </div>
        </div>
    </section>

@endsection
