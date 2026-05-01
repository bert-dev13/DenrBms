/**
 * Dashboard Page - Monitoring chart and interactive elements
 */

let monitoringChart = null;

function animateCountUp(element, targetValue) {
    const duration = 900;
    const startTime = performance.now();
    const safeTarget = Number.isFinite(targetValue) ? Math.max(0, targetValue) : 0;

    function frame(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(safeTarget * eased);
        element.textContent = current.toLocaleString();
        if (progress < 1) requestAnimationFrame(frame);
    }

    requestAnimationFrame(frame);
}

function initializeSummaryCountUp() {
    const countUpEls = document.querySelectorAll('[data-countup]');
    countUpEls.forEach((el) => {
        const target = parseInt(el.getAttribute('data-countup') || '0', 10);
        animateCountUp(el, target);
    });
}

function initializeSummaryContextTicker() {
    const contextLine = document.getElementById('summaryContextLine');
    if (!contextLine) return;

    let messages = [];
    try {
        const raw = contextLine.getAttribute('data-context-lines') || '[]';
        messages = JSON.parse(raw);
    } catch (error) {
        messages = [];
    }

    if (!Array.isArray(messages)) return;
    const cleanMessages = messages.filter((message) => typeof message === 'string' && message.trim().length > 0);
    if (cleanMessages.length <= 1) return;

    let index = 0;
    setInterval(() => {
        index = (index + 1) % cleanMessages.length;
        contextLine.textContent = cleanMessages[index];
    }, 4800);
}

function setTrendUI(trend) {
    const badge = document.getElementById('trendDirectionBadge');
    const badgeText = document.getElementById('trendDirectionText');
    const trendEl = document.getElementById('trendDirection');
    const trendMetricIcon = document.getElementById('trendMetricIcon');
    if (!trendEl) return;

    const up = { badgeClass: 'trend-focus__badge--up', valueClass: 'trend-metric__value--up', text: '↑ Increasing', icon: 'trending-up' };
    const down = { badgeClass: 'trend-focus__badge--down', valueClass: 'trend-metric__value--down', text: '↓ Decreasing', icon: 'trending-down' };
    const neutral = {
        badgeClass: 'trend-focus__badge--neutral',
        valueClass: 'trend-metric__value--neutral',
        text: trend === 'insufficient' ? '— Insufficient Data' : '→ Stable',
        icon: 'trending-up',
    };

    const state = trend === 'up' ? up : (trend === 'down' ? down : neutral);

    if (badge && badgeText) {
        badge.className = 'trend-focus__badge ' + state.badgeClass;
        badgeText.textContent = state.text;
    }

    trendEl.textContent = state.text;
    trendEl.className = 'trend-metric__value ' + state.valueClass;

    if (trendMetricIcon) {
        trendMetricIcon.setAttribute('data-lucide', state.icon);
    }
}

function buildObservationGradient(context) {
    const chart = context.chart;
    const chartArea = chart.chartArea;
    if (!chartArea) return '#15803d';

    const gradient = chart.ctx.createLinearGradient(chartArea.left, chartArea.top, chartArea.right, chartArea.bottom);
    gradient.addColorStop(0, '#166534');
    gradient.addColorStop(1, '#22c55e');
    return gradient;
}

async function loadMonitoringChart() {
    try {
        const response = await fetch('/api/dashboard/yearly-monitoring');
        const data = await response.json();

        document.getElementById('chartLoading').classList.add('hidden');
        document.getElementById('totalYears').textContent = data.total_years;

        const peakYearObservations = data.data.length > 0
            ? Math.max(...data.data.map((item) => item.observations || 0))
            : 0;
        document.getElementById('peakYearObservations').textContent = `${peakYearObservations.toLocaleString()} obs`;

        let trend = 'neutral';
        if (data.data.length >= 2) {
            const recent = data.data.slice(-3);
            const older = data.data.slice(-6, -3);
            if (recent.length > 0 && older.length > 0) {
                const recentAvg = recent.reduce((sum, item) => sum + item.observations, 0) / recent.length;
                const olderAvg = older.reduce((sum, item) => sum + item.observations, 0) / older.length;
                if (recentAvg > olderAvg) trend = 'up';
                else if (recentAvg < olderAvg) trend = 'down';
            }
        } else {
            trend = 'insufficient';
        }
        setTrendUI(trend);

        const labels = data.data.map(item => item.year.toString());
        const observations = data.data.map(item => item.observations);
        const speciesTracked = data.data.map(item => item.species_tracked);
        const latestIndex = Math.max(observations.length - 1, 0);
        const latestPointRadius = observations.map((_, index) => index === latestIndex ? 6 : 3.5);
        const latestPointHoverRadius = observations.map((_, index) => index === latestIndex ? 8 : 5);
        const latestSpeciesPointRadius = speciesTracked.map((_, index) => index === latestIndex ? 5.5 : 3);
        const latestSpeciesPointHoverRadius = speciesTracked.map((_, index) => index === latestIndex ? 7 : 4.5);

        if (monitoringChart) monitoringChart.destroy();

        const ctx = document.getElementById('monitoringChart').getContext('2d');
        monitoringChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Observations',
                        data: observations,
                        borderColor: (context) => buildObservationGradient(context),
                        backgroundColor: 'rgba(22, 163, 74, 0.05)',
                        borderWidth: 2.8,
                        fill: false,
                        tension: 0.42,
                        pointRadius: latestPointRadius,
                        pointHoverRadius: latestPointHoverRadius,
                        pointBackgroundColor: observations.map((_, index) => index === latestIndex ? '#14532d' : '#16a34a'),
                        pointBorderColor: '#bbf7d0',
                        pointBorderWidth: observations.map((_, index) => index === latestIndex ? 3 : 2),
                    },
                    {
                        label: 'Species Tracked',
                        data: speciesTracked,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 2.1,
                        fill: false,
                        tension: 0.42,
                        pointRadius: latestSpeciesPointRadius,
                        pointHoverRadius: latestSpeciesPointHoverRadius,
                        pointBackgroundColor: speciesTracked.map((_, index) => index === latestIndex ? '#1d4ed8' : '#3b82f6'),
                        pointBorderColor: '#dbeafe',
                        pointBorderWidth: speciesTracked.map((_, index) => index === latestIndex ? 2.5 : 2),
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1300,
                    easing: 'easeOutCubic',
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            color: '#374151',
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        borderColor: 'rgba(148, 163, 184, 0.4)',
                        borderWidth: 1,
                        titleColor: '#0f172a',
                        bodyColor: '#0f172a',
                        padding: 14,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 12, weight: '600' },
                        bodySpacing: 4,
                        boxPadding: 6,
                        callbacks: {
                            title: function(context) { return 'Year: ' + context[0].label; },
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y.toLocaleString()}`;
                            },
                            labelTextColor: function(context) {
                                return context.datasetIndex === 0 ? '#15803d' : '#0369a1';
                            },
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(148, 163, 184, 0.16)', drawTicks: false, lineWidth: 1 },
                        border: { display: false },
                        ticks: { font: { size: 12.5, weight: '500' }, color: '#64748b', maxRotation: 0, autoSkipPadding: 16 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.18)', drawTicks: false, lineWidth: 1 },
                        border: { display: false },
                        ticks: { font: { size: 12.5, weight: '500' }, color: '#64748b', callback: function(value) { return value.toLocaleString(); }, padding: 10 }
                    }
                },
                interaction: { intersect: false, mode: 'index' },
                elements: {
                    line: { capBezierPoints: true },
                    point: { hoverBorderWidth: 2.5 },
                },
            }
        });

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    } catch (error) {
        console.error('Error loading monitoring chart:', error);
        const chartLoading = document.getElementById('chartLoading');
        if (chartLoading) {
            chartLoading.innerHTML = '<div class="text-center text-red-600">Error loading monitoring data</div>';
            chartLoading.classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initializeSummaryContextTicker();
    initializeSummaryCountUp();
    loadMonitoringChart();
});
