import { initSearchBar } from '../shared/search_bar.js';

class ProtectedAreaSitesModalSystem {
    constructor() {
        this.overlay = null;
        this.dialog = null;
        this.focusable = [];
        this.lastFocused = null;
        this.mode = null;
        this.siteId = null;
        this.boundKeydown = this.handleKeydown.bind(this);
        this.init();
    }

    init() {
        if (document.getElementById('pas-modal-overlay')) return;

        this.overlay = document.createElement('div');
        this.overlay.id = 'pas-modal-overlay';
        this.overlay.className = 'pas-modal-overlay';
        this.overlay.setAttribute('aria-hidden', 'true');
        this.overlay.innerHTML = '<div class="pas-modal-shell"></div>';
        document.body.appendChild(this.overlay);

        this.overlay.addEventListener('click', (event) => {
            if (event.target === this.overlay) this.close();
        });

        document.addEventListener('keydown', this.boundKeydown);
    }

    async open(mode, data = {}) {
        this.mode = mode;
        this.siteId = data.siteId || null;
        this.lastFocused = document.activeElement;

        const payload = await this.prepare(mode, this.siteId);
        if (!payload) return false;

        const shell = this.overlay.querySelector('.pas-modal-shell');
        shell.innerHTML = this.render(mode, payload);
        this.dialog = shell.querySelector('.pas-modal');

        this.overlay.classList.add('is-open');
        this.overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pas-modal-open');

        this.bindDialogEvents();
        this.setupFocusTrap();

        if (typeof window.replaceLucideIcons === 'function') {
            window.replaceLucideIcons(shell);
        }

        return true;
    }

    close() {
        if (!this.overlay || !this.overlay.classList.contains('is-open')) return;

        this.overlay.classList.remove('is-open');
        this.overlay.classList.add('is-closing');
        this.overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('pas-modal-open');

        setTimeout(() => {
            this.overlay.classList.remove('is-closing');
            this.overlay.querySelector('.pas-modal-shell').innerHTML = '';
            this.dialog = null;
            this.focusable = [];
            if (this.lastFocused && typeof this.lastFocused.focus === 'function') {
                this.lastFocused.focus();
            }
            this.lastFocused = null;
            this.mode = null;
            this.siteId = null;
        }, 180);
    }

    handleKeydown(event) {
        if (!this.overlay || !this.overlay.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            this.close();
            return;
        }
        if (event.key === 'Tab') this.trapFocus(event);
    }

    setupFocusTrap() {
        if (!this.dialog) return;
        this.focusable = Array.from(this.dialog.querySelectorAll(
            'button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])'
        )).filter((el) => !el.disabled && el.offsetParent !== null);

        if (this.focusable.length > 0) {
            this.focusable[0].focus();
        } else {
            this.dialog.setAttribute('tabindex', '-1');
            this.dialog.focus();
        }
    }

    trapFocus(event) {
        if (this.focusable.length === 0) return;
        const first = this.focusable[0];
        const last = this.focusable[this.focusable.length - 1];
        const active = document.activeElement;
        if (event.shiftKey && active === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && active === last) {
            event.preventDefault();
            first.focus();
        }
    }

    async prepare(mode, siteId) {
        try {
            if (mode === 'add') return { site: null };
            const res = await this.requestJSON(`/api/protected-area-sites/${siteId}`);
            if (!res.success) throw new Error(res.error || 'Failed to load site');
            return { site: res.siteName };
        } catch (error) {
            this.notify(error.message || 'Unable to open modal', 'error');
            return null;
        }
    }

    render(mode, payload) {
        if (mode === 'delete') return this.renderDelete(payload.site);

        return `
            <section class="pas-modal" role="dialog" aria-modal="true" aria-labelledby="pas-modal-title">
                ${this.renderHeader(mode)}
                ${this.renderBody(mode, payload.site)}
                ${this.renderFooter(mode)}
            </section>
        `;
    }

    renderHeader(mode) {
        const icon = mode === 'view' ? 'eye' : mode === 'edit' ? 'pencil' : 'plus';
        const subtitle = mode === 'view'
            ? 'Review protected area site details'
            : 'Manage protected area site records';
        return `
            <div class="pas-modal-header">
                <div class="pas-modal-header-main">
                    <span class="pas-modal-header-icon" aria-hidden="true"><i data-lucide="${icon}"></i></span>
                    <div>
                        <h2 id="pas-modal-title" class="pas-modal-title">Protected Area Site</h2>
                        <p class="pas-modal-subtitle">${subtitle}</p>
                    </div>
                </div>
                <button type="button" class="pas-modal-close" data-pas-close aria-label="Close modal">
                    <i data-lucide="x"></i>
                </button>
            </div>
        `;
    }

    renderBody(mode, site) {
        if (mode === 'view') {
            const status = site?.protected_area ? 'Active' : 'Unassigned';
            return `
                <div class="pas-modal-body">
                    <div class="pas-form-grid">
                        <label class="pas-field"><span>Status</span><input class="pas-input" readonly value="${status}"></label>
                        <label class="pas-field"><span>Observations</span><input class="pas-input" readonly value="${site?.species_observations_count ?? 0}"></label>
                        <label class="pas-field pas-col-span"><span>Site Name</span><input class="pas-input" readonly value="${this.escape(site?.name)}"></label>
                        <label class="pas-field pas-col-span"><span>Protected Area</span><input class="pas-input" readonly value="${this.escape(site?.protected_area?.name) || 'Not assigned'}"></label>
                    </div>
                </div>
            `;
        }

        const formId = mode === 'edit' ? 'pas-edit-form' : 'pas-add-form';
        const buttonText = mode === 'edit' ? 'Update Site' : 'Save Site';
        return `
            <form id="${formId}" class="pas-modal-body" data-pas-form data-mode="${mode}">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <input type="hidden" name="_submit_label" value="${buttonText}">
                <div class="pas-form-section">
                    <h3 class="pas-section-title">Site Information</h3>
                    <div class="pas-form-grid pas-form-grid-single">
                        <label class="pas-field pas-col-span">
                            <span>Site Name</span>
                            <input class="pas-input" name="name" required maxlength="255" value="${mode === 'edit' ? this.escape(site?.name) : ''}" placeholder="Enter site name">
                        </label>
                        <label class="pas-field pas-col-span">
                            <span>Protected Area</span>
                            <select class="pas-input" name="protected_area_id">
                                <option value="">Select protected area (optional)</option>
                                ${this.renderProtectedAreaOptions(mode === 'edit' ? site?.protected_area_id : null)}
                            </select>
                        </label>
                    </div>
                </div>
            </form>
        `;
    }

    renderFooter(mode) {
        if (mode === 'view') {
            return `
                <div class="pas-modal-footer">
                    <button type="button" class="pas-btn pas-btn-secondary" data-pas-close>Close</button>
                </div>
            `;
        }

        const formId = mode === 'edit' ? 'pas-edit-form' : 'pas-add-form';
        const label = mode === 'edit' ? 'Update Site' : 'Save Site';
        return `
            <div class="pas-modal-footer">
                <button type="button" class="pas-btn pas-btn-secondary" data-pas-close>Cancel</button>
                <button type="submit" class="pas-btn pas-btn-primary" form="${formId}" data-pas-submit>${label}</button>
            </div>
        `;
    }

    renderDelete(site) {
        return `
            <section class="pas-modal pas-modal-delete" role="dialog" aria-modal="true" aria-labelledby="pas-delete-title">
                <div class="pas-delete-body">
                    <h2 id="pas-delete-title" class="pas-delete-title">Delete site?</h2>
                    <p class="pas-delete-name">${this.escape(site?.name || 'Selected site')}</p>
                    <p class="pas-delete-note">This action cannot be undone.</p>
                </div>
                <div class="pas-modal-footer pas-modal-footer-neutral">
                    <button type="button" class="pas-btn pas-btn-secondary" data-pas-close>Cancel</button>
                    <button type="button" class="pas-btn pas-btn-danger" data-pas-delete>Delete</button>
                </div>
            </section>
        `;
    }

    renderProtectedAreaOptions(selectedId = null) {
        if (!Array.isArray(window.protectedAreas)) return '';
        const seen = new Set();
        const uniqueAreas = window.protectedAreas.filter((area) => {
            const key = `${String(area?.code ?? '').trim().toLowerCase()}|${String(area?.name ?? '').trim().toLowerCase()}`;
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });

        return uniqueAreas.map((area) => {
            const selected = String(area.id) === String(selectedId) ? 'selected' : '';
            const name = String(area?.name ?? '').trim();
            const code = String(area?.code ?? '').trim();
            const hasCodeInName = code && name.toLowerCase().includes(`(${code.toLowerCase()})`);
            const displayLabel = hasCodeInName || !code ? name : `${name} (${code})`;
            return `<option value="${area.id}" ${selected}>${this.escape(displayLabel)}</option>`;
        }).join('');
    }

    bindDialogEvents() {
        this.overlay.querySelectorAll('[data-pas-close]').forEach((el) => {
            el.addEventListener('click', () => this.close(), { once: true });
        });

        const form = this.overlay.querySelector('form[data-pas-form]');
        if (form) {
            form.addEventListener('submit', (event) => this.submitForm(event));
        }

        const deleteButton = this.overlay.querySelector('[data-pas-delete]');
        if (deleteButton) {
            deleteButton.addEventListener('click', () => this.submitDelete());
        }
    }

    async submitForm(event) {
        event.preventDefault();
        const form = event.target;
        const mode = form.getAttribute('data-mode');
        const submitBtn = this.overlay.querySelector('[data-pas-submit]');
        if (!submitBtn) return;

        submitBtn.disabled = true;

        const fd = new FormData(form);
        const submitLabel = fd.get('_submit_label') || 'Save';
        fd.delete('_submit_label');

        try {
            let url = '/protected-area-sites';
            if (mode === 'edit' && this.siteId) {
                fd.append('_method', 'PUT');
                url = `/protected-area-sites/${this.siteId}`;
            }

            const result = await this.requestJSON(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });

            if (!result.success) throw new Error(result.error || 'Failed to save site');
            this.notify(mode === 'edit' ? 'Site updated successfully.' : 'Site created successfully.', 'success');
            this.close();
            setTimeout(() => window.location.reload(), 250);
        } catch (error) {
            this.notify(error.message || 'Failed to save site', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = submitLabel;
        }
    }

    async submitDelete() {
        if (!this.siteId) return;
        try {
            const fd = new FormData();
            fd.append('_method', 'DELETE');
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            const result = await this.requestJSON(`/protected-area-sites/${this.siteId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            if (!result.success) throw new Error(result.error || 'Failed to delete site');
            this.notify('Site deleted successfully.', 'success');
            this.close();
            setTimeout(() => window.location.reload(), 250);
        } catch (error) {
            this.notify(error.message || 'Failed to delete site', 'error');
        }
    }

    async requestJSON(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                Accept: 'application/json',
                ...(options.headers || {})
            },
            ...options
        });
        const text = await response.text();
        let json = {};
        try {
            json = text ? JSON.parse(text) : {};
        } catch {
            throw new Error('Unexpected server response');
        }
        if (!response.ok) throw new Error(json.error || json.message || `HTTP ${response.status}`);
        return json;
    }

    notify(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `pas-toast pas-toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 180);
        }, 2200);
    }

    escape(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
}

let protectedAreaSitesModalSystem;

function ensureSystem() {
    if (!protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
        window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
    }
    return protectedAreaSitesModalSystem;
}

window.openViewProtectedAreaSitesModal = (siteId) => ensureSystem().open('view', { siteId });
window.openEditProtectedAreaSitesModal = (siteId) => ensureSystem().open('edit', { siteId });
window.openAddProtectedAreaSitesModal = () => ensureSystem().open('add', {});
window.openDeleteProtectedAreaSitesModal = (siteId) => ensureSystem().open('delete', { siteId });
window.closeProtectedAreaSitesModal = () => ensureSystem().close();

function exportProtectedAreaSitesTable(format) {
    const form = document.getElementById('protected-area-sites-filter-form') || document.querySelector('form[method="GET"]');
    if (!form) return;
    const params = new URLSearchParams(new FormData(form));
    params.set('export', format);
    const searchInput = document.getElementById('protected-area-sites-search');
    if (searchInput?.value.trim()) params.set('search', searchInput.value.trim());
    const url = window.location.pathname + '?' + params.toString();
    if (format === 'print') {
        const win = window.open(url + '&print=1', '_blank');
        if (win) win.onload = () => win.print();
    } else if (format === 'excel') {
        window.location.href = url + '&excel=1';
    } else if (format === 'pdf') {
        window.location.href = url + '&pdf=1';
    }
    document.getElementById('export-dropdown')?.classList.remove('is-open');
}

function clearSiteFilters() {
    const form = document.getElementById('protected-area-sites-filter-form');
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    const search = document.getElementById('protected-area-sites-search');
    if (status) status.value = '';
    if (sort) sort.value = 'name';
    if (search) search.value = '';
    if (form) form.submit();
}

window.exportTable = exportProtectedAreaSitesTable;
window.clearSiteFilters = clearSiteFilters;

document.addEventListener('DOMContentLoaded', () => {
    ensureSystem();

    initSearchBar({
        inputId: 'protected-area-sites-search',
        clearBtnId: 'protected-area-sites-search-clear',
        formSelector: '#protected-area-sites-filter-form',
        debounceMs: 400
    });

    const filterForm = document.getElementById('protected-area-sites-filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', () => {
            const searchInput = document.getElementById('protected-area-sites-search');
            if (searchInput?.value.trim()) {
                let hidden = filterForm.querySelector('input[name="search"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'search';
                    filterForm.appendChild(hidden);
                }
                hidden.value = searchInput.value.trim();
            }
        });
    }

    const exportBtn = document.getElementById('export-dropdown-btn');
    const exportDropdown = document.getElementById('export-dropdown');
    if (exportBtn && exportDropdown) {
        exportBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            exportDropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', (event) => {
            if (!exportDropdown.contains(event.target) && event.target !== exportBtn) {
                exportDropdown.classList.remove('is-open');
            }
        });
    }
});
