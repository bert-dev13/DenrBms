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
                @if (file_exists(public_path('images/denr-logo.png')))
                    <img src="{{ asset('images/denr-logo.png') }}" alt="" class="sidebar__logo-img">
                @else
                    <span class="sidebar__logo-img d-inline-flex align-items-center justify-content-center fw-bold">D</span>
                @endif
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
        @if((auth()->user()->role ?? null) === 'admin')
            <a href="{{ route('protected-areas.index') }}" class="sidebar__nav-item {{ request()->routeIs('protected-areas.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Protected Areas">
                <i data-lucide="map-pin" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                <span class="sidebar__nav-label">Protected Areas</span>
            </a>
        @endif
        <a href="{{ route('protected-area-sites.index') }}" class="sidebar__nav-item {{ request()->routeIs('protected-area-sites.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="PA Sites">
            <i data-lucide="map" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
            <span class="sidebar__nav-label">PA Sites</span>
        </a>
        @if((auth()->user()->role ?? null) === 'admin')
            <div class="sidebar__nav-group">
                <div class="sidebar__nav-group-label">Management</div>
                <a href="{{ route('users.index') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('users.*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="User Management">
                    <i data-lucide="users" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                    <span class="sidebar__nav-label">User Management</span>
                </a>
            </div>
        @endif
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
            @if(\App\Support\UserAccess::isPaUser(auth()->user()) || \App\Support\UserAccess::isAdmin(auth()->user()))
                <a href="{{ route('reports.species-activity') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('reports.species-activity*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Species Activity Ranking">
                    <i data-lucide="activity" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                    <span class="sidebar__nav-label">Species Activity</span>
                </a>
            @endif
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
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="flex-1" onsubmit="return handleLogoutSubmit(event)">
                        @csrf
                        <button id="logout-submit-btn" type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Logout
                        </button>
                    </form>
                    <button id="logout-cancel-btn" type="button" onclick="hideLogoutModal()" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #logout-modal { position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; }
    #logout-modal-content { position: relative !important; z-index: 10001 !important; }
    #logout-cancel-btn {
        background-color: #e5e7eb;
        color: #1f2937;
        border: 1px solid #d1d5db;
    }
    #logout-cancel-btn:hover {
        background-color: #d1d5db;
    }
    #logout-cancel-btn:focus {
        --tw-ring-color: #9ca3af;
    }
    [data-theme="dark"] #logout-cancel-btn {
        background-color: #374151;
        color: #f9fafb;
        border-color: #4b5563;
    }
    [data-theme="dark"] #logout-cancel-btn:hover {
        background-color: #4b5563;
    }
</style>

<script>
    var logoutSubmitInProgress = false;

    function showLogoutModal() {
        var modal = document.getElementById('logout-modal');
        var content = document.getElementById('logout-modal-content');
        if (!modal || !content) return;
        modal.classList.remove('hidden');
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
        document.body.style.overflow = 'hidden';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    function hideLogoutModal() {
        var modal = document.getElementById('logout-modal');
        var content = document.getElementById('logout-modal-content');
        if (!modal || !content) { document.body.style.overflow = ''; return; }
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(function() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var m = document.getElementById('logout-modal');
            if (m && !m.classList.contains('hidden')) hideLogoutModal();
        }
    });

    async function handleLogoutSubmit(event) {
        if (event) {
            event.preventDefault();
        }

        if (logoutSubmitInProgress) {
            return false;
        }

        var form = document.getElementById('logout-form');
        var submitBtn = document.getElementById('logout-submit-btn');
        if (!form) {
            return false;
        }

        logoutSubmitInProgress = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging out...';
        }

        try {
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
            var response = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            });

            // Logout endpoint may redirect (HTML) or return JSON. Either way,
            // route users back to login after successful request.
            if (response.ok || response.redirected) {
                window.location.href = "{{ route('login') }}";
                return false;
            }
        } catch (e) {
            // Fall through to normal form submission as a robust fallback.
        }

        form.removeAttribute('onsubmit');
        form.submit();
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('logout-modal');
        var content = document.getElementById('logout-modal-content');
        if (modal) {
            modal.classList.add('hidden');
        }
        if (content) {
            content.classList.remove('opacity-100', 'scale-100');
            content.classList.add('opacity-0', 'scale-95');
        }
        document.body.style.overflow = '';
    });
</script>
