<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // ⬅️ WAJIB
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
     * Halaman forum - Daftar topik
     */
    public function index()
    {
        $topics = ForumTopic::with('user')
            ->withCount('messages')
            ->latest()
            ->paginate(10);

        return view('forum.index', compact('topics'));
    }

    /**
     * Halaman detail topik - Daftar pesan dalam topik
     */
    public function show($slug)
    {
        $topic = ForumTopic::where('slug', $slug)->firstOrFail();
        $messages = ForumMessage::where('topic_id', $topic->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('forum.show', compact('topic', 'messages'));
    }

    /**
     * Simpan topik baru
     */
    public function storeTopic(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        ForumTopic::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('forum.index')->with('success', 'Topik berhasil dibuat!');
    }

    /**
     * Simpan pesan dalam topik
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'topic_id' => 'required|exists:forum_topics,id'
        ]);

        ForumMessage::create([
            'user_id' => Auth::id(),
            'topic_id' => $request->topic_id,
            'message' => $request->message,
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
     * Hapus topik sendiri
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

