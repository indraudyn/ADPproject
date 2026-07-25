<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Models\Parwa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeritaController extends Controller
{
    // =========================
    // FORM CREATE CERITA
    // =========================
    public function create()
    {
        $parwas = Parwa::all();
        $versions = []; // Versions API no longer available
        return view('cerita.create', compact('parwas', 'versions'));
    }

    // =========================
    // SIMPAN CERITA BARU
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required',
            'judul' => 'required|string|max:255',
            'sub_parwa' => 'nullable|string|max:255',
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
        ]);

        $user = Auth::user();
        $status = in_array($user->role, ['admin', 'narasumber']) ? 'approved' : 'pending';

        Cerita::create([
            'user_id' => $user->id,
            'parwa_id' => $request->parwa_id,
            'judul' => $request->judul,
            'sub_parwa' => $request->sub_parwa ?? '-',
            'sumber' => $request->sumber,
            'cerita' => $request->cerita,
            'status' => $status,
        ]);

        $successMsg = 'Cerita berhasil ditambahkan';

        if ($user->role === 'admin') {
            return redirect()->route('admin.cerita.index')->with('success', $successMsg);
        } elseif ($user->role === 'narasumber') {
            return redirect()->route('narasumber.dashboard')->with('success', $successMsg);
        }

        return redirect()->route('cerita.upload')->with('success', $successMsg);
    }

    // =========================
    // LIST CERITA USER
    // =========================
    public function upload()
    {
        $user = auth()->user();
        $ceritas = Cerita::where('user_id', $user->id)->latest()->get()->map(function($c) {
            return (object)[
                'id'         => $c->id,
                'judul'      => $c->judul,
                'book'       => $c->sumber,
                'sumber'     => $c->sumber,
                'status'     => $c->status,
                'user'       => (object)['name' => $c->user ? $c->user->name : 'User'],
                'created_at' => $c->created_at,
            ];
        });

        return view('cerita.upload', compact('ceritas'));
    }

    // =========================
    // DETAIL CERITA
    // =========================
    public function show($id)
    {
        $localId = str_replace('local-', '', $id);
        $c = Cerita::with('user')->findOrFail($localId);
        
        $parwaSlug = $c->parwa ? $c->parwa->slug : \Illuminate\Support\Str::slug($c->sumber);
        $parwaNama = $c->parwa ? $c->parwa->name : $c->sumber;

        $cerita = (object)[
            'id' => $c->id,
            'judul' => $c->judul,
            'book' => $c->sumber,
            'sumber' => $c->sumber,
            'sub_parva' => $c->sub_parwa ?? '-',
            'section' => 'Bab 1',
            'isi' => $c->cerita,
            'cerita' => $c->cerita,
            'url' => '',
            'status' => $c->status,
            'user' => (object)['name' => $c->user ? $c->user->name : 'User'],
            'created_at' => $c->created_at,
            'parwa' => (object)[
                'slug' => $parwaSlug,
                'nama' => $parwaNama,
            ],
        ];

        // Fetch related stories
        $relatedStories = Cerita::where('id', '!=', $c->id)->where('status', 'approved')->take(6)->get()->map(function($item) {
            return (object)[
                'id' => $item->id,
                'judul' => $item->judul,
                'sumber' => $item->sumber,
                'user' => (object)['name' => $item->user ? $item->user->name : 'User'],
                'created_at' => $item->created_at,
            ];
        });

        // Find next/prev stories
        $prevCeritaId = null;
        $nextCeritaId = null;
        $prev = Cerita::where('id', '<', $c->id)->where('status', 'approved')->orderBy('id', 'desc')->first();
        $next = Cerita::where('id', '>', $c->id)->where('status', 'approved')->orderBy('id', 'asc')->first();
        if ($prev) $prevCeritaId = $prev->id;
        if ($next) $nextCeritaId = $next->id;

        return view('cerita.show', compact('cerita', 'relatedStories', 'prevCeritaId', 'nextCeritaId'));
    }

    // =========================
    // FORM EDIT CERITA
    // =========================
    public function edit($id)
    {
        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);
        
        if ($c->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'narasumber'])) {
            abort(403);
        }

        $cerita = (object)[
            'id' => $c->id,
            'judul' => $c->judul,
            'book' => $c->sumber,
            'sumber' => $c->sumber,
            'sub_parva' => $c->sub_parwa ?? '',
            'section' => 'Bab 1',
            'isi' => $c->cerita,
            'cerita' => $c->cerita,
            'url' => '',
        ];
        $parwas = Parwa::all();
        return view('cerita.edit', compact('cerita', 'parwas'));
    }

    // =========================
    // UPDATE CERITA
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:500',
            'cerita' => 'required',
            'sub_parwa' => 'nullable|string|max:255',
        ]);

        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);
        
        if ($c->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'narasumber'])) {
            abort(403);
        }

        $c->update([
            'judul' => $request->judul,
            'sumber' => $request->sumber,
            'cerita' => $request->cerita,
            'sub_parwa' => $request->sub_parwa ?? '-',
        ]);

        return redirect()->route('cerita.upload')->with('success', 'Cerita berhasil diperbarui');
    }

    public function index()
    {
        $ceritas = Cerita::with('user')->where('status', 'approved')->latest()->get()->map(function($c) {
            return (object)[
                'id' => $c->id,
                'judul' => $c->judul,
                'book' => $c->sumber,
                'sumber' => $c->sumber,
                'status' => $c->status,
                'user' => (object)['name' => $c->user ? $c->user->name : 'User'],
                'created_at' => $c->created_at,
            ];
        });

        // Manual pagination for user list
        $currentPage = request('page', 1);
        $perPage = 8;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $ceritas->forPage($currentPage, $perPage)->values(),
            $ceritas->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $ceritas = $paginated;
        return view('cerita.index', compact('ceritas'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $localId = str_replace('local-', '', $id);
        
        $c = Cerita::findOrFail($localId);
        
        if ($user->role !== 'admin' && $c->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menghapus cerita ini.');
        }
        
        $c->delete();
        
        return redirect()->route('cerita.upload')->with('success', 'Cerita berhasil dihapus');
    }
}
