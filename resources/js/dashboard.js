/**
 * Dashboard Page - Monitoring chart and interactive elements
 */

let monitoringChart = null;

function setTrendUI(trend) {
    const badge = document.getElementById('trendBadge');
    const trendEl = document.getElementById('trendDirection');
    if (!badge || !trendEl) return;

    const up = { badgeClass: 'chart-trend-badge--up', valueClass: 'chart-mini-stat__value--up', text: '↑ Increasing' };
    const down = { badgeClass: 'chart-trend-badge--down', valueClass: 'chart-mini-stat__value--down', text: '↓ Decreasing' };
    const neutral = { badgeClass: 'chart-trend-badge--neutral', valueClass: 'chart-mini-stat__value--neutral', text: trend === 'insufficient' ? '— Insufficient Data' : '→ Stable' };

    const state = trend === 'up' ? up : (trend === 'down' ? down : neutral);
    badge.textContent = state.text;
    badge.className = 'chart-trend-badge ' + state.badgeClass;
    trendEl.textContent = state.text;
    trendEl.className = 'chart-mini-stat__value ' + state.valueClass;
}

async function loadMonitoringChart() {
    try {
        const response = await fetch('/api/dashboard/yearly-monitoring');
        const data = await response.json();

        document.getElementById('chartLoading').classList.add('hidden');
        document.getElementById('totalYears').textContent = data.total_years;

        const avgMonitoring = data.total_years > 0 ? Math.round(data.total_observations / data.total_years) : 0;
        document.getElementById('avgMonitoring').textContent = avgMonitoring.toLocaleString();

        let trend = 'neutral';
        if (data.data.length >= 2) {
            const recent = data.data.slice(-3);
            const older = data.data.slice(-6, -3);
            if (recent.length > 0 && older.length > 0) {
                const recentAvg = recent.reduce((sum, item) => sum + item.count, 0) / recent.length;
                const olderAvg = older.reduce((sum, item) => sum + item.count, 0) / older.length;
                if (recentAvg > olderAvg) trend = 'up';
                else if (recentAvg < olderAvg) trend = 'down';
            }
        } else {
            trend = 'insufficient';
        }
        setTrendUI(trend);

        const labels = data.data.map(item => item.year.toString());
        const counts = data.data.map(item => item.count);
        const yearlyCounts = data.data.map(item => item.yearly_count);

        const backgroundColors = counts.map((count, index) => {
            if (index === 0) return 'rgba(59, 130, 246, 0.1)';
            const trendSeg = count - counts[index - 1];
            return trendSeg >= 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
        });
        const borderColors = counts.map((count, index) => {
            if (index === 0) return 'rgba(59, 130, 246, 1)';
            const trendSeg = count - counts[index - 1];
            return trendSeg >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
        });

        if (monitoringChart) monitoringChart.destroy();

        const ctx = document.getElementById('monitoringChart').getContext('2d');
        monitoringChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monitoring Observations',
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
                        borderColor: function(context) {
                            const index = context.p0DataIndex;
                            if (index === 0) return 'rgba(59, 130, 246, 1)';
                            const trendSeg = counts[index] - counts[index - 1];
                            return trendSeg >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                        }
                    }
                }]
            },
            options: {
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
                            title: function(context) { return 'Year: ' + context[0].label; },
                            label: function(context) {
                                const cumulativeTotal = context.parsed.y.toLocaleString();
                                const yearlyCount = yearlyCounts[context.dataIndex].toLocaleString();
                                let label = 'Cumulative: ' + cumulativeTotal;
                                if (context.dataIndex > 0) {
                                    const prevCumulative = counts[context.dataIndex - 1];
                                    const change = context.parsed.y - prevCumulative;
                                    const changePercent = prevCumulative > 0 ? ((change / prevCumulative) * 100).toFixed(1) : 0;
                                    label += ' · This year: ' + yearlyCount + ' (' + (change >= 0 ? '+' : '') + changePercent + '%)';
                                } else {
                                    label += ' · This year: ' + yearlyCount;
                                }
                                return label;
                            }
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
                        ticks: { font: { size: 13 }, color: '#6b7280', callback: function(value) { return value.toLocaleString(); }, padding: 10 }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    } catch (error) {
        console.error('Error loading monitoring chart:', error);
        const chartLoading = document.getElementById('chartLoading');
        if (chartLoading) {
            chartLoading.innerHTML = '<div class="text-center text-red-600">Error loading monitoring data</div>';
            chartLoading.classList.add('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', loadMonitoringChart);
