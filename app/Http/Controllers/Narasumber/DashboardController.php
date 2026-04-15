<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Models\Cerita;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // STATS CERITA (SEMUA USER SEPERTI ADMIN)
        $approvedCount   = Cerita::where('status', 'approved')->count();
        $pendingCount    = Cerita::where('status', 'pending')->count();
        $unapprovedCount = Cerita::where('status', 'unapproved')->count();

        // DATA CERITA UNTUK TABEL (DENGAN FILTRASI SEARCH)
        $search = $request->query('search');
        $ceritas = Cerita::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%{$search}%")
                         ->orWhere('sumber', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%");
                         });
        })->latest()->paginate(10);

        return view('dashboard.narasumber', compact(
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'ceritas'
        ));
    }
}
