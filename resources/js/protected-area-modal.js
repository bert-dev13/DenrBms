/**
 * Protected Area Modal System - Consolidated
 * Handles all modal interactions for protected areas (view, edit, add, delete)
 * Unified system replacing multiple modal implementations
 */

class ProtectedAreaModalSystem {
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
        const existingOverlay = document.getElementById('protected-area-modal-overlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }
        
        this.overlay = document.createElement('div');
        this.overlay.className = 'modal-overlay';
        this.overlay.id = 'protected-area-modal-overlay';
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
        const { areaId } = data;
        
        try {
            // Fetch protected area data
            const response = await fetch(`/api/protected-areas/${areaId}`, {
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
                throw new Error(result.error || 'Failed to load protected area data');
            }

            return {
                type: 'view',
                area: result.protectedArea,
                areaId
            };

        } catch (error) {
            console.error('Error preparing view data:', error);
            this.showNotification('Error loading protected area data', 'error');
            return null;
        }
    }

    async prepareEditData(data) {
        const { areaId } = data;
        
        try {
            // Fetch protected area data
            const response = await fetch(`/api/protected-areas/${areaId}`, {
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
                throw new Error(result.error || 'Failed to load protected area data');
            }

            return {
                type: 'edit',
                area: result.protectedArea,
                areaId
            };

        } catch (error) {
            console.error('Error preparing edit data:', error);
            this.showNotification('Error loading protected area data', 'error');
            return null;
        }
    }

    async prepareAddData(data) {
        // For adding a new protected area, we don't need to fetch any data
        // Just return the type to create the add modal
        return {
            type: 'add'
        };
    }

    async prepareDeleteData(data) {
        const { areaId } = data;
        
        try {
            // Fetch protected area data for confirmation
            const response = await fetch(`/api/protected-areas/${areaId}`, {
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
                throw new Error(result.error || 'Failed to load protected area data');
            }

            return {
                type: 'delete',
                area: result.protectedArea,
                areaId
            };

        } catch (error) {
            console.error('Error preparing delete data:', error);
            this.showNotification('Error loading protected area data', 'error');
            return null;
        }
    }

    createModalContent(type, data) {
        // Clear existing modal content
        this.overlay.innerHTML = '';

        // Create modal container
        const modal = document.createElement('div');
        modal.className = 'modal-content';
        
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
        const { area } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Protected Area Details</h2>
                <button class="modal-close" onclick="closeProtectedAreaModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="view-details">
                    <div class="detail-row">
                        <label>Area Code:</label>
                        <span>${area.code || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <label>Name:</label>
                        <span>${area.name || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <label>Observations:</label>
                        <span>${area.species_observations_count || 0}</span>
                    </div>
                    <div class="detail-row">
                        <label>Status:</label>
                        <span>${area.species_observations_count > 0 ? 'Active' : 'No Data'}</span>
                    </div>
                    <div class="detail-row">
                        <label>Created:</label>
                        <span>${area.created_at ? new Date(area.created_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <label>Last Updated:</label>
                        <span>${area.updated_at ? new Date(area.updated_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProtectedAreaModal()">Close</button>
            </div>
        `;
    }

    createEditModalHTML(data) {
        const { area } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Edit Protected Area</h2>
                <button class="modal-close" onclick="closeProtectedAreaModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="protectedAreaModalSystem.submitEditForm(event, ${area.id})">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Area Code</label>
                            <input type="text" class="form-input" name="code" value="${area.code || ''}" required maxlength="255" placeholder="e.g., PPLS">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-input" name="name" value="${area.name || ''}" required maxlength="255" placeholder="e.g., Puerto Princesa Subterranean River National Park">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProtectedAreaModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        `;
    }

    createAddModalHTML(data) {
        // Generate a unique code based on timestamp
        const timestamp = Date.now();
        const uniqueCode = 'PA' + timestamp.toString().slice(-4);
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Add New Protected Area</h2>
                <button class="modal-close" onclick="closeProtectedAreaModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="protectedAreaModalSystem.submitAddForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Area Code</label>
                            <input type="text" class="form-input" name="code" value="${uniqueCode}" required maxlength="255" placeholder="e.g., PA1234" pattern="[A-Z0-9]+" title="Only uppercase letters and numbers">
                            <small>Unique code for the protected area (uppercase letters and numbers only)</small>
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-input" name="name" required maxlength="255" placeholder="e.g., Puerto Princesa Subterranean River National Park">
                            <small>Full name of the protected area</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProtectedAreaModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Protected Area</button>
                </div>
            </form>
        `;
    }

    createDeleteModalHTML(data) {
        const { area } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Delete Observation</h2>
                <button class="modal-close" onclick="closeProtectedAreaModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <p class="delete-question">Delete ${area.name}?</p>
                    <p class="delete-warning">Cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProtectedAreaModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteProtectedArea(${area.id})">Delete</button>
            </div>
        `;
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
        notification.className = `notification notification-${type}`;
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

    async submitEditForm(event, areaId) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Add method override for Laravel PUT support
        formData.append('_method', 'PUT');
        
        // Add loading state to submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';

        try {
            // Log form data for debugging
            console.log('Submitting edit form for area ID:', areaId);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            const response = await fetch(`/protected-areas/${areaId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));

            const result = await response.json();
            console.log('Response result:', result);

            if (result.success) {
                this.showNotification('Protected area updated successfully!', 'success');
                this.close();
                
                // Update the table row without page reload
                this.updateTableRow(areaId, result.area);
            } else {
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                    this.showNotification(result.error || 'Validation failed', 'error');
                } else {
                    this.showNotification(result.error || 'Failed to update protected area', 'error');
                }
            }
        } catch (error) {
            console.error('Error updating protected area:', error);
            this.showNotification('Error updating protected area', 'error');
        } finally {
            // Remove loading state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
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
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';
        
        // Clear any existing errors
        this.clearFormErrors(form);

        try {
            console.log('Submitting form...');
            console.log('CSRF Token:', formData.get('_token') ? 'Present' : 'Missing');
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            const response = await fetch('/protected-areas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));

            // Check if response is JSON first
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            let result;
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                const text = await response.text();
                console.error('Non-JSON response (first 500 chars):', text.substring(0, 500));
                result = { success: false, error: 'Server returned non-JSON response' };
            }

            if (result.success) {
                this.showNotification('Protected area created successfully!', 'success');
                this.close();
                
                // Add the new row to the table without page reload
                this.addTableRow(result.area);
                this.updateRecordCount();
            } else {
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                } else {
                    this.showNotification(result.error || 'Failed to add protected area', 'error');
                }
            }
        } catch (error) {
            console.error('Error adding protected area:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack
            });
            this.showNotification('Network error: ' + error.message, 'error');
        } finally {
            // Remove loading state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = originalText;
        }
    }

    async confirmDelete(areaId) {
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            
            const response = await fetch(`/protected-areas/${areaId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Protected area deleted successfully!', 'success');
                this.close();
                this.removeTableRow(areaId);
            } else {
                this.showNotification(result.error || 'Failed to delete protected area', 'error');
            }
        } catch (error) {
            console.error('Error deleting protected area:', error);
            this.showNotification('Error deleting protected area', 'error');
        }
    }

    updateTableRow(areaId, updatedArea) {
        const row = document.querySelector(`tr[data-area-id="${areaId}"]`);
        if (row) {
            // Update area code
            const codeCell = row.querySelector('td:first-child');
            if (codeCell) {
                codeCell.innerHTML = `<div class="font-medium text-gray-900">${updatedArea.code}</div>`;
            }
            
            // Update name
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                nameCell.innerHTML = `<div class="font-medium text-gray-900">${updatedArea.name}</div>`;
            }
            
            // Update status
            const statusCell = row.querySelector('td:nth-child(4)');
            if (statusCell) {
                const statusBadge = updatedArea.species_observations_count > 0 
                    ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                    : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Data</span>';
                statusCell.innerHTML = statusBadge;
            }
        }
    }

    addTableRow(newArea) {
        const tableBody = document.getElementById('protected-area-table-body');
        if (!tableBody) {
            console.error('Table body not found!');
            return;
        }

        // Create new row element
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50 protected-area-row';
        newRow.setAttribute('data-area-id', newArea.id);
        
        // Create status badge
        const observationCount = newArea.species_observations_count || 0;
        const statusBadge = observationCount > 0 
            ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
            : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Data</span>';

        // Set row HTML
        newRow.innerHTML = `
            <td>
                <div class="font-medium text-gray-900">${newArea.code || 'N/A'}</div>
            </td>
            <td>
                <div class="font-medium text-gray-900">${newArea.name || 'N/A'}</div>
            </td>
            <td>
                <div>
                    <div class="text-sm text-gray-900">${observationCount}</div>
                    <div class="text-xs text-gray-500">observations</div>
                </div>
            </td>
            <td>${statusBadge}</td>
            <td>
                <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                    <!-- View Button -->
                    <button type="button" onclick="openViewModal(${newArea.id})" 
                       class="action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="View Protected Area">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    
                     <!-- Edit Button -->
                    <button type="button" onclick="openEditModal(${newArea.id})" 
                       class="action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="Edit Protected Area">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    
                    <!-- Delete Button -->
                    <button type="button" onclick="openDeleteModal(${newArea.id})" 
                       class="action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                       title="Delete Protected Area">
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

    removeTableRow(areaId) {
        const row = document.querySelector(`tr[data-area-id="${areaId}"]`);
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
        const currentRows = document.querySelectorAll('#protected-area-table-body .protected-area-row').length;
        console.log('Updating record count. Current rows:', currentRows);
        
        // Update the record count in the header
        const recordElements = document.querySelectorAll('h2.text-lg.font-semibold.text-gray-900');
        recordElements.forEach(element => {
            if (element.textContent.includes('Protected Areas')) {
                const newCount = `Protected Areas (${currentRows} records)`;
                element.textContent = newCount;
                console.log('Updated header to:', newCount);
            }
        });
        
        // Update the Total Areas count in the stats grid
        const totalAreasElement = document.getElementById('total-areas-count');
        console.log('Total areas element found:', !!totalAreasElement);
        if (totalAreasElement) {
            console.log('Current total areas text:', totalAreasElement.textContent);
            totalAreasElement.textContent = currentRows;
            console.log('Updated total areas to:', currentRows);
        } else {
            console.error('Total areas element not found!');
        }
    }

    clearFormErrors(form) {
        // Remove error classes
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        
        // Remove error messages
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
                    errorMessage = 'This code already exists. Please try a different code.';
                }
                
                errorDiv.textContent = errorMessage;
                input.parentNode.appendChild(errorDiv);
            }
        });
    }
}

// Initialize and make globally available
let protectedAreaModalSystem;

document.addEventListener('DOMContentLoaded', function() {
    protectedAreaModalSystem = new ProtectedAreaModalSystem();
    // Attach to window for global access
    window.protectedAreaModalSystem = protectedAreaModalSystem;
});

// Global functions for onclick handlers
function openViewModal(areaId) {
    if (!protectedAreaModalSystem) {
        protectedAreaModalSystem = new ProtectedAreaModalSystem();
        window.protectedAreaModalSystem = protectedAreaModalSystem;
    }
    protectedAreaModalSystem.open('view', { areaId });
}

function openEditModal(areaId) {
    if (!protectedAreaModalSystem) {
        protectedAreaModalSystem = new ProtectedAreaModalSystem();
        window.protectedAreaModalSystem = protectedAreaModalSystem;
    }
    protectedAreaModalSystem.open('edit', { areaId });
}

function openAddModal() {
    if (!protectedAreaModalSystem) {
        protectedAreaModalSystem = new ProtectedAreaModalSystem();
        window.protectedAreaModalSystem = protectedAreaModalSystem;
    }
    protectedAreaModalSystem.open('add', {});
}

function openDeleteModal(areaId) {
    if (!protectedAreaModalSystem) {
        protectedAreaModalSystem = new ProtectedAreaModalSystem();
        window.protectedAreaModalSystem = protectedAreaModalSystem;
    }
    protectedAreaModalSystem.open('delete', { areaId });
}

function closeProtectedAreaModal() {
    if (protectedAreaModalSystem) {
        protectedAreaModalSystem.close();
    }
}

function confirmDeleteProtectedArea(areaId) {
    if (protectedAreaModalSystem) {
        protectedAreaModalSystem.confirmDelete(areaId);
    }
}

// Attach class and functions to window object for global access
window.ProtectedAreaModalSystem = ProtectedAreaModalSystem;
window.openViewModal = openViewModal;
window.openEditModal = openEditModal;
window.openAddModal = openAddModal;
window.openDeleteModal = openDeleteModal;
window.closeProtectedAreaModal = closeProtectedAreaModal;
window.confirmDeleteProtectedArea = confirmDeleteProtectedArea;
