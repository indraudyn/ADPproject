<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

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

        return view('dashboard.user', compact(
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'ceritas'
        ));
    }
}
