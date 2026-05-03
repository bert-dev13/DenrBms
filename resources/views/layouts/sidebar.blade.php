{{-- DENR BMS Sidebar: modern admin navigation with branding, profile, and collapsible nav --}}
<button id="mobile-menu-toggle" type="button" class="mobile-menu-toggle lg:hidden fixed top-4 left-4 z-40 bg-green-600 text-white p-2.5 rounded-lg shadow-lg inline-flex items-center justify-center" onclick="toggleSidebar()" aria-label="Toggle menu">
    <i data-lucide="menu" class="lucide-icon w-5 h-5"></i>
</button>

<div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()" aria-hidden="true" role="presentation"></div>

<aside id="sidebar" class="sidebar fixed left-0 top-0 h-full z-40 flex flex-col transition-all duration-300 ease-out" aria-label="Main navigation">
    {{-- Header / Branding --}}
    <div class="sidebar__header">
        <div class="sidebar__brand">
            <div class="sidebar__logo" aria-hidden="true">
                <img src="{{ asset('images/denr-logo.png') }}" alt="" class="sidebar__logo-img">
            </div>
            <div class="sidebar__brand-text">
                <h1 class="sidebar__title">DENR BMS</h1>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar__nav" aria-label="Primary">
        <a href="{{ route('dashboard') }}" class="sidebar__nav-item {{ request()->routeIs('dashboard') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Dashboard">
            <i data-lucide="layout-dashboard" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">Dashboard</span>
        </a>
        <a href="{{ route('species-observations.index') }}" class="sidebar__nav-item {{ request()->routeIs('species-observations.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Species Observations">
            <i data-lucide="clipboard-list" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">Species Observations</span>
        </a>
        <a href="{{ route('protected-areas.index') }}" class="sidebar__nav-item {{ request()->routeIs('protected-areas.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Protected Areas">
            <i data-lucide="map-pin" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">Protected Areas</span>
        </a>
        <a href="{{ route('protected-area-sites.index') }}" class="sidebar__nav-item {{ request()->routeIs('protected-area-sites.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="PA Sites">
            <i data-lucide="map" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">PA Sites</span>
        </a>
        <div class="sidebar__nav-group">
            <div class="sidebar__nav-group-label">Analytics</div>
            <a href="{{ route('analytics.index') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('analytics.index') || request()->routeIs('analytics.export.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Analytics Overview">
                <i data-lucide="line-chart" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Overview</span>
            </a>
            <a href="{{ route('analytics.species.index') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('analytics.species.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Species Analytics">
                <i data-lucide="leaf" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Species Trend</span>
            </a>
        </div>
        <div class="sidebar__nav-group">
            <div class="sidebar__nav-group-label">Reports</div>
            <a href="{{ route('reports.endemic-species') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('reports.endemic-species*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Endemic Species Report">
                <i data-lucide="leaf" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Endemic Species Report</span>
            </a>
            <a href="{{ route('reports.migratory-species') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('reports.migratory-species*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Migratory Species Report">
                <i data-lucide="bird" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Migratory Species Report</span>
            </a>
            <a href="{{ route('reports.species-ranking') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('reports.species-ranking*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Species Rankings Report">
                <i data-lucide="trophy" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Species Rankings</span>
            </a>
        </div>
    </nav>

    {{-- Footer: profile + utilities --}}
    <div class="sidebar__footer">
        <div class="sidebar__user-wrap">
            <div class="sidebar__user" aria-label="Signed in as {{ auth()->user()->name ?? 'Juan Dela Cruz' }}">
                <div class="sidebar__avatar" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Juan Dela Cruz', 0, 1)) }}
                </div>
                <div class="sidebar__user-info">
                    <span class="sidebar__user-name">{{ auth()->user()->name ?? 'Juan Dela Cruz' }}</span>
                    <span class="sidebar__user-email">{{ auth()->user()->email ?? 'test@denr.gov.ph' }}</span>
                </div>
            </div>
        </div>
        <a href="{{ route('settings.index') }}" class="sidebar__nav-item {{ request()->routeIs('settings.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Settings">
            <i data-lucide="settings" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">Settings</span>
        </a>
        <button type="button" class="sidebar__logout-btn" onclick="showLogoutModal()" aria-label="Logout" data-tooltip="Logout">
            <i data-lucide="log-out" class="lucide-icon sidebar__nav-icon"></i>
            <span class="sidebar__nav-label">Logout</span>
        </button>
    </div>
</aside>

{{-- Logout confirmation modal --}}
<div id="logout-modal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/50 transition-opacity duration-300" onclick="hideLogoutModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="logout-modal-content" style="z-index: 10000;">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i data-lucide="log-out" class="lucide-icon w-6 h-6 text-red-600"></i>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Logout</h3>
                    <p class="text-sm text-gray-600">Are you sure you want to logout? You will need to login again to access the system.</p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Logout
                        </button>
                    </form>
                    <button type="button" onclick="hideLogoutModal()" class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #logout-modal.show #logout-modal-content { animation: modalSlideUp 0.3s ease-out forwards; }
    @keyframes modalSlideUp {
        from { opacity: 0; transform: scale(0.95) translateY(16px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    #logout-modal.hiding #logout-modal-content { animation: modalSlideDown 0.3s ease-out forwards; }
    @keyframes modalSlideDown {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.95) translateY(16px); }
    }
    #logout-modal { position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; }
    #logout-modal-content { position: relative !important; z-index: 10001 !important; }
</style>

<script>
    function showLogoutModal() {
        var modal = document.getElementById('logout-modal');
        var content = document.getElementById('logout-modal-content');
        if (!modal || !content) return;
        modal.classList.remove('hidden', 'hiding');
        modal.classList.add('show');
        modal.offsetHeight;
        setTimeout(function() { content.classList.add('show'); }, 10);
        document.body.style.overflow = 'hidden';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    function hideLogoutModal() {
        var modal = document.getElementById('logout-modal');
        var content = document.getElementById('logout-modal-content');
        if (!modal || !content) { document.body.style.overflow = ''; return; }
        modal.classList.add('hiding');
        modal.classList.remove('show');
        content.classList.remove('show');
        setTimeout(function() {
            modal.classList.add('hidden');
            modal.classList.remove('hiding');
            document.body.style.overflow = '';
        }, 300);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var m = document.getElementById('logout-modal');
            if (m && !m.classList.contains('hidden')) hideLogoutModal();
        }
    });
</script>
