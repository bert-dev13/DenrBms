/**
 * Protected Area Sites Modal System
 * Handles all modal interactions for protected area sites (view, edit, add, delete)
 * Scoped system to avoid conflicts with existing modals
 */

import { initSearchBar } from './search-bar.js';

class ProtectedAreaSitesModalSystem {
    constructor() {
        this.overlay = null;
        this.modal = null;
        this.isOpening = false;
        this.isClosing = false;
        this.init();
    }

    init() {
        // Create overlay element
        this.createOverlay();
        
        // Setup global event listeners
        this.setupEventListeners();
    }

    createOverlay() {
        // Remove any existing overlay first
        const existingOverlay = document.getElementById('protected-area-sites-modal-overlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }
        
        this.overlay = document.createElement('div');
        // Use the standard system modal overlay class so styling is centralized
        this.overlay.className = 'modal-overlay';
        this.overlay.id = 'protected-area-sites-modal-overlay';
        document.body.appendChild(this.overlay);
    }

    setupEventListeners() {
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.overlay.style.display === 'flex') {
                this.close();
            }
        });
    }

    async open(type, data = {}) {
        // Prevent multiple simultaneous opens
        if (this.isOpening || this.isClosing) {
            return false;
        }

        this.isOpening = true;

        try {
            // Prepare all data before showing modal
            const preparedData = await this.prepareModalData(type, data);
            
            if (!preparedData) {
                this.isOpening = false;
                return false;
            }

            // Create modal content
            this.createModalContent(type, preparedData);

            // Show modal
            this.showModal();

            this.isOpening = false;
            return true;
        } catch (error) {
            console.error('Error opening modal:', error);
            this.isOpening = false;
            return false;
        }
    }

    async prepareModalData(type, data) {
        switch (type) {
            case 'view':
                return await this.prepareViewData(data);
            case 'edit':
                return await this.prepareEditData(data);
            case 'add':
                return await this.prepareAddData(data);
            case 'delete':
                return await this.prepareDeleteData(data);
            default:
                console.error('Unknown modal type:', type);
                return null;
        }
    }

    async prepareViewData(data) {
        const { siteId } = data;
        
        try {
            // Fetch protected area site data
            const response = await fetch(`/api/protected-area-sites/${siteId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load protected area site data');
            }

            return {
                type: 'view',
                site: result.siteName,
                siteId
            };

        } catch (error) {
            console.error('Error preparing view data:', error);
            this.showNotification('Error loading protected area site data', 'error');
            return null;
        }
    }

    async prepareEditData(data) {
        const { siteId } = data;
        
        try {
            // Fetch protected area site data
            const response = await fetch(`/api/protected-area-sites/${siteId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load protected area site data');
            }

            return {
                type: 'edit',
                site: result.siteName,
                siteId
            };

        } catch (error) {
            console.error('Error preparing edit data:', error);
            this.showNotification('Error loading protected area site data', 'error');
            return null;
        }
    }

    async prepareAddData(data) {
        // For adding a new protected area site, we don't need to fetch any data
        // Just return the type to create the add modal
        return {
            type: 'add'
        };
    }

    async prepareDeleteData(data) {
        const { siteId } = data;
        
        try {
            // Fetch protected area site data for confirmation
            const response = await fetch(`/api/protected-area-sites/${siteId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to load protected area site data');
            }

            return {
                type: 'delete',
                site: result.siteName,
                siteId
            };

        } catch (error) {
            console.error('Error preparing delete data:', error);
            this.showNotification('Error loading protected area site data', 'error');
            return null;
        }
    }

    createModalContent(type, data) {
        this.overlay.innerHTML = '';

        const modal = document.createElement('div');
        modal.className = 'modal-content protected-area-sites-modal-content';

        switch (type) {
            case 'view':
                modal.classList.add('large', 'modal-add');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'edit':
                modal.classList.add('large', 'modal-add');
                modal.innerHTML = this.createEditModalHTML(data);
                break;
            case 'add':
                modal.classList.add('large', 'modal-add');
                modal.innerHTML = this.createAddModalHTML(data);
                break;
            case 'delete':
                modal.classList.add('small');
                modal.innerHTML = this.createDeleteModalHTML(data);
                break;
        }

        this.overlay.appendChild(modal);
        this.modal = modal;
    }

    createViewModalHTML(data) {
        const { site } = data;
        const siteName = (site.name || 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const protectedAreaName = (site.protected_area ? site.protected_area.name : 'Not assigned').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const statusText = site.protected_area ? 'Active' : 'Unassigned';
        const observationCount = site.species_observations_count ?? 0;

        return `
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">View Site</h2>
                <button class="modal-close" onclick="closeProtectedAreaSitesModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-form modal-form-add">
                <div class="modal-body modal-body-add modal-view-protected-area-site">
                    <section class="form-section">
                        <h3 class="form-section-title">Site Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label">Station Code</label>
                                <input type="text" class="form-input" value="${site.station_code || 'N/A'}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-input" value="${statusText}" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-input" value="${siteName}" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Protected Area</label>
                                <input type="text" class="form-input" value="${protectedAreaName}" readonly>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Observation Summary</h3>
                        <div class="form-section-grid">
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Total Observations</label>
                                <input type="text" class="form-input" value="${observationCount}" readonly>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeProtectedAreaSitesModal()">Close</button>
                </div>
            </div>
        `;
    }

    createEditModalHTML(data) {
        const { site } = data;
        const siteNameVal = (site.name || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const stationCodeVal = (site.station_code || '').replace(/"/g, '&quot;');

        return `
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">Edit Site</h2>
                <button class="modal-close" onclick="closeProtectedAreaSitesModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form modal-form-add" onsubmit="protectedAreaSitesModalSystem.submitEditForm(event, ${site.id})">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <div class="modal-body modal-body-add">
                    <section class="form-section">
                        <h3 class="form-section-title">Site Information</h3>
                        <div class="form-section-grid form-section-grid-full">
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Site Name</label>
                                <input type="text" class="form-input" name="name" value="${siteNameVal}" required maxlength="255" placeholder="e.g., Site A">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Station Code</label>
                                <input type="text" class="form-input" name="station_code" value="${stationCodeVal}" maxlength="255" placeholder="e.g., ST001">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Protected Area</label>
                                <select class="form-input form-select" name="protected_area_id">
                                    <option value="">Select Protected Area (Optional)</option>
                                    ${this.generateProtectedAreaOptions(site.protected_area_id)}
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary-add">Update</button>
                </div>
            </form>
        `;
    }

    createAddModalHTML(data) {
        return `
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">Add Site</h2>
                <button class="modal-close" onclick="closeProtectedAreaSitesModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form modal-form-add" onsubmit="protectedAreaSitesModalSystem.submitAddForm(event)">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <div class="modal-body modal-body-add">
                    <section class="form-section">
                        <h3 class="form-section-title">Site Information</h3>
                        <div class="form-section-grid form-section-grid-full">
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Site Name</label>
                                <input type="text" class="form-input" name="name" required maxlength="255" placeholder="e.g., Site A">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Station Code</label>
                                <input type="text" class="form-input" name="station_code" maxlength="255" placeholder="e.g., ST001">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Protected Area</label>
                                <select class="form-input form-select" name="protected_area_id">
                                    <option value="">Select Protected Area (Optional)</option>
                                    ${this.generateProtectedAreaOptions()}
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary-add">Save</button>
                </div>
            </form>
        `;
    }

    createDeleteModalHTML(data) {
        const { site } = data;
        const name = (site.name || 'this site').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

        return `
            <div class="modal-header">
                <h2 class="modal-title">Delete Site</h2>
                <button class="modal-close" onclick="closeProtectedAreaSitesModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-modal-content">
                    <div class="delete-modal-icon-wrap">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="delete-modal-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="delete-modal-title">Delete ${name}?</h3>
                    <p class="delete-modal-warning">Cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteProtectedAreaSite(${site.id})">Delete</button>
            </div>
        `;
    }

    generateProtectedAreaOptions(selectedId = null) {
        if (!window.protectedAreas || !Array.isArray(window.protectedAreas)) {
            console.warn('Protected areas data not available');
            return '<option value="">No protected areas available</option>';
        }
        
        return window.protectedAreas.map(area => 
            `<option value="${area.id}" ${area.id == selectedId ? 'selected' : ''}>${area.name} (${area.code})</option>`
        ).join('');
    }

    showModal() {
        this.overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Add animation
        setTimeout(() => {
            this.overlay.classList.add('active');
        }, 10);
    }

    close() {
        if (this.isClosing) {
            return;
        }

        this.isClosing = true;

        // Add closing animation
        this.overlay.classList.remove('active');

        setTimeout(() => {
            this.overlay.style.display = 'none';
            document.body.style.overflow = '';
            this.isClosing = false;
        }, 200);
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `protected-area-sites-notification protected-area-sites-notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    async submitEditForm(event, siteId) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Add method override for Laravel PUT support
        formData.append('_method', 'PUT');
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = '';

        try {
            const response = await fetch(`/protected-area-sites/${siteId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Protected area site updated successfully!', 'success');
                this.close();
                
                // Update the table row without page reload
                this.updateTableRow(siteId, result.siteName);
            } else {
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                    this.showNotification(result.error || 'Validation failed', 'error');
                } else {
                    this.showNotification(result.error || 'Failed to update protected area site', 'error');
                }
            }
        } catch (error) {
            console.error('Error updating protected area site:', error);
            this.showNotification('Error updating protected area site', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Update';
        }
    }

    async submitAddForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = '';
        
        // Clear any existing errors
        this.clearFormErrors(form);

        try {
            const response = await fetch('/protected-area-sites', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Protected area site created successfully!', 'success');
                this.close();
                
                // Add the new row to the table without page reload
                this.addTableRow(result.siteName);
                this.updateRecordCount();
            } else {
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                } else {
                    this.showNotification(result.error || 'Failed to add protected area site', 'error');
                }
            }
        } catch (error) {
            console.error('Error adding protected area site:', error);
            this.showNotification('Network error: ' + error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Save';
        }
    }

    async confirmDelete(siteId) {
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            
            const response = await fetch(`/protected-area-sites/${siteId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Protected area site deleted successfully!', 'success');
                this.close();
                this.removeTableRow(siteId);
            } else {
                this.showNotification(result.error || 'Failed to delete protected area site', 'error');
            }
        } catch (error) {
            console.error('Error deleting protected area site:', error);
            this.showNotification('Error deleting protected area site', 'error');
        }
    }

    updateTableRow(siteId, updatedSite) {
        console.log('Updating table row for site:', siteId, 'with data:', updatedSite);
        
        if (!updatedSite) {
            console.error('Updated site data is undefined');
            return;
        }
        
        const row = document.querySelector(`tr[data-site-id="${siteId}"]`);
        if (row) {
            // Update site name
            const nameCell = row.querySelector('td:first-child');
            if (nameCell) {
                nameCell.innerHTML = `<div class="font-medium text-gray-900">${updatedSite.name || 'N/A'}</div>`;
            }
            
            // Update protected area
            const protectedAreaCell = row.querySelector('td:nth-child(2)');
            if (protectedAreaCell) {
                if (updatedSite.protected_area) {
                    protectedAreaCell.innerHTML = `
                        <div class="text-sm text-gray-900">${updatedSite.protected_area.name || 'N/A'}</div>
                        <div class="text-xs text-gray-500">${updatedSite.protected_area.code || 'N/A'}</div>
                    `;
                } else {
                    protectedAreaCell.innerHTML = '<span class="text-sm text-gray-400">Not assigned</span>';
                }
            }
            
            // Update station code
            const stationCodeCell = row.querySelector('td:nth-child(3)');
            if (stationCodeCell) {
                if (updatedSite.station_code) {
                    stationCodeCell.innerHTML = `<span class="station-code-badge">${updatedSite.station_code}</span>`;
                } else {
                    stationCodeCell.innerHTML = '<span class="text-sm text-gray-400">N/A</span>';
                }
            }
            
            // Update status
            const statusCell = row.querySelector('td:nth-child(5)');
            if (statusCell) {
                const statusBadge = updatedSite.protected_area 
                    ? '<span class="status-badge status-badge-active">Active</span>'
                    : '<span class="status-badge status-badge-unassigned">Unassigned</span>';
                statusCell.innerHTML = statusBadge;
            }
            
            console.log('Successfully updated table row for site:', siteId);
        } else {
            console.error('Could not find row for site ID:', siteId);
        }
    }

    addTableRow(newSite) {
        const tableBody = document.getElementById('protected-area-sites-table-body');
        if (!tableBody) {
            console.error('Table body not found!');
            return;
        }

        // Create new row element
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50 protected-area-sites-row';
        newRow.setAttribute('data-site-id', newSite.id);
        
        // Create status badge
        const statusBadge = newSite.protected_area 
            ? '<span class="status-badge status-badge-active">Active</span>'
            : '<span class="status-badge status-badge-unassigned">Unassigned</span>';

        // Create station code badge
        const stationCodeBadge = newSite.station_code 
            ? `<span class="station-code-badge">${newSite.station_code}</span>`
            : '<span class="text-sm text-gray-400">N/A</span>';

        // Set row HTML
        newRow.innerHTML = `
            <td>
                <div class="font-medium text-gray-900">${newSite.name || 'N/A'}</div>
            </td>
            <td>
                ${newSite.protected_area ? 
                    `<div class="text-sm text-gray-900">${newSite.protected_area.name}</div>
                     <div class="text-xs text-gray-500">${newSite.protected_area.code}</div>` : 
                    '<span class="text-sm text-gray-400">Not assigned</span>'}
            </td>
            <td>
                ${stationCodeBadge}
            </td>
            <td>
                <div>
                    <div class="text-sm text-gray-900">${newSite.species_observations_count || 0}</div>
                    <div class="text-xs text-gray-500">observations</div>
                </div>
            </td>
            <td>
                ${statusBadge}
            </td>
            <td>
                <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                    <!-- View Button -->
                    <button type="button" onclick="openViewProtectedAreaSitesModal(${newSite.id})" 
                       class="protected-area-sites-action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="View Site">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    
                     <!-- Edit Button -->
                    <button type="button" onclick="openEditProtectedAreaSitesModal(${newSite.id})" 
                       class="protected-area-sites-action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="Edit Site">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    
                    <!-- Delete Button -->
                    <button type="button" onclick="openDeleteProtectedAreaSitesModal(${newSite.id})" 
                       class="protected-area-sites-action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="Delete Site">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </td>
        `;

        // Add row to the beginning of the table body (so new items appear at top)
        tableBody.insertBefore(newRow, tableBody.firstChild);

        // Add fade-in animation
        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(-10px)';
        newRow.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        // Trigger animation
        setTimeout(() => {
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        }, 10);
    }

    removeTableRow(siteId) {
        const row = document.querySelector(`tr[data-site-id="${siteId}"]`);
        if (row) {
            row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                row.remove();
                this.updateRecordCount();
            }, 300);
        }
    }

    updateRecordCount() {
        // Get current row count
        const currentRows = document.querySelectorAll('#protected-area-sites-table-body .protected-area-sites-row').length;
        
        console.log('Updating sites record count. Current rows:', currentRows);
        
        // Update the table header record count
        const tableHeader = document.querySelector('h2.text-lg.font-semibold.text-gray-900');
        if (tableHeader && tableHeader.textContent.includes('Protected Area Sites')) {
            const newCount = `Protected Area Sites (${currentRows} records)`;
            tableHeader.textContent = newCount;
            console.log('Updated table header to:', newCount);
        }
        
        // Update the stats card for total sites - look for the orange icon container
        const orangeIconContainers = document.querySelectorAll('.bg-orange-100');
        orangeIconContainers.forEach(container => {
            const parentCard = container.closest('.bg-white');
            if (parentCard) {
                const totalSitesElement = parentCard.querySelector('.text-2xl.font-bold.text-gray-900');
                if (totalSitesElement) {
                    const labelElement = parentCard.querySelector('.text-sm.text-gray-600');
                    if (labelElement && labelElement.textContent.includes('Total Sites')) {
                        totalSitesElement.textContent = currentRows.toString();
                        console.log('Updated Total Sites stat card to:', currentRows);
                    }
                }
            }
        });
        
        // Fallback: Look for any element with "Total Sites" text
        const allElements = document.querySelectorAll('*');
        allElements.forEach(element => {
            if (element.textContent.includes('Total Sites')) {
                const numberElement = element.querySelector('.text-2xl');
                if (numberElement && numberElement.textContent !== currentRows.toString()) {
                    numberElement.textContent = currentRows.toString();
                    console.log('Updated Total Sites via fallback to:', currentRows);
                }
            }
        });
    }

    clearFormErrors(form) {
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        form.querySelectorAll('.error-message').forEach(el => el.remove());
    }

    showFormErrors(form, errors) {
        this.clearFormErrors(form);
        
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('error');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                
                let errorMessage = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                
                // Make the error message more user-friendly
                if (errorMessage.includes('has already been taken')) {
                    errorMessage = 'This value already exists. Please try a different value.';
                }
                
                errorDiv.textContent = errorMessage;
                input.parentNode.appendChild(errorDiv);
            }
        });
    }
}

// Initialize and make globally available
let protectedAreaSitesModalSystem;

document.addEventListener('DOMContentLoaded', function() {
    protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
    // Attach to window for global access
    window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
});

// Global functions for onclick handlers
function openViewProtectedAreaSitesModal(siteId) {
    if (!protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
        window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
    }
    protectedAreaSitesModalSystem.open('view', { siteId });
}

function openEditProtectedAreaSitesModal(siteId) {
    if (!protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
        window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
    }
    protectedAreaSitesModalSystem.open('edit', { siteId });
}

function openAddProtectedAreaSitesModal() {
    if (!protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
        window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
    }
    protectedAreaSitesModalSystem.open('add', {});
}

function openDeleteProtectedAreaSitesModal(siteId) {
    if (!protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem = new ProtectedAreaSitesModalSystem();
        window.protectedAreaSitesModalSystem = protectedAreaSitesModalSystem;
    }
    protectedAreaSitesModalSystem.open('delete', { siteId });
}

function closeProtectedAreaSitesModal() {
    if (protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem.close();
    }
}

function confirmDeleteProtectedAreaSite(siteId) {
    if (protectedAreaSitesModalSystem) {
        protectedAreaSitesModalSystem.confirmDelete(siteId);
    }
}

// Attach class and functions to window object for global access
window.ProtectedAreaSitesModalSystem = ProtectedAreaSitesModalSystem;
window.openViewProtectedAreaSitesModal = openViewProtectedAreaSitesModal;
window.openEditProtectedAreaSitesModal = openEditProtectedAreaSitesModal;
window.openAddProtectedAreaSitesModal = openAddProtectedAreaSitesModal;
window.openDeleteProtectedAreaSitesModal = openDeleteProtectedAreaSitesModal;
window.closeProtectedAreaSitesModal = closeProtectedAreaSitesModal;
window.confirmDeleteProtectedAreaSite = confirmDeleteProtectedAreaSite;

// Export and dropdown init for Protected Area Sites page
function exportProtectedAreaSitesTable(format) {
    const form = document.getElementById('protected-area-sites-filter-form') || document.querySelector('form[method="GET"]');
    if (!form) return;
    const params = new URLSearchParams(new FormData(form));
    params.set('export', format);
    const searchInput = document.getElementById('protected-area-sites-search');
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

document.addEventListener('DOMContentLoaded', function() {
    // Standardized server-side search (debounced, loading state, clear button)
    initSearchBar({
        inputId: 'protected-area-sites-search',
        clearBtnId: 'protected-area-sites-search-clear',
        formSelector: '#protected-area-sites-filter-form',
        debounceMs: 400,
    });

    // Preserve search when user clicks Apply on the filter form
    const filterForm = document.getElementById('protected-area-sites-filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const searchInput = document.getElementById('protected-area-sites-search');
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
        btn.addEventListener('click', e => {
            e.stopPropagation();
            dropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', e => {
            if (!dropdown.contains(e.target) && e.target !== btn) dropdown.classList.remove('is-open');
        });
    }
});

window.exportTable = exportProtectedAreaSitesTable;

function clearSiteFilters() {
    const form = document.getElementById('protected-area-sites-filter-form');
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    const searchInput = document.getElementById('protected-area-sites-search');
    if (status) status.value = '';
    if (sort) sort.value = 'name';
    if (searchInput) searchInput.value = '';
    if (form) form.submit();
}
window.clearSiteFilters = clearSiteFilters;
