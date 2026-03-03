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
            // Fetch observation data (edit-data includes site_name_id for dropdown pre-selection)
            const response = await fetch(`/api/species-observations/edit-data/${observationId}?table_name=${encodeURIComponent(tableName || '')}`, {
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
        const placeholderDefault = siteNameSelect?.dataset.placeholderDefault || 'Select a protected area first';
        const placeholderNoSites = siteNameSelect?.dataset.placeholderNoSites || 'No sites available';

        if (!protectedAreaId) {
            siteNameSelect.innerHTML = `<option value="">${placeholderDefault}</option>`;
            siteNameSelect.disabled = true;
            siteNameSelect.value = '';
            this.updateSaveInfo(selectElement);
            return;
        }

        // Loading state
        siteNameSelect.disabled = true;
        siteNameSelect.innerHTML = '<option value="">Loading...</option>';
        siteNameSelect.value = '';

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

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Received non-JSON response:', text.substring(0, 200));
                throw new Error('Received HTML response instead of JSON. Please check if you are logged in.');
            }

            const result = await response.json();
            const siteNames = result.success && result.site_names
                ? result.site_names
                : (Array.isArray(result) ? result : result.sites || []);

            if (result.success === false && !Array.isArray(result)) {
                throw new Error(result.error || 'Failed to load site names');
            }

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const protectedAreaCode = selectedOption?.getAttribute('data-code') || '';

            const validSites = siteNames.filter(s => {
                if (protectedAreaCode === 'PPLS') return !s.name.includes('MPL');
                if (protectedAreaCode === 'MPL') return s.name.includes('MPL');
                return !['Toyota', 'MPL', 'San Roque', 'Manga', 'Quibal'].some(k => s.name.includes(k));
            });

            if (validSites.length > 0) {
                let optionsHTML = '<option value="">Select</option><option value="0">No specific site</option>';
                validSites.forEach(site => {
                    optionsHTML += `<option value="${site.id}">${site.name}</option>`;
                });
                siteNameSelect.innerHTML = optionsHTML;
                siteNameSelect.disabled = false;
            } else {
                siteNameSelect.innerHTML = `<option value="">${placeholderNoSites}</option>`;
                siteNameSelect.disabled = true;
                siteNameSelect.value = '';
            }

            this.updateSaveInfo(selectElement);
        } catch (error) {
            console.error('Error loading site names:', error);
            siteNameSelect.innerHTML = `<option value="">${placeholderNoSites}</option>`;
            siteNameSelect.disabled = true;
            siteNameSelect.value = '';
        }
    }

    updateSaveInfo(selectElement) {
        const protectedAreaSelect = selectElement?.name === 'protected_area_id' ? selectElement : document.querySelector('select[name="protected_area_id"]');
        const siteNameSelect = document.getElementById('add_modal_site_name');
        const saveInfoBanner = document.getElementById('save-info-banner');
        const saveLocationText = document.getElementById('save-location-text');
        const stationCodeInput = document.querySelector('input[name="station_code"]');
        
        if (!protectedAreaSelect || !protectedAreaSelect.value) {
            if (saveInfoBanner) saveInfoBanner.style.display = 'none';
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
        
        if (saveLocationText) saveLocationText.innerHTML = saveLocation;
        if (saveInfoBanner) saveInfoBanner.style.display = 'block';
        
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
            siteNameSelect.innerHTML = '<option value="">Select a protected area first</option>';
            siteNameSelect.disabled = true;
            siteNameSelect.value = '';
            return;
        }

        siteNameSelect.innerHTML = '<option value="">Loading...</option>';
        siteNameSelect.disabled = true;
        siteNameSelect.value = '';

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
        submitBtn.textContent = 'Saving...';

        try {
            // Convert FormData to object (disabled fields are excluded, so add site_name_id explicitly)
            const formDataObj = Object.fromEntries(formData);
            
            // Remove table_name as backend will determine it
            delete formDataObj.table_name;
            
            // site_name_id: disabled selects aren't in FormData; normalize to '' when absent or "0"
            const siteSelect = form.querySelector('select[name="site_name_id"]');
            formDataObj.site_name_id = (siteSelect?.disabled || !formDataObj.site_name_id || formDataObj.site_name_id === '0')
                ? ''
                : formDataObj.site_name_id;
            
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
            submitBtn.textContent = 'Save Observation';
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

        if (!selectedAreaId) {
            siteNameSelect.innerHTML = '<option value="">Select a protected area first</option>';
            siteNameSelect.disabled = true;
            siteNameSelect.value = '';
            return;
        }

        await this.loadModalSiteNames(selectedAreaId);

        if (observation.site_name_id) {
            const optionExists = Array.from(siteNameSelect.options).some(opt => opt.value == observation.site_name_id);
            if (optionExists && !siteNameSelect.disabled) {
                siteNameSelect.value = String(observation.site_name_id);
            } else {
                siteNameSelect.value = '';
            }
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
                modal.classList.add('large', 'modal-add');
                modal.innerHTML = this.createViewModalHTML(data);
                break;
            case 'add':
                modal.classList.add('large', 'modal-add');
                modal.innerHTML = this.createAddModalHTML(data);
                break;
            case 'edit':
                modal.classList.add('large', 'modal-add');
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
        const protectedAreaName = observation.protected_area?.name || 'N/A';
        const siteName = observation.site_name?.name || 'No specific site';
        const stationCode = (observation.station_code || 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const patrolYear = observation.patrol_year || 'N/A';
        const patrolSemester = observation.patrol_semester ? (observation.patrol_semester === 1 ? '1st' : '2nd') : 'N/A';
        const bioGroup = observation.bio_group === 'fauna' ? 'Fauna' : (observation.bio_group === 'flora' ? 'Flora' : 'N/A');
        const recordedCount = observation.recorded_count ?? 'N/A';
        const commonName = (observation.common_name || 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const scientificName = (observation.scientific_name || 'Not specified').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

        return `
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">View Observation</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-form modal-form-add">
                <div class="modal-body modal-body-add">
                    <section class="form-section">
                        <h3 class="form-section-title">Location Details</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label">Protected Area</label>
                                <input type="text" class="form-input" value="${protectedAreaName.replace(/"/g, '&quot;')}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Station Code</label>
                                <input type="text" class="form-input" value="${stationCode}" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-input" value="${siteName.replace(/"/g, '&quot;')}" readonly>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Record Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label">Patrol Year</label>
                                <input type="text" class="form-input" value="${patrolYear}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Patrol Semester</label>
                                <input type="text" class="form-input" value="${patrolSemester}" readonly>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Species Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label">Bio Group</label>
                                <input type="text" class="form-input" value="${bioGroup}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Recorded Count</label>
                                <input type="text" class="form-input" value="${recordedCount}" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Common Name</label>
                                <input type="text" class="form-input" value="${commonName}" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Scientific Name</label>
                                <input type="text" class="form-input" value="${scientificName}" readonly>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeModal()">Close</button>
                </div>
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
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">Add New Observation</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form modal-form-add" onsubmit="modalSystem.submitAddForm(event)">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <div class="modal-body modal-body-add">
                    <section class="form-section">
                        <h3 class="form-section-title">Location Details</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label form-label-required">Protected Area</label>
                                <select class="form-input form-select" name="protected_area_id" required onchange="modalSystem.handleAddProtectedAreaChange(this)">
                                    <option value="">Select</option>
                                    ${protectedAreaOptions}
                                </select>
                            </div>
                            <div class="form-group form-group-site-name">
                                <label class="form-label">Site Name</label>
                                <select class="form-input form-select" name="site_name_id" id="add_modal_site_name" disabled data-placeholder-default="Select a protected area first" data-placeholder-no-sites="No sites available" onchange="modalSystem.updateSaveInfo(this.form.querySelector('select[name=protected_area_id]'))">
                                    <option value="">Select a protected area first</option>
                                </select>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Station Code</label>
                                <input type="text" class="form-input" name="station_code" required maxlength="60" placeholder="e.g. PPLS-MAIN">
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Record Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label form-label-required">Transaction Code</label>
                                <input type="text" class="form-input" name="transaction_code" required maxlength="50" placeholder="e.g. OBS-2024-001">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Patrol Year</label>
                                <select class="form-input form-select" name="patrol_year" required>
                                    <option value="">Select</option>
                                    ${yearOptions}
                                </select>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Patrol Semester</label>
                                <select class="form-input form-select" name="patrol_semester" required>
                                    <option value="">Select</option>
                                    ${semesterOptions}
                                </select>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Species Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label form-label-required">Bio Group</label>
                                <select class="form-input form-select" name="bio_group" required>
                                    <option value="">Select</option>
                                    ${bioGroupOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Common Name</label>
                                <input type="text" class="form-input" name="common_name" required maxlength="150" placeholder="e.g. Philippine Eagle">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Scientific Name</label>
                                <input type="text" class="form-input" name="scientific_name" required maxlength="200" placeholder="e.g. Pithecophaga jefferyi">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Recorded Count</label>
                                <input type="number" class="form-input" name="recorded_count" required min="0" value="0" placeholder="0">
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary-add" id="addObservationSubmitBtn">Save Observation</button>
                </div>
            </form>
        `;
    }

    createEditModalHTML(data) {
        const { observation, protectedAreas, bioGroups, years, semesters } = data;
        const safeTableName = (observation.table_name || '').replace(/'/g, "\\'");
        const safeTransactionCode = (observation.transaction_code || '').replace(/"/g, '&quot;');
        const safeStationCode = (observation.station_code || '').replace(/"/g, '&quot;');
        const safeCommonName = (observation.common_name || '').replace(/"/g, '&quot;');
        const safeScientificName = (observation.scientific_name || '').replace(/"/g, '&quot;');

        const protectedAreaOptions = (protectedAreas || []).map(area =>
            `<option value="${area.id}" data-code="${area.code || ''}" ${observation.protected_area_id == area.id ? 'selected' : ''}>${area.name || ''}</option>`
        ).join('');

        const bioGroupOptions = Object.entries(bioGroups || {}).map(([key, value]) =>
            `<option value="${key}" ${observation.bio_group === key ? 'selected' : ''}>${value}</option>`
        ).join('');

        const yearOptions = (years || []).map(year =>
            `<option value="${year}" ${observation.patrol_year == year ? 'selected' : ''}>${year}</option>`
        ).join('');

        const semesterOptions = Object.entries(semesters || {}).map(([key, value]) =>
            `<option value="${key}" ${observation.patrol_semester == key ? 'selected' : ''}>${value}</option>`
        ).join('');

        return `
            <div class="modal-header modal-header-add">
                <h2 class="modal-title">Edit Observation</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="modal-form modal-form-add" onsubmit="modalSystem.submitEditForm(event, ${observation.id}, '${safeTableName}')">
                <input type="hidden" name="observation_id" value="${observation.id}">
                <input type="hidden" name="table_name" value="${observation.table_name || ''}">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                <div class="modal-body modal-body-add">
                    <section class="form-section">
                        <h3 class="form-section-title">Location Details</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label form-label-required">Protected Area</label>
                                <select class="form-input form-select" name="protected_area_id" required onchange="modalSystem.handleProtectedAreaChange(this)">
                                    <option value="">Select</option>
                                    ${protectedAreaOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Station Code</label>
                                <input type="text" class="form-input" name="station_code" value="${safeStationCode}" maxlength="60" readonly>
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Site Name</label>
                                <select class="form-input form-select" name="site_name_id" id="modal_site_name" disabled data-placeholder-default="Select a protected area first" data-placeholder-no-sites="No sites available">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Record Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Transaction Code</label>
                                <input type="text" class="form-input" name="transaction_code" value="${safeTransactionCode}" required maxlength="50">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Patrol Year</label>
                                <select class="form-input form-select" name="patrol_year" required>
                                    <option value="">Select</option>
                                    ${yearOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Patrol Semester</label>
                                <select class="form-input form-select" name="patrol_semester" required>
                                    <option value="">Select</option>
                                    ${semesterOptions}
                                </select>
                            </div>
                        </div>
                    </section>
                    <hr class="form-section-divider">
                    <section class="form-section">
                        <h3 class="form-section-title">Species Information</h3>
                        <div class="form-section-grid">
                            <div class="form-group">
                                <label class="form-label form-label-required">Bio Group</label>
                                <select class="form-input form-select" name="bio_group" required>
                                    <option value="">Select</option>
                                    ${bioGroupOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-label-required">Recorded Count</label>
                                <input type="number" class="form-input" name="recorded_count" value="${observation.recorded_count ?? 0}" required min="0">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label form-label-required">Common Name</label>
                                <input type="text" class="form-input" name="common_name" value="${safeCommonName}" required maxlength="150">
                            </div>
                            <div class="form-group form-group-span-full">
                                <label class="form-label">Scientific Name</label>
                                <input type="text" class="form-input" name="scientific_name" value="${safeScientificName}" maxlength="200">
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer modal-footer-add">
                    <button type="button" class="btn btn-secondary-add" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary-add" id="editObservationSubmitBtn">Update Observation</button>
                </div>
            </form>
        `;
    }



    async loadModalSiteNames(protectedAreaId) {
        const siteNameSelect = document.getElementById('modal_site_name');
        if (!siteNameSelect) return;

        const protectedAreaSelect = document.querySelector('select[name="protected_area_id"]');
        const selectedOption = protectedAreaSelect?.options[protectedAreaSelect?.selectedIndex];
        const protectedAreaCode = selectedOption?.getAttribute('data-code') || '';

        try {
            const response = await fetch(`/api/species-observations/site-names/${protectedAreaId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const result = await response.json();
            const rawSites = result.success && result.site_names ? result.site_names : (result.sites || result);
            const siteNames = Array.isArray(rawSites) ? rawSites : [];

            const validSites = siteNames.filter(s => {
                if (protectedAreaCode === 'PPLS') return !s.name.includes('MPL');
                if (protectedAreaCode === 'MPL') return s.name.includes('MPL');
                return !['Toyota', 'MPL', 'San Roque', 'Manga', 'Quibal'].some(k => s.name.includes(k));
            });

            siteNameSelect.innerHTML = '';
            const noSpecific = document.createElement('option');
            noSpecific.value = '';
            noSpecific.textContent = 'No specific site';
            siteNameSelect.appendChild(noSpecific);

            if (validSites.length > 0) {
                validSites.forEach(site => {
                    const opt = document.createElement('option');
                    opt.value = site.id;
                    opt.textContent = site.name;
                    siteNameSelect.appendChild(opt);
                });
                siteNameSelect.disabled = false;
            } else {
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'No sites available';
                siteNameSelect.innerHTML = '';
                siteNameSelect.appendChild(emptyOpt);
                siteNameSelect.disabled = true;
                siteNameSelect.value = '';
            }
        } catch (error) {
            console.error('Error loading site names:', error);
            siteNameSelect.innerHTML = '<option value="">Error loading sites</option>';
            siteNameSelect.disabled = true;
            siteNameSelect.value = '';
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
            const formDataObj = Object.fromEntries(formData);
            formDataObj._method = 'PUT';

            // Disabled site select is excluded from FormData; ensure site_name_id is sent
            const siteSelect = form.querySelector('select[name="site_name_id"]');
            formDataObj.site_name_id = (siteSelect?.disabled || !formDataObj.site_name_id || formDataObj.site_name_id === '0')
                ? ''
                : formDataObj.site_name_id;
            
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
            submitBtn.textContent = 'Update Observation';
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
        const { observation, observationId, tableName } = data;
        // Use observationId from row (data) - more reliable than observation.id from API
        const id = observationId ?? observation?.id;
        const name = (observation?.common_name || 'this item').replace(/'/g, "\\'");
        const safeTableName = (tableName || observation?.table_name || '').replace(/'/g, "\\'");
        
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
                <button class="btn btn-secondary" onclick="window.closeModal()">Cancel</button>
                <button class="btn btn-danger" data-observation-id="${id}" data-table-name="${safeTableName}" onclick="window.modalSystem.confirmDeleteFromButton(this)">Delete</button>
            </div>
        `;
    }

    confirmDeleteFromButton(button) {
        const observationId = button.getAttribute('data-observation-id');
        const tableName = button.getAttribute('data-table-name') || '';
        if (observationId) {
            this.confirmDelete(observationId, tableName);
        }
    }

    async confirmDelete(observationId, tableName) {
        try {
            const url = window.routes.speciesObservationsDestroy.replace(':id', String(observationId));
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table_name: tableName
                })
            });

            const result = await response.json();
            console.log('Delete response:', result);

            if (result.success) {
                this.showNotification('Observation deleted successfully!', 'success');
                this.close();
                
                // Reload page to guarantee fresh data (removes stale row, updates pagination/counts)
                window.location.reload();
            } else {
                this.showNotification(result.message || result.error || 'Failed to delete observation', 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
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
