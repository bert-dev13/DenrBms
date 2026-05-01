{{-- Top Bar – minimalist header: page title left, actions right --}}
<header class="topbar-header" role="banner">
    <div class="topbar-inner">
        <div class="topbar">
            <div class="topbar__left">
                <h1 class="topbar__title">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="topbar__right">
                @include('components.theme-toggle')
            </div>
        </div>
    </div>
</header>
