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
                var theme = 'light';
                if (document.body) {
                    document.body.classList.add('no-theme-transition');
                } else {
                    var checkBody = setInterval(function() {
                        if (document.body) {
                            document.body.classList.add('no-theme-transition');
                            clearInterval(checkBody);
                        }
                    }, 1);
                }
                document.documentElement.removeAttribute('data-theme');
                document.documentElement.classList.remove('dark-theme');
                window.__initialTheme = theme;
                window.__loginPageTheme = 'light';
            } catch (e) {}
        })();
    </script>
    
    <title>DENR BMS - Login | Biodiversity Management System</title>

    <!-- Fonts - Poppins for modern government-grade typography -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/icons.css', 'resources/css/Login.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/icons.js', 'resources/js/Login.js'])
</head>
<body class="antialiased login-page">
    
    <div class="login-container" role="main" style="--login-bg-image: url('{{ asset('images/background-denr.webp') }}');">
        <div class="login-overlay" aria-hidden="true"></div>
        <div class="login-content">
            <div class="login-card">
                <!-- Branding -->
                <header class="login-header">
                    <div class="login-logo-wrap" aria-hidden="true">
                        <img src="{{ asset('images/denr-logo.png') }}" alt="" class="login-logo" width="72" height="72" />
                    </div>
                    <h1 class="login-title">DENR BMS</h1>
                    <p class="login-subtitle">Biodiversity Management System</p>
                </header>

                <!-- Login Form -->
                <form class="login-form" action="{{ route('login.submit') }}" method="POST" id="loginFormElement" novalidate>
                @csrf
                
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-error" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-error" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true"><i data-lucide="mail" class="lucide-icon"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') error @enderror"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            aria-required="true"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            aria-describedby="email-error"
                        >
                    </div>
                    <span id="email-error" class="error-message" role="alert">
                        @error('email') {{ $message }} @enderror
                    </span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true"><i data-lucide="lock" class="lucide-icon"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') error @enderror"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            aria-required="true"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            aria-describedby="password-error password-toggle-desc"
                        >
                        <button 
                            type="button" 
                            class="password-toggle" 
                            aria-label="Toggle password visibility"
                            id="password-toggle"
                            aria-pressed="false"
                        >
                            <span class="eye-icon"><i data-lucide="eye" class="lucide-icon" aria-hidden="true"></i></span>
                            <span class="eye-off-icon"><i data-lucide="eye-off" class="lucide-icon" aria-hidden="true"></i></span>
                        </button>
                    </div>
                    <span id="password-toggle-desc" class="sr-only">Click to show or hide password</span>
                    <span id="password-error" class="error-message" role="alert">
                        @error('password') {{ $message }} @enderror
                    </span>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} aria-describedby="remember-desc">
                        <span class="checkmark"></span>
                        <span id="remember-desc">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="login-button" id="loginSubmitBtn">
                    <span class="login-button-text">Sign In</span>
                </button>
            </form>
            </div>
        </div>
    </div>

</body>
</html>
