<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumAdminController extends Controller
{
    public function index()
    {
        $messages = ForumMessage::with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.forum.index', compact('messages'));
    }

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
