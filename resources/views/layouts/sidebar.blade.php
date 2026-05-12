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
                <a href="{{ route('reports.species-activity') }}" class="sidebar__nav-item sidebar__nav-item--sub {{ request()->routeIs('reports.species-activity*') ? 'sidebar__nav-item--active' : '' }}" data-tooltip="Observation Rankings">
                    <i data-lucide="activity" class="lucide-icon sidebar__nav-icon" stroke-width="1.75"></i>
                    <span class="sidebar__nav-label">Observation Rankings</span>
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
        <button type="button" class="sidebar__logout-btn" onclick="openLogoutConfirmModal()" aria-label="Logout" data-tooltip="Logout">
            <i data-lucide="log-out" class="lucide-icon sidebar__nav-icon"></i>
            <span class="sidebar__nav-label">Logout</span>
        </button>
    </div>
</aside>

{{-- Logout confirmation — rectangular layout, lc-* namespace (replaces prior modal) --}}
<div id="lc-logout-root" class="lc-root fixed inset-0 z-[9999] hidden" aria-hidden="true">
    <div
        id="lc-logout-backdrop"
        class="lc-backdrop"
        onclick="closeLogoutConfirmModal()"
        aria-hidden="true"
    ></div>
    <div class="lc-shell">
        <div
            id="lc-logout-dialog"
            class="lc-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="lc-logout-title"
            aria-describedby="lc-logout-desc"
            tabindex="-1"
        >
            <div class="lc-dialog__inner">
                <div class="lc-dialog__iconBadge" aria-hidden="true">
                    <i data-lucide="alert-triangle" class="lucide-icon lc-dialog__iconSvg" stroke-width="2"></i>
                </div>
                <div class="lc-dialog__main">
                    <h2 id="lc-logout-title" class="lc-dialog__title">Confirm Logout</h2>
                    <p id="lc-logout-desc" class="lc-dialog__text">
                        Are you sure you want to logout? You will need to sign in again to access the system.
                    </p>
                </div>
                <div class="lc-dialog__actions">
                    <button
                        id="lc-logout-cancel"
                        type="button"
                        onclick="closeLogoutConfirmModal()"
                        class="lc-btn lc-btn--cancel"
                    >
                        Cancel
                    </button>
                    <form id="lc-logout-form" action="{{ route('logout') }}" method="POST" class="lc-dialog__form" onsubmit="return lcHandleLogoutSubmit(event)">
                        @csrf
                        <button
                            id="lc-logout-submit"
                            type="submit"
                            class="lc-btn lc-btn--logout"
                        >
                            <span class="lc-logout-submit__label">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .lc-root {
        position: fixed !important;
        inset: 0 !important;
    }
    .lc-backdrop {
        position: absolute;
        inset: 0;
        z-index: 0;
        background: rgba(15, 23, 42, 0.48);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .lc-root.lc-root--open .lc-backdrop {
        opacity: 1;
    }
    .lc-shell {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        pointer-events: none;
    }
    .lc-dialog {
        pointer-events: auto;
        position: relative;
        z-index: 10001;
        width: 100%;
        max-width: min(22rem, calc(100vw - 2rem));
        margin-inline: auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow:
            0 1px 2px rgba(15, 23, 42, 0.04),
            0 12px 32px -8px rgba(15, 23, 42, 0.14);
        transform-origin: center center;
        opacity: 0;
        transform: scale(0.97) scaleX(0.94);
        transition:
            opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1),
            transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
    }
    .lc-root.lc-root--open .lc-dialog {
        opacity: 1;
        transform: scale(1) scaleX(1);
    }
    .lc-dialog__inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1.1rem 1.15rem 1.05rem;
        gap: 0.65rem;
    }
    .lc-dialog__iconBadge {
        width: 2.875rem;
        height: 2.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #ea580c;
        border-radius: 10px;
    }
    .lc-dialog__iconSvg {
        width: 1.35rem;
        height: 1.35rem;
    }
    .lc-dialog__main {
        width: 100%;
        max-width: 19rem;
        margin-inline: auto;
    }
    .lc-dialog__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: -0.02em;
        color: #0f172a;
        line-height: 1.3;
    }
    .lc-dialog__text {
        margin: 0.4rem 0 0;
        font-size: 0.8125rem;
        line-height: 1.5;
        color: #475569;
    }
    .lc-dialog__actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 0.55rem;
        margin-top: 0.35rem;
        width: 100%;
    }
    .lc-dialog__form {
        margin: 0;
        display: inline-block;
    }
    .lc-btn {
        min-height: 2.35rem;
        padding: 0 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition:
            background-color 0.2s ease,
            border-color 0.2s ease,
            color 0.2s ease,
            box-shadow 0.2s ease,
            transform 0.18s ease,
            opacity 0.2s ease;
    }
    .lc-btn:focus-visible {
        outline: 2px solid #64748b;
        outline-offset: 2px;
    }
    .lc-btn--cancel {
        background: #fff;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-weight: 500;
    }
    .lc-btn--cancel:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }
    .lc-btn--logout {
        background: #dc2626;
        color: #fff;
        border: 1px solid #dc2626;
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.25);
    }
    .lc-btn--logout:hover:not(:disabled) {
        background: #b91c1c;
        border-color: #b91c1c;
        box-shadow: 0 2px 6px rgba(185, 28, 28, 0.35);
        transform: translateY(-1px);
    }
    .lc-btn--logout:active:not(:disabled) {
        transform: translateY(0);
    }
    .lc-btn--cancel:active:not(:disabled) {
        transform: scale(0.98);
    }
    .lc-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none !important;
    }
    [data-theme="dark"] .lc-dialog {
        background: #1e293b;
        border-color: rgba(51, 65, 85, 0.95);
        box-shadow:
            0 0 0 1px rgba(148, 163, 184, 0.06),
            0 16px 40px -10px rgba(0, 0, 0, 0.5);
    }
    [data-theme="dark"] .lc-dialog__title {
        color: #f1f5f9;
    }
    [data-theme="dark"] .lc-dialog__text {
        color: #94a3b8;
    }
    [data-theme="dark"] .lc-dialog__iconBadge {
        background: rgba(234, 88, 12, 0.12);
        border-color: rgba(251, 146, 60, 0.35);
        color: #fb923c;
    }
    [data-theme="dark"] .lc-btn--cancel {
        background: #334155;
        border-color: #475569;
        color: #e2e8f0;
    }
    [data-theme="dark"] .lc-btn--cancel:hover:not(:disabled) {
        background: #475569;
        border-color: #64748b;
    }
    [data-theme="dark"] .lc-btn--logout:focus-visible,
    [data-theme="dark"] .lc-btn--cancel:focus-visible {
        outline-color: #94a3b8;
    }
    @media (max-width: 520px) {
        .lc-dialog__inner {
            padding: 1rem 1rem 0.95rem;
        }
        .lc-dialog__actions {
            flex-direction: column;
            align-items: center;
            max-width: 14rem;
            margin-left: auto;
            margin-right: auto;
        }
        .lc-dialog__form {
            display: flex;
            width: 100%;
            justify-content: center;
        }
        .lc-btn {
            width: 100%;
            min-width: 0;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .lc-backdrop,
        .lc-dialog,
        .lc-btn {
            transition-duration: 0.01ms !important;
        }
        .lc-btn--logout:hover:not(:disabled),
        .lc-btn--cancel:active:not(:disabled) {
            transform: none !important;
        }
    }
</style>

<script>
    var lcLogoutSubmitBusy = false;
    var lcLogoutPrevFocus = null;
    var lcLogoutCloseTimer = null;
    var LC_LOGOUT_ANIM_MS = 240;

    function openLogoutConfirmModal() {
        var root = document.getElementById('lc-logout-root');
        var dialog = document.getElementById('lc-logout-dialog');
        if (!root || !dialog) return;
        if (!root.classList.contains('hidden')) {
            try { dialog.focus(); } catch (err) {}
            return;
        }
        if (lcLogoutCloseTimer) {
            clearTimeout(lcLogoutCloseTimer);
            lcLogoutCloseTimer = null;
        }
        lcLogoutPrevFocus = document.activeElement;
        root.classList.remove('hidden');
        root.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        void root.offsetWidth;
        requestAnimationFrame(function() {
            root.classList.add('lc-root--open');
            if (typeof window.replaceLucideIcons === 'function') {
                window.replaceLucideIcons(root);
            } else if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        });
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                var cancelBtn = document.getElementById('lc-logout-cancel');
                if (cancelBtn && typeof cancelBtn.focus === 'function') {
                    try { cancelBtn.focus(); } catch (e) {}
                }
            });
        });
    }

    function closeLogoutConfirmModal() {
        var root = document.getElementById('lc-logout-root');
        var dialog = document.getElementById('lc-logout-dialog');
        if (!root || !dialog) {
            document.body.style.overflow = '';
            return;
        }
        if (lcLogoutSubmitBusy) {
            return;
        }
        if (root.classList.contains('hidden')) {
            return;
        }
        root.classList.remove('lc-root--open');
        root.setAttribute('aria-hidden', 'true');
        if (lcLogoutCloseTimer) {
            clearTimeout(lcLogoutCloseTimer);
        }
        lcLogoutCloseTimer = setTimeout(function() {
            lcLogoutCloseTimer = null;
            root.classList.add('hidden');
            document.body.style.overflow = '';
            if (lcLogoutPrevFocus && typeof lcLogoutPrevFocus.focus === 'function') {
                try { lcLogoutPrevFocus.focus(); } catch (err) {}
            }
            lcLogoutPrevFocus = null;
        }, LC_LOGOUT_ANIM_MS);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        if (lcLogoutSubmitBusy) return;
        var root = document.getElementById('lc-logout-root');
        if (root && !root.classList.contains('hidden')) {
            e.preventDefault();
            closeLogoutConfirmModal();
        }
    });

    async function lcHandleLogoutSubmit(event) {
        if (event) {
            event.preventDefault();
        }
        if (lcLogoutSubmitBusy) {
            return false;
        }
        var form = document.getElementById('lc-logout-form');
        var submitBtn = document.getElementById('lc-logout-submit');
        var cancelBtn = document.getElementById('lc-logout-cancel');
        if (!form) {
            return false;
        }
        lcLogoutSubmitBusy = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            var label = submitBtn.querySelector('.lc-logout-submit__label');
            if (label) {
                label.textContent = 'Logging out...';
            } else {
                submitBtn.textContent = 'Logging out...';
            }
        }
        if (cancelBtn) {
            cancelBtn.disabled = true;
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
            if (response.ok || response.redirected) {
                window.location.href = "{{ route('login') }}";
                return false;
            }
        } catch (e) {
            /* fallback below */
        }
        form.removeAttribute('onsubmit');
        form.submit();
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var root = document.getElementById('lc-logout-root');
        if (root) {
            root.classList.remove('lc-root--open');
            root.classList.add('hidden');
        }
        document.body.style.overflow = '';
        if (lcLogoutCloseTimer) {
            clearTimeout(lcLogoutCloseTimer);
            lcLogoutCloseTimer = null;
        }
    });
</script>
