<?php   

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cerita;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. TOTAL USER
        $totalUser = User::count();

        // 2. STATUS CERITA (SEMUA USER)
        $approvedCount   = Cerita::where('status', 'approved')->count();
        $pendingCount    = Cerita::where('status', 'pending')->count();
        $unapprovedCount = Cerita::where('status', 'unapproved')->count();

        // 3. DATA USER UNTUK TABEL
        $users = User::latest()->get();

        return view('dashboard.admin', compact(
            'totalUser',
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'users'
        ));
    }
}
