/**
 * Species Rankings report — filters, search sync, and export (no site filter).
 */

function clearRankingFilters() {
    const form = document.getElementById('species-ranking-filter-form');
    if (!form) return;
    const searchInput = document.getElementById('species-ranking-search');
    if (searchInput) searchInput.value = '';
    window.location.href = form.action.split('?')[0];
}

function syncSearchIntoFilterForm() {
    const searchInput = document.getElementById('species-ranking-search');
    const hidden = document.getElementById('ranking-filters-search-hidden');
    if (searchInput && hidden) {
        hidden.value = searchInput.value.trim();
    }
}

function getSpeciesRankingExportBaseUrl(format) {
    const metaName = {
        pdf: 'species-ranking-export-pdf',
        excel: 'species-ranking-export-excel',
        print: 'species-ranking-export-print',
    }[format];
    return document.querySelector(`meta[name="${metaName}"]`)?.getAttribute('content')?.trim() || '';
}

/**
 * Export uses the same filters as the index: filter panel + the search box in the action bar
 * (not only the hidden field on the filter form, which can be stale until Apply).
 */
function buildSpeciesRankingExportQueryString() {
    const params = new URLSearchParams();
    const filterForm = document.getElementById('species-ranking-filter-form');
    if (filterForm) {
        ['protected_area_id', 'bio_group', 'patrol_year', 'patrol_semester', 'rank_order'].forEach((name) => {
            const el = filterForm.querySelector(`[name="${name}"]`);
            if (el && el.value !== '' && el.value != null) {
                params.set(name, String(el.value));
            }
        });
    }
    const searchInput = document.getElementById('species-ranking-search');
    const q = searchInput?.value?.trim();
    if (q) {
        params.set('search', q);
    }
    return params.toString();
}

function speciesRankingExport(format) {
    const base = getSpeciesRankingExportBaseUrl(format);
    if (!base) {
        return;
    }
    const qs = buildSpeciesRankingExportQueryString();
    const url = qs ? `${base}?${qs}` : base;

    const dropdown = document.getElementById('species-ranking-export-dropdown');
    const btn = document.getElementById('species-ranking-export-btn');
    dropdown?.classList.remove('is-open');
    if (btn) {
        btn.setAttribute('aria-expanded', 'false');
    }

    if (format === 'print') {
        const w = window.open(url, '_blank');
        if (w) {
            w.addEventListener('load', () => w.print(), { once: true });
        }
        return;
    }
    window.location.href = url;
}

function initSpeciesRankingExportDropdown() {
    const btn = document.getElementById('species-ranking-export-btn');
    const dropdown = document.getElementById('species-ranking-export-dropdown');
    if (!btn || !dropdown) {
        return;
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = !dropdown.classList.contains('is-open');
        dropdown.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== btn) {
            dropdown.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    dropdown.querySelectorAll('[data-species-ranking-export]').forEach((item) => {
        item.addEventListener('click', () => {
            const fmt = item.getAttribute('data-species-ranking-export');
            if (fmt === 'pdf' || fmt === 'excel' || fmt === 'print') {
                speciesRankingExport(fmt);
            }
        });
    });
}

window.clearRankingFilters = clearRankingFilters;
window.speciesRankingExport = speciesRankingExport;

document.addEventListener('DOMContentLoaded', () => {
    initSpeciesRankingExportDropdown();

    document.getElementById('species-ranking-filter-form')?.addEventListener('submit', syncSearchIntoFilterForm);

    if (window.replaceLucideIcons) {
        window.replaceLucideIcons();
    }
});
