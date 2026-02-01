<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Login page uses fixed light theme - NO dark mode -->
    <script>
        (function() {
            try {
                // Force light theme for login page - ignore any stored theme
                var theme = 'light';
                
                // Add no-transition class IMMEDIATELY to prevent any animations
                if (document.body) {
                    document.body.classList.add('no-theme-transition');
                } else {
                    // Body not ready yet, add it as soon as it's available
                    (function() {
                        var checkBody = setInterval(function() {
                            if (document.body) {
                                document.body.classList.add('no-theme-transition');
                                clearInterval(checkBody);
                            }
                        }, 1);
                    })();
                }
                
                // Ensure light theme is applied - NEVER apply dark theme to login page
                document.documentElement.removeAttribute('data-theme');
                document.documentElement.classList.remove('dark-theme');
                
                // Store the initial theme for later use (but don't apply it now)
                window.__initialTheme = theme;
                window.__loginPageTheme = 'light'; // Flag to indicate login page
            } catch (e) {
                // Silently fail to prevent script errors
            }
        })();
    </script>
    
    <title>DENR BMS - Login | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles - Login page uses fixed light theme -->
    @vite(['resources/css/app.css', 'resources/css/Login.css'])
    
    <!-- Scripts - NO theme.js for login page -->
    @vite(['resources/js/bootstrap.js', 'resources/js/Login.js'])
</head>
<body class="antialiased">
    
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1 class="login-title">DENR BMS</h1>
                <p class="login-subtitle">Biodiversity Management System</p>
            </div>

            <!-- Login Form -->
            <form class="login-form" action="{{ route('login.submit') }}" method="POST" id="loginFormElement">
                @csrf
                
                <!-- Success and Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input @error('email') error @enderror"
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                    <span class="error-message">
                        @error('email') {{ $message }} @enderror
                    </span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password
                    </label>
                    <div class="password-input-container">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') error @enderror"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            Show
                        </button>
                    </div>
                    <span class="error-message">
                        @error('password') {{ $message }} @enderror
                    </span>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="login-button" id="loginSubmitBtn">
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p class="footer-text">
                    Department of Environment and Natural Resources
                </p>
                <p class="footer-version">
                    Version 1.0.0
                </p>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for password toggle and debugging -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Show';
            }
        }
        
        // Debug function to check form data
        function debugFormData() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            console.log('Current form values:', { email, password });
            console.log('Email trimmed:', email.trim());
            console.log('Email empty check:', !email || email.trim() === '');
        }
        
        // Add debug button temporarily
        document.addEventListener('DOMContentLoaded', function() {
            // Debug form submission
            const form = document.getElementById('loginFormElement');
            if (form) {
                form.addEventListener('submit', function(e) {
                    debugFormData();
                    console.log('Form submitting...');
                });
            }
        });
    </script>
</body>
</html>
