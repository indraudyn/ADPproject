<?php

namespace App\Http\Controllers;

use App\Services\BackendApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(BackendApiService $apiService)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $apiResponse = $apiService->getProfile();
            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                $apiUser = $apiData['data'] ?? $apiData['user'] ?? null;
                
                if ($apiUser) {
                    $user->update([
                        'name' => $apiUser['name'],
                        'email' => $apiUser['email'],
                        'role' => $apiUser['role'] ?? $user->role, // sync role if present
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Backend API profile fetch failed, using local session: " . $e->getMessage());
        }

        return view('profile');
    }

    public function update(Request $request, BackendApiService $apiService)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $apiResponse = $apiService->updateProfile([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if (!$apiResponse->successful()) {
                $msg = $apiResponse->json('message') ?? 'Gagal memperbarui profil di server backend.';
                return back()->withErrors(['email' => $msg])->withInput();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Backend API profile update failed, syncing locally only: " . $e->getMessage());
        }

        $user->name  = $request->name;
        $user->email = $request->email;

        // Handle cropped photo from base64 (Cropper.js)
        if ($request->filled('photo_data')) {
            $data = $request->input('photo_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (in_array($type, ['jpg', 'jpeg', 'png'])) {
                    $data = base64_decode($data);

                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }

                    $filename = 'profile/' . uniqid() . '.' . $type;
                    Storage::disk('public')->put($filename, $data);
                    $user->photo = $filename;
                }
            }
        } 
        // Fallback to standard file upload
        elseif ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('profile', 'public');
        }

        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function destroyPhoto(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->photo = null;
            $user->save();
        }

        return redirect()->route('profile');
    }
}
