/**
 * Standardized search bar behavior: debounce, clear button, loading state, server-side submit.
 * Use on pages with a form (GET) and an input with data-search-bar or matching ids.
 *
 * @param {Object} options
 * @param {string} options.inputId - ID of the search input
 * @param {string} options.clearBtnId - ID of the clear button
 * @param {string} [options.searchBtnId] - ID of the search submit button/icon
 * @param {string} [options.formSelector='form[method="GET"]'] - Form to read params from for URL build
 * @param {number} [options.debounceMs=400] - Debounce delay before submitting
 * @param {boolean} [options.submitOnly=false] - If true, only the search button triggers submit
 * @param {function(string)} [options.onSearch] - Called with trimmed query; return false to prevent default (build URL and navigate). If not provided, default behavior is: build URL from form + search, navigate.
 * @param {function()} [options.onClear] - Called when clear is clicked (after clearing input). Optional.
 */
export function initSearchBar(options) {
    const {
        inputId,
        clearBtnId,
        searchBtnId,
        formSelector = 'form[method="GET"]',
        debounceMs = 400,
        submitOnly = false,
        onSearch,
        onClear,
    } = options;

    const input = document.getElementById(inputId);
    const clearBtn = document.getElementById(clearBtnId);
    const searchBtn = searchBtnId ? document.getElementById(searchBtnId) : null;
    if (!input) return;

    const wrap = input.closest('.action-bar__search');
    const loadingEl = wrap?.querySelector('[data-search-loading]');

    let debounceTimer = null;
    let isSubmitting = false;

    function setLoading(loading) {
        isSubmitting = loading;
        if (wrap) wrap.classList.toggle('is-searching', loading);
        if (loadingEl) loadingEl.classList.toggle('hidden', !loading);
        if (clearBtn) clearBtn.classList.toggle('hidden', loading || !input.value.trim());
        if (input) input.disabled = loading;
    }

    function updateClearVisibility() {
        if (clearBtn) clearBtn.classList.toggle('hidden', !input.value.trim());
    }

    function getTrimmedQuery() {
        return (input?.value || '').trim();
    }

    function buildUrl(query) {
        const form = document.querySelector(formSelector);
        const params = new URLSearchParams(form ? new FormData(form) : window.location.search);
        if (query) {
            params.set('search', query);
        } else {
            params.delete('search');
        }
        params.delete('page');
        return window.location.pathname + '?' + params.toString();
    }

    function performSearch(query) {
        const trimmed = (query !== undefined && query !== null ? String(query) : getTrimmedQuery()).trim();
        if (onSearch) {
            const preventDefault = onSearch(trimmed);
            if (preventDefault === false) return;
        }
        const url = buildUrl(trimmed);
        setLoading(true);
        window.location.href = url;
    }

    function clearSearch() {
        if (isSubmitting) return;
        input.value = '';
        updateClearVisibility();
        if (onClear) onClear();
        if (onSearch) {
            const preventDefault = onSearch('');
            if (preventDefault === false) return;
        }
        const url = buildUrl('');
        setLoading(true);
        window.location.href = url;
    }

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        updateClearVisibility();
        if (submitOnly) return;
        const query = getTrimmedQuery();
        debounceTimer = setTimeout(() => {
            if (isSubmitting) return;
            performSearch(query);
        }, debounceMs);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (submitOnly) return;
            clearTimeout(debounceTimer);
            if (!isSubmitting) performSearch(getTrimmedQuery());
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault();
            if (submitOnly) {
                input.value = '';
                updateClearVisibility();
                return;
            }
            clearSearch();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            clearSearch();
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            clearTimeout(debounceTimer);
            if (!isSubmitting) performSearch(getTrimmedQuery());
        });
    }

    updateClearVisibility();
    return { performSearch, clearSearch, getTrimmedQuery, buildUrl };
}

export default initSearchBar;
