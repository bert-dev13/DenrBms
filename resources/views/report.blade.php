@extends('layouts.app')

@section('title', 'Reports')
@section('header', 'Reports')

@section('head')
@vite(['resources/css/reports.css', 'resources/js/reports.js'])
@endsection

@section('content')
<div class="reports-page">
    @if (session('success'))
        <div class="reports-alert reports-alert--success">
            <i data-lucide="check-circle" class="lucide-icon reports-alert__icon"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Summary Stats Grid (KPI Cards) -->
    <div class="kpi-grid mb-6">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-card-icon kpi-card-icon--green">
                <i data-lucide="map-pin" class="lucide-icon"></i>
            </div>
            <div class="kpi-card-body">
                <p class="kpi-card-label">Total Areas</p>
                <p class="kpi-card-value">{{ number_format($stats['total_areas']) }}</p>
                <span class="kpi-card-meta kpi-card-meta--neutral">protected zones</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-card-icon kpi-card-icon--blue">
                <i data-lucide="building-2" class="lucide-icon"></i>
            </div>
            <div class="kpi-card-body">
                <p class="kpi-card-label">Total Sites</p>
                <p class="kpi-card-value">{{ number_format($stats['total_sites']) }}</p>
                <span class="kpi-card-meta kpi-card-meta--neutral">monitoring locations</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--purple">
            <div class="kpi-card-icon kpi-card-icon--purple">
                <i data-lucide="clipboard-list" class="lucide-icon"></i>
            </div>
            <div class="kpi-card-body">
                <p class="kpi-card-label">Total Observations</p>
                <p class="kpi-card-value">{{ number_format($stats['total_observations']) }}</p>
                <span class="kpi-card-meta kpi-card-meta--neutral">species recorded</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--orange">
            <div class="kpi-card-icon kpi-card-icon--orange">
                <i data-lucide="panda" class="lucide-icon"></i>
            </div>
            <div class="kpi-card-body">
                <p class="kpi-card-label">Species Tracked</p>
                <p class="kpi-card-value">{{ number_format($stats['species_diversity']) }}</p>
                <span class="kpi-card-meta kpi-card-meta--neutral">unique species</span>
            </div>
        </div>
    </div>

    @php
        $activeAreasCount = collect($areaData)->filter(fn($a) => $a['observations'] > 0)->count();
        $inactiveCount = count($areaData) - $activeAreasCount;
    @endphp

    <!-- Protected Areas Overview Table -->
    <section class="reports-table-card reports-table-card--areas">
        <div class="reports-table-card__header">
            <div class="reports-table-card__header-main">
                <h2 class="reports-table-card__title">Protected Areas Overview</h2>
                <div class="reports-table-card__badges">
                    <span class="reports-badge reports-badge--active">{{ $activeAreasCount }} Active</span>
                    <span class="reports-badge reports-badge--inactive">{{ $inactiveCount }} Inactive</span>
                </div>
            </div>
            <div class="reports-table-card__actions">
                <div class="action-bar__export-wrap">
                    <button type="button" id="reports-areas-export-btn" class="action-bar__export-btn">
                        <i data-lucide="download" class="lucide-icon"></i>
                        <span>Export</span>
                        <i data-lucide="chevron-down" class="lucide-icon"></i>
                    </button>
                    <div id="reports-areas-export-dropdown" class="action-bar__export-dropdown">
                        <button type="button" onclick="exportReportTable('areas', 'pdf')">
                            <i data-lucide="file-text" class="lucide-icon"></i>
                            <span>Export as PDF</span>
                        </button>
                        <button type="button" onclick="exportReportTable('areas', 'excel')">
                            <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                            <span>Export as Excel</span>
                        </button>
                        <button type="button" onclick="exportReportTable('areas', 'print')">
                            <i data-lucide="printer" class="lucide-icon"></i>
                            <span>Print</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="reports-table-wrap">
            <table class="reports-table" role="grid">
                <thead class="reports-table__head">
                    <tr>
                        <th class="reports-table__th reports-table__th--code">Area Code</th>
                        <th class="reports-table__th reports-table__th--name">Area Name</th>
                        <th class="reports-table__th reports-table__th--num">Observations</th>
                        <th class="reports-table__th reports-table__th--num">Species Count</th>
                        <th class="reports-table__th reports-table__th--status">Status</th>
                    </tr>
                </thead>
                <tbody class="reports-table__body">
                    @forelse($areaData as $areaId => $data)
                        <tr class="reports-table__row">
                            <td class="reports-table__cell reports-table__cell--code">
                                <div class="reports-table__code-cell">
                                    <span class="reports-table__code-avatar" aria-hidden="true">{{ substr($data['code'], 0, 2) }}</span>
                                    <span class="reports-table__truncate" title="{{ $data['code'] }}">{{ $data['code'] }}</span>
                                </div>
                            </td>
                            <td class="reports-table__cell reports-table__cell--name">
                                <span class="reports-table__truncate" title="{{ $data['name'] }}">{{ $data['name'] }}</span>
                            </td>
                            <td class="reports-table__cell reports-table__cell--num" title="{{ number_format($data['observations']) }}">
                                {{ number_format($data['observations']) }}
                            </td>
                            <td class="reports-table__cell reports-table__cell--num" title="{{ number_format($data['species']) }}">
                                {{ number_format($data['species']) }}
                            </td>
                            <td class="reports-table__cell reports-table__cell--status">
                                @if($data['observations'] > 0)
                                    <span class="reports-badge reports-badge--active">Active</span>
                                @else
                                    <span class="reports-badge reports-badge--inactive">No Data</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="reports-table__row reports-table__row--empty">
                            <td colspan="5" class="reports-table__empty">
                                <i data-lucide="file-bar-chart" class="lucide-icon reports-table__empty-icon"></i>
                                <h3 class="reports-table__empty-title">No area data available</h3>
                                <p class="reports-table__empty-text">Protected area data will appear here once available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="reports-table-card__footer">
            <span class="reports-table-card__count">Showing {{ count($areaData) }} {{ count($areaData) === 1 ? 'area' : 'areas' }}</span>
        </div>
    </section>

    <!-- Top Species Table -->
    <section class="reports-table-card reports-table-card--species">
        <div class="reports-table-card__header">
            <div class="reports-table-card__header-main">
                <h2 class="reports-table-card__title">Top Observed Species</h2>
            </div>
            <div class="reports-table-card__actions">
                <div class="action-bar__export-wrap">
                    <button type="button" id="reports-species-export-btn" class="action-bar__export-btn">
                        <i data-lucide="download" class="lucide-icon"></i>
                        <span>Export</span>
                        <i data-lucide="chevron-down" class="lucide-icon"></i>
                    </button>
                    <div id="reports-species-export-dropdown" class="action-bar__export-dropdown">
                        <button type="button" onclick="exportReportTable('species', 'pdf')">
                            <i data-lucide="file-text" class="lucide-icon"></i>
                            <span>Export as PDF</span>
                        </button>
                        <button type="button" onclick="exportReportTable('species', 'excel')">
                            <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                            <span>Export as Excel</span>
                        </button>
                        <button type="button" onclick="exportReportTable('species', 'print')">
                            <i data-lucide="printer" class="lucide-icon"></i>
                            <span>Print</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="reports-table-wrap">
            <table class="reports-table" role="grid">
                <thead class="reports-table__head">
                    <tr>
                        <th class="reports-table__th reports-table__th--rank">#</th>
                        <th class="reports-table__th reports-table__th--name">Scientific Name</th>
                        <th class="reports-table__th reports-table__th--name">Common Name</th>
                        <th class="reports-table__th reports-table__th--num">Total Count</th>
                    </tr>
                </thead>
                <tbody class="reports-table__body">
                    @php $counter = 1; @endphp
                    @forelse($topSpecies as $species)
                        <tr class="reports-table__row">
                            <td class="reports-table__cell reports-table__cell--rank">{{ $counter }}</td>
                            @php $counter++; @endphp
                            <td class="reports-table__cell reports-table__cell--name">
                                <span class="reports-table__scientific-name reports-table__truncate" title="{{ $species['scientific_name'] ?: 'N/A' }}">{{ $species['scientific_name'] ?: 'N/A' }}</span>
                            </td>
                            <td class="reports-table__cell reports-table__cell--name">
                                <span class="reports-table__truncate" title="{{ $species['common_name'] ?: 'N/A' }}">{{ $species['common_name'] ?: 'N/A' }}</span>
                            </td>
                            <td class="reports-table__cell reports-table__cell--num" title="{{ number_format($species['total_count']) }}">
                                {{ number_format($species['total_count']) }}
                            </td>
                        </tr>
                    @empty
                        <tr class="reports-table__row reports-table__row--empty">
                            <td colspan="4" class="reports-table__empty">
                                <i data-lucide="leaf" class="lucide-icon reports-table__empty-icon"></i>
                                <h3 class="reports-table__empty-title">No species data available</h3>
                                <p class="reports-table__empty-text">Species observation data will appear here once available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="reports-table-card__footer">
            <span class="reports-table-card__count">Showing {{ count($topSpecies) }} of {{ count($topSpecies) }} top species</span>
        </div>
    </section>
</div>
@endsection
