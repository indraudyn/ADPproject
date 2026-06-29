<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumMessage;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumAdminController extends Controller
{
    /**
     * Halaman daftar topik forum untuk admin (semua status: pending, approved, rejected)
     */
    public function index()
    {
        $pendingTopics  = ForumTopic::with('user')->pending()->latest()->get();
        $approvedTopics = ForumTopic::with('user')->approved()->latest()->get();
        $rejectedTopics = ForumTopic::with('user')->where('status', 'rejected')->latest()->get();

        return view('admin.forum.index', compact('pendingTopics', 'approvedTopics', 'rejectedTopics'));
    }

    /**
     * Approve topik forum
     */
    public function approve($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update(['status' => 'approved']);

        return back()->with('success', "Topik \"{$topic->title}\" berhasil disetujui.");
    }

    /**
     * Reject topik forum
     */
    public function reject($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update(['status' => 'rejected']);

        return back()->with('success', "Topik \"{$topic->title}\" berhasil ditolak.");
    }

    /**
     * Hapus topik forum
     */
    public function destroy($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->delete();

        return back()->with('success', 'Topik berhasil dihapus.');
    }

    /**
     * Kirim pesan oleh admin ke topik
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        ForumMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->route('admin.forum.index');
    }
}
