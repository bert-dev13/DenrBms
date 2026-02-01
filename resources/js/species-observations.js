/**
 * Species Observations Modal System
 * Handles all modal functionality for species observations page
 */

class SpeciesObservationsModalSystem {
    constructor() {
        this.activeModal = null;
        this.modalOverlay = null;
        this.modalContent = null;
        this.currentData = null;
        this.isOpening = false;
        this.isClosing = false;
        
        this.init();
    }

    init() {
        this.createOverlay();
        this.setupEventListeners();
    }

    createOverlay() {
        // Create overlay once and reuse
        this.modalOverlay = document.createElement('div');
        this.modalOverlay.className = 'modal-overlay';
        this.modalOverlay.setAttribute('role', 'dialog');
        this.modalOverlay.setAttribute('aria-modal', 'true');
        this.modalOverlay.setAttribute('aria-hidden', 'true');
        
        document.body.appendChild(this.modalOverlay);
    }

    setupEventListeners() {
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal && !this.isClosing) {
                this.close();
            }
        });

        // Close on backdrop click
        this.modalOverlay.addEventListener('click', (e) => {
            if (e.target === this.modalOverlay && !this.isClosing) {
                this.close();
            }
        });
    }

    async open(type, data = {}) {
        console.log('SpeciesObservationsModalSystem.open called with:', { type, data });
        
        // Prevent multiple simultaneous opens
        if (this.isOpening || this.isClosing) {
            console.log('Modal already opening or closing, returning false');
            return false;
        }

        this.isOpening = true;

        try {
            const preparedData = await this.prepareModalData(type, data);
            
            if (!preparedData) {
                console.log('Failed to prepare data, returning false');
                this.isOpening = false;
                return false;
            }

            console.log('Prepared data:', preparedData);

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
            case 'delete':
                return await this.prepareDeleteData(data);
            default:
                console.error('Unknown modal type:', type);
                return null;
        }
    }

    async prepareViewData(data) {
        const { observationId, tableName } = data;
        
        try {
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!result.success) {
                this.showNotification(result.message || 'Failed to load observation', 'error');
                return null;
            }

            // Get dropdown data
            const dropdownData = await this.getDropdownData();

            return {
                type: 'view',
                observation: result.observation,
                ...dropdownData,
                observationId,
                tableName,
                originalTableName: result.observation.table_name
            };

        } catch (error) {
            console.error('Error preparing view data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    async prepareEditData(data) {
        const { observationId, tableName } = data;
        
        try {
            const response = await fetch(`/api/species-observations/edit-data/${observationId}?table_name=${tableName}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!result.success) {
                this.showNotification(result.message || 'Failed to load observation', 'error');
                return null;
            }

            // Get dropdown data
            const dropdownData = await this.getDropdownData();

            return {
                type: 'edit',
                observation: result.observation,
                ...dropdownData,
                observationId,
                tableName,
                originalTableName: result.observation.table_name
            };

        } catch (error) {
            console.error('Error preparing edit data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }


    async prepareDeleteData(data) {
        const { observationId, tableName } = data;
        
        try {
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!result.success) {
                this.showNotification(result.message || 'Failed to load observation', 'error');
                return null;
            }

            return {
                type: 'delete',
                observation: result.observation,
                observationId,
                tableName
            };

        } catch (error) {
            console.error('Error preparing delete data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    async getDropdownData() {
        try {
            // Get protected areas
            const protectedAreasResponse = await fetch('/api/protected-areas', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const protectedAreasData = await protectedAreasResponse.json();

            // Get bio groups
            const bioGroupsResponse = await fetch('/api/bio-groups', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const bioGroupsData = await bioGroupsResponse.json();

            // Get years and semesters from dropdowns
            const yearSelect = document.getElementById('year');
            const semesterSelect = document.getElementById('semester');
            
            const years = [];
            const semesters = {};

            if (yearSelect) {
                Array.from(yearSelect.options).forEach(option => {
                    if (option.value) {
                        years.push(option.value);
                    }
                });
            }

            if (semesterSelect) {
                Array.from(semesterSelect.options).forEach(option => {
                    if (option.value) {
                        semesters[option.value] = option.textContent;
                    }
                });
            }

            return {
                protectedAreas: protectedAreasData.success ? protectedAreasData.protectedAreas : [],
                bioGroups: bioGroupsData.success ? bioGroupsData.bioGroups : [],
                years,
                semesters
            };

        } catch (error) {
            console.error('Error getting dropdown data:', error);
            return {
                protectedAreas: [],
                bioGroups: [],
                years: [],
                semesters: {}
            };
        }
    }

    createModalContent(type, data) {
        const modal = document.createElement('div');
        modal.className = 'modal-content';

        // Set modal size based on type
        switch (type) {
            case 'view':
                modal.classList.add('large');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'edit':
                modal.classList.add('xlarge');
                modal.innerHTML = this.createEditModalHTML(data);
                break;
            case 'delete':
                modal.classList.add('small');
                modal.innerHTML = this.createDeleteModalHTML(data);
                break;
        }

        // Clear previous content and add new
        this.modalOverlay.innerHTML = '';
        this.modalOverlay.appendChild(modal);
        this.modalContent = modal;
        this.currentData = data;

        // Setup form event listeners
        this.setupFormListeners();
    }

    setupFormListeners() {
        // Setup dynamic dependent dropdowns
        const protectedAreaSelect = document.getElementById('protected_area_id');
        const siteNameSelect = document.getElementById('site_name');

        if (protectedAreaSelect && siteNameSelect) {
            protectedAreaSelect.addEventListener('change', (e) => {
                this.handleProtectedAreaChange(e.target);
            });
        }
    }

    async handleProtectedAreaChange(select) {
        const siteNameSelect = document.getElementById('site_name');
        if (!siteNameSelect) return;

        const protectedAreaId = select.value;
        const selectedOption = select.options[select.selectedIndex];
        const protectedAreaCode = selectedOption.getAttribute('data-code');
        
        if (protectedAreaId) {
            try {
                const response = await fetch(`/protected-areas/${protectedAreaId}/site-names`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Clear current options
                    siteNameSelect.innerHTML = '<option value="">Select Site</option>';
                    
                    // Add new options with validation
                    data.siteNames.forEach(site => {
                        // Client-side validation: Filter sites based on protected area
                        let isValidSite = true;
                        
                        if (protectedAreaCode === 'PPLS') {
                            // PPLS can have Toyota sites
                            if (site.name.includes('MPL')) {
                                isValidSite = false;
                            }
                        } else if (protectedAreaCode === 'MPL') {
                            // MPL can only have MPL sites
                            if (!site.name.includes('MPL')) {
                                isValidSite = false;
                            }
                        } else {
                            // Other protected areas should not have PPLS or MPL specific sites
                            if (site.name.includes('Toyota') || site.name.includes('MPL') || 
                                site.name.includes('San Roque') || site.name.includes('Manga') || 
                                site.name.includes('Quibal')) {
                                isValidSite = false;
                            }
                        }
                        
                        if (isValidSite) {
                            const option = document.createElement('option');
                            option.value = site.station_code;
                            option.textContent = site.name;
                            siteNameSelect.appendChild(option);
                        }
                    });
                    
                    siteNameSelect.disabled = false;
                    
                    // If no valid sites available
                    if (siteNameSelect.options.length <= 1) {
                        siteNameSelect.innerHTML = '<option value="">No valid sites available</option>';
                        siteNameSelect.disabled = true;
                    }
                }
            } catch (error) {
                console.error('Error fetching site names:', error);
                siteNameSelect.innerHTML = '<option value="">Error loading sites</option>';
                siteNameSelect.disabled = true;
            }
        } else {
            // Clear site names when no protected area is selected
            siteNameSelect.innerHTML = '<option value="">Select Protected Area First</option>';
            siteNameSelect.disabled = true;
        }
    }

    showModal() {
        this.modalOverlay.classList.add('active');
        this.modalOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        
        this.setFocusTrap();
    }

    close() {
        if (this.isClosing) return;
        
        this.isClosing = true;
        
        this.modalOverlay.classList.remove('active');
        this.modalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            this.modalOverlay.innerHTML = '';
            this.modalContent = null;
            this.currentData = null;
            this.isClosing = false;
        }, 150);
    }

    setFocusTrap() {
        if (!this.modalContent) return;

        const focusableElements = this.modalContent.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    createViewModalHTML(data) {
        const { observation } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Observation Details</h2>
                <button class="modal-close" onclick="window.speciesObservationsModal.close()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="view-grid">
                    <div class="view-item">
                        <div class="view-label">Common Name</div>
                        <div class="view-value">${observation.common_name || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Scientific Name</div>
                        <div class="view-value">${observation.scientific_name || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Protected Area</div>
                        <div class="view-value">${observation.protected_area?.name || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Station Code</div>
                        <div class="view-value">${observation.station_code || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Bio Group</div>
                        <div class="view-value">${observation.bio_group || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Year</div>
                        <div class="view-value">${observation.year || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Semester</div>
                        <div class="view-value">${observation.semester || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Table</div>
                        <div class="view-value">${observation.table_name || 'N/A'}</div>
                    </div>
                    <div class="view-item full-width">
                        <div class="view-label">Remarks</div>
                        <div class="view-value">${observation.remarks || 'N/A'}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.speciesObservationsModal.close()">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.speciesObservationsModal.editFromView()">Edit</button>
            </div>
        `;
    }

    createEditModalHTML(data) {
        const { observation, protectedAreas, bioGroups, years, semesters } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Edit Observation</h2>
                <button class="modal-close" onclick="window.speciesObservationsModal.close()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="editObservationForm" onsubmit="event.preventDefault(); window.speciesObservationsModal.submitEditForm();">
                    <div class="modal-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Common Name</label>
                                <input type="text" name="common_name" value="${observation.common_name || ''}" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Scientific Name</label>
                                <input type="text" name="scientific_name" value="${observation.scientific_name || ''}" required class="form-input">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Protected Area</label>
                                <select name="protected_area_id" required class="form-select" onchange="window.speciesObservationsModal.handleProtectedAreaChange(this)">
                                    <option value="">Select Protected Area</option>
                                    ${protectedAreas.map(area => 
                                        `<option value="${area.id}" ${observation.protected_area?.id == area.id ? 'selected' : ''}>${area.name}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Station Code</label>
                                <select name="station_code" required class="form-select">
                                    <option value="">Select Protected Area First</option>
                                    ${observation.station_code ? `<option value="${observation.station_code}" selected>${observation.station_code}</option>` : ''}
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Bio Group</label>
                                <select name="bio_group" required class="form-select">
                                    <option value="">Select Bio Group</option>
                                    ${bioGroups.map(group => 
                                        `<option value="${group}" ${observation.bio_group === group ? 'selected' : ''}>${group}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Year</label>
                                <select name="year" required class="form-select">
                                    <option value="">Select Year</option>
                                    ${years.map(year => 
                                        `<option value="${year}" ${observation.year == year ? 'selected' : ''}>${year}</option>`
                                    ).join('')}
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Semester</label>
                                <select name="semester" required class="form-select">
                                    <option value="">Select Semester</option>
                                    ${Object.entries(semesters).map(([value, label]) => 
                                        `<option value="${value}" ${observation.semester === value ? 'selected' : ''}>${label}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-input" rows="3">${observation.remarks || ''}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.speciesObservationsModal.close()">Cancel</button>
                <button type="submit" form="editObservationForm" class="btn btn-primary">Update</button>
            </div>
        `;
    }


    createDeleteModalHTML(data) {
        const { observation } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Delete Observation</h2>
                <button class="modal-close" onclick="window.speciesObservationsModal.close()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <p class="confirmation-message">Are you sure you want to delete this observation?</p>
                    <div class="observation-summary">
                        <div class="summary-item">
                            <strong>Common Name:</strong> ${observation.common_name || 'N/A'}
                        </div>
                        <div class="summary-item">
                            <strong>Protected Area:</strong> ${observation.protected_area?.name || 'N/A'}
                        </div>
                        <div class="summary-item">
                            <strong>Station Code:</strong> ${observation.station_code || 'N/A'}
                        </div>
                        <div class="summary-item">
                            <strong>Table:</strong> ${observation.table_name || 'N/A'}
                        </div>
                    </div>
                    <p class="warning-text">⚠️ This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.speciesObservationsModal.close()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="window.speciesObservationsModal.confirmDelete()">Delete</button>
            </div>
        `;
    }

    editFromView() {
        if (this.currentData && this.currentData.observationId) {
            this.close();
            setTimeout(() => {
                this.open('edit', {
                    observationId: this.currentData.observationId,
                    tableName: this.currentData.tableName
                });
            }, 150);
        }
    }

    async submitEditForm() {
        const form = document.getElementById('editObservationForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Updating...';

        try {
            const response = await fetch(`/species-observations/${this.currentData.observationId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table_name: this.currentData.tableName,
                    ...Object.fromEntries(formData)
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation updated successfully!', 'success');
                this.close();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Failed to update observation', 'error');
            }
        } catch (error) {
            console.error('Error updating observation:', error);
            this.showNotification('Error updating observation', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Update';
        }
    }

    editFromView() {
        if (this.currentData && this.currentData.observationId) {
            this.close();
            setTimeout(() => {
                this.open('edit', {
                    observationId: this.currentData.observationId,
                    tableName: this.currentData.tableName
                });
            }, 150);
        }
    }

    async confirmDelete() {
        try {
            const response = await fetch(`/species-observations/${this.currentData.observationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table_name: this.currentData.tableName
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation deleted successfully!', 'success');
                this.close();
                
                // Remove the row from the table instantly
                this.removeObservationRow(this.currentData.observationId);
            } else {
                this.showNotification(result.message || result.error || 'Failed to delete observation', 'error');
            }
        } catch (error) {
            this.showNotification('Error deleting observation', 'error');
        }
    }

    removeObservationRow(observationId) {
        // Find and remove the row from the table using a more reliable method
        const rows = document.querySelectorAll('.observation-row');
        let rowRemoved = false;
        
        rows.forEach(row => {
            // Look for the delete button with the specific observation ID
            const deleteBtn = row.querySelector(`button[onclick*="openDeleteModal(${observationId}"]`);
            if (deleteBtn) {
                row.remove();
                rowRemoved = true;
                
                // Update the count if it exists
                const countElement = document.querySelector('h2');
                if (countElement) {
                    const countText = countElement.textContent;
                    const countMatch = countText.match(/\((\d+)\s+records\)/);
                    if (countMatch) {
                        const currentCount = parseInt(countMatch[1]);
                        const newCount = Math.max(0, currentCount - 1);
                        countElement.textContent = countText.replace(/\((\d+)\s+records\)/, `(${newCount} records)`);
                    }
                }
            }
        });
        
        if (!rowRemoved) {
            console.warn(`Could not find row for observation ID: ${observationId}`);
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            transform: translateX(0);
            transition: transform 0.2s ease;
        `;

        document.body.appendChild(notification);

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 200);
        }, 3000);
    }
}

// Initialize the species observations modal system
let speciesObservationsModal;

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSpeciesObservationsModal);
} else {
    initializeSpeciesObservationsModal();
}

function initializeSpeciesObservationsModal() {
    try {
        speciesObservationsModal = new SpeciesObservationsModalSystem();
        
        // Make sure it's available globally
        window.speciesObservationsModal = speciesObservationsModal;
        
        // Global functions for onclick handlers
        window.openViewModal = (observationId, tableName) => {
            speciesObservationsModal.open('view', { observationId, tableName });
        };

        window.openEditModal = (observationId, tableName) => {
            speciesObservationsModal.open('edit', { observationId, tableName });
        };

        window.openDeleteModal = (observationId, tableName) => {
            speciesObservationsModal.open('delete', { observationId, tableName });
        };

        window.closeModal = () => {
            speciesObservationsModal.close();
        };
        
        console.log('SpeciesObservationsModalSystem initialized successfully');
    } catch (error) {
        console.error('Error initializing SpeciesObservationsModalSystem:', error);
    }
}
