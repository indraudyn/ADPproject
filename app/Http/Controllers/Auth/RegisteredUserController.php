<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BackendApiService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, BackendApiService $apiService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $apiResponse = $apiService->register($request->name, $request->email, $request->password);
            
            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                $token = $apiData['token'] ?? null;
                $apiUser = $apiData['data'] ?? $apiData['user'] ?? null;

                // Sync local user record using updateOrCreate to prevent duplicate key errors
                $user = User::where('email', $apiUser['email'] ?? $request->email)->first();
                
                if (!$user) {
                    $user = User::create([
                        'email' => $apiUser['email'] ?? $request->email,
                        'name' => $apiUser['name'] ?? $request->name,
                        'password' => Hash::make($request->password),
                        'role' => $apiUser['role'] ?? 'user',
                    ]);
                } else {
                    $newRole = $apiUser['role'] ?? $user->role;

                    $user->update([
                        'name' => $apiUser['name'] ?? $request->name,
                        'password' => Hash::make($request->password),
                        'role' => $newRole,
                    ]);
                }

                if ($token) {
                    $request->session()->put('jwt_token', $token);
                }

                event(new Registered($user));
                Auth::login($user);

                return redirect(route('dashboard', absolute: false));
            } else {
                $msg = $apiResponse->json('message') ?? 'Registrasi di server backend gagal (Status: ' . $apiResponse->status() . ').';
                return back()->withErrors(['email' => $msg])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal terhubung ke server backend: ' . $e->getMessage()])->withInput();
        }
    }
}
