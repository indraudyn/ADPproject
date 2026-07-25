<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Models\Parwa;
use App\Services\TranslationService;
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
            'bahasa' => 'required|in:id,en',
        ]);

        $user = Auth::user();
        $status = in_array($user->role, ['admin', 'narasumber']) ? 'approved' : 'pending';

        if ($request->bahasa === 'en') {
            $isi = $request->cerita;
            $isiId = TranslationService::translateText($request->cerita, 'en', 'id');
        } else {
            $isiId = $request->cerita;
            $isi = TranslationService::translateText($request->cerita, 'id', 'en');
        }

        Cerita::create([
            'user_id' => $user->id,
            'parwa_id' => $request->parwa_id,
            'judul' => $request->judul,
            'sub_parwa' => $request->sub_parwa ?? '-',
            'sumber' => $request->sumber,
            'isi' => $isi,
            'isi_id' => $isiId,
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
        $ceritas = Cerita::where('userId', $user->id)->orderBy('createdAt', 'desc')->get()->map(function($c) {
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
            'isi' => $c->isi ?? $c->isi_id,
            'cerita' => $c->isi ?? $c->isi_id,
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
            'isi' => $c->isi ?? $c->isi_id,
            'cerita' => $c->isi ?? $c->isi_id,
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
            'bahasa' => 'nullable|in:id,en',
        ]);

        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);
        
        if ($c->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'narasumber'])) {
            abort(403);
        }

        $updateData = [
            'judul' => $request->judul,
            'sumber' => $request->sumber,
            'sub_parwa' => $request->sub_parwa ?? '-',
        ];

        // Edit form might not pass bahasa, if it does, update accordingly
        if ($request->has('bahasa')) {
            if ($request->bahasa === 'en') {
                $updateData['isi'] = $request->cerita;
                $updateData['isi_id'] = TranslationService::translateText($request->cerita, 'en', 'id');
            } else {
                $updateData['isi_id'] = $request->cerita;
                $updateData['isi'] = TranslationService::translateText($request->cerita, 'id', 'en');
            }
        } else {
            // Default behavior if bahasa is missing: update whatever is not null
            if (!empty($c->isi)) {
                $updateData['isi'] = $request->cerita;
                $updateData['isi_id'] = TranslationService::translateText($request->cerita, 'en', 'id');
            } else {
                $updateData['isi_id'] = $request->cerita;
                $updateData['isi'] = TranslationService::translateText($request->cerita, 'id', 'en');
            }
        }

        $c->update($updateData);

        return redirect()->route('cerita.upload')->with('success', 'Cerita berhasil diperbarui');
    }

    public function index()
    {
        $ceritas = Cerita::with('user')->where('status', 'approved')->orderBy('createdAt', 'desc')->get()->map(function($c) {
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
