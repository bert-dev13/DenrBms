@extends('layouts.app')

@section('title', 'Settings')
@section('header', 'Settings')

@section('head')
@vite(['resources/css/pages/settings.css', 'resources/js/pages/settings.js'])
@endsection

@section('content')
<div class="settings-page">
    <div class="settings-container">
        @if (session('success'))
        <div id="settings-success-toast" class="settings-toast settings-toast--success" role="alert">
            <i data-lucide="check-circle" class="lucide-icon settings-toast__icon"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Profile Information -->
        <section class="settings-section" aria-labelledby="profile-heading">
            <h2 id="profile-heading" class="settings-section__title">Profile Information</h2>

            @if ($errors->has('name') || $errors->has('email'))
            <div class="settings-alert settings-alert--error" role="alert">
                <i data-lucide="circle-x" class="lucide-icon settings-alert__icon"></i>
                <div class="settings-alert__body">
                    @foreach (['name', 'email'] as $field)
                        @error($field)<p class="mb-0">{{ $message }}</p>@enderror
                    @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('settings.profile.update') }}" method="POST" class="settings-form" id="settings-profile-form">
                @csrf
                <div class="settings-row">
                    <label for="name" class="settings-row__label">Full Name</label>
                    <div class="settings-row__control">
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="settings-input" required autocomplete="name">
                    </div>
                </div>
                <div class="settings-row">
                    <label for="email" class="settings-row__label">Email Address</label>
                    <div class="settings-row__control">
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="settings-input" required autocomplete="email">
                    </div>
                </div>
                <div class="settings-row">
                    <span class="settings-row__label">Role</span>
                    <div class="settings-row__control">
                        <span class="settings-role-badge" aria-label="Your role">{{ ucfirst($user->role ?? 'User') }}</span>
                    </div>
                </div>
                <div class="settings-row settings-row--actions">
                    <div class="settings-row__label"></div>
                    <div class="settings-row__control">
                        <button type="submit" class="settings-btn settings-btn--primary" id="profile-submit-btn">
                            <span class="settings-btn__text">Update Profile</span>
                            <span class="settings-btn__spinner" aria-hidden="true"><i data-lucide="loader-2" class="lucide-icon spin"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <!-- Account Security -->
        <section class="settings-section" aria-labelledby="security-heading">
            <h2 id="security-heading" class="settings-section__title">Account Security</h2>
            <h3 class="settings-section__subtitle">Change Password</h3>

            @if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
            <div class="settings-alert settings-alert--error" role="alert">
                <i data-lucide="circle-x" class="lucide-icon settings-alert__icon"></i>
                <div class="settings-alert__body">
                    @error('current_password')<p class="mb-0">{{ $message }}</p>@enderror
                    @error('password')<p class="mb-0">{{ $message }}</p>@enderror
                    @error('password_confirmation')<p class="mb-0">{{ $message }}</p>@enderror
                </div>
            </div>
            @endif

            <form action="{{ route('settings.password.update') }}" method="POST" class="settings-form" id="settings-password-form">
                @csrf
                <div class="settings-row">
                    <label for="current_password" class="settings-row__label">Current Password</label>
                    <div class="settings-row__control">
                        <div class="settings-password-wrap">
                            <input type="password" id="current_password" name="current_password" class="settings-input settings-input--password" autocomplete="current-password">
                            <button type="button" class="settings-password-toggle" aria-label="Show password" data-target="current_password">
                                <i data-lucide="eye" class="lucide-icon settings-password-toggle__show"></i>
                                <i data-lucide="eye-off" class="lucide-icon settings-password-toggle__hide"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="settings-row">
                    <label for="password" class="settings-row__label">New Password</label>
                    <div class="settings-row__control">
                        <div class="settings-password-wrap">
                            <input type="password" id="password" name="password" class="settings-input settings-input--password" autocomplete="new-password">
                            <button type="button" class="settings-password-toggle" aria-label="Show password" data-target="password">
                                <i data-lucide="eye" class="lucide-icon settings-password-toggle__show"></i>
                                <i data-lucide="eye-off" class="lucide-icon settings-password-toggle__hide"></i>
                            </button>
                        </div>
                        <p class="settings-helper">Minimum 8 characters</p>
                    </div>
                </div>
                <div class="settings-row">
                    <label for="password_confirmation" class="settings-row__label">Confirm New Password</label>
                    <div class="settings-row__control">
                        <div class="settings-password-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="settings-input settings-input--password" autocomplete="new-password">
                            <button type="button" class="settings-password-toggle" aria-label="Show password" data-target="password_confirmation">
                                <i data-lucide="eye" class="lucide-icon settings-password-toggle__show"></i>
                                <i data-lucide="eye-off" class="lucide-icon settings-password-toggle__hide"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="settings-row settings-row--actions">
                    <div class="settings-row__label"></div>
                    <div class="settings-row__control">
                        <button type="submit" class="settings-btn settings-btn--warning" id="password-submit-btn">
                            <span class="settings-btn__text">Update Password</span>
                            <span class="settings-btn__spinner" aria-hidden="true"><i data-lucide="loader-2" class="lucide-icon spin"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
