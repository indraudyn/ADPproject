<?php   

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cerita;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request, \App\Services\BackendApiService $apiService)
    {
        // 1. TOTAL USER & USER LIST FROM BACKEND API
        $totalUser = 0;
        $users = collect();

        try {
            $response = $apiService->getAdminUsers(1, 100);
            if ($response->successful()) {
                $apiData = $response->json();
                $totalUser = $apiData['total'] ?? 0;
                $usersData = $apiData['users'] ?? [];
                $users = collect($usersData)->map(fn($u) => (object)$u);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil users dari backend untuk dashboard: " . $e->getMessage());
            // Fallback to local
            $totalUser = User::count();
            $users = User::latest()->get();
        }

        // 2. STATUS CERITA (SEMUA USER) - Diambil murni dari backend API (getAdminUploads) dan fallback lokal
        $backendCeritaApproved = 0;
        $backendCeritaPending = 0;
        $backendCeritaUnapproved = 0;

        try {
            $response = $apiService->getAdminUploads();
            if ($response->successful()) {
                $items = collect($response->json()['items'] ?? []);
                $backendCeritaApproved = $items->filter(fn($item) => ($item['status'] ?? 'approved') === 'approved')->count();
                $backendCeritaPending = $items->filter(fn($item) => ($item['status'] ?? '') === 'pending')->count();
                $backendCeritaUnapproved = $items->filter(fn($item) => in_array($item['status'] ?? '', ['unapproved', 'rejected']))->count();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil total parwa cerita dari backend: " . $e->getMessage());
        }

        // Akumulasikan cerita lokal
        $localApproved = 0;
        $localPending = 0;
        $localUnapproved = 0;
        try {
            $localApproved = \App\Models\Cerita::where('status', 'approved')->count();
            $localPending = \App\Models\Cerita::where('status', 'pending')->count();
            $localUnapproved = \App\Models\Cerita::where('status', 'unapproved')->count();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil count cerita lokal: " . $e->getMessage());
        }

        // 3. Local Videos (all)
        $videoApproved = 0;
        $videoPending = 0;
        $videoRejected = 0;
        try {
            $videoApproved = \App\Models\Video::where('status', 'approved')->count();
            $videoPending = \App\Models\Video::where('status', 'pending')->count();
            $videoRejected = \App\Models\Video::where('status', 'rejected')->count();
        } catch (\Exception $e) {}

        // 4. Local Audios (all)
        $audioApproved = 0;
        $audioPending = 0;
        $audioRejected = 0;
        try {
            $audioApproved = \App\Models\Audio::where('status', 'approved')->count();
            $audioPending = \App\Models\Audio::where('status', 'pending')->count();
            $audioRejected = \App\Models\Audio::where('status', 'rejected')->count();
        } catch (\Exception $e) {}

        // Calculate final counts (Cerita + Video + Audio)
        $approvedCount = $backendCeritaApproved + $localApproved + $videoApproved + $audioApproved;
        $pendingCount    = $backendCeritaPending + $localPending + $videoPending + $audioPending;
        $unapprovedCount = $backendCeritaUnapproved + $localUnapproved + $videoRejected + $audioRejected;

        // 3. DATA USER UNTUK TABEL (DENGAN FILTRASI SEARCH)
        $search = $request->query('search');
        if ($search) {
            $users = $users->filter(function ($u) use ($search) {
                return stripos($u->name, $search) !== false || stripos($u->email, $search) !== false;
            });
            $totalUser = $users->count();
        }

        return view('dashboard.admin', compact(
            'totalUser',
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'users'
        ));
    }
}
