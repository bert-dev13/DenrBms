/**
 * New Modal System - Instant, Stable, and Reliable
 * 
 * Features:
 * - Instant display with no delays
 * - Fixed stable dimensions
 * - Data prepared before showing
 * - Backdrop and modal render together
 * - Simple predictable animations
 * - Consistent behavior across all modal types
 * - No race conditions or state conflicts
 * - Responsive and clean layout
 */

console.log('Species Observation Modal Script Loading...');

class ModalSystem {
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

        // Prevent body scroll when modal is open
        this.modalOverlay.addEventListener('wheel', (e) => {
            if (e.target === this.modalOverlay) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    async close() {
        if (this.isClosing || !this.activeModal) {
            return;
        }

        this.isClosing = true;

        // Hide modal immediately
        this.modalOverlay.classList.remove('active');
        
        // Set accessibility attributes
        this.modalOverlay.setAttribute('aria-hidden', 'true');

        // Restore body scroll
        document.body.style.overflow = '';

        // Clear content after animation
        setTimeout(() => {
            this.modalOverlay.innerHTML = '';
            this.modalContent = null;
            this.currentData = null;
            this.activeModal = null;
            this.isClosing = false;
        }, 100);
    }

    async prepareModalData(type, data) {
        console.log('prepareModalData called with type:', type, 'data:', data);
        switch (type) {
            case 'view':
                return await this.prepareViewData(data);
            case 'add':
                console.log('Handling add case');
                return await this.prepareAddData(data);
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
            // Fetch observation data
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
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
                throw new Error(result.error || 'Failed to load observation data');
            }

            return {
                type: 'view',
                observation: result.observation,
                observationId,
                tableName
            };

        } catch (error) {
            console.error('Error preparing view data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    getSelectOptions(selectElement) {
        if (!selectElement) return [];
        
        return Array.from(selectElement.options).map(option => ({
            id: option.value,
            name: option.textContent,
            code: option.getAttribute('data-code') || null
        })).filter(option => option.id);
    }

    async prepareAddData(data) {
        console.log('prepareAddData called');
        try {
            // Get filter options from existing page elements
            const protectedAreasSelect = document.querySelector('#protected_area_id');
            const bioGroupSelect = document.querySelector('#bio_group');
            const yearSelect = document.querySelector('#patrol_year');
            const semesterSelect = document.querySelector('#patrol_semester');
            
            let protectedAreas = [];
            let bioGroups = {};
            let years = [];
            let semesters = {};

            // Extract data from existing selects
            if (protectedAreasSelect) {
                Array.from(protectedAreasSelect.options).forEach(option => {
                    if (option.value) {
                        protectedAreas.push({
                            id: option.value,
                            name: option.textContent,
                            code: option.getAttribute('data-code') || ''
                        });
                    }
                });
            }

            if (bioGroupSelect) {
                Array.from(bioGroupSelect.options).forEach(option => {
                    if (option.value) {
                        bioGroups[option.value] = option.textContent;
                    }
                });
            }

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
                type: 'add',
                protectedAreas,
                bioGroups,
                years,
                semesters
            };
        } catch (error) {
            console.error('Error preparing add data:', error);
            this.showNotification('Error loading form data', 'error');
            return null;
        }
    }

    async loadSiteNamesForAdd(protectedAreaId) {
        const siteNameSelect = document.querySelector('#site_name_id');
        
        if (!protectedAreaId) {
            siteNameSelect.innerHTML = '<option value="">No specific site</option>';
            siteNameSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch(`/api/species-observations/site-names/${protectedAreaId}`, {
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
                throw new Error(result.error || 'Failed to load site names');
            }

            const siteNames = result.site_names || [];
            
            if (siteNames.length > 0) {
                const options = '<option value="">Select Site</option>' + 
                    siteNames.map(site => `<option value="${site.id}">${site.name}</option>`).join('');
                siteNameSelect.innerHTML = options;
                siteNameSelect.disabled = false;
            } else {
                siteNameSelect.innerHTML = '<option value="">No specific site</option>';
                siteNameSelect.disabled = true;
            }
        } catch (error) {
            console.error('Error loading site names:', error);
            siteNameSelect.innerHTML = '<option value="">Error loading sites</option>';
            siteNameSelect.disabled = true;
        }
    }

    async prepareEditData(data) {
        const { observationId, tableName } = data;
        
        try {
            // Fetch observation data
            const response = await fetch(`/api/species-observations/data/${observationId}?table_name=${tableName}`, {
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
                throw new Error(result.error || 'Failed to load observation data');
            }

            // Get filter options from existing page elements
            const protectedAreasSelect = document.querySelector('#protected_area_id');
            const bioGroupSelect = document.querySelector('#bio_group');
            const yearSelect = document.querySelector('#patrol_year');
            const semesterSelect = document.querySelector('#patrol_semester');
            
            let protectedAreas = [];
            let bioGroups = {};
            let years = [];
            let semesters = {};

            // Extract data from existing selects
            if (protectedAreasSelect) {
                Array.from(protectedAreasSelect.options).forEach(option => {
                    if (option.value) {
                        protectedAreas.push({
                            id: option.value,
                            name: option.textContent,
                            code: option.getAttribute('data-code') || ''
                        });
                    }
                });
            }

            if (bioGroupSelect) {
                Array.from(bioGroupSelect.options).forEach(option => {
                    if (option.value) {
                        bioGroups[option.value] = option.textContent;
                    }
                });
            }

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
                type: 'edit',
                observation: result.observation,
                protectedAreas,
                bioGroups,
                years,
                semesters,
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

    async open(type, data = {}) {
        console.log('ModalSystem.open called with type:', type, 'data:', data);
        // Prevent multiple simultaneous opens
        if (this.isOpening || this.isClosing) {
            console.log('Modal already opening or closing, returning false');
            return false;
        }

        this.isOpening = true;

        try {
            // Prepare all data before showing modal
            const preparedData = await this.prepareModalData(type, data);
            
            if (!preparedData) {
                console.log('Failed to prepare modal data, cancelling open');
                this.isOpening = false;
                return false;
            }

            // Store current data
            this.currentData = preparedData;
            this.activeModal = type;

            // Create modal content with prepared data
            this.modalContent = this.createModalContent(type, preparedData);
            
            // Clear and set new content
            this.modalOverlay.innerHTML = '';
            this.modalOverlay.appendChild(this.modalContent);

            // Set accessibility attributes
            this.modalOverlay.setAttribute('aria-hidden', 'false');

            // Prevent body scroll
            document.body.style.overflow = 'hidden';

            // Show modal instantly (no delays)
            requestAnimationFrame(() => {
                this.modalOverlay.classList.add('active');
                
                // Initialize site name dropdown for edit modal
                if (type === 'edit' && preparedData.observation) {
                    this.initializeEditSiteName(preparedData.observation);
                }
                
                // Focus first focusable element
                this.setFocusTrap();
            });

            this.isOpening = false;
            return true;

        } catch (error) {
            console.error('Error opening modal:', error);
            this.isOpening = false;
            return false;
        }
    }

    async handleAddProtectedAreaChange(selectElement) {
        const protectedAreaId = selectElement.value;
        const siteNameSelect = document.getElementById('add_modal_site_name');
        
        if (!protectedAreaId) {
            siteNameSelect.innerHTML = '<option value="">No specific site</option>';
            siteNameSelect.disabled = true;
            this.updateSaveInfo(null);
            return;
        }

        try {
            const response = await fetch(`/api/species-observations/site-names/${protectedAreaId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If we get HTML instead of JSON, there might be an authentication issue
                const text = await response.text();
                console.error('Received non-JSON response:', text.substring(0, 200));
                throw new Error('Received HTML response instead of JSON. Please check if you are logged in.');
            }

            const result = await response.json();
            console.log('API response:', result);
            
            // Handle different response formats
            let siteNames = [];
            if (result.success && result.site_names) {
                siteNames = result.site_names;
            } else if (Array.isArray(result)) {
                // Direct array response (backward compatibility)
                siteNames = result;
            } else {
                throw new Error(result.error || 'Failed to load site names');
            }
            
            // Get selected protected area code for validation
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const protectedAreaCode = selectedOption.getAttribute('data-code');
            
            if (siteNames.length > 0) {
                let optionsHTML = '<option value="">No specific site</option>';
                
                siteNames.forEach(siteName => {
                    // Client-side validation: Filter sites based on protected area
                    let isValidSite = true;
                    let validationMessage = '';
                    
                    if (protectedAreaCode === 'PPLS') {
                        // PPLS can have Toyota sites
                        if (siteName.name.includes('MPL')) {
                            isValidSite = false;
                            validationMessage = 'MPL sites cannot be assigned to PPLS';
                        }
                    } else if (protectedAreaCode === 'MPL') {
                        // MPL can only have MPL sites
                        if (!siteName.name.includes('MPL')) {
                            isValidSite = false;
                            validationMessage = 'Only MPL sites can be assigned to MPL';
                        }
                    } else {
                        // Other protected areas should not have PPLS or MPL specific sites
                        if (siteName.name.includes('Toyota') || siteName.name.includes('MPL') || 
                            siteName.name.includes('San Roque') || siteName.name.includes('Manga') || 
                            siteName.name.includes('Quibal')) {
                            isValidSite = false;
                            validationMessage = 'This site can only be assigned to its designated protected area';
                        }
                    }
                    
                    if (isValidSite) {
                        optionsHTML += `<option value="${siteName.id}">${siteName.name}</option>`;
                    }
                });
                
                siteNameSelect.innerHTML = optionsHTML;
                siteNameSelect.disabled = false;
                
                if (siteNames.filter(s => {
                    let isValid = true;
                    if (protectedAreaCode === 'PPLS' && s.name.includes('MPL')) isValid = false;
                    if (protectedAreaCode === 'MPL' && !s.name.includes('MPL')) isValid = false;
                    if (protectedAreaCode !== 'PPLS' && protectedAreaCode !== 'MPL' && 
                        (s.name.includes('Toyota') || s.name.includes('MPL') || 
                         s.name.includes('San Roque') || s.name.includes('Manga') || 
                         s.name.includes('Quibal'))) isValid = false;
                    return isValid;
                }).length === 0) {
                    // No valid sites for this protected area
                    siteNameSelect.innerHTML = '<option value="">No valid sites available</option>';
                    siteNameSelect.disabled = true;
                }
            } else {
                siteNameSelect.innerHTML = '<option value="">No sites available</option>';
                siteNameSelect.disabled = true;
            }
            
            this.updateSaveInfo(null);
            
        } catch (error) {
            console.error('Error loading site names:', error);
            siteNameSelect.innerHTML = '<option value="">Error loading sites</option>';
            siteNameSelect.disabled = true;
            this.updateSaveInfo(null);
        }
    }

    updateSaveInfo(selectElement) {
        const protectedAreaSelect = selectElement?.name === 'protected_area_id' ? selectElement : document.querySelector('select[name="protected_area_id"]');
        const siteNameSelect = document.getElementById('add_modal_site_name');
        const saveInfoBanner = document.getElementById('save-info-banner');
        const saveLocationText = document.getElementById('save-location-text');
        const stationCodeInput = document.querySelector('input[name="station_code"]');
        
        if (!protectedAreaSelect || !protectedAreaSelect.value) {
            saveInfoBanner.style.display = 'none';
            if (stationCodeInput) {
                stationCodeInput.value = '';
                stationCodeInput.placeholder = 'Auto-generated';
            }
            return;
        }
        
        const selectedOption = protectedAreaSelect.options[protectedAreaSelect.selectedIndex];
        const protectedAreaName = selectedOption.textContent;
        const protectedAreaCode = selectedOption.getAttribute('data-code');
        const selectedSiteId = siteNameSelect?.value;
        const selectedSiteOption = siteNameSelect?.options[siteNameSelect?.selectedIndex];
        const siteName = selectedSiteId && selectedSiteOption ? selectedSiteOption.textContent : '';
        
        let saveLocation = '';
        let stationCode = '';
        
        if (selectedSiteId && siteName && siteName !== 'Select Site') {
            // Protected Area + Site
            saveLocation = `<strong>Site Level:</strong> ${siteName} within ${protectedAreaName}`;
            stationCode = this.getStationCodeForSite(siteName, protectedAreaCode);
        } else {
            // Protected Area Only
            saveLocation = `<strong>Protected Area Level:</strong> ${protectedAreaName}`;
            stationCode = `${protectedAreaCode}-MAIN`;
        }
        
        saveLocationText.innerHTML = saveLocation;
        saveInfoBanner.style.display = 'block';
        
        if (stationCodeInput) {
            stationCodeInput.value = stationCode;
            stationCodeInput.placeholder = stationCode;
        }
    }

    getStationCodeForSite(siteName, protectedAreaCode) {
        // Use the same mappings as in the backend
        const siteMappings = {
            'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan': 'TOYOTA-S1',
            'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan': 'SANROQUE-S1',
            'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan': 'MANGA-S1',
            'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan': 'QUIBAL-S1',
            'MPL SITE 1 – San Mariano, Lal-lo, Cagayan': 'R2-MPL-BMS-T - S1',
            'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan': 'R2-MPL-BMS-T - S2',
            'SITE 1 AMULUNG': 'AM100',
        };
        
        // For sites not in the mapping, try to extract from the site name or use the station_code from database
        if (siteMappings[siteName]) {
            return siteMappings[siteName];
        }
        
        // Fallback: try to extract station code from site name
        const stationCodeMatch = siteName.match(/([A-Z]+\d+)/);
        if (stationCodeMatch) {
            return stationCodeMatch[1];
        }
        
        return `${protectedAreaCode}-SITE`;
    }

    // Handle Protected Area change for edit modal
    async handleProtectedAreaChange(selectElement) {
        const protectedAreaId = selectElement.value;
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!protectedAreaId) {
            siteNameSelect.innerHTML = '<option value="">No specific site</option>';
            siteNameSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch(`/api/species-observations/site-names/${protectedAreaId}`, {
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
            
            let siteNames = [];
            if (result.success && result.site_names) {
                siteNames = result.site_names;
            } else if (Array.isArray(result)) {
                siteNames = result;
            } else {
                throw new Error(result.error || 'Failed to load site names');
            }
            
            // Get selected protected area code for validation
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const protectedAreaCode = selectedOption.getAttribute('data-code');
            
            if (siteNames.length > 0) {
                let optionsHTML = '<option value="">No specific site</option>';
                
                siteNames.forEach(siteName => {
                    // Client-side validation: Filter sites based on protected area
                    let isValidSite = true;
                    
                    if (protectedAreaCode === 'PPLS') {
                        if (siteName.name.includes('MPL')) {
                            isValidSite = false;
                        }
                    } else if (protectedAreaCode === 'MPL') {
                        if (!siteName.name.includes('MPL')) {
                            isValidSite = false;
                        }
                    } else {
                        if (siteName.name.includes('Toyota') || siteName.name.includes('MPL') || 
                            siteName.name.includes('San Roque') || siteName.name.includes('Manga') || 
                            siteName.name.includes('Quibal')) {
                            isValidSite = false;
                        }
                    }
                    
                    if (isValidSite) {
                        optionsHTML += `<option value="${siteName.id}">${siteName.name}</option>`;
                    }
                });
                
                siteNameSelect.innerHTML = optionsHTML;
                siteNameSelect.disabled = false;
            } else {
                siteNameSelect.innerHTML = '<option value="">No sites available</option>';
                siteNameSelect.disabled = true;
            }
            
        } catch (error) {
            console.error('Error loading site names:', error);
            siteNameSelect.innerHTML = '<option value="">Error loading sites</option>';
            siteNameSelect.disabled = true;
        }
    }

    async submitAddForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Adding...';

        try {
            // Convert FormData to object
            const formDataObj = Object.fromEntries(formData);
            
            // Remove table_name as backend will determine it
            delete formDataObj.table_name;
            
            // Ensure station_code is not empty before sending
            if (!formDataObj.station_code || formDataObj.station_code.trim() === '') {
                delete formDataObj.station_code; // Let backend generate it
            }
            
            // Log the data being sent for debugging
            console.log('Submitting observation data:', formDataObj);
            
            const response = await fetch(`/species-observations`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formDataObj)
            });

            // Check if response is ok before parsing JSON
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server returned error:', response.status, errorText);
                
                // Try to parse as JSON if possible
                let result;
                try {
                    result = JSON.parse(errorText);
                } catch {
                    result = { message: `Server error: ${response.status}` };
                }
                
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation added successfully!', 'success');
                
                // Trigger count refreshes if requested
                if (result.refresh_counts) {
                    this.refreshObservationCounts();
                }
                
                this.close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                // Show detailed error messages if available
                let errorMessage = result.message || 'Failed to add observation';
                
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    errorMessage = errorMessages.join(', ');
                }
                
                // Show debug info if available (for development)
                if (result.debug_info) {
                    console.error('Debug info:', result.debug_info);
                    if (result.debug_info.original_message) {
                        errorMessage += ' (Details: ' + result.debug_info.original_message + ')';
                    }
                }
                
                this.showNotification(errorMessage, 'error');
                console.error('Validation errors:', result.errors);
                console.error('Server response:', result);
            }
        } catch (error) {
            console.error('Error adding observation:', error);
            this.showNotification(error.message || 'Error adding observation', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Add Observation';
        }
    }


    refreshObservationCounts() {
        // Trigger count refreshes across the page
        try {
            // Update any count displays that might be on the page
            const countElements = document.querySelectorAll('[data-observation-count]');
            countElements.forEach(element => {
                const currentCount = parseInt(element.textContent) || 0;
                element.textContent = currentCount + 1;
                element.classList.add('updated');
                setTimeout(() => element.classList.remove('updated'), 2000);
            });

            // Update protected area counts if they exist
            const protectedAreaCounts = document.querySelectorAll('[data-protected-area-count]');
            protectedAreaCounts.forEach(element => {
                const protectedAreaId = element.getAttribute('data-protected-area-count');
                // This would ideally be an API call to get the updated count
                // For now, we'll trigger a page reload which handles it properly
            });

            // Dispatch custom event for other components to listen to
            window.dispatchEvent(new CustomEvent('observationCountsUpdated', {
                detail: { timestamp: Date.now() }
            }));

            console.log('Observation counts refreshed');
        } catch (error) {
            console.error('Error refreshing observation counts:', error);
        }
    }

    async initializeEditSiteName(observation) {
        const protectedAreaSelect = document.querySelector('select[name="protected_area_id"]');
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!protectedAreaSelect || !siteNameSelect) return;
        
        const selectedAreaId = protectedAreaSelect.value;
        
        if (selectedAreaId) {
            // Load site names and then set the current value
            await this.loadModalSiteNames(selectedAreaId);
            
            // Set the current site name if it exists
            if (observation.site_name_id) {
                siteNameSelect.value = observation.site_name_id;
            }
        } else {
            // Disable site name dropdown for other areas
            siteNameSelect.disabled = true;
        }
    }


    async prepareDeleteData(data) {
        const { observationId, tableName } = data;
        
        try {
            // Fetch observation data for delete confirmation
            const url = `/api/species-observations/data/${observationId}?table_name=${tableName}`;
            
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                // Try to get error details from response
                let errorDetails = '';
                try {
                    const errorData = await response.json();
                    errorDetails = JSON.stringify(errorData, null, 2);
                    console.error('Server error details:', errorData);
                } catch (e) {
                    errorDetails = await response.text();
                    console.error('Server error text:', errorDetails);
                }
                
                throw new Error(`HTTP error! status: ${response.status}. Details: ${errorDetails}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                console.error('API returned error:', result);
                throw new Error(result.error || 'Failed to load observation data');
            }

            const returnData = {
                type: 'delete',
                observation: result.observation,
                observationId,
                tableName
            };
            
            return returnData;

        } catch (error) {
            console.error('Error preparing delete data:', error);
            this.showNotification('Error loading observation data', 'error');
            return null;
        }
    }

    createModalContent(type, data) {
        const modal = document.createElement('div');
        modal.className = 'modal-content';

        switch (type) {
            case 'view':
                modal.classList.add('large');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'add':
                modal.classList.add('large');
                modal.innerHTML = this.createAddModalHTML(data);
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

        return modal;
    }

    createViewModalHTML(data) {
        const { observation } = data;
        
        const bioGroupBadge = observation.bio_group === 'fauna' 
            ? '<span class="badge fauna">Fauna</span>'
            : '<span class="badge flora">Flora</span>';

        return `
            <div class="modal-header">
                <h2 class="modal-title">View Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="view-grid">
                    <div class="view-item">
                        <div class="view-label">Protected Area</div>
                        <div class="view-value">${observation.protected_area?.name || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Site Name</div>
                        <div class="view-value">${observation.site_name?.name || 'No specific site'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Transaction Code</div>
                        <div class="view-value">${observation.transaction_code || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Station Code</div>
                        <div class="view-value">${observation.station_code || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Patrol Year</div>
                        <div class="view-value">${observation.patrol_year || 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Patrol Semester</div>
                        <div class="view-value">${observation.patrol_semester ? observation.patrol_semester + ' Semester' : 'N/A'}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Bio Group</div>
                        <div class="view-value">${bioGroupBadge}</div>
                    </div>
                    <div class="view-item">
                        <div class="view-label">Common Name</div>
                        <div class="view-value">${observation.common_name || 'N/A'}</div>
                    </div>
                    <div class="view-item full-width">
                        <div class="view-label">Scientific Name</div>
                        <div class="view-value"><em>${observation.scientific_name || 'Not specified'}</em></div>
                    </div>
                    <div class="view-item full-width">
                        <div class="view-label">Recorded Count</div>
                        <div class="view-value">${observation.recorded_count || 'N/A'}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button class="btn btn-primary" onclick="modalSystem.editFromView()">Edit</button>
            </div>
        `;
    }

    createAddModalHTML(data) {
        console.log('createAddModalHTML called with data:', data);
        const { protectedAreas, bioGroups, years, semesters } = data;
        
        const protectedAreaOptions = protectedAreas ? protectedAreas.map(area => 
            `<option value="${area.id}" data-code="${area.code}">${area.name}</option>`
        ).join('') : '';

        const bioGroupOptions = bioGroups ? Object.entries(bioGroups).map(([key, value]) => 
            `<option value="${key}">${value}</option>`
        ).join('') : '';

        const yearOptions = years ? years.map(year => 
            `<option value="${year}">${year}</option>`
        ).join('') : '';

        const semesterOptions = semesters ? Object.entries(semesters).map(([key, value]) => 
            `<option value="${key}">${value}</option>`
        ).join('') : '';

        return `
            <div class="modal-header">
                <h2 class="modal-title">Add New Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="modalSystem.submitAddForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <!-- Save Information Display -->
                    <div class="save-info-banner" id="save-info-banner" style="display: none;">
                        <div class="save-info-content">
                            <h6><i class="fas fa-info-circle"></i> Where this observation will be saved:</h6>
                            <p id="save-location-text">Select a protected area to see where this observation will be saved.</p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Protected Area</label>
                            <select class="form-select" name="protected_area_id" required onchange="modalSystem.handleAddProtectedAreaChange(this); modalSystem.updateSaveInfo(this)">
                                <option value="">Select Protected Area</option>
                                ${protectedAreaOptions}
                            </select>
                            <div class="form-help">The protected area where this observation was made.</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site Name <small class="text-muted">(Optional)</small></label>
                            <select class="form-select" name="site_name_id" id="add_modal_site_name" onchange="modalSystem.updateSaveInfo(this)">
                                <option value="">No specific site</option>
                            </select>
                            <div class="form-help">Select a specific site or leave blank to save at protected area level.</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Transaction Code</label>
                            <input type="text" class="form-input" name="transaction_code" required maxlength="50" placeholder="e.g., OBS-2024-001">
                            <div class="form-help">Unique identifier for this observation record.</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Station Code</label>
                            <input type="text" class="form-input" name="station_code" required maxlength="60" placeholder="Auto-generated" readonly>
                            <div class="form-help">Automatically assigned based on your selection.</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Patrol Year</label>
                            <select class="form-select" name="patrol_year" required>
                                <option value="">Select Year</option>
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Patrol Semester</label>
                            <select class="form-select" name="patrol_semester" required>
                                <option value="">Select Semester</option>
                                ${semesterOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Bio Group</label>
                            <select class="form-select" name="bio_group" required>
                                <option value="">Select Bio Group</option>
                                ${bioGroupOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Common Name</label>
                            <input type="text" class="form-input" name="common_name" required maxlength="150" placeholder="Enter common name">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Scientific Name</label>
                            <input type="text" class="form-input" name="scientific_name" maxlength="200" placeholder="Enter scientific name (optional)">
                            <div class="form-help">Latin name of the species (if known).</div>
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Recorded Count</label>
                            <input type="number" class="form-input" name="recorded_count" required min="0" placeholder="Enter count">
                            <div class="form-help">Number of individuals observed.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Observation</button>
                </div>
            </form>
        `;
    }

    createEditModalHTML(data) {
        const { observation, protectedAreas, bioGroups, years, semesters } = data;
        
        const protectedAreaOptions = protectedAreas.map(area => 
            `<option value="${area.id}" data-code="${area.code}" ${observation.protected_area_id == area.id ? 'selected' : ''}>${area.name}</option>`
        ).join('');

        const bioGroupOptions = Object.entries(bioGroups).map(([key, value]) => 
            `<option value="${key}" ${observation.bio_group === key ? 'selected' : ''}>${value}</option>`
        ).join('');

        const yearOptions = years.map(year => 
            `<option value="${year}" ${observation.patrol_year == year ? 'selected' : ''}>${year}</option>`
        ).join('');

        const semesterOptions = Object.entries(semesters).map(([key, value]) => 
            `<option value="${key}" ${observation.patrol_semester == key ? 'selected' : ''}>${value}</option>`
        ).join('');

        return `
            <div class="modal-header">
                <h2 class="modal-title">Edit Observation</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form" onsubmit="modalSystem.submitEditForm(event, ${observation.id}, '${observation.table_name}')">
                <div class="modal-body">
                    <input type="hidden" name="observation_id" value="${observation.id}">
                    <input type="hidden" name="table_name" value="${observation.table_name || ''}">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Protected Area</label>
                            <select class="form-select" name="protected_area_id" required onchange="modalSystem.handleProtectedAreaChange(this)">
                                <option value="">Select Protected Area</option>
                                ${protectedAreaOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <select class="form-select" name="site_name_id" id="modal_site_name">
                                <option value="">No specific site</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Transaction Code</label>
                            <input type="text" class="form-input" name="transaction_code" value="${observation.transaction_code || ''}" required maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Station Code</label>
                            <input type="text" class="form-input" name="station_code" value="${observation.station_code || ''}" required maxlength="60">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Patrol Year</label>
                            <select class="form-select" name="patrol_year" required>
                                <option value="">Select Year</option>
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Patrol Semester</label>
                            <select class="form-select" name="patrol_semester" required>
                                <option value="">Select Semester</option>
                                ${semesterOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Bio Group</label>
                            <select class="form-select" name="bio_group" required>
                                <option value="">Select Bio Group</option>
                                ${bioGroupOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Common Name</label>
                            <input type="text" class="form-input" name="common_name" value="${observation.common_name || ''}" required maxlength="150">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Scientific Name</label>
                            <input type="text" class="form-input" name="scientific_name" value="${observation.scientific_name || ''}" maxlength="200">
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label required">Recorded Count</label>
                            <input type="number" class="form-input" name="recorded_count" value="${observation.recorded_count || ''}" required min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        `;
    }



    async loadModalSiteNames(protectedAreaId) {
        const siteNameSelect = document.getElementById('modal_site_name');
        
        if (!siteNameSelect) {
            console.error('Site name select element not found!');
            return;
        }
        
        try {
            // Use the correct API route with /api prefix
            const url = `/api/species-observations/site-names/${protectedAreaId}`;
            
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const siteNames = await response.json();
            
            // Create a document fragment for efficient DOM manipulation
            const fragment = document.createDocumentFragment();
            
            // Add default option
            const noSpecificSiteOption = document.createElement('option');
            noSpecificSiteOption.value = '';
            noSpecificSiteOption.textContent = 'No specific site';
            fragment.appendChild(noSpecificSiteOption);
            
            // Add site name options only if sites exist
            if (siteNames && siteNames.length > 0) {
                siteNames.forEach(siteName => {
                    const option = document.createElement('option');
                    option.value = siteName.id;
                    option.textContent = siteName.name;
                    fragment.appendChild(option);
                });
                
                // Enable dropdown only if sites exist
                siteNameSelect.disabled = false;
            } else {
                // Keep dropdown disabled if no sites exist
                siteNameSelect.disabled = true;
            }
            
            // Clear existing options and add new ones
            siteNameSelect.innerHTML = '';
            siteNameSelect.appendChild(fragment);
            
        } catch (error) {
            console.error('Error loading site names:', error);
            // Keep dropdown disabled on error
            siteNameSelect.disabled = true;
        }
    }


    getYearOptions(selectedYear = null) {
        const currentYear = new Date().getFullYear();
        const years = [];
        for (let year = currentYear + 1; year >= currentYear - 10; year--) {
            years.push(`<option value="${year}" ${year == selectedYear ? 'selected' : ''}>${year}</option>`);
        }
        return years.join('');
    }

    async submitEditForm(event, observationId, tableName) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';

        try {
            // Add _method field to simulate PUT request for Laravel
            const formDataObj = Object.fromEntries(formData);
            formDataObj._method = 'PUT';
            
            // Use the original table_name if available, otherwise determine from protected area
            if (this.currentData && this.currentData.originalTableName) {
                formDataObj.table_name = this.currentData.originalTableName;
            } else if (!formDataObj.table_name || formDataObj.table_name === '') {
                // Determine table_name based on protected area
                const protectedAreaId = formDataObj.protected_area_id;
                let tableName = 'bms_species_observations'; // default
                
                // Map protected area IDs to table names
                const areaTableMap = {
                    '1': 'bangan_observations',      // Bangan Hill National Park
                    '2': 'baua_observations',       // Baua
                    '3': 'casecnan_observations',   // Casecnan
                    '4': 'dipaniong_observations',  // Dipaniong
                    '5': 'dupax_observations',      // Dupax
                };
                
                tableName = areaTableMap[protectedAreaId] || 'bms_species_observations';
                formDataObj.table_name = tableName;
            }
            
            const response = await fetch(`/species-observations/${observationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formDataObj)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation updated successfully!', 'success');
                this.close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                // Show detailed error messages if available
                let errorMessage = result.message || 'Failed to update observation';
                
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    errorMessage = errorMessages.join(', ');
                }
                
                this.showNotification(errorMessage, 'error');
                console.error('Validation errors:', result.errors);
            }
        } catch (error) {
            console.error('Error updating observation:', error);
            this.showNotification('Error updating observation', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Save Changes';
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

    setFocusTrap() {
        if (!this.modalContent) return;

        const focusableElements = this.modalContent.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    createDeleteModalHTML(data) {
        const { observation } = data;
        
        return `
            <div class="modal-header">
                <h2 class="modal-title">Delete Observation</h2>
                <button class="modal-close" onclick="window.closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 1rem 0;">
                    <div style="width: 36px; height: 36px; margin: 0 auto 0.5rem; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 0.9rem; font-weight: 600; color: #111827;">Delete ${observation.common_name || 'this item'}?</h3>
                    <p style="margin: 0; color: #ef4444; font-size: 0.7rem; font-weight: 500;">Cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="window.closeModal()">Cancel</button>
                <button class="btn btn-danger" onclick="window.modalSystem.confirmDelete(${observation.id}, '${observation.table_name || ''}')">Delete</button>
            </div>
        `;
    }

    async confirmDelete(observationId, tableName) {
        try {
            const response = await fetch(window.routes.speciesObservationsDestroy.replace(':id', observationId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table_name: tableName
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Observation deleted successfully!', 'success');
                this.close();
                
                // Remove the row from the table instantly
                this.removeObservationRow(observationId);
            } else {
                this.showNotification(result.message || result.error || 'Failed to delete observation', 'error');
            }
        } catch (error) {
            this.showNotification('Error deleting observation', 'error');
        }
    }

    removeObservationRow(observationId) {
        // Find and remove the row from the table using data attributes
        const rows = document.querySelectorAll('.observation-row');
        let rowRemoved = false;
        
        rows.forEach(row => {
            // Look for the delete button with the specific observation ID using data attributes
            const deleteBtn = row.querySelector(`button[data-observation-id="${observationId}"]`);
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
                
                // Handle pagination - if current page is now empty, go to previous page
                this.handlePaginationAfterDelete();
            }
        });
        
        if (!rowRemoved) {
            console.warn(`Could not find row for observation ID: ${observationId}`);
            // Fallback: try to find by any attribute containing the ID
            rows.forEach(row => {
                if (row.innerHTML.includes(`observation-id="${observationId}"`) || 
                    row.innerHTML.includes(`data-observation-id="${observationId}"`)) {
                    row.remove();
                    rowRemoved = true;
                    
                    // Update count for fallback case too
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
                    
                    // Handle pagination for fallback case
                    this.handlePaginationAfterDelete();
                }
            });
        }
        
        if (!rowRemoved) {
            console.warn(`Still could not find row for observation ID: ${observationId} after fallback search`);
        }
    }

    handlePaginationAfterDelete() {
        // Check if current page has no more rows
        const remainingRows = document.querySelectorAll('.observation-row');
        
        if (remainingRows.length === 0) {
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const currentPage = parseInt(urlParams.get('page')) || 1;
            
            if (currentPage > 1) {
                // Go to previous page
                urlParams.set('page', currentPage - 1);
                const newUrl = window.location.pathname + '?' + urlParams.toString();
                window.location.href = newUrl;
            } else {
                // Stay on first page but reload to get updated data
                window.location.reload();
            }
        } else {
            // Update the "showing X to Y of Z results" text
            const showingText = document.querySelector('.text-sm.text-gray-700');
            if (showingText) {
                const text = showingText.textContent;
                const match = text.match(/Showing (\d+) to (\d+) of (\d+) results/);
                if (match) {
                    const total = parseInt(match[3]) - 1; // Decrease total by 1
                    const firstItem = remainingRows.length > 0 ? 1 : 0;
                    const lastItem = remainingRows.length;
                    
                    if (total > 0) {
                        showingText.textContent = `Showing ${firstItem} to ${lastItem} of ${total} results`;
                    } else {
                        showingText.textContent = 'No results found';
                    }
                }
            }
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 10000;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-weight: 500;
            max-width: 400px;
            transform: translateX(100%);
            transition: transform 0.2s ease;
        `;

        if (type === 'success') {
            notification.style.backgroundColor = '#10b981';
            notification.style.color = 'white';
        } else if (type === 'error') {
            notification.style.backgroundColor = '#ef4444';
            notification.style.color = 'white';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 200);
        }, 3000);
    }
}

// Initialize the modal system
let modalSystem;

// Initialize modal system function
function initializeModalSystem() {
    console.log('Initializing ModalSystem...');
    try {
        modalSystem = new ModalSystem();
        
        // Make sure it's available globally
        window.modalSystem = modalSystem;
        
        // Global functions for onclick handlers
        window.openViewModal = (observationId, tableName) => {
            modalSystem.open('view', { observationId, tableName });
        };

        window.openAddModal = () => {
            console.log('Opening add modal...');
            modalSystem.open('add', {});
        };

        window.openEditModal = (observationId, tableName) => {
            modalSystem.open('edit', { observationId, tableName });
        };

        window.openDeleteModal = (observationId, tableName) => {
            modalSystem.open('delete', { observationId, tableName });
        };

        window.closeModal = () => {
            modalSystem.close();
        };
        
        console.log('ModalSystem initialized successfully!');
    } catch (error) {
        console.error('Error initializing ModalSystem:', error);
    }
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeModalSystem);
} else {
    initializeModalSystem();
}
