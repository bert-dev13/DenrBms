/**
 * Protected Area Sites Modal System
 * Handles all modal interactions for protected area sites (view, edit, add, delete)
 * Scoped system to avoid conflicts with existing modals
 */

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
        this.overlay.className = 'protected-area-sites-modal-overlay';
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
        // Clear existing modal content
        this.overlay.innerHTML = '';

        // Create modal container
        const modal = document.createElement('div');
        modal.className = 'protected-area-sites-modal-content';
        
        // Add appropriate size class
        switch (type) {
            case 'view':
                modal.classList.add('large');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'edit':
                modal.classList.add('medium');
                modal.innerHTML = this.createEditModalHTML(data);
                break;
            case 'add':
                modal.classList.add('medium');
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
        
        return `
            <div class="protected-area-sites-modal-header">
                <h2 class="protected-area-sites-modal-title">Protected Area Site Details</h2>
                <button class="protected-area-sites-modal-close" onclick="closeProtectedAreaSitesModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="protected-area-sites-modal-body">
                <div class="protected-area-sites-view-details">
                    <div class="protected-area-sites-detail-row">
                        <label>Site Name:</label>
                        <span>${site.name || 'N/A'}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Station Code:</label>
                        <span>${site.station_code || 'N/A'}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Protected Area:</label>
                        <span>${site.protected_area ? site.protected_area.name : 'Not assigned'}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Observations:</label>
                        <span>${site.species_observations_count || 0}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Status:</label>
                        <span>${site.protected_area ? 'Active' : 'Unassigned'}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Created:</label>
                        <span>${site.created_at ? new Date(site.created_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                    <div class="protected-area-sites-detail-row">
                        <label>Last Updated:</label>
                        <span>${site.updated_at ? new Date(site.updated_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </div>
            </div>
            <div class="protected-area-sites-modal-footer">
                <button type="button" class="protected-area-sites-btn protected-area-sites-btn-secondary" onclick="closeProtectedAreaSitesModal()">Close</button>
            </div>
        `;
    }

    createEditModalHTML(data) {
        const { site } = data;
        
        return `
            <div class="protected-area-sites-modal-header">
                <h2 class="protected-area-sites-modal-title">Edit Protected Area Site</h2>
                <button class="protected-area-sites-modal-close" onclick="closeProtectedAreaSitesModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="protected-area-sites-modal-form" onsubmit="protectedAreaSitesModalSystem.submitEditForm(event, ${site.id})">
                <div class="protected-area-sites-modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label required">Site Name</label>
                            <input type="text" class="protected-area-sites-form-input" name="name" value="${site.name || ''}" required maxlength="255" placeholder="e.g., Site A">
                        </div>
                    </div>
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label">Station Code</label>
                            <input type="text" class="protected-area-sites-form-input" name="station_code" value="${site.station_code || ''}" maxlength="255" placeholder="e.g., ST001">
                        </div>
                    </div>
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label">Protected Area</label>
                            <select class="protected-area-sites-form-select" name="protected_area_id">
                                <option value="">Select Protected Area (Optional)</option>
                                ${this.generateProtectedAreaOptions(site.protected_area_id)}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="protected-area-sites-modal-footer">
                    <button type="button" class="protected-area-sites-btn protected-area-sites-btn-secondary" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                    <button type="submit" class="protected-area-sites-btn protected-area-sites-btn-primary">Save Changes</button>
                </div>
            </form>
        `;
    }

    createAddModalHTML(data) {
        return `
            <div class="protected-area-sites-modal-header">
                <h2 class="protected-area-sites-modal-title">Add New Protected Area Site</h2>
                <button class="protected-area-sites-modal-close" onclick="closeProtectedAreaSitesModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="protected-area-sites-modal-form" onsubmit="protectedAreaSitesModalSystem.submitAddForm(event)">
                <div class="protected-area-sites-modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label required">Site Name</label>
                            <input type="text" class="protected-area-sites-form-input" name="name" required maxlength="255" placeholder="e.g., Site A">
                        </div>
                    </div>
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label">Station Code</label>
                            <input type="text" class="protected-area-sites-form-input" name="station_code" maxlength="255" placeholder="e.g., ST001">
                        </div>
                    </div>
                    
                    <div class="protected-area-sites-form-row single">
                        <div class="protected-area-sites-form-group">
                            <label class="protected-area-sites-form-label">Protected Area</label>
                            <select class="protected-area-sites-form-select" name="protected_area_id">
                                <option value="">Select Protected Area (Optional)</option>
                                ${this.generateProtectedAreaOptions()}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="protected-area-sites-modal-footer">
                    <button type="button" class="protected-area-sites-btn protected-area-sites-btn-secondary" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                    <button type="submit" class="protected-area-sites-btn protected-area-sites-btn-primary">Add Site</button>
                </div>
            </form>
        `;
    }

    createDeleteModalHTML(data) {
        const { site } = data;
        
        return `
            <div class="protected-area-sites-modal-header">
                <h2 class="protected-area-sites-modal-title">Delete Protected Area Site</h2>
                <button class="protected-area-sites-modal-close" onclick="closeProtectedAreaSitesModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="protected-area-sites-modal-body">
                <div class="protected-area-sites-delete-confirmation">
                    <p class="protected-area-sites-delete-question">Delete ${site.name}?</p>
                    <p class="protected-area-sites-delete-warning">Cannot be undone</p>
                </div>
            </div>
            <div class="protected-area-sites-modal-footer">
                <button type="button" class="protected-area-sites-btn protected-area-sites-btn-secondary" onclick="closeProtectedAreaSitesModal()">Cancel</button>
                <button type="button" class="protected-area-sites-btn protected-area-sites-btn-danger" onclick="confirmDeleteProtectedAreaSite(${site.id})">Delete</button>
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
        
        // Add loading state to submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.classList.add('protected-area-sites-loading');
        submitBtn.textContent = 'Saving...';

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
            // Remove loading state
            submitBtn.disabled = false;
            submitBtn.classList.remove('protected-area-sites-loading');
            submitBtn.textContent = originalText;
        }
    }

    async submitAddForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Add loading state to submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.classList.add('protected-area-sites-loading');
        submitBtn.textContent = 'Saving...';
        
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
            // Remove loading state
            submitBtn.disabled = false;
            submitBtn.classList.remove('protected-area-sites-loading');
            submitBtn.textContent = originalText;
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
        // Remove error classes
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        
        // Remove error messages
        form.querySelectorAll('.protected-area-sites-error-message').forEach(el => el.remove());
    }

    showFormErrors(form, errors) {
        this.clearFormErrors(form);
        
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('error');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'protected-area-sites-error-message';
                
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
