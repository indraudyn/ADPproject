<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumMessage;
use App\Models\ForumTopic;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Pastikan user login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman forum - Admin lihat semua topik (termasuk pending), user hanya approved
     */
    public function index()
    {
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';

        $topics = ForumTopic::with('user')
            ->withCount('messages')
            ->when(!$isAdmin, fn($q) => $q->approved()) // non-admin hanya lihat approved
            ->when(auth()->check(), function ($query) {
                $query->withExists(['messages as user_participated' => function ($q) {
                    $q->where('user_id', auth()->id());
                }]);
            })
            ->latest()
            ->paginate(10);

        return view('forum.index', compact('topics'));
    }

    /**
     * Halaman detail topik - Daftar pesan dalam topik
     */
    public function show($slug)
    {
        // Admin bisa lihat topik apapun, user biasa hanya approved
        $query = ForumTopic::where('slug', $slug);
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $query->approved();
        }
        $topic = $query->firstOrFail();

        $messages = ForumMessage::where('topic_id', $topic->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('forum.show', compact('topic', 'messages'));
    }

    /**
     * Simpan topik baru - status default 'pending', menunggu approval admin
     */
    public function storeTopic(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        ForumTopic::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => 'pending', // perlu disetujui admin
        ]);

        return redirect()->route('forum.index')
            ->with('pending', 'Topik berhasil dikirim! Menunggu persetujuan admin.');
    }

    /**
     * Simpan pesan dalam topik
     */
    public function store(Request $request)
    {
        $request->validate([
            'message'  => 'required|string',
            'topic_id' => 'required|exists:forum_topics,id'
        ]);

        ForumMessage::create([
            'user_id'  => Auth::id(),
            'topic_id' => $request->topic_id,
            'message'  => $request->message,
        ]);

        return redirect()->back();
    }

    /**
     * Hapus pesan sendiri
     */
    public function destroy($id)
    {
        $message = ForumMessage::findOrFail($id);

        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->back();
    }

    /**
     * Hapus topik (admin only)
     */
    public function destroyTopic($id)
    {
        $topic = ForumTopic::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat menghapus topik.');
        }

        $topic->delete();

        return redirect()->route('forum.index')->with('success', 'Topik berhasil dihapus!');
    }
}
