<!-- Mobile Menu Toggle Button -->
@if(request()->cookie('sidebar-preference') !== 'desktop' || (request()->cookie('sidebar-preference') === null && request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad/', request()->header('User-Agent'))))
<button id="mobile-menu-toggle" class="mobile-menu-toggle fixed top-4 left-4 z-40 p-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 lg:hidden">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
    <span class="sr-only">Toggle sidebar menu</span>
</button>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>
@endif
