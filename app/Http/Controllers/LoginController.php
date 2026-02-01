<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the login request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
            
            if ($request->expectsJson()) {
                return response()->json($response, 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Get the authenticated user
            $user = Auth::user();
            
            // Update login statistics
            $user->update([
                'last_login_at' => now(),
                'login_count' => $user->login_count + 1,
            ]);
            
            // Check if user is active
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $response = [
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact administrator.'
                ];
                
                if ($request->expectsJson()) {
                    return response()->json($response, 403);
                }

                return redirect()->back()
                    ->with('error', 'Your account has been deactivated. Please contact administrator.')
                    ->withInput($request->except('password'));
            }

            // Log successful login (simplified)
            \Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            // Prepare success response
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'redirect' => route('dashboard'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response);
            }

            // Redirect to intended page or dashboard
            return redirect()->intended(route('dashboard'));
        }

        // Authentication failed
        $response = [
            'success' => false,
            'message' => 'Invalid email or password. Please try again.'
        ];
        
        if ($request->expectsJson()) {
            return response()->json($response, 401);
        }

        return redirect()->back()
            ->with('error', 'Invalid email or password. Please try again.')
            ->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout if user was authenticated (simplified)
        if ($user) {
            \Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the dashboard after successful login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard', [
            'user' => $user,
            'recentActivity' => $this->getRecentActivity($user),
            'systemStats' => $this->getSystemStats(),
        ]);
    }

    /**
     * Get recent activity for the user.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getRecentActivity($user)
    {
        // You can implement this based on your activity logging system
        return [
            'last_login' => $user->last_login_at ?? 'Never',
            'total_logins' => $user->login_count ?? 0,
        ];
    }

    /**
     * Get system statistics for dashboard.
     *
     * @return array
     */
    private function getSystemStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'system_version' => '1.0.0',
        ];
    }
}
