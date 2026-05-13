<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Background photo: add public/images/background-denr.webp or public/images/login-bg.webp (WebP, ~1920px wide, ideally under 300KB). --}}
    @php
        $loginBgCandidates = ['images/background-denr.webp', 'images/login-bg.webp'];
        $loginBgRelative = null;
        foreach ($loginBgCandidates as $rel) {
            if (file_exists(public_path($rel))) {
                $loginBgRelative = $rel;
                break;
            }
        }
        $loginBgUrl = $loginBgRelative ? asset($loginBgRelative) : null;
        $loginHasPhoto = $loginBgUrl !== null;
    @endphp

    @if ($loginHasPhoto)
        <link rel="preload" as="image" href="{{ $loginBgUrl }}" fetchpriority="high">
    @endif

    {{-- First-paint layout for background layer without waiting for Vite CSS (photo is <img>, not CSS background-image). --}}
    <style id="login-critical-bg">
        body.login-page{margin:0;background-color:#e8f2ec}
        .login-container{box-sizing:border-box;position:relative;overflow:hidden;min-height:100vh;min-height:100dvh;display:flex;align-items:center;justify-content:center;padding:24px;background-color:#e8f2ec}
        .login-bg-photo{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;z-index:0;pointer-events:none}
        .login-overlay{position:absolute;inset:0;z-index:1;pointer-events:none}
        .login-content{position:relative;z-index:2;width:100%;display:flex;flex-direction:column;align-items:center;max-width:460px}
    </style>

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

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet"></noscript>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/shared/app.css', 'resources/css/shared/icons.css', 'resources/css/pages/login.css'])

    <!-- Scripts -->
    @vite(['resources/js/shared/bootstrap.js', 'resources/js/shared/icons.js', 'resources/js/pages/login.js'])
</head>
<body class="antialiased login-page">
    <div class="login-container {{ $loginHasPhoto ? 'login-container--photo' : 'login-container--no-photo' }}" role="main">
        @if ($loginHasPhoto)
            <img
                class="login-bg-photo"
                src="{{ $loginBgUrl }}"
                alt=""
                width="1920"
                height="1080"
                decoding="sync"
                fetchpriority="high"
            >
        @endif
        <div class="login-overlay" aria-hidden="true"></div>
        <div class="login-content">
            <div class="login-card">
                <!-- Branding -->
                <header class="login-header">
                    <div class="login-logo-wrap" aria-hidden="true">
                        @if (file_exists(public_path('images/denr-logo.png')))
                            <img src="{{ asset('images/denr-logo.png') }}" alt="" class="login-logo" width="72" height="72" />
                        @else
                            <span class="login-logo d-inline-flex align-items-center justify-content-center fw-bold">DENR</span>
                        @endif
                    </div>
                    <p class="login-kicker">DENR BMS</p>
                    <h1 class="login-title">Biodiversity Management System</h1>
                    <p class="login-subtitle">Secure access for biodiversity monitoring and protected area records.</p>
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
