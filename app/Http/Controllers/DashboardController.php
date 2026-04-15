<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Models\Video;
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

        // Hitung status cerita milik user
        $approvedCount = Cerita::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();

        $pendingCount = Cerita::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $unapprovedCount = Cerita::where('user_id', $userId)
            ->where('status', 'unapproved')
            ->count();

        // Tampilkan hanya cerita approved
        $ceritas = Cerita::where('user_id', $userId)
            ->where('status', 'approved')
            ->latest()
            ->get();

        // Video stats
        $videoApprovedCount = Video::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();

        $videoPendingCount = Video::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $videoUnapprovedCount = Video::where('user_id', $userId)
            ->where('status', 'rejected')
            ->count();

        // Tampilkan daftar video user
        $videos = Video::where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard.user', compact(
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'ceritas',
            'videoApprovedCount',
            'videoPendingCount',
            'videoUnapprovedCount',
            'videos'
        ));
    }
}
