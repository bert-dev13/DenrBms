/**
 * Protected Areas Page - Filters, search, export, modal init
 */

import { initSearchBar } from '../shared/search_bar.js';

function ensureModalSystem() {
    if (typeof ProtectedAreaModalSystem !== 'undefined') {
        if (!window.protectedAreaModalSystem) {
            window.protectedAreaModalSystem = new ProtectedAreaModalSystem();
        }
    }
}

function clearProtectedAreaFilters() {
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    const searchInput = document.getElementById('protected-area-search');
    if (status) status.value = '';
    if (sort) sort.value = 'name';
    if (searchInput) searchInput.value = '';
    const form = document.getElementById('protected-areas-filter-form');
    if (form) form.submit();
}

function exportTable(format) {
    const form = document.getElementById('protected-areas-filter-form') || document.querySelector('form[method="GET"]');
    if (!form) return;
    const params = new URLSearchParams(new FormData(form));
    params.set('export', format);
    const searchInput = document.getElementById('protected-area-search');
    if (searchInput?.value.trim()) params.set('search', searchInput.value.trim());
    const url = window.location.pathname + '?' + params.toString();
    switch (format) {
        case 'print':
            const w = window.open(url + '&print=1', '_blank');
            if (w) w.onload = () => w.print();
            break;
        case 'excel':
            window.location.href = url + '&excel=1';
            if (window.showNotification) showNotification('Excel export started.', 'success');
            break;
        case 'pdf':
            window.location.href = url + '&pdf=1';
            if (window.showNotification) showNotification('PDF export started.', 'success');
            break;
        default:
            if (window.showNotification) showNotification('Invalid export format', 'error');
    }
    document.getElementById('export-dropdown')?.classList.remove('is-open');
}

document.addEventListener('DOMContentLoaded', () => {
    ensureModalSystem();

    // Standardized server-side search (debounced, loading state, clear button)
    initSearchBar({
        inputId: 'protected-area-search',
        clearBtnId: 'protected-area-search-clear',
        searchBtnId: 'protected-area-search-submit',
        formSelector: '#protected-areas-filter-form',
        debounceMs: 400,
        submitOnly: true,
    });

    // Preserve search when user clicks Apply on the filter form
    const filterForm = document.getElementById('protected-areas-filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', () => {
            const searchInput = document.getElementById('protected-area-search');
            if (searchInput?.value.trim()) {
                let input = filterForm.querySelector('input[name="search"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'search';
                    filterForm.appendChild(input);
                }
                input.value = searchInput.value.trim();
            }
        });
    }

    const btn = document.getElementById('export-dropdown-btn');
    const dropdown = document.getElementById('export-dropdown');
    if (btn && dropdown) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== btn) dropdown.classList.remove('is-open');
        });
    }
});

window.clearProtectedAreaFilters = clearProtectedAreaFilters;
window.exportTable = exportTable;
