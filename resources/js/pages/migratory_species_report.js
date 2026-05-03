function currentQuery() {
    const params = new URLSearchParams(window.location.search);
    const search = document.getElementById('search');
    if (search && search.value.trim() !== '') {
        params.set('search', search.value.trim());
    }
    params.delete('export');
    return params;
}

function exportMigratory(format) {
    const params = currentQuery();
    params.set('export', format);
    const url = `${window.location.pathname}?${params.toString()}`;
    if (format === 'print') {
        const tab = window.open(url, '_blank');
        if (tab) tab.onload = () => tab.print();
    } else {
        window.location.href = url;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const clearBtn = document.getElementById('migratory-clear-filters');
    const filterForm = document.getElementById('migratory-filter-form');
    const searchInput = document.getElementById('search');

    if (clearBtn && filterForm) {
        clearBtn.addEventListener('click', () => {
            const pa = document.getElementById('protected_area_id');
            const site = document.getElementById('site_id');
            if (pa) pa.value = '';
            if (site) site.value = '';
            if (searchInput) searchInput.value = '';
            filterForm.submit();
        });
    }

    const exportBtn = document.getElementById('migratory-export-btn');
    const exportMenu = document.getElementById('migratory-export-dropdown');
    if (exportBtn && exportMenu) {
        exportBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            exportMenu.classList.toggle('is-open');
        });

        exportMenu.querySelectorAll('button[data-export]').forEach((btn) => {
            btn.addEventListener('click', () => {
                exportMigratory(btn.dataset.export);
                exportMenu.classList.remove('is-open');
            });
        });

        document.addEventListener('click', (event) => {
            if (!exportMenu.contains(event.target) && event.target !== exportBtn) {
                exportMenu.classList.remove('is-open');
            }
        });
    }
});
