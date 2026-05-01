/**
 * Species Observation Page - Entry point
 * Loads modal system and shared search behavior
 */
import './species_observation_modal.js';
import { initSearchBar } from '../shared/search_bar.js';

function getSpeciesFilterForm() {
    return document.getElementById('species-observations-filter-form') || document.querySelector('form[method="GET"]');
}

function ensureSearchValuePersistedOnSubmit() {
    const filterForm = getSpeciesFilterForm();
    if (!filterForm) return;

    filterForm.addEventListener('submit', () => {
        const searchInput = document.getElementById('species-observations-search');
        if (!searchInput?.value.trim()) return;

        let hidden = filterForm.querySelector('input[name="search"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'search';
            filterForm.appendChild(hidden);
        }
        hidden.value = searchInput.value.trim();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const api = initSearchBar({
        inputId: 'species-observations-search',
        clearBtnId: 'species-observations-search-clear',
        searchBtnId: 'species-observations-search-submit',
        formSelector: '#species-observations-filter-form',
        debounceMs: 400,
        submitOnly: true,
    });

    ensureSearchValuePersistedOnSubmit();

    if (api) {
        window.performServerSearch = (query = '') => api.performSearch(query);
        window.clearSearch = () => api.clearSearch();
    }
});
