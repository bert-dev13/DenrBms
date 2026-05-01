/**
 * Protected Area Modal System (clean rebuild)
 * - Fully namespaced to avoid selector conflicts
 * - Accessible: ESC close, overlay close, focus trap
 * - Smooth open/close transitions without layout shifts
 */

class ProtectedAreaModalSystem {
    constructor() {
        this.overlay = null;
        this.dialog = null;
        this.focusable = [];
        this.lastFocused = null;
        this.activeType = null;
        this.activeId = null;
        this.boundKeydown = this.handleGlobalKeydown.bind(this);
        this.init();
    }

    init() {
        if (document.getElementById('pa-modal-overlay')) return;

        this.overlay = document.createElement('div');
        this.overlay.id = 'pa-modal-overlay';
        this.overlay.className = 'pa-modal-overlay';
        this.overlay.setAttribute('aria-hidden', 'true');
        this.overlay.innerHTML = '<div class="pa-modal-shell"></div>';
        document.body.appendChild(this.overlay);

        this.overlay.addEventListener('click', (event) => {
            if (event.target === this.overlay) this.close();
        });

        document.addEventListener('keydown', this.boundKeydown);
    }

    async open(type, payload = {}) {
        this.activeType = type;
        this.activeId = payload.areaId || null;
        this.lastFocused = document.activeElement;

        const data = await this.loadData(type, payload.areaId);
        if (!data) return false;

        const shell = this.overlay.querySelector('.pa-modal-shell');
        shell.innerHTML = this.render(type, data);
        this.dialog = shell.querySelector('.pa-modal');

        this.overlay.classList.add('is-open');
        this.overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pa-modal-open');

        this.wireDialogEvents();
        this.initializeFocusTrap();

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
        document.body.classList.remove('pa-modal-open');

        window.setTimeout(() => {
            this.overlay.classList.remove('is-closing');
            this.overlay.querySelector('.pa-modal-shell').innerHTML = '';
            this.dialog = null;
            this.focusable = [];
            if (this.lastFocused && typeof this.lastFocused.focus === 'function') {
                this.lastFocused.focus();
            }
            this.lastFocused = null;
            this.activeType = null;
            this.activeId = null;
        }, 180);
    }

    handleGlobalKeydown(event) {
        if (!this.overlay || !this.overlay.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            this.close();
            return;
        }

        if (event.key === 'Tab' && this.dialog) {
            this.trapFocus(event);
        }
    }

    initializeFocusTrap() {
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

    wireDialogEvents() {
        const closeButtons = this.overlay.querySelectorAll('[data-pa-close]');
        closeButtons.forEach((button) => {
            button.addEventListener('click', () => this.close(), { once: true });
        });

        const form = this.overlay.querySelector('form[data-pa-form]');
        if (form) {
            form.addEventListener('submit', (event) => this.submitForm(event));
        }

        const deleteButton = this.overlay.querySelector('[data-pa-delete-confirm]');
        if (deleteButton) {
            deleteButton.addEventListener('click', () => this.submitDelete());
        }
    }

    async loadData(type, areaId) {
        try {
            if (type === 'add') return { area: null };
            const res = await this.requestJSON(`/api/protected-areas/${areaId}`);
            if (!res.success) throw new Error(res.error || 'Unable to load protected area');
            return { area: res.protectedArea };
        } catch (error) {
            this.notify(error.message || 'Failed to open modal', 'error');
            return null;
        }
    }

    render(type, data) {
        if (type === 'delete') return this.renderDelete(data.area);

        return `
            <section class="pa-modal" role="dialog" aria-modal="true" aria-labelledby="pa-modal-title">
                ${this.renderHeader(type)}
                ${this.renderBody(type, data.area)}
                ${this.renderFooter(type)}
            </section>
        `;
    }

    renderHeader(type) {
        const icon = type === 'view' ? 'eye' : type === 'edit' ? 'pencil' : 'plus';
        const subtitle = type === 'view'
            ? 'Protected area details'
            : 'Manage protected area records';

        return `
            <div class="pa-modal-header">
                <div class="pa-modal-header-main">
                    <span class="pa-modal-header-icon" aria-hidden="true"><i data-lucide="${icon}"></i></span>
                    <div>
                        <h2 id="pa-modal-title" class="pa-modal-title">Protected Area</h2>
                        <p class="pa-modal-subtitle">${subtitle}</p>
                    </div>
                </div>
                <button type="button" class="pa-modal-close" data-pa-close aria-label="Close modal">
                    <i data-lucide="x"></i>
                </button>
            </div>
        `;
    }

    renderBody(type, area) {
        if (type === 'view') {
            const status = (area?.species_observations_count || 0) > 0 ? 'Active' : 'No Data';
            return `
                <div class="pa-modal-body">
                    <div class="pa-form-grid">
                        <label class="pa-field"><span>Area Code</span><input class="pa-input" readonly value="${this.escape(area?.code)}"></label>
                        <label class="pa-field"><span>Status</span><input class="pa-input" readonly value="${status}"></label>
                        <label class="pa-field pa-col-span"><span>Name</span><input class="pa-input" readonly value="${this.escape(area?.name)}"></label>
                        <label class="pa-field"><span>Observations</span><input class="pa-input" readonly value="${area?.species_observations_count ?? 0}"></label>
                    </div>
                </div>
            `;
        }

        const codeValue = type === 'edit' ? this.escape(area?.code) : this.generateDefaultCode();
        const nameValue = type === 'edit' ? this.escape(area?.name) : '';
        const submitLabel = type === 'edit' ? 'Update Protected Area' : 'Save Protected Area';
        const formId = type === 'edit' ? 'pa-edit-form' : 'pa-add-form';
        return `
            <form id="${formId}" class="pa-modal-body" data-pa-form data-mode="${type}">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <div class="pa-form-section">
                    <h3 class="pa-section-title">Identification</h3>
                    <div class="pa-form-grid pa-form-grid-single">
                        <label class="pa-field pa-col-span">
                            <span>Area Code</span>
                            <input class="pa-input" name="code" required maxlength="255" value="${codeValue}" placeholder="e.g. PA1001">
                        </label>
                        <label class="pa-field pa-col-span">
                            <span>Name</span>
                            <input class="pa-input" name="name" required maxlength="255" value="${nameValue}" placeholder="Enter protected area name">
                        </label>
                    </div>
                </div>
                <input type="hidden" name="_submit_label" value="${submitLabel}">
            </form>
        `;
    }

    renderFooter(type) {
        if (type === 'view') {
            return `
                <div class="pa-modal-footer">
                    <button type="button" class="pa-btn pa-btn-secondary" data-pa-close>Close</button>
                </div>
            `;
        }

        const label = type === 'edit' ? 'Update Protected Area' : 'Save Protected Area';
        const formId = type === 'edit' ? 'pa-edit-form' : 'pa-add-form';
        return `
            <div class="pa-modal-footer">
                <button type="button" class="pa-btn pa-btn-secondary" data-pa-close>Cancel</button>
                <button type="submit" class="pa-btn pa-btn-primary" form="${formId}" data-pa-submit>${label}</button>
            </div>
        `;
    }

    renderDelete(area) {
        return `
            <section class="pa-modal pa-modal-delete" role="dialog" aria-modal="true" aria-labelledby="pa-delete-title">
                <div class="pa-delete-body">
                    <h2 id="pa-delete-title" class="pa-delete-title">Delete protected area?</h2>
                    <p class="pa-delete-name">${this.escape(area?.name || 'Selected area')}</p>
                    <p class="pa-delete-note">This action cannot be undone.</p>
                </div>
                <div class="pa-modal-footer">
                    <button type="button" class="pa-btn pa-btn-secondary" data-pa-close>Cancel</button>
                    <button type="button" class="pa-btn pa-btn-danger" data-pa-delete-confirm>Delete</button>
                </div>
            </section>
        `;
    }

    async submitForm(event) {
        event.preventDefault();
        const form = event.target;
        const mode = form.getAttribute('data-mode');
        const submitBtn = this.overlay.querySelector('[data-pa-submit]');
        if (!submitBtn) return;

        submitBtn.disabled = true;

        const formData = new FormData(form);
        const submitLabel = formData.get('_submit_label') || 'Save';
        formData.delete('_submit_label');

        try {
            let url = '/protected-areas';
            if (mode === 'edit' && this.activeId) {
                formData.append('_method', 'PUT');
                url = `/protected-areas/${this.activeId}`;
            }

            const result = await this.requestJSON(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            if (!result.success) throw new Error(result.error || 'Failed to save protected area');
            this.notify(mode === 'edit' ? 'Protected area updated.' : 'Protected area added.', 'success');
            this.close();
            window.setTimeout(() => window.location.reload(), 250);
        } catch (error) {
            this.notify(error.message || 'Failed to save protected area', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = submitLabel;
        }
    }

    async submitDelete() {
        if (!this.activeId) return;
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            const result = await this.requestJSON(`/protected-areas/${this.activeId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            if (!result.success) throw new Error(result.error || 'Failed to delete protected area');
            this.notify('Protected area deleted.', 'success');
            this.close();
            window.setTimeout(() => window.location.reload(), 250);
        } catch (error) {
            this.notify(error.message || 'Failed to delete protected area', 'error');
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
        let payload = {};
        try {
            payload = text ? JSON.parse(text) : {};
        } catch {
            throw new Error('Unexpected server response');
        }
        if (!response.ok) throw new Error(payload.error || payload.message || `HTTP ${response.status}`);
        return payload;
    }

    notify(message, type = 'info') {
        const n = document.createElement('div');
        n.className = `pa-toast pa-toast-${type}`;
        n.textContent = message;
        document.body.appendChild(n);
        window.setTimeout(() => n.classList.add('show'), 10);
        window.setTimeout(() => {
            n.classList.remove('show');
            window.setTimeout(() => n.remove(), 180);
        }, 2200);
    }

    generateDefaultCode() {
        const last4 = String(Date.now()).slice(-4);
        return `PA${last4}`;
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

let protectedAreaModalSystem;

function ensureSystem() {
    if (!protectedAreaModalSystem) {
        protectedAreaModalSystem = new ProtectedAreaModalSystem();
        window.protectedAreaModalSystem = protectedAreaModalSystem;
    }
    return protectedAreaModalSystem;
}

window.openViewModal = (areaId) => ensureSystem().open('view', { areaId });
window.openEditModal = (areaId) => ensureSystem().open('edit', { areaId });
window.openAddModal = () => ensureSystem().open('add', {});
window.openDeleteModal = (areaId) => ensureSystem().open('delete', { areaId });
window.closeProtectedAreaModal = () => ensureSystem().close();

document.addEventListener('DOMContentLoaded', () => ensureSystem());
