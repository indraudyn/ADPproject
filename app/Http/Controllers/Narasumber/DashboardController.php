<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Services\BackendApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request, BackendApiService $apiService)
    {
        $backendCeritaApproved = 0;
        $backendCeritaPending = 0;
        $backendCeritaUnapproved = 0;
        $backendCeritas = collect();

        $search = $request->query('search');

        try {
            $response = $apiService->getAdminUploads();
            if ($response->successful()) {
                $items = collect($response->json()['items'] ?? []);

                // Stats from remote backend
                $backendCeritaApproved = $items->filter(fn($item) => ($item['status'] ?? 'approved') === 'approved')->count();
                $backendCeritaPending = $items->filter(fn($item) => ($item['status'] ?? '') === 'pending')->count();
                $backendCeritaUnapproved = $items->filter(fn($item) => in_array($item['status'] ?? '', ['unapproved', 'rejected']))->count();

                // Map items to behave like Eloquent models in the view
                $backendCeritas = $items->map(function($c) {
                    return (object)[
                        'id' => $c['id'],
                        'judul' => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                        'book' => $c['book'] ?? 'Backend API',
                        'sumber' => $c['book'] ?? 'Backend API',
                        'status' => $c['status'] ?? 'pending',
                        'user' => (object)['name' => $c['user']['name'] ?? 'User'],
                        'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    ];
                });
            }
        } catch (\Exception $e) {
            Log::warning("Gagal memuat dashboard narasumber dari backend: " . $e->getMessage());
        }

        // 2. Local Cerita
        $localApproved = 0;
        $localPending = 0;
        $localUnapproved = 0;
        $localCeritas = collect();
        try {
            $localApproved = \App\Models\Cerita::where('status', 'approved')->count();
            $localPending = \App\Models\Cerita::where('status', 'pending')->count();
            $localUnapproved = \App\Models\Cerita::where('status', 'unapproved')->count();

            $localCeritas = \App\Models\Cerita::with('user')->get()->map(function($c) {
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
            Log::warning("Gagal mengambil data cerita lokal di dashboard narasumber: " . $e->getMessage());
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

        // Combine Stories
        $ceritasList = $backendCeritas->concat($localCeritas);

        if ($search) {
            $ceritasList = $ceritasList->filter(function ($c) use ($search) {
                return stripos($c->judul, $search) !== false || stripos($c->sumber, $search) !== false;
            });
        }

        // Paginate manually
        $currentPage = request('page', 1);
        $perPage = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $ceritasList->forPage($currentPage, $perPage)->values(),
            $ceritasList->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $ceritas = $paginated;

        return view('dashboard.narasumber', compact(
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'ceritas'
        ));
    }
}
