<?php   

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cerita;
use App\Models\Video;
use App\Models\Audio;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. TOTAL USER & USER LIST
        $search = $request->query('search');
        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->latest()->get();
        $totalUser = User::count();

        // 2. STATUS CERITA (SEMUA USER)
        $ceritaApproved = Cerita::where('status', 'approved')->count();
        $ceritaPending = Cerita::where('status', 'pending')->count();
        $ceritaUnapproved = Cerita::where('status', 'unapproved')->count();

        // 3. STATUS VIDEOS (SEMUA USER)
        $videoApproved = Video::where('status', 'approved')->count();
        $videoPending = Video::where('status', 'pending')->count();
        $videoRejected = Video::where('status', 'rejected')->count();

        // 4. STATUS AUDIOS (SEMUA USER)
        $audioApproved = Audio::where('status', 'approved')->count();
        $audioPending = Audio::where('status', 'pending')->count();
        $audioRejected = Audio::where('status', 'rejected')->count();

        // Calculate final counts (Cerita + Video + Audio)
        $approvedCount = $ceritaApproved + $videoApproved + $audioApproved;
        $pendingCount = $ceritaPending + $videoPending + $audioPending;
        $unapprovedCount = $ceritaUnapproved + $videoRejected + $audioRejected;

        return view('dashboard.admin', compact(
            'totalUser',
            'approvedCount',
            'pendingCount',
            'unapprovedCount',
            'users'
        ));
    }
}
