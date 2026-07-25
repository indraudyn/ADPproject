<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Models\Cerita;
use App\Models\Video;
use App\Models\Audio;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // 1. Local Cerita
        $ceritaQuery = Cerita::with('user');
        
        if ($search) {
            $ceritaQuery->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                      ->orWhere('sumber', 'like', "%{$search}%");
            });
        }

        $ceritasList = $ceritaQuery->latest()->get()->map(function($c) {
            return (object)[
                'id'         => $c->id,
                'judul'      => $c->judul,
                'book'       => $c->sumber,
                'sumber'     => $c->sumber,
                'status'     => $c->status,
                'user'       => (object)['name' => $c->user ? $c->user->name : 'User Lokal'],
                'created_at' => $c->created_at,
            ];
        });

        $ceritaApproved = Cerita::where('status', 'approved')->count();
        $ceritaPending = Cerita::where('status', 'pending')->count();
        $ceritaUnapproved = Cerita::where('status', 'unapproved')->count();

        // 2. Local Videos (all)
        $videoApproved = Video::where('status', 'approved')->count();
        $videoPending = Video::where('status', 'pending')->count();
        $videoRejected = Video::where('status', 'rejected')->count();

        // 3. Local Audios (all)
        $audioApproved = Audio::where('status', 'approved')->count();
        $audioPending = Audio::where('status', 'pending')->count();
        $audioRejected = Audio::where('status', 'rejected')->count();

        // Calculate final counts (Cerita + Video + Audio)
        $approvedCount = $ceritaApproved + $videoApproved + $audioApproved;
        $pendingCount = $ceritaPending + $videoPending + $audioPending;
        $unapprovedCount = $ceritaUnapproved + $videoRejected + $audioRejected;

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
