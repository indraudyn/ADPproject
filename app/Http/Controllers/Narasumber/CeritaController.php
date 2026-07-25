<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Models\Cerita;
use App\Models\Parwa;
use Illuminate\Http\Request;

class CeritaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $ceritaQuery = Cerita::with('user');

        if ($search) {
            $ceritaQuery->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                      ->orWhere('sumber', 'like', "%{$search}%");
            });
        }

        $ceritasList = $ceritaQuery->latest()->get()->map(function($c) {
            return (object)[
                'id'         => $c->id,
                'judul'      => $c->judul,
                'book'       => $c->sumber,
                'sub_parva'  => $c->sub_parwa ?? '-',
                'section'    => '-',
                'isi'        => $c->cerita,
                'sumber'     => $c->sumber,
                'status'     => $c->status,
                'user'       => (object)['name' => $c->user ? $c->user->name : 'User'],
                'created_at' => $c->created_at,
            ];
        });

        // Paginate manually
        $currentPage = request('page', 1);
        $perPage = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $ceritasList->forPage($currentPage, $perPage)->values(),
            $ceritasList->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $ceritas = $paginated;

        return view('narasumber.cerita.index', compact('ceritas', 'search'));
    }

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
            'section' => '-',
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
        return view('narasumber.cerita.show', compact('cerita'));
    }

    public function edit($id)
    {
        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);

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
        return view('narasumber.cerita.edit', compact('cerita', 'parwas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'parwa_book' => 'required|string|max:255',
            'sub_parwa'  => 'nullable|string|max:255',
            'sumber'     => 'nullable|string|max:500',
            'section'    => 'nullable|string|max:255',
            'cerita'     => 'required|string',
        ]);

        $bookName = $request->parwa_book;
        $localId = str_replace('local-', '', $id);
        
        $c = Cerita::findOrFail($localId);
        $c->update([
            'judul'    => $request->judul,
            'sumber'   => $bookName,
            'cerita'   => $request->cerita,
            'sub_parwa'=> $request->sub_parwa ?? '-',
        ]);
        
        return redirect()->route('narasumber.cerita.index')->with('success', 'Cerita berhasil diperbarui');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,unapproved,pending'
        ]);

        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);
        $c->update(['status' => $request->status]);
        
        return back()->with('success', 'Status cerita berhasil diperbarui');
    }

    public function destroy($id)
    {
        $localId = str_replace('local-', '', $id);
        $c = Cerita::findOrFail($localId);
        $c->delete();
        
        return redirect()->route('narasumber.cerita.index')->with('success', 'Cerita berhasil dihapus');
    }
}
