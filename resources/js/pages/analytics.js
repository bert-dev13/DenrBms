function clearAnalyticsFilters() {
    const form = document.getElementById('analytics-filter-form');
    if (!form) return;
    window.location.href = form.action.split('?')[0];
}

let analyticsYearlyChart = null;

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

function computeTrendDirection(yearlyRows) {
    if (yearlyRows.length < 2) return 'Insufficient Data';
    const prev = yearlyRows[yearlyRows.length - 2].observations;
    const curr = yearlyRows[yearlyRows.length - 1].observations;
    if (curr > prev) return 'Increasing';
    if (curr < prev) return 'Decreasing';
    return 'Stable';
}

function renderYearlyMonitoringChart(payload) {
    const canvas = document.getElementById('analytics-yearly-monitoring-chart');
    const emptyState = document.getElementById('analytics-yearly-chart-empty');
    const yearsEl = document.getElementById('analytics-total-years');
    const peakEl = document.getElementById('analytics-peak-year-observations');
    const directionEl = document.getElementById('analytics-yearly-direction');

    const rows = deriveYearlyFromTimeseries(payload);
    if (yearsEl) yearsEl.textContent = String(rows.length);
    if (peakEl) peakEl.textContent = rows.length ? Math.max(...rows.map((item) => item.observations)).toLocaleString() : '0';
    if (directionEl) directionEl.textContent = computeTrendDirection(rows);

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
            labels: rows.map((item) => String(item.year)),
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
            scales: {
                y: { beginAtZero: true },
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
}

window.clearAnalyticsFilters = clearAnalyticsFilters;

document.addEventListener('DOMContentLoaded', initAnalyticsPage);
window.addEventListener('beforeunload', () => {
    if (analyticsYearlyChart) analyticsYearlyChart.destroy();
});
