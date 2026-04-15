<?php   

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cerita;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. TOTAL USER
        $totalUser = User::count();

        // 2. STATUS CERITA (SEMUA USER)
        $approvedCount   = Cerita::where('status', 'approved')->count();
        $pendingCount    = Cerita::where('status', 'pending')->count();
        $unapprovedCount = Cerita::where('status', 'unapproved')->count();

        // 3. DATA USER UNTUK TABEL (DENGAN FILTRASI SEARCH)
        $search = $request->query('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->latest()->get();

        return view('dashboard.admin', compact(
            'totalUser',
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'users'
        ));
    }
}
