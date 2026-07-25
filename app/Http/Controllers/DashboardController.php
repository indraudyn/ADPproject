<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Models\Video;
use App\Models\Audio;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'narasumber') {
            return redirect()->route('narasumber.dashboard');
        }

        // 1. Dapatkan data Cerita dari database lokal
        $ceritaApproved = Cerita::where('userId', $userId)->where('status', 'approved')->count();
        $ceritaPending = Cerita::where('userId', $userId)->where('status', 'pending')->count();
        $ceritaUnapproved = Cerita::where('userId', $userId)->whereIn('status', ['unapproved', 'rejected'])->count();

        $ceritas = Cerita::where('userId', $userId)
            ->latest()
            ->get()
            ->map(function($c) {
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

        // 2. Hitung status video milik user
        $videoApproved = Video::where('user_id', $userId)->where('status', 'approved')->count();
        $videoPending = Video::where('user_id', $userId)->where('status', 'pending')->count();
        $videoRejected = Video::where('user_id', $userId)->where('status', 'rejected')->count();

        // 3. Hitung status audio milik user
        $audioApproved = Audio::where('user_id', $userId)->where('status', 'approved')->count();
        $audioPending = Audio::where('user_id', $userId)->where('status', 'pending')->count();
        $audioRejected = Audio::where('user_id', $userId)->where('status', 'rejected')->count();

        // Total gabungan cerita, video, dan audio
        $approvedCount = $ceritaApproved + $videoApproved + $audioApproved;
        $pendingCount = $ceritaPending + $videoPending + $audioPending;
        $unapprovedCount = $ceritaUnapproved + $videoRejected + $audioRejected;

        // Tampilkan daftar video & audio user
        $videos = Video::where('user_id', $userId)->latest()->get();
        $audios = Audio::where('user_id', $userId)->latest()->get();

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
