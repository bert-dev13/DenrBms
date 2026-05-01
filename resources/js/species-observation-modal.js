/**
 * Species Observation Modal System (clean rebuild)
 */

class SpeciesObservationModalSystem {
    constructor() {
        this.overlay = null;
        this.modalType = null;
        this.modalData = null;
        this.init();
    }

    init() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'so-modal-overlay';
        this.overlay.setAttribute('aria-hidden', 'true');
        this.overlay.innerHTML = '<div class="so-modal-shell"></div>';
        document.body.appendChild(this.overlay);

        this.overlay.addEventListener('click', (event) => {
            if (event.target === this.overlay) {
                this.close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.overlay.classList.contains('is-open')) {
                this.close();
            }
        });
    }

    async open(type, data = {}) {
        this.modalType = type;
        this.modalData = await this.prepareData(type, data);
        if (!this.modalData) return false;

        const shell = this.overlay.querySelector('.so-modal-shell');
        shell.innerHTML = this.renderModal(type, this.modalData);
        if (typeof window.replaceLucideIcons === 'function') {
            window.replaceLucideIcons(shell);
        }

        this.overlay.classList.add('is-open');
        this.overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        if (type === 'edit') {
            this.loadSiteNames('so_edit_protected_area', 'so_edit_site_name', this.modalData.observation.site_name_id || '');
        }

        return true;
    }

    close() {
        this.overlay.classList.remove('is-open');
        this.overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        this.overlay.querySelector('.so-modal-shell').innerHTML = '';
        this.modalType = null;
        this.modalData = null;
    }

    async prepareData(type, data) {
        try {
            if (type === 'add') return this.prepareFormData();
            if (type === 'view' || type === 'edit' || type === 'delete') {
                const endpoint = type === 'edit'
                    ? `/api/species-observations/edit-data/${data.observationId}?table_name=${encodeURIComponent(data.tableName || '')}`
                    : `/api/species-observations/data/${data.observationId}?table_name=${encodeURIComponent(data.tableName || '')}`;
                const response = await this.requestJSON(endpoint);
                if (!response.success) throw new Error(response.error || 'Failed to load observation');

                if (type === 'view' || type === 'delete') {
                    return {
                        observation: response.observation,
                        observationId: data.observationId,
                        tableName: data.tableName
                    };
                }

                return {
                    ...(this.prepareFormData()),
                    observation: response.observation,
                    observationId: data.observationId,
                    tableName: data.tableName
                };
            }
            return null;
        } catch (error) {
            this.notify(error.message || 'Failed to open modal', 'error');
            return null;
        }
    }

    prepareFormData() {
        const selectToOptions = (selector) => {
            const node = document.querySelector(selector);
            if (!node) return [];
            return Array.from(node.options)
                .filter((opt) => opt.value)
                .map((opt) => ({
                    value: opt.value,
                    label: opt.textContent.trim(),
                    code: opt.getAttribute('data-code') || ''
                }));
        };

        return {
            protectedAreas: selectToOptions('#protected_area_id'),
            bioGroups: selectToOptions('#bio_group'),
            years: selectToOptions('#patrol_year'),
            semesters: selectToOptions('#patrol_semester')
        };
    }

    renderModal(type, data) {
        if (type === 'delete') {
            return this.renderDeleteModal(data);
        }

        const sizeClass = 'so-modal--large';
        return `
            <section class="so-modal ${sizeClass}" role="dialog" aria-modal="true">
                ${this.renderHeader(type)}
                ${this.renderBody(type, data)}
                ${this.renderFooter(type, data)}
            </section>
        `;
    }

    renderDeleteModal(data) {
        const name = data.observation?.common_name || 'this observation';
        const safeName = String(name).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return `
            <section class="so-modal so-modal--small so-modal--delete" role="dialog" aria-modal="true">
                <div class="so-delete-body">
                    <h3 class="so-delete-title">Delete observation?</h3>
                    <p class="so-delete-subtitle">${safeName}</p>
                    <p class="so-delete-note">This action cannot be undone.</p>
                </div>
                <div class="so-delete-footer">
                    <button class="so-btn so-btn-cancel" type="button" onclick="window.closeModal()">Cancel</button>
                    <button class="so-btn so-btn-danger" type="button" onclick="window.modalSystem.confirmDelete('${data.observationId}', '${(data.tableName || '').replace(/'/g, "\\'")}')">Delete</button>
                </div>
            </section>
        `;
    }

    getHeaderIcon(type) {
        if (type === 'edit') {
            return 'pencil';
        }

        if (type === 'delete') {
            return 'trash-2';
        }

        if (type === 'view') {
            return 'eye';
        }

        return 'leaf';
    }

    renderHeader(type) {
        return `
            <div class="so-modal-header">
                <div class="so-modal-header-left">
                    <span class="so-modal-header-icon" aria-hidden="true">
                        <i data-lucide="${this.getHeaderIcon(type)}"></i>
                    </span>
                    <div>
                        <h2 class="so-modal-title">Species Observation</h2>
                        <p class="so-modal-subtitle">Manage biodiversity field records</p>
                    </div>
                </div>
                <button type="button" class="so-modal-close" onclick="window.closeModal()" aria-label="Close">×</button>
            </div>
        `;
    }

    renderBody(type, data) {
        if (type === 'view') return this.renderView(data.observation);
        if (type === 'add') return this.renderAdd(data);
        if (type === 'edit') return this.renderEdit(data);
        return this.renderDelete(data);
    }

    renderFooter(type, data) {
        if (type === 'view') {
            return `
                <div class="so-modal-footer">
                    <button class="so-btn so-btn-cancel" type="button" onclick="window.closeModal()">Close</button>
                </div>
            `;
        }
        const formId = type === 'add' ? 'so-add-form' : 'so-edit-form';
        return `
            <div class="so-modal-footer">
                <button class="so-btn so-btn-cancel" type="button" onclick="window.closeModal()">Cancel</button>
                <button class="so-btn so-btn-save" type="submit" form="${formId}">Save Observation</button>
            </div>
        `;
    }

    renderView(observation) {
        const safe = (v) => String(v ?? 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const site = observation.site_name?.name || 'No specific site';
        const semester = observation.patrol_semester === 1 ? '1st Semester' : observation.patrol_semester === 2 ? '2nd Semester' : 'N/A';
        return `
            <div class="so-modal-body">
                <div class="so-grid">
                    <label>Protected Area<input class="so-input" readonly value="${safe(observation.protected_area?.name)}"></label>
                    <label>Station Code<input class="so-input" readonly value="${safe(observation.station_code)}"></label>
                    <label class="so-span-2">Site Name<input class="so-input" readonly value="${safe(site)}"></label>
                    <label>Bio Group<input class="so-input" readonly value="${safe(observation.bio_group)}"></label>
                    <label>Recorded Count<input class="so-input" readonly value="${safe(observation.recorded_count)}"></label>
                    <label class="so-span-2">Common Name<input class="so-input" readonly value="${safe(observation.common_name)}"></label>
                    <label class="so-span-2">Scientific Name<input class="so-input" readonly value="${safe(observation.scientific_name)}"></label>
                    <label>Patrol Year<input class="so-input" readonly value="${safe(observation.patrol_year)}"></label>
                    <label>Patrol Semester<input class="so-input" readonly value="${safe(semester)}"></label>
                </div>
            </div>
        `;
    }

    renderAdd(data) {
        return `
            <form id="so-add-form" class="so-modal-body" onsubmit="window.modalSystem.submitAdd(event)">
                ${this.renderFormFields(data, null)}
            </form>
        `;
    }

    renderEdit(data) {
        return `
            <form id="so-edit-form" class="so-modal-body" onsubmit="window.modalSystem.submitEdit(event, '${data.observationId}', '${(data.tableName || '').replace(/'/g, "\\'")}')">
                ${this.renderFormFields(data, data.observation)}
            </form>
        `;
    }

    renderFormFields(data, observation) {
        const selected = (a, b) => String(a) === String(b) ? 'selected' : '';
        const options = (list, current) => list.map((o) => `<option value="${o.value}" data-code="${o.code || ''}" ${selected(o.value, current)}>${o.label}</option>`).join('');
        return `
            <div class="so-grid">
                <label>Protected Area
                    <select class="so-input" id="${observation ? 'so_edit_protected_area' : 'so_add_protected_area'}" name="protected_area_id" required onchange="window.modalSystem.onProtectedAreaChange('${observation ? 'so_edit_protected_area' : 'so_add_protected_area'}','${observation ? 'so_edit_site_name' : 'so_add_site_name'}')">
                        <option value="">Select</option>
                        ${options(data.protectedAreas, observation?.protected_area_id)}
                    </select>
                </label>
                <label>Station Code<input class="so-input" name="station_code" required maxlength="60" value="${observation?.station_code || ''}"></label>
                <label class="so-span-2">Site Name
                    <select class="so-input" id="${observation ? 'so_edit_site_name' : 'so_add_site_name'}" name="site_name_id">
                        <option value="">No specific site</option>
                    </select>
                </label>
                <label>Bio Group
                    <select class="so-input" name="bio_group" required>
                        <option value="">Select</option>
                        ${options(data.bioGroups, observation?.bio_group)}
                    </select>
                </label>
                <label>Recorded Count<input class="so-input" name="recorded_count" type="number" min="0" required value="${observation?.recorded_count ?? 0}"></label>
                <label class="so-span-2">Common Name<input class="so-input" name="common_name" required maxlength="150" value="${observation?.common_name || ''}"></label>
                <label class="so-span-2">Scientific Name<input class="so-input" name="scientific_name" required maxlength="200" value="${observation?.scientific_name || ''}"></label>
                <label>Patrol Year
                    <select class="so-input" name="patrol_year" required>
                        <option value="">Select</option>
                        ${options(data.years, observation?.patrol_year)}
                    </select>
                </label>
                <label>Patrol Semester
                    <select class="so-input" name="patrol_semester" required>
                        <option value="">Select</option>
                        ${options(data.semesters, observation?.patrol_semester)}
                    </select>
                </label>
                <label class="so-span-2">Transaction Code<input class="so-input" name="transaction_code" required maxlength="50" value="${observation?.transaction_code || ''}"></label>
            </div>
        `;
    }

    renderDelete() { return ''; }

    async onProtectedAreaChange(areaId, siteId) {
        await this.loadSiteNames(areaId, siteId, '');
    }

    async loadSiteNames(areaId, siteId, selectedValue = '') {
        const areaSelect = document.getElementById(areaId);
        const siteSelect = document.getElementById(siteId);
        if (!areaSelect || !siteSelect) return;

        const protectedAreaId = areaSelect.value;
        if (!protectedAreaId) {
            siteSelect.innerHTML = '<option value="">No specific site</option>';
            return;
        }

        siteSelect.innerHTML = '<option value="">Loading...</option>';
        try {
            const result = await this.requestJSON(`/api/species-observations/site-names/${protectedAreaId}`);
            const siteNames = result.success && Array.isArray(result.site_names) ? result.site_names : [];
            let html = '<option value="">No specific site</option>';
            siteNames.forEach((site) => {
                const isSelected = String(site.id) === String(selectedValue) ? 'selected' : '';
                html += `<option value="${site.id}" ${isSelected}>${site.name}</option>`;
            });
            siteSelect.innerHTML = html;
        } catch (error) {
            siteSelect.innerHTML = '<option value="">No specific site</option>';
        }
    }

    async submitAdd(event) {
        event.preventDefault();
        const form = event.target;
        const payload = Object.fromEntries(new FormData(form));
        if (!payload.site_name_id) payload.site_name_id = '';

        try {
            const result = await this.requestJSON('/species-observations', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!result.success) throw new Error(result.message || 'Failed to save observation');
            this.notify('Observation added successfully.', 'success');
            this.close();
            setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            this.notify(error.message || 'Failed to save observation', 'error');
        }
    }

    async submitEdit(event, observationId, tableName) {
        event.preventDefault();
        const form = event.target;
        const payload = Object.fromEntries(new FormData(form));
        payload._method = 'PUT';
        payload.table_name = tableName || payload.table_name || '';
        if (!payload.site_name_id) payload.site_name_id = '';

        try {
            const result = await this.requestJSON(`/species-observations/${observationId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!result.success) throw new Error(result.message || 'Failed to update observation');
            this.notify('Observation updated successfully.', 'success');
            this.close();
            setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            this.notify(error.message || 'Failed to update observation', 'error');
        }
    }

    async confirmDelete(observationId, tableName) {
        try {
            const url = window.routes.speciesObservationsDestroy.replace(':id', String(observationId));
            const result = await this.requestJSON(url, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ table_name: tableName || '' })
            });
            if (!result.success) throw new Error(result.message || 'Failed to delete observation');
            this.notify('Observation deleted successfully.', 'success');
            this.close();
            setTimeout(() => window.location.reload(), 300);
        } catch (error) {
            this.notify(error.message || 'Failed to delete observation', 'error');
        }
    }

    async requestJSON(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
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
        if (!response.ok) throw new Error(json.message || `HTTP ${response.status}`);
        return json;
    }

    notify(message, type = 'info') {
        const notice = document.createElement('div');
        notice.className = `so-notice so-notice-${type}`;
        notice.textContent = message;
        document.body.appendChild(notice);
        setTimeout(() => notice.remove(), 2500);
    }
}

const modalSystem = new SpeciesObservationModalSystem();
window.modalSystem = modalSystem;
window.openAddModal = () => modalSystem.open('add', {});
window.openViewModal = (observationId, tableName) => modalSystem.open('view', { observationId, tableName });
window.openEditModal = (observationId, tableName) => modalSystem.open('edit', { observationId, tableName });
window.openDeleteModal = (observationId, tableName) => modalSystem.open('delete', { observationId, tableName });
window.closeModal = () => modalSystem.close();
