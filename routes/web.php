<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProtectedAreaController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpeciesActivityController;
use App\Http\Controllers\SpeciesObservationController;
use App\Http\Controllers\SpeciesRankingController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

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
Route::middleware(['auth', 'pa.scope'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/yearly-monitoring', [DashboardController::class, 'getYearlyMonitoringData'])->name('dashboard.yearly-monitoring');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/species', [AnalyticsController::class, 'species'])->name('analytics.species.index');
    Route::get('/analytics/export/excel', [AnalyticsController::class, 'exportExcel'])->name('analytics.export.excel');
});

// Species observations routes (protected)
Route::middleware(['auth', 'pa.scope'])->group(function () {
    Route::get('/api/species-observations/data/{id}', [SpeciesObservationController::class, 'getObservationData'])->name('species-observations.data');
    Route::get('/api/species-observations/edit-data/{id}', [SpeciesObservationController::class, 'getObservationForEdit'])->name('species-observations.edit-data');
    Route::get('/api/species-observations/site-names/{protectedAreaId}', [SpeciesObservationController::class, 'getSiteNames'])->name('species-observations.site-names');

    Route::get('/species-observations', [SpeciesObservationController::class, 'index'])->name('species-observations.index');
    Route::post('/species-observations', [SpeciesObservationController::class, 'store'])->name('species-observations.store');
    Route::get('/species-observations/{id}', [SpeciesObservationController::class, 'show'])->name('species-observations.show');
    Route::get('/species-observations/{id}/edit', [SpeciesObservationController::class, 'edit'])->name('species-observations.edit');
    Route::put('/species-observations/{id}', [SpeciesObservationController::class, 'update'])->name('species-observations.update');
    Route::delete('/species-observations/{id}', [SpeciesObservationController::class, 'destroy'])->name('species-observations.destroy');
});

// Protected areas routes (admin only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/protected-areas', [ProtectedAreaController::class, 'index'])->name('protected-areas.index');
    Route::post('/protected-areas', [ProtectedAreaController::class, 'store'])->name('protected-areas.store');
    Route::get('/protected-areas/{protectedAreaId}/site-names', [SpeciesObservationController::class, 'getSiteNames'])->name('protected-areas.site-names');
    Route::get('/protected-areas/{protectedAreaId}/sites', [SpeciesObservationController::class, 'getSiteNames'])->name('protected-areas.sites');
    Route::get('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'show'])->name('protected-areas.show');
    Route::get('/protected-areas/{protectedArea}/edit', [ProtectedAreaController::class, 'edit'])->name('protected-areas.edit');
    Route::put('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'update'])->name('protected-areas.update');
    Route::delete('/protected-areas/{protectedArea}', [ProtectedAreaController::class, 'destroy'])->name('protected-areas.destroy');
    Route::get('/api/protected-areas/{id}', [ProtectedAreaController::class, 'getProtectedAreaData'])->name('protected-areas.data');
});

// Protected area sites routes (protected by PA scope)
Route::middleware(['auth', 'pa.scope'])->group(function () {
    Route::get('/protected-area-sites', [ProtectedAreaController::class, 'sites'])->name('protected-area-sites.index');
    Route::post('/protected-area-sites', [ProtectedAreaController::class, 'storeSite'])->name('protected-area-sites.store');
    Route::get('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'showSite'])->name('protected-area-sites.show');
    Route::get('/protected-area-sites/{siteName}/edit', [ProtectedAreaController::class, 'editSite'])->name('protected-area-sites.edit');
    Route::put('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'updateSite'])->name('protected-area-sites.update');
    Route::delete('/protected-area-sites/{siteName}', [ProtectedAreaController::class, 'destroySite'])->name('protected-area-sites.destroy');
    Route::get('/api/protected-area-sites/{id}', [ProtectedAreaController::class, 'getSiteData'])->name('protected-area-sites.data');
});

// Species activity report (admin + PA users; PA data scoped)
Route::middleware(['auth', 'report.user', 'pa.scope'])->group(function () {
    Route::redirect('/reports/endemic-species', '/reports/species-activity');

    Route::get('/reports/species-activity', [SpeciesActivityController::class, 'index'])
        ->name('reports.species-activity');
    Route::get('/reports/species-activity/export/print', [SpeciesActivityController::class, 'exportPrint'])
        ->name('reports.species-activity.export.print');
    Route::get('/reports/species-activity/export/excel', [SpeciesActivityController::class, 'exportExcel'])
        ->name('reports.species-activity.export.excel');
    Route::get('/reports/species-activity/export/pdf', [SpeciesActivityController::class, 'exportPdf'])
        ->name('reports.species-activity.export.pdf');
});

// Reports (protected)
Route::middleware(['auth', 'pa.scope'])->group(function () {
    Route::get('/reports/species-ranking', [SpeciesRankingController::class, 'index'])
        ->name('reports.species-ranking');
    Route::get('/reports/species-ranking/export/print', [SpeciesRankingController::class, 'exportPrint'])
        ->name('reports.species-ranking.export.print');
    Route::get('/reports/species-ranking/export/excel', [SpeciesRankingController::class, 'exportExcel'])
        ->name('reports.species-ranking.export.excel');
    Route::get('/reports/species-ranking/export/pdf', [SpeciesRankingController::class, 'exportPdf'])
        ->name('reports.species-ranking.export.pdf');
});

// Settings routes (protected)
Route::middleware(['auth', 'pa.scope'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/export/excel', [UserManagementController::class, 'exportExcel'])->name('users.export.excel');
    Route::get('/users/export/pdf', [UserManagementController::class, 'exportPdf'])->name('users.export.pdf');
    Route::get('/users/export/print', [UserManagementController::class, 'exportPrint'])->name('users.export.print');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});
