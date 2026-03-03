/**
 * Reports Page
 */

function exportReportTable(table, format) {
    const params = new URLSearchParams();
    params.set('export', format);
    params.set('table', table);

    const baseUrl = window.location.pathname + '?' + params.toString();

    switch (format) {
        case 'print': {
            const w = window.open(baseUrl + '&print=1', '_blank');
            if (w) {
                w.onload = () => w.print();
            }
            break;
        }
        case 'excel':
            window.location.href = baseUrl + '&excel=1';
            if (window.showNotification) {
                window.showNotification('Excel export started.', 'success');
            }
            break;
        case 'pdf':
            window.location.href = baseUrl + '&pdf=1';
            if (window.showNotification) {
                window.showNotification('PDF export started.', 'success');
            }
            break;
        default:
            if (window.showNotification) {
                window.showNotification('Invalid export format', 'error');
            }
    }

    document.getElementById('reports-areas-export-dropdown')?.classList.remove('is-open');
    document.getElementById('reports-species-export-dropdown')?.classList.remove('is-open');
}

function initReportsExportDropdown(buttonId, dropdownId) {
    const btn = document.getElementById(buttonId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;

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

document.addEventListener('DOMContentLoaded', () => {
    initReportsExportDropdown('reports-areas-export-btn', 'reports-areas-export-dropdown');
    initReportsExportDropdown('reports-species-export-btn', 'reports-species-export-dropdown');
});

window.exportReportTable = exportReportTable;
