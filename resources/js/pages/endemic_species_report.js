function getEndemicExportRoutes() {
    const el = document.getElementById('endemic-export-routes');
    if (!el) {
        return { pdf: '', excel: '', print: '' };
    }
    return {
        pdf: el.dataset.pdfUrl || '',
        excel: el.dataset.excelUrl || '',
        print: el.dataset.printUrl || '',
    };
}

function buildEndemicExportQueryString() {
    const form = document.getElementById('endemic-report-filter-form');
    if (!form) return '';
    const params = new URLSearchParams(new FormData(form));
    const searchInput = document.getElementById('search');
    if (searchInput?.value.trim()) {
        params.set('search', searchInput.value.trim());
    }
    return params.toString();
}

function exportEndemicTable(format) {
    const routes = getEndemicExportRoutes();
    const qs = buildEndemicExportQueryString();
    let base = routes.pdf;
    if (format === 'excel') base = routes.excel;
    if (format === 'print') base = routes.print;
    if (!base) return;

    const url = qs ? `${base}?${qs}` : base;
    if (format === 'print') {
        const w = window.open(url, '_blank');
        if (w) w.onload = () => w.print();
    } else {
        window.location.href = url;
    }

    document.getElementById('endemic-export-dropdown')?.classList.remove('is-open');
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('endemic-export-dropdown-btn');
    const dropdown = document.getElementById('endemic-export-dropdown');
    if (btn && dropdown) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== btn) {
                dropdown.classList.remove('is-open');
            }
        });
    }
});

window.exportEndemicTable = exportEndemicTable;
