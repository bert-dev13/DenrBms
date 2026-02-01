<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
        'remember' => 'boolean'
    ]);

    // For demonstration purposes, we'll return a mock response
    // In a real application, you would use Laravel's authentication system
    if ($credentials['email'] === 'admin@denr.gov.ph' && $credentials['password'] === 'password') {
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => 1,
                'name' => 'DENR Administrator',
                'email' => 'admin@denr.gov.ph',
                'role' => 'admin'
            ],
            'token' => 'mock-jwt-token-' . time(),
            'redirect' => '/dashboard'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
        'errors' => [
            'email' => 'Invalid email or password',
            'password' => 'Invalid email or password'
        ]
    ], 401);
});

// Protected Areas API Routes
Route::get('/protected-areas/data/{id}', function ($id) {
    try {
        $area = \App\Models\ProtectedArea::withCount('speciesObservations')->find($id);
        
        if (!$area) {
            return response()->json([
                'success' => false,
                'error' => 'Protected area not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'area' => $area
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to load protected area data: ' . $e->getMessage()
        ], 500);
    }
});

// Get site names for a protected area
Route::get('/species-observations/site-names/{protectedAreaId}', function ($protectedAreaId) {
    try {
        $protectedArea = \App\Models\ProtectedArea::find($protectedAreaId);
        
        if (!$protectedArea) {
            return response()->json([
                'success' => false,
                'error' => 'Protected area not found'
            ], 404);
        }

        $siteNames = \App\Models\SiteName::where('protected_area_id', $protectedAreaId)
            ->orderBy('name')
            ->get(['id', 'name', 'station_code']);

        return response()->json([
            'success' => true,
            'site_names' => $siteNames
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to load site names: ' . $e->getMessage()
        ], 500);
    }
});
