<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Models\Video;
use App\Services\BackendApiService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(BackendApiService $apiService)
    {
        $user = Auth::user();
        $userId = $user->id;

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'narasumber') {
            return redirect()->route('narasumber.dashboard');
        }

        // 1. Dapatkan data Cerita dari backend API
        $backendCeritaApproved = 0;
        $backendCeritaPending = 0;
        $backendCeritaUnapproved = 0;
        $backendCeritas = collect();

        try {
            $response = $apiService->getUserUploads();
            if ($response->successful()) {
                $items = collect($response->json()['items'] ?? []);
                
                // Hitung dari backend
                $backendCeritaApproved = $items->filter(fn($item) => ($item['status'] ?? 'approved') === 'approved')->count();
                $backendCeritaPending = $items->filter(fn($item) => ($item['status'] ?? '') === 'pending')->count();
                $backendCeritaUnapproved = $items->filter(fn($item) => in_array($item['status'] ?? '', ['unapproved', 'rejected']))->count();

                // Map items to behave like Eloquent models
                $backendCeritas = $items->map(function($c) use ($user) {
                    return (object)[
                        'id'         => $c['id'],
                        'judul'      => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                        'book'       => $c['book'] ?? 'Backend API',
                        'sumber'     => $c['book'] ?? 'Backend API',
                        'status'     => $c['status'] ?? 'pending',
                        'user'       => (object)['name' => $user->name],
                        'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    ];
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil upload cerita dari backend untuk dashboard: " . $e->getMessage());
        }

        // 2. Dapatkan data Cerita dari database lokal
        $localCeritaApproved = Cerita::where('user_id', $userId)->where('status', 'approved')->count();
        $localCeritaPending = Cerita::where('user_id', $userId)->where('status', 'pending')->count();
        $localCeritaUnapproved = Cerita::where('user_id', $userId)->where('status', 'unapproved')->count();

        $localCeritas = collect();
        try {
            $localCeritas = Cerita::where('user_id', $userId)
                ->latest()
                ->get()
                ->map(function($c) {
                    return (object)[
                        'id'         => 'local-' . $c->id,
                        'judul'      => $c->judul,
                        'book'       => $c->sumber,
                        'sumber'     => $c->sumber,
                        'status'     => $c->status,
                        'user'       => (object)['name' => $c->user ? $c->user->name : 'User Lokal'],
                        'created_at' => $c->created_at,
                    ];
                });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil list cerita lokal untuk dashboard: " . $e->getMessage());
        }

        // 3. Gabungkan data Cerita (Backend + Lokal)
        $ceritaApproved = $backendCeritaApproved + $localCeritaApproved;
        $ceritaPending = $backendCeritaPending + $localCeritaPending;
        $ceritaUnapproved = $backendCeritaUnapproved + $localCeritaUnapproved;

        // Gabungkan daftar cerita milik user (backend + lokal)
        $ceritas = $backendCeritas->concat($localCeritas)->sortByDesc('created_at')->values();

        // 4. Hitung status video milik user (semua video local)
        $videoApproved = Video::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();

        $videoPending = Video::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $videoRejected = Video::where('user_id', $userId)
            ->where('status', 'rejected')
            ->count();

        // 5. Hitung status audio milik user
        $audioApproved = \App\Models\Audio::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();

        $audioPending = \App\Models\Audio::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $audioRejected = \App\Models\Audio::where('user_id', $userId)
            ->where('status', 'rejected')
            ->count();

        // Total gabungan cerita, video, dan audio
        $approvedCount = $ceritaApproved + $videoApproved + $audioApproved;
        $pendingCount = $ceritaPending + $videoPending + $audioPending;
        $unapprovedCount = $ceritaUnapproved + $videoRejected + $audioRejected;

        // Tampilkan daftar video & audio user
        $videos = Video::where('user_id', $userId)
            ->latest()
            ->get();

        $audios = \App\Models\Audio::where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard.user', compact(
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'ceritas',
            'videos',
            'audios'
        ));
    }
}
