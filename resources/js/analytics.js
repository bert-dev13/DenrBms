/**
 * Analytics Page - Protected area charts and species trends
 * Matches Dashboard line graph UI (chart-widget, Chart.js options).
 */

let analyticsChart = null;
let speciesTrendsChart = null;

function setTrendUI(badgeEl, valueEl, trend) {
    const up = { badgeClass: 'chart-trend-badge--up', valueClass: 'chart-mini-stat__value--up', text: '↑ Increasing' };
    const down = { badgeClass: 'chart-trend-badge--down', valueClass: 'chart-mini-stat__value--down', text: '↓ Decreasing' };
    const neutral = { badgeClass: 'chart-trend-badge--neutral', valueClass: 'chart-mini-stat__value--neutral', text: trend === 'insufficient' ? '— Insufficient Data' : '→ Stable' };
    const state = trend === 'up' ? up : (trend === 'down' ? down : neutral);
    if (badgeEl) {
        badgeEl.textContent = state.text;
        badgeEl.className = 'chart-trend-badge ' + state.badgeClass;
    }
    if (valueEl) {
        valueEl.textContent = state.text;
        valueEl.className = 'chart-mini-stat__value ' + state.valueClass;
    }
}

/** Shared Chart.js options (matches Dashboard) */
function getChartOptions(counts, yearlyCounts, labelFn) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                titleColor: '#fff',
                bodyColor: '#fff',
                padding: 14,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    title: ctx => 'Year: ' + ctx[0].label,
                    label: labelFn
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 13 }, color: '#6b7280', maxRotation: 0, autoSkipPadding: 16 }
            },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.06)', drawTicks: false },
                border: { display: false },
                ticks: { font: { size: 13 }, color: '#6b7280', callback: v => v.toLocaleString(), padding: 10 }
            }
        },
        interaction: { intersect: false, mode: 'index' }
    };
}

function loadAnalyticsData(protectedAreaId) {
    const chartContainer = document.getElementById('chartContainer');
    const chartLoading = document.getElementById('chartLoading');
    const noDataMessage = document.getElementById('noDataMessage');
    if (!chartContainer || !chartLoading) return;

    if (!protectedAreaId) {
        chartContainer.style.display = 'none';
        return;
    }

    chartContainer.style.display = 'block';
    chartLoading.style.display = 'flex';
    if (noDataMessage) noDataMessage.style.display = 'none';

    fetch(`/analytics/data?protected_area_id=${protectedAreaId}`)
        .then(r => r.json())
        .then(data => {
            chartLoading.style.display = 'none';
            document.getElementById('chartTitle').textContent = `Observation Trends – ${data.protected_area.name}`;
            document.getElementById('chartSubtitle').textContent = `Yearly observation patterns for ${data.protected_area.name} (${data.protected_area.code})`;

            if (data.data.length === 0) {
                chartLoading.style.display = 'none';
                if (noDataMessage) noDataMessage.style.display = 'flex';
                document.getElementById('totalYears').textContent = '0';
                document.getElementById('totalObservations').textContent = '0';
                setTrendUI(document.getElementById('paTrendBadge'), document.getElementById('trendDirection'), 'insufficient');
                document.getElementById('chartStats').style.display = 'grid';
                return;
            }

            const labels = data.data.map(i => i.year.toString());
            const counts = data.data.map(i => i.count);
            const yearlyCounts = data.data.map(i => i.yearly_count);

            document.getElementById('totalYears').textContent = data.total_years;
            document.getElementById('totalObservations').textContent = data.total_observations.toLocaleString();
            document.getElementById('chartStats').style.display = 'grid';

            let trend = 'neutral';
            if (data.data.length >= 2) {
                const recent = data.data.slice(-3);
                const older = data.data.slice(-6, -3);
                if (recent.length > 0 && older.length > 0) {
                    const recentAvg = recent.reduce((s, i) => s + i.yearly_count, 0) / recent.length;
                    const olderAvg = older.reduce((s, i) => s + i.yearly_count, 0) / older.length;
                    if (recentAvg > olderAvg) trend = 'up';
                    else if (recentAvg < olderAvg) trend = 'down';
                }
            } else {
                trend = 'insufficient';
            }
            setTrendUI(document.getElementById('paTrendBadge'), document.getElementById('trendDirection'), trend);

            const backgroundColors = counts.map((c, i) => {
                if (i === 0) return 'rgba(59, 130, 246, 0.1)';
                return (c - counts[i - 1]) >= 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
            });
            const borderColors = counts.map((c, i) => {
                if (i === 0) return 'rgba(59, 130, 246, 1)';
                return (c - counts[i - 1]) >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
            });

            if (analyticsChart) analyticsChart.destroy();
            const ctx = document.getElementById('analyticsChart').getContext('2d');
            analyticsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Observation Count',
                        data: counts,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: borderColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        segment: {
                            borderColor: function(segCtx) {
                                const i = segCtx.p0DataIndex;
                                if (i === 0) return 'rgba(59, 130, 246, 1)';
                                return (counts[i] - counts[i - 1]) >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                            }
                        }
                    }]
                },
                options: getChartOptions(counts, yearlyCounts, function(ctx) {
                    let label = 'Cumulative: ' + ctx.parsed.y.toLocaleString();
                    if (ctx.dataIndex > 0) {
                        const prev = counts[ctx.dataIndex - 1];
                        const ch = ctx.parsed.y - prev;
                        const pct = prev > 0 ? ((ch / prev) * 100).toFixed(1) : 0;
                        label += ' · This year: ' + yearlyCounts[ctx.dataIndex].toLocaleString() + ' (' + (ch >= 0 ? '+' : '') + pct + '%)';
                    } else {
                        label += ' · This year: ' + yearlyCounts[ctx.dataIndex].toLocaleString();
                    }
                    return label;
                })
            });
        })
        .catch(err => {
            console.error(err);
            chartLoading.style.display = 'flex';
            chartLoading.innerHTML = '<div class="text-center" style="color: #fca5a5;">Error loading analytics data</div>';
        });
}

function loadSpeciesTrendsData() {
    const loading = document.getElementById('speciesLoadingState');
    const empty = document.getElementById('speciesEmptyState');
    const refreshBtn = document.getElementById('speciesRefreshBtn');
    loading.style.display = 'block';
    if (empty) empty.style.display = 'none';
    if (refreshBtn) refreshBtn.classList.add('loading');

    fetch('/analytics/species-trends')
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (refreshBtn) refreshBtn.classList.remove('loading');
            if (data.species_list && data.species_list.length > 0) {
                const select = document.getElementById('speciesSelect');
                select.innerHTML = '<option value="">Choose a species...</option>';
                data.species_list.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.scientific_name;
                    opt.textContent = s.common_name || s.scientific_name;
                    opt.setAttribute('data-total-obs', s.total_observations);
                    opt.setAttribute('data-rank', s.rank);
                    opt.setAttribute('data-common-name', s.common_name || s.scientific_name);
                    select.appendChild(opt);
                });
                select.value = data.species_list[0].scientific_name;
                select.dispatchEvent(new Event('change'));
            } else if (empty) {
                empty.style.display = 'block';
            }
        })
        .catch(() => {
            loading.style.display = 'none';
            if (refreshBtn) refreshBtn.classList.remove('loading');
            if (empty) empty.style.display = 'block';
        });
}

function onSpeciesSelectChange() {
    const select = document.getElementById('speciesSelect');
    const empty = document.getElementById('speciesEmptyState');
    const rankCard = document.getElementById('speciesRankCard');
    if (!select || !select.value) {
        document.getElementById('speciesChartContainer').style.display = 'none';
        if (rankCard) rankCard.style.display = 'none';
        if (empty) empty.style.display = 'block';
        return;
    }
    const opt = select.options[select.selectedIndex];
    const rank = opt.getAttribute('data-rank');
    const commonName = opt.getAttribute('data-common-name');
    const totalObs = opt.getAttribute('data-total-obs');
    if (rankCard) {
        document.getElementById('speciesRankNum').textContent = '#' + rank;
        document.getElementById('speciesRankName').textContent = commonName || select.value;
        document.getElementById('speciesTotalObsCard').textContent = Number(totalObs).toLocaleString();
        document.getElementById('speciesRankBadge').textContent = Number(totalObs).toLocaleString() + ' obs';
        rankCard.style.display = 'flex';
    }
    loadSpeciesTrendData(select.value);
}

function loadSpeciesTrendData(scientificName) {
    const container = document.getElementById('speciesChartContainer');
    const loading = document.getElementById('speciesTrendsLoading');
    const noData = document.getElementById('speciesTrendsNoData');
    const empty = document.getElementById('speciesEmptyState');
    container.style.display = 'block';
    if (empty) empty.style.display = 'none';
    loading.style.display = 'flex';
    if (noData) noData.style.display = 'none';

    fetch(`/analytics/species-trend-data?scientific_name=${encodeURIComponent(scientificName)}`)
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (data.error) {
                if (noData) noData.style.display = 'flex';
                return;
            }
            const years = data.years || [];
            const counts = data.data || [];
            const totalObs = counts.reduce((a, b) => a + b, 0);

            document.getElementById('speciesChartTitle').textContent = (data.species_info?.common_name || data.species_info?.scientific_name || scientificName) + ' – Observation Trends';
            document.getElementById('speciesTotalYears').textContent = years.length;
            document.getElementById('speciesTotalObs').textContent = totalObs.toLocaleString();
            document.getElementById('speciesChartStats').style.display = 'grid';

            let trend = 'neutral';
            if (counts.length >= 2) {
                const recent = counts.slice(-3);
                const older = counts.slice(-6, -3);
                if (recent.length > 0 && older.length > 0) {
                    const recentAvg = recent.reduce((a, b) => a + b, 0) / recent.length;
                    const olderAvg = older.reduce((a, b) => a + b, 0) / older.length;
                    if (recentAvg > olderAvg) trend = 'up';
                    else if (recentAvg < olderAvg) trend = 'down';
                }
            } else {
                trend = 'insufficient';
            }
            setTrendUI(document.getElementById('speciesTrendBadge'), document.getElementById('speciesTrendDirection'), trend);

            const backgroundColors = counts.map((c, i) => {
                if (i === 0) return 'rgba(59, 130, 246, 0.1)';
                return (c - (counts[i - 1] || 0)) >= 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
            });
            const borderColors = counts.map((c, i) => {
                if (i === 0) return 'rgba(59, 130, 246, 1)';
                return (c - (counts[i - 1] || 0)) >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
            });

            if (speciesTrendsChart) speciesTrendsChart.destroy();
            const ctx = document.getElementById('speciesTrendsChart').getContext('2d');
            speciesTrendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: years,
                    datasets: [{
                        label: 'Observations',
                        data: counts,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: borderColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        segment: {
                            borderColor: function(segCtx) {
                                const i = segCtx.p0DataIndex;
                                if (i === 0) return 'rgba(59, 130, 246, 1)';
                                return (counts[i] - (counts[i - 1] || 0)) >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                            }
                        }
                    }]
                },
                options: getChartOptions(counts, counts, function(ctx) {
                    return 'Observations: ' + ctx.parsed.y.toLocaleString();
                })
            });
        })
        .catch(() => {
            loading.style.display = 'flex';
            loading.innerHTML = '<div class="text-center text-danger">Error loading species trend data</div>';
        });
}

function refreshSpeciesTrends() {
    loadSpeciesTrendsData();
}

document.getElementById('protectedAreaSelect')?.addEventListener('change', function() {
    loadAnalyticsData(this.value);
});

document.getElementById('speciesSelect')?.addEventListener('change', onSpeciesSelectChange);

document.addEventListener('DOMContentLoaded', () => {
    const banganOption = document.getElementById('protectedAreaSelect')?.querySelector('option[value][selected]');
    if (banganOption) loadAnalyticsData(banganOption.value);
    loadSpeciesTrendsData();
});

window.refreshSpeciesTrends = refreshSpeciesTrends;
