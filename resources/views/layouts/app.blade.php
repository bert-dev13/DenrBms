<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- FOUC Prevention Script - Apply theme immediately -->
    <script>
        (function() {
            try {
                // Get stored theme with fallback to system preference
                var storedTheme = localStorage.getItem('denr-bms-theme');
                var theme = storedTheme || 
                          (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                
                // Apply theme IMMEDIATELY to prevent FOUC (before any CSS loads)
                if (theme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark-theme');
                }
                
                // Add no-transition class to prevent initial animations
                // Use inline style to ensure it's applied immediately
                var style = document.createElement('style');
                style.setAttribute('data-no-transition', 'true');
                style.innerHTML = 'body.no-theme-transition, body.no-theme-transition *, body.no-theme-transition *::before, body.no-theme-transition *::after { transition: none !important; }';
                document.head.appendChild(style);
                
                // Add class to body as soon as it's available
                if (document.body) {
                    document.body.classList.add('no-theme-transition');
                    if (theme === 'dark') {
                        document.body.setAttribute('data-theme', 'dark');
                    }
                } else {
                    // Body not ready yet, add it immediately when DOM is constructed
                    document.addEventListener('DOMContentLoaded', function() {
                        document.body.classList.add('no-theme-transition');
                        if (theme === 'dark') {
                            document.body.setAttribute('data-theme', 'dark');
                        }
                    });
                    
                    // Also try to add it immediately using DOMContentLoaded check
                    if (document.readyState === 'loading') {
                        document.addEventListener('readystatechange', function() {
                            if (document.readyState === 'interactive' && document.body) {
                                document.body.classList.add('no-theme-transition');
                                if (theme === 'dark') {
                                    document.body.setAttribute('data-theme', 'dark');
                                }
                            }
                        });
                    }
                }
                
                // Store the initial theme for later use
                window.__initialTheme = theme;
                window.__foucPreventionApplied = true;
            } catch (e) {
                // Silently fail to prevent script errors
                window.__foucPreventionApplied = false;
            }
        })();
    </script>
    
    <title>DENR BMS - @yield('title', 'Dashboard') | Biodiversity Management System</title>

    <!-- Critical: keep sidebar overlay hidden until JS shows it (prevents gray stuck overlay) -->
    <style type="text/css">
        #sidebar-overlay.hidden { display: none !important; visibility: hidden !important; pointer-events: none !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    @vite(['resources/css/shared/app.css', 'resources/css/shared/icons.css', 'resources/css/shared/sidebar.css', 'resources/css/shared/theme.css', 'resources/css/shared/topbar.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/shared/app.js', 'resources/js/shared/bootstrap.js', 'resources/js/shared/icons.js', 'resources/js/shared/sidebar.js', 'resources/js/shared/theme.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('head')
</head>
<body class="antialiased bg-gray-50">
    @include('layouts.sidebar')

    <!-- Top bar: fixed sibling of main (outside main to avoid scroll/stacking quirks) -->
    @include('components.top_bar')

    <!-- Main content: offset by sidebar (desktop) and topbar via .main-content-wrapper -->
    <main class="main-content-wrapper min-h-screen">
        <div class="main-content-wrap p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>window.csrfToken = '{{ csrf_token() }}';</script>

    <!-- Global Success Message Auto-Hide Script -->
    <script>
        // Auto-hide success messages after 1 second
        document.addEventListener('DOMContentLoaded', function() {
            // Find all success messages with specific IDs or classes
            const successMessages = document.querySelectorAll('[id*="success"], .success-message, .alert-success');
            
            successMessages.forEach(function(message) {
                // Add transition class if not already present
                if (!message.classList.contains('transition-opacity')) {
                    message.classList.add('transition-opacity', 'duration-300');
                }
                
                // Auto-hide after 1 second
                setTimeout(function() {
                    message.style.opacity = '0';
                    
                    // Remove from DOM after fade out
                    setTimeout(function() {
                        message.remove();
                    }, 300);
                }, 1000);
            });
        });
    </script>
    
    @stack('scripts')
    @stack('styles')
</body>
</html>
