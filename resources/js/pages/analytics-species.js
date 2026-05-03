function clearSpeciesAnalyticsFilters() {
    const form = document.getElementById('analytics-species-filter-form');
    if (!form) return;
    window.location.href = form.action.split('?')[0];
}

let speciesTrendChart = null;
let topIncreasingChart = null;
let topDecreasingChart = null;
let yearlyTotalChart = null;
let speciesPayloadCache = {};

function formatFullNumber(value) {
    return new Intl.NumberFormat('en-US').format(Number(value || 0));
}

function buildSpeciesYearlyRows(payload, speciesKey) {
    const trendMap = payload?.species_trends || {};
    const rows = Array.isArray(trendMap?.[speciesKey]) ? trendMap[speciesKey] : [];
    return rows
        .map((row) => ({
            year: Number(row.year || 0),
            recorded_count_sum: Number(row.recorded_count_sum || 0),
        }))
        .filter((row) => Number.isFinite(row.year) && row.year > 0)
        .sort((a, b) => a.year - b.year);
}

function getDirectionLabel(direction) {
    if (direction === 'increasing') return 'Increasing';
    if (direction === 'decreasing') return 'Decreasing';
    if (direction === 'flat') return 'Flat';
    return 'No Data';
}

function computeDirection(rows) {
    if (rows.length < 2) return rows.length ? 'flat' : 'no_data';
    const latest = Number(rows[rows.length - 1]?.recorded_count_sum || 0);
    const previous = Number(rows[rows.length - 2]?.recorded_count_sum || 0);
    if (latest > previous) return 'increasing';
    if (latest < previous) return 'decreasing';
    return 'flat';
}

function paintDirectionBadge(direction) {
    const badge = document.getElementById('species-trend-direction');
    if (!badge) return;
    badge.textContent = `Trend: ${getDirectionLabel(direction)}`;
    badge.classList.remove(
        'species-trend-badge--up',
        'species-trend-badge--down',
        'species-trend-badge--neutral'
    );
    if (direction === 'increasing') {
        badge.classList.add('species-trend-badge--up');
        return;
    }
    if (direction === 'decreasing') {
        badge.classList.add('species-trend-badge--down');
        return;
    }
    badge.classList.add('species-trend-badge--neutral');
}

function renderSpeciesTrendChart(payload, speciesKey) {
    const canvas = document.getElementById('species-yearly-trend-chart');
    const emptyState = document.getElementById('species-yearly-trend-empty');
    const rows = buildSpeciesYearlyRows(payload, speciesKey);
    paintDirectionBadge(computeDirection(rows));

    if (!rows.length) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (speciesTrendChart) {
            speciesTrendChart.destroy();
            speciesTrendChart = null;
        }
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (!canvas || typeof Chart === 'undefined') return;

    if (speciesTrendChart) {
        speciesTrendChart.destroy();
        speciesTrendChart = null;
    }

    speciesTrendChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: rows.map((row) => String(row.year)),
            datasets: [
                {
                    label: 'Recorded Count (Σ)',
                    data: rows.map((row) => row.recorded_count_sum),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.25,
                    pointRadius: 3.5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                tooltip: {
                    callbacks: {
                        label(context) {
                            return `Recorded Count (Σ): ${formatFullNumber(context.raw)}`;
                        },
                        afterBody(tooltipItems) {
                            if (!tooltipItems.length) return '';
                            const index = tooltipItems[0].dataIndex;
                            const current = rows[index]?.recorded_count_sum ?? 0;
                            const previous = rows[index - 1]?.recorded_count_sum ?? null;
                            if (previous === null) return 'Baseline year';
                            const delta = current - previous;
                            if (delta === 0) return 'No change from previous year';
                            return `${delta > 0 ? 'Increase' : 'Decrease'}: ${formatFullNumber(Math.abs(delta))} recorded count`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback(value) {
                            return formatFullNumber(value);
                        },
                    },
                },
            },
        },
    });
}

function normalizeGrowthRows(payload, key) {
    const rows = Array.isArray(payload?.[key]) ? payload[key] : [];
    return rows
        .map((row) => ({
            label: String(row.label || 'Unspecified'),
            delta: Number(row.delta || 0),
            deltaAbs: Number(row.delta_abs || row.decline_abs || 0),
            earliestYear: Number(row.earliest_year || 0),
            latestYear: Number(row.latest_year || 0),
        }))
        .filter((row) => row.label.trim() !== '');
}

function renderSpeciesGrowthChart({
    canvasId,
    emptyId,
    rows,
    title,
    color,
    valueKey,
    directionLabel,
    valueLabel,
    includeSign = false,
}) {
    const canvas = document.getElementById(canvasId);
    const emptyState = document.getElementById(emptyId);
    if (!canvas || typeof Chart === 'undefined') return null;
    if (!rows.length) {
        if (emptyState) emptyState.classList.remove('hidden');
        return null;
    }
    if (emptyState) emptyState.classList.add('hidden');

    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels: rows.map((row) => row.label),
            datasets: [
                {
                    label: title,
                    data: rows.map((row) => Number(row[valueKey] || 0)),
                    backgroundColor: color,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label(context) {
                            const value = Number(context.raw || 0);
                            const formatted = includeSign && value !== 0
                                ? `${value > 0 ? '+' : '-'}${formatFullNumber(Math.abs(value))}`
                                : formatFullNumber(value);
                            return `${valueLabel}: ${formatted}`;
                        },
                        afterBody(tooltipItems) {
                            if (!tooltipItems.length) return '';
                            const row = rows[tooltipItems[0].dataIndex];
                            return `${directionLabel} (${row.earliestYear} to ${row.latestYear})`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback(value) {
                            return formatFullNumber(value);
                        },
                    },
                },
            },
        },
    });
}

function renderYearlyTotalChart(payload) {
    const canvas = document.getElementById('species-yearly-total-chart');
    const emptyState = document.getElementById('species-yearly-total-empty');
    const rows = Array.isArray(payload?.yearly_total_counts) ? payload.yearly_total_counts : [];
    if (!canvas || typeof Chart === 'undefined') return;

    if (!rows.length) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (yearlyTotalChart) {
            yearlyTotalChart.destroy();
            yearlyTotalChart = null;
        }
        return;
    }
    if (emptyState) emptyState.classList.add('hidden');
    if (yearlyTotalChart) {
        yearlyTotalChart.destroy();
        yearlyTotalChart = null;
    }

    yearlyTotalChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: rows.map((row) => String(row.year)),
            datasets: [
                {
                    label: 'Total Observations',
                    data: rows.map((row) => Number(row.total_observations || 0)),
                    backgroundColor: '#2563eb',
                    borderRadius: 5,
                },
                {
                    label: 'Total Recorded Count (Σ)',
                    data: rows.map((row) => Number(row.total_recorded_count || 0)),
                    backgroundColor: '#16a34a',
                    borderRadius: 5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback(value) {
                            return formatFullNumber(value);
                        },
                    },
                },
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label(context) {
                            return `${context.dataset.label}: ${formatFullNumber(context.raw)}`;
                        },
                    },
                },
            },
        },
    });
}

function renderAdditionalCharts(payload) {
    const increasingRows = normalizeGrowthRows(payload, 'top_increasing_species');
    const decreasingRows = normalizeGrowthRows(payload, 'top_decreasing_species');

    if (topIncreasingChart) {
        topIncreasingChart.destroy();
        topIncreasingChart = null;
    }
    if (topDecreasingChart) {
        topDecreasingChart.destroy();
        topDecreasingChart = null;
    }

    topIncreasingChart = renderSpeciesGrowthChart({
        canvasId: 'species-top-increasing-chart',
        emptyId: 'species-top-increasing-empty',
        rows: increasingRows,
        title: 'Total Growth',
        color: '#16a34a',
        valueKey: 'delta',
        directionLabel: 'Growth',
        valueLabel: 'Growth',
        includeSign: true,
    });

    topDecreasingChart = renderSpeciesGrowthChart({
        canvasId: 'species-top-decreasing-chart',
        emptyId: 'species-top-decreasing-empty',
        rows: decreasingRows,
        title: 'Total Decline',
        color: '#dc2626',
        valueKey: 'deltaAbs',
        directionLabel: 'Decline',
        valueLabel: 'Decline',
    });

    renderYearlyTotalChart(payload);
}

function bindSpeciesFilter() {
    const form = document.getElementById('analytics-species-filter-form');
    if (!form) return;
    const filters = form.querySelectorAll('.analytics-auto-filter');
    if (!filters.length) return;
    filters.forEach((filter) => {
        filter.addEventListener('change', () => form.submit());
    });
}

function bindSpeciesTrendSelector() {
    const selector = document.getElementById('species-trend-selector');
    if (!selector) return;
    selector.addEventListener('change', (event) => {
        const key = String(event.target?.value || '').trim();
        renderSpeciesTrendChart(speciesPayloadCache, key);
    });
}

function initSpeciesAnalyticsPage() {
    if (window.__speciesAnalyticsPageInitialized) return;
    window.__speciesAnalyticsPageInitialized = true;

    bindSpeciesFilter();
    speciesPayloadCache = window.analyticsSpeciesPayload || {};
    bindSpeciesTrendSelector();
    const initialKey = String(speciesPayloadCache?.selected_species_key || '').trim();
    renderSpeciesTrendChart(speciesPayloadCache, initialKey);
    renderAdditionalCharts(speciesPayloadCache);
}

window.clearSpeciesAnalyticsFilters = clearSpeciesAnalyticsFilters;
document.addEventListener('DOMContentLoaded', initSpeciesAnalyticsPage);
window.addEventListener('beforeunload', () => {
    if (speciesTrendChart) speciesTrendChart.destroy();
    if (topIncreasingChart) topIncreasingChart.destroy();
    if (topDecreasingChart) topDecreasingChart.destroy();
    if (yearlyTotalChart) yearlyTotalChart.destroy();
});
