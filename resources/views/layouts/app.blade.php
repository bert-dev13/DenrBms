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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/theme.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/theme.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="antialiased bg-gray-50">
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle lg:hidden fixed top-4 left-4 z-40 bg-blue-600 text-white p-3 rounded-lg shadow-lg" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('header', 'Dashboard')</h1>
                        @hasSection('breadcrumb')
                            <nav class="flex mt-2" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        @include('components.theme-toggle')
                        
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            Jan 29, 2026 4:30 PM
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
