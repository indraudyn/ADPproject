<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, \App\Services\BackendApiService $apiService)
    {
        $search = $request->search;
        $users = collect();

        try {
            $response = $apiService->getAdminUsers(1, 100);
            if ($response->successful()) {
                $apiData = $response->json();
                $usersData = $apiData['users'] ?? [];
                $users = collect($usersData)->map(fn($u) => (object)$u);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil users dari backend untuk list: " . $e->getMessage());
            // Fallback
            $users = User::orderBy('name')->get();
        }

        if ($search) {
            $users = $users->filter(function ($u) use ($search) {
                return stripos($u->name, $search) !== false || stripos($u->email, $search) !== false;
            });
        }

        // Paginate manually
        $currentPage = request('page', 1);
        $perPage = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->forPage($currentPage, $perPage)->values(),
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $users = $paginated;

        return view('admin.users.index', compact('users', 'search'));
    }

    public function updateRole(Request $request, $id, \App\Services\BackendApiService $apiService)
    {
        $request->validate([
            'role' => 'required|in:admin,user,narasumber'
        ]);

        \Illuminate\Support\Facades\Log::info("updateRole called: id={$id}, requested_role={$request->role}");

        // Find the email of the backend user with ID = $id
        $email = null;
        try {
            $resp = $apiService->getAdminUsers(1, 100);
            if ($resp->successful()) {
                $usersList = $resp->json()['users'] ?? [];
                foreach ($usersList as $u) {
                    if ($u['id'] == $id) {
                        $email = $u['email'] ?? null;
                        break;
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning("Gagal mengambil users list dari backend: Status " . $resp->status());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mencari email backend user: " . $e->getMessage());
        }

        \Illuminate\Support\Facades\Log::info("Found email for backend user: '{$email}'");

        // 1. Update on backend API directly using the backend ID ($id)
        try {
            $apiResp = $apiService->updateAdminUser($id, ['role' => $request->role]);
            \Illuminate\Support\Facades\Log::info("Backend updateAdminUser result: Status " . $apiResp->status());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memperbarui role di backend: " . $e->getMessage());
        }

        // 2. Sync locally by email (if user exists locally)
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->role = $request->role;
                $user->save();
                \Illuminate\Support\Facades\Log::info("Local user role synced to '{$request->role}' for email '{$email}'");
            } else {
                \Illuminate\Support\Facades\Log::warning("Local user not found for email '{$email}'");
            }
        } else {
            \Illuminate\Support\Facades\Log::warning("Local sync skipped: email is null");
        }

        return back()->with('success', 'Role berhasil diubah');
    }

    public function destroy($id, \App\Services\BackendApiService $apiService)
    {
        // Find the email of the backend user with ID = $id
        $email = null;
        try {
            $resp = $apiService->getAdminUsers(1, 100);
            if ($resp->successful()) {
                $usersList = $resp->json()['users'] ?? [];
                foreach ($usersList as $u) {
                    if ($u['id'] == $id) {
                        $email = $u['email'] ?? null;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mencari email backend user untuk delete: " . $e->getMessage());
        }

        // Cegah admin menghapus dirinya sendiri
        if ($email && auth()->user()->email === $email) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 1. Coba hapus di backend API directly using the backend ID ($id)
        try {
            $apiService->deleteAdminUser($id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal menghapus user di backend: " . $e->getMessage());
        }

        // 2. Hapus di database lokal by email
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->delete();
            }
        }

        return back()->with('success', 'User berhasil dihapus.');
    }
}
