<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Services\BackendApiService;
use Illuminate\Http\Request;

class CeritaController extends Controller
{
    public function index(Request $request, BackendApiService $apiService)
    {
        $ceritas = collect();
        $search = $request->query('search');

        try {
            $response = $apiService->getAdminUploads();
            if ($response->successful()) {
                $items = $response->json()['items'] ?? [];
                foreach ($items as $c) {
                    $ceritas->push((object)[
                        'id'         => $c['id'],
                        'judul'      => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                        'book'       => $c['book'] ?? 'Backend API',
                        'sub_parva'  => $c['sub_parva'] ?? '-',
                        'section'    => $c['section'] ?? '-',
                        'isi'        => $c['isi'] ?? '',
                        'sumber'     => $c['book'] ?? 'Backend API',
                        'status'     => $c['status'] ?? 'pending',
                        'user'       => (object)['name' => isset($c['user']['name']) ? $c['user']['name'] : 'User'],
                        'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil list cerita narasumber dari backend: " . $e->getMessage());
        }

        if ($search) {
            $ceritas = $ceritas->filter(function ($c) use ($search) {
                return stripos($c->judul, $search) !== false || stripos($c->sumber, $search) !== false;
            });
        }

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

        return view('narasumber.cerita.index', compact('ceritas', 'search'));
    }

    public function show($id, BackendApiService $apiService)
    {
        try {
            $response = $apiService->getParwaById($id);
            if ($response->successful()) {
                $c = $response->json();
                
                $bookSearch = str_replace('Parva', 'Parwa', $c['book'] ?? '');
                $bookSearch = str_replace('Swargarohanika', 'Svargarohana', $bookSearch);
                $parwaModel = \App\Models\Parwa::where('name', 'like', "%{$bookSearch}%")->first();
                $parwaSlug = $parwaModel ? $parwaModel->slug : \Illuminate\Support\Str::slug($c['book'] ?? 'adi-parva');
                $parwaNama = $parwaModel ? $parwaModel->name : ($c['book'] ?? 'Adi Parwa');

                $cerita = (object)[
                    'id' => $c['id'],
                    'judul' => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                    'book' => $c['book'] ?? 'Backend API',
                    'sumber' => $c['book'] ?? 'Backend API',
                    'sub_parva' => $c['sub_parva'] ?? '-',
                    'section' => $c['section'] ?? '-',
                    'isi' => $c['isi'] ?? '',
                    'cerita' => $c['isi'] ?? '',
                    'url' => $c['url'] ?? '',
                    'status' => 'approved',
                    'user' => (object)['name' => 'User Backend'],
                    'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    'parwa' => (object)[
                        'slug' => $parwaSlug,
                        'nama' => $parwaNama,
                    ],
                ];
                return view('narasumber.cerita.show', compact('cerita'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil detail cerita narasumber dari backend: " . $e->getMessage());
        }
        abort(404);
    }

    public function edit($id, BackendApiService $apiService)
    {
        try {
            $response = $apiService->getParwaById($id);
            if ($response->successful()) {
                $c = $response->json();

                // Detect which language was used when uploading
                $isiEn = $c['isi']    ?? '';
                $isiId = $c['isi_id'] ?? '';
                $hasEn = ($isiEn !== '' && $isiEn !== '-' && strlen(trim(strip_tags($isiEn))) > 10);
                $hasId = ($isiId !== '' && $isiId !== '-' && strlen(trim(strip_tags($isiId))) > 10);

                if ($hasId && !$hasEn) {
                    $contentLang     = 'id';
                    $editableContent = $isiId;
                } elseif ($hasEn && !$hasId) {
                    $contentLang     = 'en';
                    $editableContent = $isiEn;
                } else {
                    $contentLang     = 'id';
                    $editableContent = $isiId;
                }

                $cerita = (object)[
                    'id'           => $c['id'],
                    'judul'        => $c['judul'] ?? $c['title'] ?? '',
                    'book'         => $c['book'] ?? '',
                    'sumber'       => $c['url'] ?? $c['book'] ?? '',
                    'sub_parva'    => $c['sub_parva'] ?? '',
                    'section'      => $c['section'] ?? '',
                    'isi'          => $editableContent,
                    'isi_en'       => $isiEn,
                    'isi_id'       => $isiId,
                    'cerita'       => $editableContent,
                    'content_lang' => $contentLang,
                    'url'          => $c['url'] ?? '',
                ];
                $parwas = [];
                try {
                    $catResp = $apiService->getCategories();
                    if ($catResp->successful()) {
                        $catData = $catResp->json();
                        $parwas = collect($catData['data'] ?? [])->map(fn($item) => (object)$item);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Gagal mengambil kategori parwa untuk edit narasumber: " . $e->getMessage());
                    $parwas = \App\Models\Parwa::all()->map(fn($p) => (object)['book' => $p->name]);
                }
                return view('narasumber.cerita.edit', compact('cerita', 'parwas'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil edit cerita narasumber dari backend: " . $e->getMessage());
        }
        abort(404);
    }

    public function update(Request $request, $id, BackendApiService $apiService)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'parwa_book' => 'required|string|max:255',
            'sub_parwa'  => 'nullable|string|max:255',
            'sumber'     => 'nullable|string|max:500',
            'section'    => 'nullable|string|max:255',
            'cerita'     => 'required|string',
            'content_lang' => 'nullable|in:id,en',
        ]);

        $bookName = $request->parwa_book;

        // Determine which language field to update
        $contentLang = $request->content_lang ?? 'id';

        // Fetch current backend data to preserve the other language field
        $currentIsiEn = '';
        $currentIsiId = '';
        try {
            $fetchResp = $apiService->getParwaById($id);
            if ($fetchResp->successful()) {
                $current      = $fetchResp->json();
                $currentIsiEn = $current['isi']    ?? '';
                $currentIsiId = $current['isi_id'] ?? '';
            }
        } catch (\Exception $ex) {}

        if ($contentLang === 'id') {
            $isiEnPayload = $currentIsiEn ?: '-';
            $isiIdPayload = $request->cerita;
        } else {
            $isiEnPayload = $request->cerita;
            $isiIdPayload = $currentIsiId ?: '-';
        }

        try {
            $response = $apiService->updateParwa($id, [
                'book'      => $bookName,
                'sub_parva' => $request->sub_parwa ?: '-',
                'section'   => $request->section ?: 'Bab 1',
                'judul'     => $request->judul,
                'isi'       => $isiEnPayload,
                'isi_id'    => $isiIdPayload,
                'url'       => $request->sumber ?: null,
            ]);

            if (!$response->successful()) {
                $msg = $response->json('message') ?? 'Gagal memperbarui cerita di backend.';
                return back()->withErrors(['error' => $msg])->withInput();
            }

            return redirect()->route('narasumber.cerita.index')->with('success', 'Cerita berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui cerita di backend: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $id, BackendApiService $apiService)
    {
        $request->validate([
            'status' => 'required|in:approved,unapproved,pending'
        ]);

        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::findOrFail($localId);
                $c->update(['status' => $request->status]);
                return back()->with('success', 'Status cerita lokal berhasil diperbarui');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memperbarui status cerita lokal: ' . $e->getMessage());
            }
        }

        try {
            // Fetch backend story to maintain its values
            $response = $apiService->getParwaById($id);
            if ($response->successful()) {
                $c = $response->json();
                $updateResp = $apiService->updateParwa($id, [
                    'book' => $c['book'] ?? '',
                    'sub_parva' => $c['sub_parva'] ?? '-',
                    'section' => $c['section'] ?? '-',
                    'judul' => $c['judul'] ?? '',
                    'isi' => $c['isi'] ?? '',
                    'isi_id' => $c['isi_id'] ?? '',
                    'status' => $request->status,
                    'url' => $c['url'] ?? null,
                ]);
                 if ($updateResp->successful()) {
                    return back()->with('success', 'Status cerita di backend berhasil diperbarui');
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memperbarui status cerita di backend (Narasumber): " . $e->getMessage());
        }

        return back()->with('error', 'Gagal memperbarui status cerita di backend');
    }

    public function destroy($id, BackendApiService $apiService)
    {
        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::findOrFail($localId);
                $c->delete();
                return redirect()->route('narasumber.cerita.index')->with('success', 'Cerita lokal berhasil dihapus');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal menghapus cerita lokal: ' . $e->getMessage());
            }
        }

        try {
            $apiService->deleteParwa($id);
            return redirect()->route('narasumber.cerita.index')->with('success', 'Cerita berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus cerita dari backend: ' . $e->getMessage());
        }
    }
}
