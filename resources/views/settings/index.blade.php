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
                
                // Apply theme immediately to prevent FOUC
                if (theme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark-theme');
                }
                
                // Store the initial theme for later use
                window.__initialTheme = theme;
            } catch (e) {
                // Silently fail to prevent script errors
            }
        })();
    </script>
    
    <title>DENR BMS - Settings | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/theme.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/theme.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
    </script>
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
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Settings</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        @include('components.theme-toggle')
                        
                        <span class="text-sm text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            {{ now()->format('M j, Y g:i A') }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Settings Content -->
        <main class="p-6">
            <!-- Success Toast Popup -->
            @if (session('success'))
                <div id="success-toast" class="fixed top-4 right-4 z-50 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 max-w-sm mx-4" style="transform: translateX(calc(100% + 1rem));">
                    <div class="flex items-center min-w-0">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium text-sm truncate">{{ session('success') }}</span>
                    </div>
                </div>
                
                <script>
                    // Show toast popup and auto-hide after 3 seconds
                    document.addEventListener('DOMContentLoaded', function() {
                        const toast = document.getElementById('success-toast');
                        
                        if (toast) {
                            // Slide in from right with proper positioning
                            setTimeout(function() {
                                toast.style.transform = 'translateX(0)';
                            }, 100);
                            
                            // Auto-hide after 3 seconds (longer for better readability)
                            setTimeout(function() {
                                toast.style.transform = 'translateX(calc(100% + 1rem))';
                                
                                // Remove from DOM after slide out
                                setTimeout(function() {
                                    toast.remove();
                                }, 300);
                            }, 3000);
                        }
                    });
                </script>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6">
                <!-- Profile Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Settings</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('settings.profile.update') }}" method="POST">
                            @csrf
                            
                            <!-- User Information -->
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">User Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" id="name" name="name" value="{{ $user->name }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" id="email" name="email" value="{{ $user->email }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <input type="text" id="role" value="{{ ucfirst($user->role ?? 'User') }}" readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Account Settings</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('settings.password.update') }}" method="POST">
                            @csrf
                            
                            <!-- Change Password -->
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Change Password</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                        <input type="password" id="password" name="password" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Export settings function
        function exportSettings() {
            const settings = {
                profile: {
                    name: '{{ $user->name }}',
                    email: '{{ $user->email }}',
                    role: '{{ $user->role ?? "User" }}'
                },
                preferences: {
                    notifications_enabled: {{ session('preferences.notifications_enabled', true) ? 'true' : 'false' }},
                    dark_mode: {{ session('preferences.dark_mode', false) ? 'true' : 'false' }}
                }
            };
            
            const dataStr = JSON.stringify(settings, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
            
            const exportFileDefaultName = 'settings_' + new Date().toISOString().slice(0, 10) + '.json';
            
            const linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();
        }

        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                // Create wrapper container for proper positioning
                const wrapper = document.createElement('div');
                wrapper.className = 'relative';
                wrapper.style.display = 'flex';
                wrapper.style.alignItems = 'center';
                wrapper.style.position = 'relative';
                
                // Get the current container and move input inside wrapper
                const container = input.parentElement;
                container.insertBefore(wrapper, input);
                wrapper.appendChild(input);
                
                // Add right padding to input to prevent text overlap
                input.style.paddingRight = '2.5rem';
                input.style.width = '100%';
                
                // Create toggle button with proper centering
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'absolute right-2 flex items-center justify-center text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700';
                toggleBtn.style.width = '1.25rem';
                toggleBtn.style.height = '1.25rem';
                toggleBtn.style.top = '50%';
                toggleBtn.style.transform = 'translateY(-50%)';
                toggleBtn.style.zIndex = '10';
                toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
                
                // Set initial icon
                toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                
                // Add button to wrapper
                wrapper.appendChild(toggleBtn);
                
                // Handle click events
                toggleBtn.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    if (type === 'text') {
                        toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
                        toggleBtn.setAttribute('aria-label', 'Hide password');
                    } else {
                        toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                        toggleBtn.setAttribute('aria-label', 'Show password');
                    }
                });
                
                // Ensure proper focus handling
                toggleBtn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                });
            });
        });
    </script>
</body>
</html>
