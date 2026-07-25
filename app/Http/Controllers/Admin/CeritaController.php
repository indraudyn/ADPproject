<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cerita;
use App\Models\Parwa;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class CeritaController extends Controller
{
    public function index()
    {
        $ceritas = Cerita::with('user')->latest()->get()->map(function($c) {
            return (object)[
                'id'         => $c->id,
                'judul'      => $c->judul,
                'book'       => $c->sumber,
                'sub_parva'  => $c->sub_parwa ?? '-',
                'section'    => '-',
                'isi'        => $c->isi ?? $c->isi_id,
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
            $ceritas->forPage($currentPage, $perPage)->values(),
            $ceritas->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $ceritas = $paginated;
        return view('admin.cerita.index', compact('ceritas'));
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
        return view('admin.cerita.show', compact('cerita'));
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
            'isi' => $c->isi ?? $c->isi_id,
            'cerita' => $c->isi ?? $c->isi_id,
            'url' => '',
        ];
        $parwas = Parwa::all();
        return view('admin.cerita.edit', compact('cerita', 'parwas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'        => 'required|string|max:255',
            'parwa_book'   => 'required|string|max:255',
            'sub_parwa'    => 'nullable|string|max:255',
            'sumber'       => 'nullable|string|max:500',
            'section'      => 'nullable|string|max:255',
            'cerita'       => 'required|string',
        ]);

        $bookName = $request->parwa_book;
        $localId = str_replace('local-', '', $id);
        
        $c = Cerita::findOrFail($localId);
        $updateData = [
            'judul'    => $request->judul,
            'sumber'   => $bookName,
            'sub_parwa'=> $request->sub_parwa ?? '-',
        ];

        // Ensure we save to the correct language column based on content_lang from the form
        if ($request->has('content_lang')) {
            if ($request->content_lang === 'en') {
                $updateData['isi'] = $request->cerita;
                $updateData['isi_id'] = TranslationService::translateText($request->cerita, 'en', 'id');
            } else {
                $updateData['isi_id'] = $request->cerita;
                $updateData['isi'] = TranslationService::translateText($request->cerita, 'id', 'en');
            }
        } else {
            // fallback
            if (!empty($c->isi)) {
                $updateData['isi'] = $request->cerita;
                $updateData['isi_id'] = TranslationService::translateText($request->cerita, 'en', 'id');
            } else {
                $updateData['isi_id'] = $request->cerita;
                $updateData['isi'] = TranslationService::translateText($request->cerita, 'id', 'en');
            }
        }

        $c->update($updateData);
        
        return redirect()->route('admin.cerita.index')->with('success', 'Cerita berhasil diperbarui');
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
        
        return redirect()->route('admin.cerita.index')->with('success', 'Cerita berhasil dihapus');
    }
}
