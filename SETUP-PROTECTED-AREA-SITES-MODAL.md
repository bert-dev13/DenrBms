# Protected Area Sites Modal System Setup

## Overview
This modal system provides a complete CRUD interface for Protected Area Sites with the same UI/UX design and behavior as the Protected Area modal system. It's fully scoped to prevent conflicts with existing modals.

## Files Created

### 1. CSS File
**Location**: `resources/css/protected-area-sites-modal.css`
- Contains all scoped styles for the Protected Area Sites modal system
- Uses `protected-area-sites-` prefix for all classes to prevent conflicts
- Includes responsive design, animations, and accessibility features

### 2. JavaScript File
**Location**: `resources/js/protected-area-sites-modal.js`
- Complete modal system class `ProtectedAreaSitesModalSystem`
- Handles View, Add, Edit, and Delete operations
- Includes dynamic data loading and UI updates without page reload
- Proper error handling and notifications

### 3. Example Integration
**Location**: `examples/protected-area-sites-modal-integration.blade.php`
- Complete example showing how to integrate the modal system
- Includes proper Vite references and data passing
- Shows table structure with action buttons

## Setup Instructions

### Step 1: Add CSS and JS to Your Blade Template
Add the modal system files to your existing Protected Area Sites blade template:

```blade
<!-- In the <head> section -->
@vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/protected-area-sites-modal.css'])

<!-- Before closing </body> tag -->
@vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/protected-area-sites-modal.js'])
```

### Step 2: Add Global JavaScript Variables
Add this script before closing the `</head>` tag:

```blade
<script>
    window.csrfToken = '{{ csrf_token() }}';
    // Pass protected areas data to JavaScript for modal dropdowns
    window.protectedAreas = @json(\App\Models\ProtectedArea::orderBy('name')->get(['id', 'name', 'code']));
</script>
```

### Step 3: Update Table Structure
Update your table to use the proper IDs and classes:

```blade
<table class="responsive-table">
    <thead>
        <tr>
            <th>Site Name</th>
            <th>Protected Area</th>
            <th>Station Code</th>
            <th>Observations</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="protected-area-sites-table-body">
        @forelse ($siteNames as $site)
            <tr class="hover:bg-gray-50 protected-area-sites-row" data-site-id="{{ $site->id }}">
                <!-- Table cells as shown in example -->
                <td>
                    <div class="font-medium text-gray-900">
                        {{ $site->name }}
                    </div>
                </td>
                <!-- ... other cells ... -->
                <td>
                    <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                        <!-- View Button -->
                        <button type="button" onclick="openViewProtectedAreaSitesModal({{ $site->id }})" 
                           class="protected-area-sites-action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                           title="View Site">
                            <!-- SVG icon -->
                        </button>
                        
                        <!-- Edit Button -->
                        <button type="button" onclick="openEditProtectedAreaSitesModal({{ $site->id }})" 
                           class="protected-area-sites-action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                           title="Edit Site">
                            <!-- SVG icon -->
                        </button>
                        
                        <!-- Delete Button -->
                        <button type="button" onclick="openDeleteProtectedAreaSitesModal({{ $site->id }})" 
                           class="protected-area-sites-action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                           title="Delete Site">
                            <!-- SVG icon -->
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <!-- Empty state -->
        @endforelse
    </tbody>
</table>
```

### Step 4: Update Add Button
Change your "Add Protected Area Site" button to use the new modal:

```blade
<button onclick="openAddProtectedAreaSitesModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    <span>Add Protected Area Site</span>
</button>
```

### Step 5: Add Search Functionality
Add search input and clear button:

```blade
<input
    type="text"
    id="protected-area-sites-search"
    class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
           focus:ring-2 focus:ring-green-500 focus:border-transparent"
    placeholder="Search protected area sites..."
    autocomplete="off"
    oninput="filterProtectedAreaSitesTable()"
/>

<button
    id="protected-area-sites-search-clear"
    type="button"
    class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
           text-gray-400 hover:text-gray-600 hidden bg-transparent"
    onclick="clearProtectedAreaSitesSearch()"
    style="right: 2px;"
>
    <!-- Clear icon SVG -->
</button>
```

### Step 6: Add JavaScript Functions
Add these functions to your page:

```javascript
// Client-side search over protected area sites table
function filterProtectedAreaSitesTable() {
    const input = document.getElementById('protected-area-sites-search');
    const clearBtn = document.getElementById('protected-area-sites-search-clear');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#protected-area-sites-table-body .protected-area-sites-row');

    if (clearBtn) {
        clearBtn.classList.toggle('hidden', filter.length === 0);
    }

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function clearProtectedAreaSitesSearch() {
    const input = document.getElementById('protected-area-sites-search');
    input.value = '';
    filterProtectedAreaSitesTable();
}

// Initialize modal system when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Update the modal system to include protected areas data
    if (window.protectedAreaSitesModalSystem) {
        window.protectedAreaSitesModalSystem.generateProtectedAreaOptions = function(selectedId = null) {
            if (!window.protectedAreas) return '';
            
            return window.protectedAreas.map(area => 
                `<option value="${area.id}" ${area.id == selectedId ? 'selected' : ''}>${area.name} (${area.code})</option>`
            ).join('');
        };
    }
});
```

## Required API Endpoints

The modal system expects these API endpoints to exist:

### GET `/api/protected-area-sites/{id}`
Returns protected area site data in this format:
```json
{
    "success": true,
    "siteName": {
        "id": 1,
        "name": "Site A",
        "station_code": "ST001",
        "protected_area_id": 1,
        "protected_area": {
            "id": 1,
            "name": "Protected Area Name",
            "code": "PA001"
        },
        "species_observations_count": 5,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### POST `/protected-area-sites`
Creates a new protected area site. Returns:
```json
{
    "success": true,
    "siteName": {
        // Same structure as above
    }
}
```

### POST `/protected-area-sites/{id}` (with `_method: PUT`)
Updates an existing protected area site. Returns same structure as create.

### POST `/protected-area-sites/{id}` (with `_method: DELETE`)
Deletes a protected area site. Returns:
```json
{
    "success": true
}
```

## Features

### ✅ Modal Types
- **View**: Read-only display of site details
- **Add**: Create new protected area site
- **Edit**: Update existing protected area site
- **Delete**: Confirmation modal with immediate UI removal

### ✅ UI/UX Features
- Smooth animations and transitions
- Loading states during operations
- Error handling with user-friendly messages
- Success notifications
- Responsive design for mobile devices
- Keyboard navigation (ESC to close)
- Click outside to close

### ✅ Data Management
- Dynamic data loading without page reload
- Real-time table updates after CRUD operations
- Record count updates
- Client-side search/filtering
- Form validation with error display

### ✅ Security & Compatibility
- CSRF protection
- Laravel Blade integration
- No ID conflicts with existing modals
- Scoped CSS classes
- Proper error handling

## Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Troubleshooting

### Modal doesn't open
1. Check that the JavaScript file is properly loaded
2. Verify the `protectedAreaSitesModalSystem` is initialized
3. Check browser console for JavaScript errors

### Styles not applied
1. Ensure the CSS file is included in Vite
2. Check that class names match exactly
3. Verify no CSS conflicts with existing styles

### API calls failing
1. Verify the API endpoints exist and return correct format
2. Check CSRF token is present
3. Ensure proper headers are being sent

### Dropdown not populated
1. Verify `window.protectedAreas` data is passed correctly
2. Check the `generateProtectedAreaOptions` function is updated
3. Ensure protected areas data structure is correct

## Notes
- This system is completely independent from the Protected Area modal system
- All CSS classes are prefixed with `protected-area-sites-` to prevent conflicts
- The system automatically handles UI updates without page reload
- Includes comprehensive error handling and user feedback
