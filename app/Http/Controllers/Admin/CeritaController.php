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

    public function edit($id)
    {
        $cerita = Cerita::findOrFail($id);
        $parwas = \App\Models\Parwa::all();
        return view('admin.cerita.edit', compact('cerita', 'parwas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'parwa_id'  => 'required|exists:parwas,id',
            'sub_parwa' => 'required|string|max:255',
            'sumber'    => 'required|string|max:255',
            'cerita'    => 'required|string',
        ]);

        $cerita = Cerita::findOrFail($id);
        $cerita->update($request->all());

        return redirect()->route('admin.cerita.index')->with('success', 'Cerita berhasil diperbarui');
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

    public function destroy($id)
    {
        $cerita = Cerita::findOrFail($id);
        $cerita->delete();

        return back()->with('success', 'Cerita berhasil dihapus');
    }
}
