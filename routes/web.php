<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpeciesObservationController;
use App\Http\Controllers\ProtectedAreaController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot password routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Dashboard routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/yearly-monitoring', [DashboardController::class, 'getYearlyMonitoringData'])->name('dashboard.yearly-monitoring');
});

// Species observations routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/api/species-observations/data/{id}', [SpeciesObservationController::class, 'getObservationData'])->name('species-observations.data');
    Route::get('/api/species-observations/edit-data/{id}', [SpeciesObservationController::class, 'getObservationForEdit'])->name('species-observations.edit-data');
    Route::get('/api/species-observations/site-names/{protectedAreaId}', [SpeciesObservationController::class, 'getSiteNames'])->name('species-observations.site-names');
    
    Route::get('/species-observations', [SpeciesObservationController::class, 'index'])->name('species-observations.index');
    Route::post('/species-observations', [SpeciesObservationController::class, 'store'])->name('species-observations.store');
    Route::get('/species-observations/{speciesObservation}', [SpeciesObservationController::class, 'show'])->name('species-observations.show');
    Route::get('/species-observations/{speciesObservation}/edit', [SpeciesObservationController::class, 'edit'])->name('species-observations.edit');
    Route::put('/species-observations/{speciesObservation}', [SpeciesObservationController::class, 'update'])->name('species-observations.update');
    Route::delete('/species-observations/{speciesObservation}', [SpeciesObservationController::class, 'destroy'])->name('species-observations.destroy');
});

// Protected areas routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/protected-areas', [ProtectedAreaController::class, 'index'])->name('protected-areas.index');
    Route::post('/protected-areas', [ProtectedAreaController::class, 'store'])->name('protected-areas.store');
    Route::get('/protected-area-sites', [ProtectedAreaController::class, 'sites'])->name('protected-area-sites.index');
    Route::get('/protected-areas/{protectedAreaId}/site-names', [SpeciesObservationController::class, 'getSiteNames'])->name('protected-areas.site-names');
    Route::get('/protected-areas/{protectedAreaId}/sites', [SpeciesObservationController::class, 'getSiteNames'])->name('protected-areas.sites');
    Route::get('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'show'])->name('protected-areas.show');
    Route::get('/protected-areas/{protectedArea}/edit', [ProtectedAreaController::class, 'edit'])->name('protected-areas.edit');
    Route::put('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'update'])->name('protected-areas.update');
    Route::delete('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'destroy'])->name('protected-areas.destroy');
    Route::get('/api/protected-areas/{id}', [ProtectedAreaController::class, 'getProtectedAreaData'])->name('protected-areas.data');
    
    // Protected area sites routes
    Route::post('/protected-area-sites', [ProtectedAreaController::class, 'storeSite'])->name('protected-area-sites.store');
    Route::get('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'showSite'])->name('protected-area-sites.show');
    Route::get('/protected-area-sites/{siteName}/edit', [ProtectedAreaController::class, 'editSite'])->name('protected-area-sites.edit');
    Route::put('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'updateSite'])->name('protected-area-sites.update');
    Route::delete('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'destroySite'])->name('protected-area-sites.destroy');
    Route::get('/api/protected-area-sites/{id}', [ProtectedAreaController::class, 'getSiteData'])->name('protected-area-sites.data');
});

// Analytics routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'getObservationData'])->name('analytics.data');
    Route::get('/analytics/species-trends', [AnalyticsController::class, 'getTopSpeciesTrends'])->name('analytics.species-trends');
    Route::get('/analytics/species-trend-data', [AnalyticsController::class, 'getSpeciesTrendData'])->name('analytics.species-trend-data');
});

// Reports routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Settings routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
});
