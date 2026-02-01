<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-white text-gray-800 z-50 shadow-2xl border-r border-gray-200">
    <div class="flex flex-col h-full">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">DENR BMS</h2>
                    <p class="text-xs text-gray-500">Biodiversity Management</p>
                </div>
            </div>
            
            <!-- User Profile Section -->
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="relative">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-sm font-semibold">
                        {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::check() ? Auth::user()->name : 'Guest User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::check() ? Auth::user()->email : 'Not logged in' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Section -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-green-500 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>
            
            <a href="{{ route('species-observations.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('species-observations.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-green-500 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span>Species Observations</span>
                @if(request()->routeIs('species-observations.*'))
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>
            
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-green-500 hover:text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Protected Areas</span>
            </a>
            
            <a href="{{ route('reports.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-green-500 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                </svg>
                <span>Reports</span>
                @if(request()->routeIs('reports.*'))
                    <span class="ml-auto w-2 h-2 bg-white rounded-full"></span>
                @endif
            </a>
            
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-green-500 hover:text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Users</span>
            </a>
            
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-green-500 hover:text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Settings</span>
            </a>
        </nav>
        
        <!-- Bottom Section -->
        <div class="p-4 border-t border-gray-200 space-y-2">
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-green-500 hover:text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Profile</span>
            </a>
            
            @if(Auth::check())
            <button onclick="showLogoutModal()" class="nav-item group w-full flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-green-500 hover:text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
            @endif
        </div>
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