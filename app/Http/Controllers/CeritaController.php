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
        $parwas = \App\Models\Parwa::all();
        return view('cerita.create', compact('parwas'));
    }

    // =========================
    // SIMPAN CERITA BARU
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'judul' => 'required|string|max:255',
            'sub_parwa' => 'nullable|string|max:255',
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
        ]);

        $user = Auth::user();
        $status = ($user->role === 'admin' || $user->role === 'narasumber') ? 'approved' : 'pending';

        Cerita::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'parwa_id' => $request->parwa_id,
            'sub_parwa' => $request->sub_parwa,
            'sumber'  => $request->sumber,
            'cerita'  => $request->cerita,
            'status'  => $status,
        ]);

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.cerita.index')
                ->with('success', 'Cerita berhasil ditambahkan');
        } elseif ($user->role === 'narasumber') {
            return redirect()
                ->route('narasumber.dashboard')
                ->with('success', 'Cerita berhasil ditambahkan');
        }

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
        $relatedStories = Cerita::where('parwa_id', $cerita->parwa_id)
            ->where('sub_parwa', $cerita->sub_parwa)
            ->where('status', 'approved')
            ->where('id', '!=', $cerita->id)
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('cerita.show', compact('cerita', 'relatedStories'));
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
            'judul' => 'required|string|max:255',
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
        ]);

        $cerita->update([
            'judul' => $request->judul,
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
