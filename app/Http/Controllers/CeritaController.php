<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeritaController extends Controller
{
    // =========================
    // FORM CREATE CERITA
    // =========================
    public function create()
    {
        return view('cerita.create');
    }

    // =========================
    // SIMPAN CERITA BARU
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
        ]);

        Cerita::create([
            'user_id' => Auth::id(),
            'sumber'  => $request->sumber,
            'cerita'  => $request->cerita,
            'status'  => 'pending',
        ]);

        return redirect()
            ->route('cerita.upload')
            ->with('success', 'Cerita berhasil diupload dan menunggu persetujuan');
    }

    // =========================
    // LIST CERITA USER
    // =========================
    public function upload()
    {
        $ceritas = Cerita::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('cerita.upload', compact('ceritas'));
    }

    // =========================
    // DETAIL CERITA
    // =========================
    public function show(Cerita $cerita)
    {
        return view('cerita.show', compact('cerita'));
    }

    // =========================
    // FORM EDIT CERITA
    // =========================
    public function edit(Cerita $cerita)
    {
        // keamanan: hanya pemilik cerita
        if ($cerita->user_id !== Auth::id()) {
            abort(403);
        }

        return view('cerita.edit', compact('cerita'));
    }

    // =========================
    // UPDATE CERITA
    // =========================
    public function update(Request $request, Cerita $cerita)
    {
        if ($cerita->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
        ]);

        $cerita->update([
            'sumber' => $request->sumber,
            'cerita' => $request->cerita,
        ]);

        return redirect()
            ->route('cerita.upload')
            ->with('success', 'Cerita berhasil diperbarui');
    }

    public function index()
    {
        $ceritas = Cerita::where('status', 'approved')
            ->with('user')
            ->latest()
            ->get();

        return view('cerita.index', compact('ceritas'));
    }

}
