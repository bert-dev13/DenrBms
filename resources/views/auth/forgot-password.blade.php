<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>DENR BMS - Forgot Password | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/Login.css'])
</head>
<body class="antialiased">
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1 class="login-title">DENR BMS</h1>
                <p class="login-subtitle">Reset Your Password</p>
            </div>

            <!-- Forgot Password Form -->
            <form class="login-form" action="{{ route('password.email') }}" method="POST">
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
                        placeholder="Enter your registered email address"
                        value="{{ old('email') }}"
                        required
                    >
                    <span class="error-message">
                        @error('email') {{ $message }} @enderror
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Enter your email address and we will send you a link to reset your password.
                    </p>
                </div>

                <button type="submit" class="login-button">
                    Send Reset Link
                </button>
            </form>

            <!-- Back to Login -->
            <div class="login-footer">
                <a href="{{ route('login') }}" class="forgot-password-link">
                    ← Back to Login
                </a>
                <p class="footer-version">
                    Version 1.0.0
                </p>
            </div>
        </div>
    </div>
</body>
</html>
