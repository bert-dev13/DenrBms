function clearActivityFilters() {
    const form = document.getElementById('species-activity-filter-form');
    if (!form) return;
    window.location.href = form.action.split('?')[0];
}

function syncActivitySearch() {
    const searchInput = document.getElementById('species-activity-search');
    const hidden = document.getElementById('activity-filters-search-hidden');
    if (searchInput && hidden) hidden.value = searchInput.value.trim();
}

function exportActivity(format) {
    const meta = {
        pdf: 'species-activity-export-pdf',
        excel: 'species-activity-export-excel',
        print: 'species-activity-export-print',
    }[format];
    const base = document.querySelector(`meta[name="${meta}"]`)?.getAttribute('content');
    if (!base) return;
    const params = new URLSearchParams();
    const form = document.getElementById('species-activity-filter-form');
    if (form) {
        ['protected_area_id', 'bio_group', 'patrol_year', 'patrol_semester', 'rank_order'].forEach((name) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el && el.value) params.set(name, el.value);
        });
    }
    const search = document.getElementById('species-activity-search')?.value?.trim();
    if (search) params.set('search', search);
    const url = params.toString() ? `${base}?${params}` : base;
    if (format === 'print') {
        const w = window.open(url, '_blank');
        if (w) w.addEventListener('load', () => w.print(), { once: true });
        return;
    }
    window.location.href = url;
}

window.clearActivityFilters = clearActivityFilters;

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('species-activity-filter-form')?.addEventListener('submit', syncActivitySearch);
    const btn = document.getElementById('species-activity-export-btn');
    const menu = document.getElementById('species-activity-export-dropdown');
    if (btn && menu) {
        btn.addEventListener('click', () => menu.classList.toggle('is-open'));
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && e.target !== btn) menu.classList.remove('is-open');
        });
        menu.querySelectorAll('[data-species-activity-export]').forEach((item) => {
            item.addEventListener('click', () => exportActivity(item.getAttribute('data-species-activity-export')));
        });
    }
});

