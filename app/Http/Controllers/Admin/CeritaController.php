<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cerita;
use Illuminate\Http\Request;

class CeritaController extends Controller
{
    public function index()
    {
        $ceritas = Cerita::latest()->paginate(10);
        return view('admin.cerita.index', compact('ceritas'));
    }

    public function show($id)
    {
        $cerita = Cerita::findOrFail($id);
        return view('admin.cerita.show', compact('cerita'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,unapproved'
        ]);

        Cerita::where('id', $id)
            ->update(['status' => $request->status]);

        return back()->with('success', 'Status berhasil diperbarui');
    }
}
