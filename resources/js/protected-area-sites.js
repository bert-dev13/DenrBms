// Protected Area Sites Page JavaScript

// Sidebar toggle function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
        sidebar.classList.add('-translate-x-full');
        document.querySelector('.sidebar-overlay').classList.add('hidden');
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
    }
});

// View Site Modal Functions
function openViewSiteModal(siteId) {
    console.log('Opening view modal for site:', siteId);
    
    // Fetch site data
    fetch(`/api/protected-area-sites/${siteId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Site data:', data);
        
        if (data.success) {
            // Populate modal with site data
            populateViewSiteModal(data.siteName);
            // Show modal
            const modal = document.getElementById('viewSiteModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert(data.error || 'Failed to load site data.');
        }
    })
    .catch(error => {
        console.error('Error fetching site:', error);
        alert('An error occurred while loading site data.');
    });
}

function closeViewSiteModal() {
    const modal = document.getElementById('viewSiteModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function populateViewSiteModal(site) {
    const modalContent = document.getElementById('viewSiteModalContent');
    
    modalContent.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <!-- Site Name -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    <p class="text-gray-900">${site.name || 'N/A'}</p>
                </div>
            </div>

            <!-- Station Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Station Code</label>
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    <p class="text-gray-900">${site.station_code || 'N/A'}</p>
                </div>
            </div>

            <!-- Protected Area -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Protected Area</label>
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    ${site.protected_area ? `
                        <p class="text-gray-900">${site.protected_area.name || 'N/A'}</p>
                        <p class="text-xs text-gray-500">${site.protected_area.code || ''}</p>
                    ` : '<p class="text-gray-400">Not assigned</p>'}
                </div>
            </div>

            <!-- Status -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                        site.status === 'Active' 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-gray-100 text-gray-800'
                    }">
                        ${site.status || 'Unassigned'}
                    </span>
                </div>
            </div>
        </div>
    `;
}

// Edit Site Modal Functions
function openEditSiteModal(siteId) {
    console.log('Opening edit modal for site:', siteId);
    
    if (siteId === undefined) {
        // Create new site
        document.getElementById('editSiteId').value = '';
        document.getElementById('editSiteName').value = '';
        document.getElementById('editSiteProtectedAreaId').value = '';
        
        const modal = document.getElementById('editSiteModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        return;
    }
    
    // Fetch site data for editing
    fetch(`/api/protected-area-sites/${siteId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Site data:', data);
        
        if (data.success) {
            // Populate form with site data
            document.getElementById('editSiteId').value = data.siteName.id;
            document.getElementById('editSiteName').value = data.siteName.name || '';
            document.getElementById('editSiteProtectedAreaId').value = data.siteName.protected_area_id || '';
            
            // Load protected areas for dropdown
            loadProtectedAreasForEdit();
            
            // Show modal
            const modal = document.getElementById('editSiteModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert(data.error || 'Failed to load site data.');
        }
    })
    .catch(error => {
        console.error('Error fetching site:', error);
        alert('An error occurred while loading site data.');
    });
}

function loadProtectedAreasForEdit() {
    // Fetch protected areas for dropdown
    fetch('/protected-areas', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse HTML to extract protected areas (this is a simplified approach)
        // In a real app, you'd have an API endpoint for this
        const select = document.getElementById('editSiteProtectedAreaId');
        // Protected areas should be loaded from the page data or via API
        // For now, we'll keep the existing options
    })
    .catch(error => {
        console.error('Error loading protected areas:', error);
    });
}

function closeEditSiteModal() {
    const modal = document.getElementById('editSiteModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('editSiteForm').reset();
}

function submitEditSiteForm() {
    const form = document.getElementById('editSiteForm');
    const siteId = document.getElementById('editSiteId').value;
    
    // Disable submit button
    const submitBtn = document.getElementById('editSiteSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    const url = siteId ? `/protected-area-sites/${siteId}` : '/protected-area-sites';
    const method = siteId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            _method: method,
            name: document.getElementById('editSiteName').value,
            protected_area_id: document.getElementById('editSiteProtectedAreaId').value || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            closeEditSiteModal();
            window.location.reload();
        } else {
            alert(data.error || 'Failed to save site.');
            submitBtn.disabled = false;
            submitBtn.textContent = siteId ? 'Update' : 'Create';
        }
    })
    .catch(error => {
        console.error('Error saving site:', error);
        alert('An error occurred while saving site.');
        submitBtn.disabled = false;
        submitBtn.textContent = siteId ? 'Update' : 'Create';
    });
}

// Delete confirmation modal functions for sites
let deleteSiteForm = null;

function confirmDeleteSite(button) {
    console.log('Delete confirmation triggered for site');
    
    // Get the form from the button
    deleteSiteForm = button.closest('form');
    console.log('Form action:', deleteSiteForm.action);
    console.log('Form method:', deleteSiteForm.method);
    
    // Open the confirmation modal
    openDeleteSiteModal();
}

function openDeleteSiteModal() {
    const modal = document.getElementById('deleteSiteConfirmModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteSiteModal() {
    const modal = document.getElementById('deleteSiteConfirmModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    deleteSiteForm = null;
}

function confirmDeleteSiteAction() {
    console.log('=== DELETE SITE ACTION STARTED ===');
    console.log('deleteSiteForm:', deleteSiteForm);
    
    if (!deleteSiteForm) {
        console.error('No deleteSiteForm found!');
        return;
    }
    
    // Prevent multiple submissions
    const confirmBtn = document.getElementById('confirmDeleteSiteBtn');
    console.log('confirmBtn:', confirmBtn);
    
    if (confirmBtn.disabled) {
        console.log('Button already disabled, returning');
        return;
    }
    
    console.log('User confirmed deletion, submitting form');
    
    // Disable button to prevent multiple submissions
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Deleting...';
    
    // Submit the form
    fetch(deleteSiteForm.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            _method: 'DELETE'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Delete response:', data);
        
        if (data.success) {
            // Close modal and reload page
            closeDeleteSiteModal();
            window.location.reload();
        } else {
            alert(data.error || 'Failed to delete site.');
            // Re-enable button
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Yes';
        }
    })
    .catch(error => {
        console.error('Error deleting site:', error);
        alert('An error occurred while deleting site.');
        // Re-enable button
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Yes';
    });
}

// Clear filters (status + sort)
function clearSiteFilters() {
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    if (status) status.value = '';
    if (sort) sort.value = 'name';

    // Submit the form to reset filters
    status.closest('form').submit();
}

// Client-side search over sites table
function filterSitesTable() {
    const input = document.getElementById('site-search');
    const clearBtn = document.getElementById('site-search-clear');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.responsive-table tbody tr');

    if (clearBtn) {
        clearBtn.classList.toggle('hidden', filter.length === 0);
    }

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function clearSiteSearch() {
    const input = document.getElementById('site-search');
    input.value = '';
    filterSitesTable();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search clear button visibility
    const searchInput = document.getElementById('site-search');
    const clearBtn = document.getElementById('site-search-clear');
    
    if (searchInput && clearBtn) {
        clearBtn.classList.toggle('hidden', searchInput.value.length === 0);
    }
});
