<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\BackendApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, BackendApiService $apiService): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $apiResponse = $apiService->login($request->email, $request->password);
            
            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                $token = $apiData['token'] ?? null;
                $apiUser = $apiData['data'] ?? $apiData['user'] ?? null;

                if ($apiUser) {
                    // Find or create local user record to keep relation and login integrity
                    $user = User::where('email', $apiUser['email'])->first();
                    
                    if (!$user) {
                        $user = User::create([
                            'email' => $apiUser['email'],
                            'name' => $apiUser['name'],
                            'role' => $apiUser['role'] ?? 'user',
                            'password' => Hash::make($request->password),
                        ]);
                    } else {
                        // Sync role from backend API directly
                        $newRole = $apiUser['role'] ?? $user->role;

                        // Update name, password and role locally
                        $user->update([
                            'name' => $apiUser['name'],
                            'password' => Hash::make($request->password),
                            'role' => $newRole,
                        ]);
                    }

                    // Store backend JWT token to session
                    if ($token) {
                        $request->session()->put('jwt_token', $token);
                    }

                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();

                    // Redirect based on user role for better UX
                    if ($user->role === 'admin') {
                        return redirect()->route('admin.dashboard');
                    } elseif ($user->role === 'narasumber') {
                        return redirect()->route('narasumber.dashboard');
                    }

                    return redirect()->to('/');
                } else {
                    return back()->withErrors(['email' => 'Data user dari backend tidak valid.'])->withInput();
                }
            } else {
                $msg = $apiResponse->json('message') ?? 'Email atau password salah pada server backend.';
                return back()->withErrors(['email' => $msg])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal terhubung ke server backend: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Clear all parwa detail caches on logout
        \App\Services\ParwaCacheService::invalidateAll();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
