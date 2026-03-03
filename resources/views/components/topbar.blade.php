{{-- Top Bar – minimalist header: page title left, actions right --}}
<header class="topbar-header" role="banner">
    <div class="topbar-inner">
        <div class="topbar">
            <div class="topbar__left">
                <h1 class="topbar__title">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="topbar__right">
                <a href="{{ route('settings.index') }}" class="topbar__action topbar__action--icon" aria-label="Profile" title="Profile">
                    <i data-lucide="user" class="lucide-icon topbar-icon" aria-hidden="true"></i>
                </a>
                @include('components.theme-toggle')
            </div>
        </div>
    </div>
</header>
