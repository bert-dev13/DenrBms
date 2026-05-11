function clearAnalyticsFilters() {
    const form = document.getElementById('analytics-filter-form');
    if (!form) return;
    window.location.href = form.action.split('?')[0];
}

let analyticsYearlyChart = null;
let analyticsTopSpeciesChart = null;
let analyticsTopSpeciesObsChart = null;
let analyticsBioGroupChart = null;
let analyticsSpeciesDistributionChart = null;

function debounce(fn, delay = 450) {
    let timeout = null;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
}

function deriveYearlyFromTimeseries(payload) {
    const yearlyRows = Array.isArray(payload?.yearly_timeseries) ? payload.yearly_timeseries : [];
    if (yearlyRows.length) {
        return yearlyRows
            .map((row) => ({
                year: Number(row.year || 0),
                observations: Number(row.observations || 0),
                species_count: Number(row.species_tracked || 0),
            }))
            .filter((row) => Number.isFinite(row.year) && row.year > 0)
            .sort((a, b) => a.year - b.year);
    }

    const timeseries = payload?.timeseries || [];
    const yearly = {};

    timeseries.forEach((row) => {
        const year = Number(row.year || String(row.label || '').slice(0, 4));
        if (!Number.isFinite(year) || year <= 0) return;
        if (!yearly[year]) {
            yearly[year] = { year, observations: 0, species_count: 0 };
        }
        yearly[year].observations += Number(row.observation_count || 0);
        yearly[year].species_count += Number(row.species_count || 0);
    });

    return Object.values(yearly).sort((a, b) => a.year - b.year);
}

function buildTrendRows(payload) {
    return deriveYearlyFromTimeseries(payload).map((row) => ({
        label: String(row.year),
        observations: Number(row.observations || 0),
        species_count: Number(row.species_count || 0),
    }));
}

function getPeak(rows, metricKey) {
    if (!rows.length) return null;
    let peak = rows[0];
    rows.forEach((row) => {
        if ((row?.[metricKey] || 0) > (peak?.[metricKey] || 0)) peak = row;
    });
    return peak;
}

function formatFullNumber(value) {
    return new Intl.NumberFormat('en-US').format(Number(value || 0));
}

function truncateLabel(label, limit = 24) {
    const text = String(label || '').trim();
    if (text.length <= limit) return text;
    return `${text.slice(0, limit - 1)}…`;
}

function renderYearlyMonitoringChart(payload) {
    const canvas = document.getElementById('analytics-yearly-monitoring-chart');
    const emptyState = document.getElementById('analytics-yearly-chart-empty');
    const rows = buildTrendRows(payload);

    if (!rows.length) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (analyticsYearlyChart) {
            analyticsYearlyChart.destroy();
            analyticsYearlyChart = null;
        }
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (!canvas || typeof Chart === 'undefined') return;

    if (analyticsYearlyChart) {
        analyticsYearlyChart.destroy();
        analyticsYearlyChart = null;
    }

    analyticsYearlyChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: rows.map((item) => item.label),
            datasets: [
                {
                    label: 'Observations',
                    data: rows.map((item) => item.observations),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.12)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.28,
                    pointRadius: 3.5,
                },
                {
                    label: 'Species Tracked',
                    data: rows.map((item) => item.species_count),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.28,
                    pointRadius: 3,
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
                        afterBody(tooltipItems) {
                            if (!tooltipItems.length) return '';
                            const first = tooltipItems[0];
                            const row = rows[first.dataIndex];
                            const obsPeak = getPeak(rows, 'observations');
                            const speciesPeak = getPeak(rows, 'species_count');
                            const notices = [];
                            if (obsPeak && obsPeak.label === row.label) notices.push('Peak observations period');
                            if (speciesPeak && speciesPeak.label === row.label) notices.push('Peak species period');
                            return notices;
                        },
                    },
                },
            },
            scales: {
                y: { beginAtZero: true },
            },
        },
    });
}

function createHorizontalBarChart(
    canvasId,
    emptyId,
    labels,
    values,
    color,
    valueLabel,
    emptyText = 'No data available.',
    tooltipExtraLines = null
) {
    const canvas = document.getElementById(canvasId);
    const emptyState = document.getElementById(emptyId);
    if (!canvas || typeof Chart === 'undefined') return null;

    if (!labels.length) {
        if (emptyState) {
            emptyState.textContent = emptyText;
            emptyState.classList.remove('hidden');
        }
        return null;
    }

    if (emptyState) emptyState.classList.add('hidden');
    const fullLabels = labels.map((label) => String(label || ''));
    const shortLabels = fullLabels.map((label) => truncateLabel(label, 22));

    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels: shortLabels,
            datasets: [
                {
                    label: valueLabel,
                    data: values,
                    backgroundColor: color,
                    borderRadius: 5,
                },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: { left: 8, right: 8, top: 4, bottom: 0 },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title(tooltipItems) {
                            if (!tooltipItems.length) return '';
                            return fullLabels[tooltipItems[0].dataIndex] || '';
                        },
                        label(context) {
                            const main = `${valueLabel}: ${formatFullNumber(context.raw)}`;
                            if (typeof tooltipExtraLines === 'function') {
                                const extra = tooltipExtraLines(context.dataIndex);
                                if (Array.isArray(extra) && extra.length) return [main, ...extra];
                                if (typeof extra === 'string' && extra) return [main, extra];
                            }
                            return main;
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        maxTicksLimit: 6,
                        callback(value) {
                            return formatFullNumber(value);
                        },
                    },
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        font: { size: 11 },
                        callback(value, index) {
                            return shortLabels[index] || value;
                        },
                    },
                },
            },
        },
    });
}

function renderTopSpeciesChart(payload) {
    if (analyticsTopSpeciesChart) {
        analyticsTopSpeciesChart.destroy();
        analyticsTopSpeciesChart = null;
    }

    const rows = Array.isArray(payload?.top_species) ? payload.top_species.slice(0, 8) : [];
    analyticsTopSpeciesChart = createHorizontalBarChart(
        'analytics-top-species-chart',
        'analytics-top-species-empty',
        rows.map((row) => String(row.common_name || row.scientific_name || 'Unspecified')),
        rows.map((row) => Number(row.recorded_count_sum || 0)),
        'rgba(37, 99, 235, 0.7)',
        'Recorded Count',
        'No species data available.'
    );
}

function renderTopSpeciesObservationChart(payload) {
    if (analyticsTopSpeciesObsChart) {
        analyticsTopSpeciesObsChart.destroy();
        analyticsTopSpeciesObsChart = null;
    }

    const rows = Array.isArray(payload?.top_species_observation) ? payload.top_species_observation.slice(0, 8) : [];
    analyticsTopSpeciesObsChart = createHorizontalBarChart(
        'analytics-top-species-obs-chart',
        'analytics-top-species-obs-empty',
        rows.map((row) => String(row.species_name || row.scientific_name || 'Unspecified')),
        rows.map((row) => Number(row.observation_records || 0)),
        'rgba(5, 122, 85, 0.78)',
        'Observation frequency',
        'No species observation data available.',
        (index) => [
            `Total Recorded Count (Σ): ${formatFullNumber(rows[index]?.recorded_count_sum || 0)}`,
            `Protected areas: ${formatFullNumber(rows[index]?.protected_area_count || 0)}`,
        ]
    );
}

function renderBioGroupChart(payload) {
    const canvas = document.getElementById('analytics-bio-group-chart');
    const emptyState = document.getElementById('analytics-bio-group-empty');
    if (analyticsBioGroupChart) {
        analyticsBioGroupChart.destroy();
        analyticsBioGroupChart = null;
    }
    if (!canvas || typeof Chart === 'undefined') return;

    const rows = Array.isArray(payload?.bio_group_breakdown) ? payload.bio_group_breakdown : [];
    if (!rows.length) {
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    analyticsBioGroupChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: rows.map((row) => String(row.label || 'Unspecified')),
            datasets: [
                {
                    data: rows.map((row) => Number(row.observation_count || 0)),
                    backgroundColor: [
                        'rgba(14, 165, 233, 0.85)',
                        'rgba(168, 85, 247, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(34, 197, 94, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { position: 'bottom' } },
        },
    });
}

const SPECIES_DISTRIBUTION_COLORS = [
    'rgba(37, 99, 235, 0.9)',
    'rgba(5, 122, 85, 0.9)',
    'rgba(124, 58, 237, 0.88)',
    'rgba(217, 119, 6, 0.9)',
    'rgba(219, 39, 119, 0.88)',
    'rgba(14, 165, 233, 0.9)',
    'rgba(100, 116, 139, 0.88)',
    'rgba(234, 88, 12, 0.88)',
    'rgba(71, 85, 105, 0.78)',
    'rgba(22, 163, 74, 0.88)',
];

function renderSpeciesObservationDistributionChart(payload) {
    const canvas = document.getElementById('analytics-species-distribution-chart');
    const emptyState = document.getElementById('analytics-species-distribution-empty');

    if (analyticsSpeciesDistributionChart) {
        analyticsSpeciesDistributionChart.destroy();
        analyticsSpeciesDistributionChart = null;
    }
    if (!canvas || typeof Chart === 'undefined') return;

    const dist = payload?.species_observation_distribution;
    const sliceMeta = Array.isArray(dist?.slices) ? dist.slices : [];
    const topTenSigmaCombined = Number(dist?.grand_total_recorded_count || 0);

    if (!sliceMeta.length || topTenSigmaCombined <= 0) {
        if (emptyState) {
            emptyState.textContent = 'No species observation data available.';
            emptyState.classList.remove('hidden');
        }
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');

    const labels = sliceMeta.map((s) => {
        const name = String(s.species_name || 'Unspecified');
        const pct = s.percent_share != null ? Number(s.percent_share).toFixed(1) : '0.0';
        return `${truncateLabel(name, 24)} (${pct}%)`;
    });
    const values = sliceMeta.map((s) => Number(s.recorded_count_sum || 0));
    const colors = sliceMeta.map((_, i) => SPECIES_DISTRIBUTION_COLORS[i % SPECIES_DISTRIBUTION_COLORS.length]);

    analyticsSpeciesDistributionChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 1,
                    borderColor: '#ffffff',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 8,
                        font: { size: 10 },
                    },
                },
                tooltip: {
                    displayColors: false,
                    callbacks: {
                        title(items) {
                            const i = items[0]?.dataIndex ?? 0;
                            return String(sliceMeta[i]?.species_name || '');
                        },
                        label(item) {
                            const i = item.dataIndex;
                            const m = sliceMeta[i];
                            const sum = Number(m?.recorded_count_sum ?? item.raw ?? 0);
                            const obs = Number(
                                m?.observation_records != null
                                    ? m.observation_records
                                    : (m?.observation_frequency ?? 0),
                            );
                            return [
                                `Observations: ${formatFullNumber(obs)}`,
                                `Recorded count: ${formatFullNumber(sum)}`,
                            ];
                        },
                    },
                },
            },
        },
    });
}

function bindAutoFilters() {
    const form = document.getElementById('analytics-filter-form');
    if (!form) return;

    const autoFilters = Array.from(form.querySelectorAll('.analytics-auto-filter'));
    const submitNow = () => form.submit();
    const debouncedSubmit = debounce(submitNow, 500);
    autoFilters.forEach((element) => {
        if (element.tagName === 'INPUT') {
            element.addEventListener('input', debouncedSubmit);
        } else {
            element.addEventListener('change', submitNow);
        }
    });

}

function initAnalyticsPage() {
    if (window.__analyticsPageInitialized) return;
    window.__analyticsPageInitialized = true;

    bindAutoFilters();
    const payload = window.analyticsPayload || {};
    renderYearlyMonitoringChart(payload);
    renderTopSpeciesChart(payload);
    renderTopSpeciesObservationChart(payload);
    renderBioGroupChart(payload);
    renderSpeciesObservationDistributionChart(payload);
}

window.clearAnalyticsFilters = clearAnalyticsFilters;

document.addEventListener('DOMContentLoaded', initAnalyticsPage);
window.addEventListener('beforeunload', () => {
    if (analyticsYearlyChart) analyticsYearlyChart.destroy();
    if (analyticsTopSpeciesChart) analyticsTopSpeciesChart.destroy();
    if (analyticsTopSpeciesObsChart) analyticsTopSpeciesObsChart.destroy();
    if (analyticsBioGroupChart) analyticsBioGroupChart.destroy();
    if (analyticsSpeciesDistributionChart) analyticsSpeciesDistributionChart.destroy();
});
