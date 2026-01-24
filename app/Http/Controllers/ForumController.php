<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // ⬅️ WAJIB
use Illuminate\Http\Request;
use App\Models\ForumMessage;
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
     * Halaman forum
     */
    public function index()
    {
        $messages = ForumMessage::with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('forum.index', compact('messages'));
    }

    /**
     * Simpan pesan
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        ForumMessage::create([
            'user_id' => Auth::id(),
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
}
