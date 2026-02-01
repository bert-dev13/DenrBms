<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl z-35 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <!-- Header -->
    <div class="bg-gradient-to-br from-green-600 to-green-700 p-6 text-white">
        <div class="flex items-center space-x-3">
            <div>
                <h1 class="text-xl font-bold">DENR BMS</h1>
                <p class="text-green-100 text-sm">Biodiversity Management System</p>
            </div>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Species Observations -->
        <a href="{{ route('species-observations.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('species-observations.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="whitespace-nowrap">Species Observations</span>
        </a>

        <!-- Protected Areas -->
        <a href="{{ route('protected-areas.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('protected-areas.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="whitespace-nowrap">Protected Areas</span>
        </a>

        <!-- Protected Area Sites -->
        <a href="{{ route('protected-area-sites.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('protected-area-sites.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200 ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span class="whitespace-nowrap">Protected Area Sites</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('analytics.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('analytics.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="whitespace-nowrap">Analytics</span>
        </a>

        <!-- Reports -->
        <a href="{{ route('reports.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
            </svg>
            <span class="whitespace-nowrap">Reports</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('settings.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"></path>
            </svg>
            <span class="whitespace-nowrap">Settings</span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-gray-200">
        <button onclick="showLogoutModal()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span class="whitespace-nowrap">Logout</span>
        </button>
    </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" onclick="hideLogoutModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="logout-modal-content" style="z-index: 10000;">
            <div class="p-6">
                <!-- Warning Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                
                <!-- Confirmation Message -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Logout</h3>
                    <p class="text-sm text-gray-600">Are you sure you want to logout? You will need to login again to access the system.</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Logout
                        </button>
                    </form>
                    <button onclick="hideLogoutModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #logout-modal.show #logout-modal-content {
        animation: modalSlideUp 0.3s ease-out forwards;
    }
    
    @keyframes modalSlideUp {
        from { 
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to { 
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    #logout-modal.hiding #logout-modal-content {
        animation: modalSlideDown 0.3s ease-out forwards;
    }
    
    @keyframes modalSlideDown {
        from { 
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        to { 
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
    }
    
    /* Ensure modal is always on top */
    #logout-modal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }
    
    #logout-modal-content {
        position: relative !important;
        z-index: 10001 !important;
    }
</style>

<script>
    function showLogoutModal() {
        const modal = document.getElementById('logout-modal');
        const modalContent = document.getElementById('logout-modal-content');
        
        if (!modal || !modalContent) {
            console.error('Logout modal elements not found');
            return;
        }
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.remove('hiding');
        modal.classList.add('show');
        
        // Force reflow
        modal.offsetHeight;
        
        // Show content with animation
        setTimeout(() => {
            modalContent.classList.add('show');
        }, 10);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }
    
    function hideLogoutModal() {
        const modal = document.getElementById('logout-modal');
        const modalContent = document.getElementById('logout-modal-content');
        
        if (!modal || !modalContent) {
            console.error('Logout modal elements not found');
            return;
        }
        
        // Add hiding animation
        modal.classList.add('hiding');
        modal.classList.remove('show');
        modalContent.classList.remove('show');
        
        // Hide after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('hiding');
            document.body.style.overflow = '';
        }, 300);
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('logout-modal');
            if (modal && !modal.classList.contains('hidden')) {
                hideLogoutModal();
            }
        }
    });
</script>
