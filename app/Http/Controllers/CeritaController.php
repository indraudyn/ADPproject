<?php

namespace App\Http\Controllers;

use App\Models\Cerita;
use App\Services\BackendApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeritaController extends Controller
{
    // =========================
    // FORM CREATE CERITA
    // =========================
    public function create(BackendApiService $apiService)
    {
        $parwas = [];
        try {
            $response = $apiService->getCategories();
            if ($response->successful()) {
                $apiData = $response->json();
                $parwas = collect($apiData['data'] ?? [])->map(fn($c) => (object)$c);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil kategori parwa untuk create: " . $e->getMessage());
            $parwas = \App\Models\Parwa::all()->map(fn($p) => (object)['book' => $p->name]);
        }

        // Fetch existing versions from backend
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk create: " . $e->getMessage());
        }

        return view('cerita.create', compact('parwas', 'versions'));
    }

    // =========================
    // SIMPAN CERITA BARU
    // =========================
    public function store(Request $request, BackendApiService $apiService)
    {
        $request->validate([
            'parwa_id' => 'required',
            'section' => 'required|string|max:255',
            'section_custom' => 'nullable|required_if:section,custom_input|string|max:255',
            'versi_tipe' => 'required|in:existing,new',
            'versi_existing' => 'nullable|required_if:versi_tipe,existing|string|max:255',
            'versi_baru' => 'nullable|required_if:versi_tipe,new|string|max:255',
            'judul' => 'required|string|max:255',
            'sub_parva' => 'nullable|string|max:255',
            'sumber' => 'required|string|max:255',
            'cerita' => 'required',
            'bahasa' => 'required|in:id,en',
        ]);

        $selectedBook = $request->parwa_id;
        $localName = $selectedBook ?: $request->sumber;

        $bookMap = [
            'Adi Parwa' => 'Adi Parva',
            'Sabha Parwa' => 'Sabha Parva',
            'Vana Parwa' => 'Vana Parva',
            'Virata Parwa' => 'Virata Parva',
            'Udyoga Parwa' => 'Udyoga Parva',
            'Bhishma Parwa' => 'Bhishma Parva',
            'Drona Parwa' => 'Drona Parva',
            'Karna Parwa' => 'Karna Parva',
            'Shalya Parwa' => 'Shalya Parva',
            'Sauptika Parwa' => 'Sauptika Parva',
            'Stri Parwa' => 'Stri Parva',
            'Shanti Parwa' => 'Shanti Parva',
            'Anushasana Parwa' => 'Anushasana Parva',
            'Ashvamedhika Parwa' => 'Ashvamedhika Parva',
            'Ashramavasika Parwa' => 'Ashramavasika Parva',
            'Mausala Parwa' => 'Mausala Parva',
            'Mahaprasthanika Parwa' => 'Mahaprasthanika Parva',
            'Svargarohana Parwa' => 'Swargarohanika Parva',
        ];

        $bookName = $bookMap[$localName] ?? $localName;

        $user = Auth::user();

        // Determine status based on user role (user -> pending, admin/narasumber -> approved)
        $status = 'pending';
        if (in_array($user->role, ['admin', 'narasumber'])) {
            $status = 'approved';
        }

        // Map content based on language selection to satisfy both required fields on backend
        $isiEn = '-';
        $isiId = '-';
        if ($request->bahasa === 'en') {
            $isiEn = $request->cerita;
        } else {
            $isiId = $request->cerita;
        }

        // Determine version name
        $versionName = $request->versi_tipe === 'existing' ? $request->versi_existing : $request->versi_baru;

        // Resolve chapter/section name
        $sectionName = $request->section;
        if ($sectionName === 'custom_input') {
            $sectionName = $request->section_custom;
        }
        
        // Format the title to match backend pattern: "{Book} - {Version}, tr. - {Section}"
        $finalJudul = "{$bookName} - {$versionName}, tr. - {$sectionName}";

        try {
            $payload = [
                'book' => $bookName,
                'sub_parva' => $request->sub_parwa ?? '-',
                'section' => $sectionName,
                'judul' => $finalJudul,
                'isi' => $isiEn,
                'isi_id' => $isiId,
                'status' => $status,
                'url' => $request->sumber,
                'versionName' => $versionName,
            ];

            \Illuminate\Support\Facades\Log::info('Backend createParwa payload', $payload);

            // First attempt: use current user's token (BackendApiService will attach session token)
            $response = $apiService->createParwa($payload, false);

            // If user token is unauthorized/forbidden, retry with admin token if configured
            if (!$response->successful() && in_array($response->status(), [401, 403], true)) {
                \Illuminate\Support\Facades\Log::info('createParwa: user token failed, attempting fallback', ['status' => $response->status()]);
                if ($apiService->hasAdminToken()) {
                    \Illuminate\Support\Facades\Log::info('createParwa: retrying with admin token');
                    $response = $apiService->createParwa($payload, true);
                } else {
                    $statusResponse = $response->status();
                    if ($statusResponse === 401) {
                        return back()->withErrors(['error' => 'Gagal menyimpan cerita ke backend: otentikasi token tidak valid atau sudah kadaluarsa. Silakan login ulang.'])->withInput();
                    }
                    return back()->withErrors(['error' => 'Unggahan cerita user ditolak oleh backend karena token admin belum diset. Silakan hubungi admin untuk menyetel token admin di file .env.'])->withInput();
                }
            }

            if ($response->successful()) {
                // Invalidate cache for this book so fresh data shows on the detail page
                \App\Services\ParwaCacheService::invalidateForBook($bookName);
                $successMsg = 'Cerita berhasil ditambahkan ke backend';
            } else {
                $statusResponse = $response->status();
                $rawBody = trim($response->body());
                $msg = $response->json('message') ?: $rawBody ?: 'Server eksternal error';

                \Illuminate\Support\Facades\Log::warning('Gagal menyimpan cerita ke backend', [
                    'status' => $statusResponse,
                    'message' => $msg,
                    'payload' => $payload,
                    'response_body' => $response->body(),
                ]);

                if ($statusResponse === 401) {
                    $msg = 'Gagal menyimpan cerita ke backend: otentikasi token tidak valid atau sudah kadaluarsa.';
                } elseif ($statusResponse === 403) {
                    $msg = 'Gagal menyimpan cerita ke backend: akses ditolak oleh backend.';
                } else {
                    $msg = 'Gagal menyimpan cerita ke backend: ' . $msg;
                }

                return back()->withErrors(['error' => $msg])->withInput();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal menyimpan cerita ke backend: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal terhubung ke backend: ' . $e->getMessage()])->withInput();
        }

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.cerita.index')
                ->with('success', $successMsg);
        } elseif ($user->role === 'narasumber') {
            return redirect()
                ->route('narasumber.dashboard')
                ->with('success', $successMsg);
        }

        return redirect()
            ->route('cerita.upload')
            ->with('success', $successMsg);
    }

    // =========================
    // LIST CERITA USER
    // =========================
    public function upload(BackendApiService $apiService)
    {
        $user = auth()->user();
        $ceritas = collect();

        try {
            $response = $apiService->getUserUploads();
            if ($response->successful()) {
                $items = $response->json()['items'] ?? [];
                $ceritas = collect($items)->map(function($c) use ($user) {
                    return (object)[
                        'id'         => $c['id'],
                        'judul'      => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                        'book'       => $c['book'] ?? 'Backend API',
                        'sumber'     => $c['book'] ?? 'Backend API',
                        'status'     => $c['status'] ?? 'pending',
                        'user'       => (object)['name' => $user->name],
                        'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    ];
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil list upload cerita dari backend: " . $e->getMessage());
        }

        return view('cerita.upload', compact('ceritas'));
    }

    // =========================
    // DETAIL CERITA
    // =========================
    public function show($id, BackendApiService $apiService)
    {
        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::with('user')->findOrFail($localId);
                
                $bookSearch = str_replace('Parva', 'Parwa', $c->sumber);
                $bookSearch = str_replace('Swargarohanika', 'Svargarohana', $bookSearch);
                $parwaModel = \App\Models\Parwa::where('name', 'like', "%{$bookSearch}%")->first();
                $parwaSlug = $parwaModel ? $parwaModel->slug : \Illuminate\Support\Str::slug($c->sumber);
                $parwaNama = $parwaModel ? $parwaModel->name : $c->sumber;

                $cerita = (object)[
                    'id' => $id,
                    'judul' => $c->judul,
                    'book' => $c->sumber,
                    'sumber' => $c->sumber,
                    'sub_parva' => $c->sub_parwa ?? '-',
                    'section' => 'Bab 1',
                    'isi' => $c->cerita,
                    'cerita' => $c->cerita,
                    'url' => '',
                    'status' => $c->status,
                    'user' => (object)['name' => $c->user ? $c->user->name : 'User Lokal'],
                    'created_at' => $c->created_at,
                    'parwa' => (object)[
                        'slug' => $parwaSlug,
                        'nama' => $parwaNama,
                    ],
                ];

                // Fetch related stories from local + backend
                $relatedStories = collect();
                try {
                    $relLocal = \App\Models\Cerita::where('id', '!=', $localId)->take(3)->get()->map(function($item) {
                        return (object)[
                            'id' => 'local-' . $item->id,
                            'judul' => $item->judul,
                            'sumber' => $item->sumber,
                            'user' => (object)['name' => $item->user ? $item->user->name : 'User Lokal'],
                            'created_at' => $item->created_at,
                        ];
                    });
                    $relatedStories = $relatedStories->concat($relLocal);
                } catch (\Exception $e) {}

                try {
                    $relResp = $apiService->getParwas(1, 5);
                    if ($relResp->successful()) {
                        $relData = $relResp->json();
                        $relBackend = collect($relData['items'] ?? [])
                            ->filter(fn($item) => $item['id'] > 2105)
                            ->take(3)
                            ->map(function($item) {
                                return (object)[
                                    'id' => $item['id'],
                                    'judul' => $item['judul'] ?? $item['title'] ?? 'Cerita Parwa',
                                    'sumber' => $item['book'] ?? 'Backend API',
                                    'user' => (object)['name' => 'User Backend'],
                                    'created_at' => \Carbon\Carbon::parse($item['createdAt'] ?? now()),
                                ];
                            });
                        $relatedStories = $relatedStories->concat($relBackend);
                    }
                } catch (\Exception $e) {}

                // Find next/prev local stories
                $prevCeritaId = null;
                $nextCeritaId = null;
                try {
                    $prev = \App\Models\Cerita::where('id', '<', $localId)->orderBy('id', 'desc')->first();
                    $next = \App\Models\Cerita::where('id', '>', $localId)->orderBy('id', 'asc')->first();
                    if ($prev) $prevCeritaId = 'local-' . $prev->id;
                    if ($next) $nextCeritaId = 'local-' . $next->id;
                } catch (\Exception $ex) {}

                return view('cerita.show', compact('cerita', 'relatedStories', 'prevCeritaId', 'nextCeritaId'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengambil detail cerita lokal: " . $e->getMessage());
                abort(404);
            }
        }

        try {
            $response = $apiService->getParwaById($id);
            if ($response->successful()) {
                $c = $response->json();
                
                $bookSearch = str_replace('Parva', 'Parwa', $c['book'] ?? '');
                $bookSearch = str_replace('Swargarohanika', 'Svargarohana', $bookSearch);
                $parwaModel = \App\Models\Parwa::where('name', 'like', "%{$bookSearch}%")->first();
                $parwaSlug = $parwaModel ? $parwaModel->slug : \Illuminate\Support\Str::slug($c['book'] ?? 'adi-parva');
                $parwaNama = $parwaModel ? $parwaModel->nama : ($c['book'] ?? 'Adi Parwa');

                // Detect which language has actual content (based on what was uploaded)
                $isiEn   = $c['isi']    ?? '';
                $isiId   = $c['isi_id'] ?? '';

                $hasEn = ($isiEn !== '' && $isiEn !== '-' && strlen(trim(strip_tags($isiEn))) > 10);
                $hasId = ($isiId !== '' && $isiId !== '-' && strlen(trim(strip_tags($isiId))) > 10);

                // Determine content language for display label
                if ($hasEn && $hasId) {
                    $contentLang = 'both';
                } elseif ($hasEn) {
                    $contentLang = 'en';
                } elseif ($hasId) {
                    $contentLang = 'id';
                } else {
                    $contentLang = 'id';
                }

                // Pick content based on UI locale with smart fallback:
                // If the locale matches available content → show it
                // If the locale doesn't match → fallback to whichever has content
                $locale = session('locale', 'id');
                if ($locale === 'id') {
                    $displayedContent = $hasId ? $isiId : ($hasEn ? $isiEn : $isiId);
                } else {
                    $displayedContent = $hasEn ? $isiEn : ($hasId ? $isiId : $isiEn);
                }

                $cerita = (object)[
                    'id'          => $c['id'],
                    'judul'       => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                    'book'        => $c['book'] ?? 'Backend API',
                    'sumber'      => $c['book'] ?? 'Backend API',
                    'sub_parva'   => $c['sub_parva'] ?? '-',
                    'section'     => $c['section'] ?? '-',
                    'isi'         => $displayedContent,
                    'isi_en'      => $isiEn,
                    'isi_id'      => $isiId,
                    'cerita'      => $displayedContent,
                    'content_lang'=> $contentLang,
                    'url'         => $c['url'] ?? '',
                    'status'      => 'approved',
                    'user'        => (object)['name' => 'User Backend'],
                    'created_at'  => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                    'parwa'       => (object)[
                        'slug' => $parwaSlug,
                        'nama' => $parwaNama,
                    ],
                ];

                // Fetch related stories from backend
                $relatedStories = [];
                try {
                    $relResp = $apiService->getParwas(1, 10);
                    if ($relResp->successful()) {
                        $relData = $relResp->json();
                        $relatedStories = collect($relData['items'] ?? [])
                            ->filter(fn($item) => $item['id'] > 2105 && $item['id'] != $id)
                            ->take(6)
                            ->map(function($item) {
                                return (object)[
                                    'id' => $item['id'],
                                    'judul' => $item['judul'] ?? $item['title'] ?? 'Cerita Parwa',
                                    'sumber' => $item['book'] ?? 'Backend API',
                                    'user' => (object)['name' => 'User Backend'],
                                    'created_at' => \Carbon\Carbon::parse($item['createdAt'] ?? now()),
                                ];
                            });
                    }
                } catch (\Exception $e) {
                    // Ignore related stories fetch failure
                }

                // Find next/prev backend stories
                $prevCeritaId = null;
                $nextCeritaId = null;
                try {
                    $listResp = $apiService->getParwas(1, 100);
                    if ($listResp->successful()) {
                        $listData = $listResp->json();
                        $allItems = collect($listData['items'] ?? [])->filter(fn($item) => $item['id'] > 2105)->values();
                        
                        $currentIdx = -1;
                        foreach ($allItems as $idx => $item) {
                            if ($item['id'] == $id) {
                                $currentIdx = $idx;
                                break;
                            }
                        }
                        
                        if ($currentIdx !== -1) {
                            if (isset($allItems[$currentIdx - 1])) {
                                $prevCeritaId = $allItems[$currentIdx - 1]['id'];
                            }
                            if (isset($allItems[$currentIdx + 1])) {
                                $nextCeritaId = $allItems[$currentIdx + 1]['id'];
                            }
                        }
                    }
                } catch (\Exception $ex) {
                    \Illuminate\Support\Facades\Log::warning("Gagal mengambil list cerita untuk navigasi: " . $ex->getMessage());
                }

                return view('cerita.show', compact('cerita', 'relatedStories', 'prevCeritaId', 'nextCeritaId'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil detail cerita dari backend: " . $e->getMessage());
        }
        abort(404);
    }

    // =========================
    // FORM EDIT CERITA
    // =========================
    public function edit($id, BackendApiService $apiService)
    {
        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::findOrFail($localId);
                $cerita = (object)[
                    'id' => $id,
                    'judul' => $c->judul,
                    'book' => $c->sumber,
                    'sumber' => $c->sumber,
                    'sub_parva' => $c->sub_parwa ?? '',
                    'section' => 'Bab 1',
                    'isi' => $c->cerita,
                    'cerita' => $c->cerita,
                    'url' => '',
                ];
                $parwas = \App\Models\Parwa::all();
                return view('cerita.edit', compact('cerita', 'parwas'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengambil edit cerita lokal: " . $e->getMessage());
                abort(404);
            }
        }

        try {
            $response = $apiService->getParwaById($id);
            if ($response->successful()) {
                $c = $response->json();

                // Detect which language was used when uploading
                $isiEn = $c['isi']    ?? '';
                $isiId = $c['isi_id'] ?? '';
                $hasEn = ($isiEn !== '' && $isiEn !== '-' && strlen(trim(strip_tags($isiEn))) > 10);
                $hasId = ($isiId !== '' && $isiId !== '-' && strlen(trim(strip_tags($isiId))) > 10);

                // Content lang: prefer id if available, else en
                if ($hasId && !$hasEn) {
                    $contentLang    = 'id';
                    $editableContent = $isiId;
                } elseif ($hasEn && !$hasId) {
                    $contentLang    = 'en';
                    $editableContent = $isiEn;
                } else {
                    // Both available: default to id
                    $contentLang    = 'id';
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
                $parwas = \App\Models\Parwa::all();
                return view('cerita.edit', compact('cerita', 'parwas'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil edit cerita dari backend: " . $e->getMessage());
        }
        abort(404);
    }

    // =========================
    // UPDATE CERITA
    // =========================
    public function update(Request $request, $id, BackendApiService $apiService)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:500',
            'cerita' => 'required',
            'section' => 'nullable|string|max:255',
        ]);

        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::findOrFail($localId);
                $c->update([
                    'judul' => $request->judul,
                    'sumber' => $request->sumber,
                    'cerita' => $request->cerita,
                    'sub_parwa' => $request->sub_parwa ?? '-',
                ]);
                return redirect()
                    ->route('cerita.upload')
                    ->with('success', 'Cerita lokal berhasil diperbarui');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Gagal memperbarui cerita lokal: ' . $e->getMessage()])->withInput();
            }
        }

        try {
            // Determine which field to update based on upload language
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

            // Save to the correct language field, preserve the other
            if ($contentLang === 'id') {
                $isiEnPayload = $currentIsiEn ?: '-';
                $isiIdPayload = $request->cerita;
            } else {
                $isiEnPayload = $request->cerita;
                $isiIdPayload = $currentIsiId ?: '-';
            }

            $payload = [
                'book'      => $request->book ?: $request->sumber,
                'judul'     => $request->judul ?: '',
                'isi'       => $isiEnPayload,
                'isi_id'    => $isiIdPayload,
                'sub_parva' => '-',
                'section'   => $request->section ?: 'Bab 1',
                'url'       => $request->sumber ?: null,
            ];

            // First attempt: use current user's token
            $response = $apiService->updateParwa($id, $payload, false);

            // If user token is unauthorized/forbidden, retry with admin token
            if (!$response->successful() && in_array($response->status(), [401, 403], true)) {
                if ($apiService->hasAdminToken()) {
                    $response = $apiService->updateParwa($id, $payload, true);
                }
            }

            if (!$response->successful()) {
                $msg = $response->json('message') ?? 'Gagal memperbarui cerita di backend.';
                return back()->withErrors(['error' => $msg])->withInput();
            }

            // Invalidate cache for the affected book
            $bookForCache = $request->book ?: $request->sumber;
            \App\Services\ParwaCacheService::invalidateForBook($bookForCache);

            return redirect()
                ->route('cerita.upload')
                ->with('success', 'Cerita berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui cerita di backend: ' . $e->getMessage()]);
        }
    }

    public function index(BackendApiService $apiService)
    {
        $ceritas = collect();
        try {
            $response = $apiService->getParwas(1, 100);
            if ($response->successful()) {
                $apiData = $response->json();
                $items = $apiData['items'] ?? [];
                
                $ceritas = collect($items)
                    ->filter(fn($c) => $c['id'] > 2105)
                    ->map(function($c) {
                        return (object)[
                            'id' => $c['id'],
                            'judul' => $c['judul'] ?? $c['title'] ?? 'Cerita Parwa',
                            'book' => $c['book'] ?? 'Backend API',
                            'sumber' => $c['book'] ?? 'Backend API', // view uses 'sumber'
                            'status' => 'approved',
                            'user' => (object)['name' => 'User Backend'],
                            'created_at' => \Carbon\Carbon::parse($c['createdAt'] ?? now()),
                        ];
                    });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memuat index cerita dari backend: " . $e->getMessage());
        }

        // Merge local stories
        try {
            $localCeritas = \App\Models\Cerita::with('user')->get()->map(function($c) {
                return (object)[
                    'id' => 'local-' . $c->id,
                    'judul' => $c->judul,
                    'book' => $c->sumber,
                    'sumber' => $c->sumber,
                    'status' => $c->status,
                    'user' => (object)['name' => $c->user ? $c->user->name : 'User Lokal'],
                    'created_at' => $c->created_at,
                ];
            });
            $ceritas = $ceritas->concat($localCeritas);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil list index cerita lokal: " . $e->getMessage());
        }

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

    public function destroy($id, BackendApiService $apiService)
    {
        $user = auth()->user();

        if (strpos($id, 'local-') === 0) {
            $localId = str_replace('local-', '', $id);
            try {
                $c = \App\Models\Cerita::findOrFail($localId);
                // Validasi kepemilikan lokal: Hanya admin atau pemilik cerita yang boleh menghapus
                if ($user->role !== 'admin' && $c->user_id !== $user->id) {
                    return back()->with('error', 'Anda tidak memiliki hak untuk menghapus cerita ini.');
                }
                $c->delete();
                return redirect()
                    ->route('cerita.upload')
                    ->with('success', 'Cerita lokal berhasil dihapus');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal menghapus cerita lokal: ' . $e->getMessage());
            }
        }

        try {
            // First attempt: use current user's token
            $response = $apiService->deleteParwa($id, false);

            // If user token is unauthorized/forbidden, retry with admin token
            if (!$response->successful() && in_array($response->status(), [401, 403], true)) {
                if ($apiService->hasAdminToken()) {
                    $response = $apiService->deleteParwa($id, true);
                }
            }

            if (!$response->successful()) {
                $msg = $response->json('message') ?? 'Gagal menghapus cerita di backend.';
                return back()->with('error', $msg);
            }

            // Invalidate all parwa caches since we don't know the book from the ID alone
            \App\Services\ParwaCacheService::invalidateAll();

            return redirect()
                ->route('cerita.upload')
                ->with('success', 'Cerita berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus cerita: ' . $e->getMessage());
        }
    }

    private function getVersionNameFromTitle(string $title): string
    {
        $title = trim($title);
        // Format 1: "{Book} - {Version}, tr. - {Section}"
        if (strpos($title, ', tr.') !== false) {
            $parts = explode(' - ', $title);
            if (count($parts) >= 2) {
                $verPart = $parts[1]; // e.g. "Budi's, tr."
                $verSubParts = explode(', tr.', $verPart);
                return trim($verSubParts[0]);
            }
        }
        // Format 2: "{Book} Sec {Num} - {Version}"
        $parts = explode(' - ', $title);
        if (count($parts) >= 2) {
            return trim($parts[count($parts) - 1]);
        }
        return $title;
    }

    private function isUserStory(string $userName, string $storyTitle): bool
    {
        $userName = strtolower(trim($userName));
        $versionName = strtolower($this->getVersionNameFromTitle($storyTitle));
        
        $normalize = function(string $name) {
            $name = preg_replace("/['’]s\b/", "", $name);
            $name = preg_replace("/[^a-z0-9]/", "", $name);
            return $name;
        };
        
        $nUser = $normalize($userName);
        $nVersion = $normalize($versionName);
        
        if (empty($nUser) || empty($nVersion)) {
            return false;
        }
        
        if ($nUser === $nVersion || strpos($nUser, $nVersion) !== false || strpos($nVersion, $nUser) !== false) {
            return true;
        }
        
        $words = preg_split('/\s+/', $userName);
        foreach ($words as $word) {
            $nWord = $normalize($word);
            if (strlen($nWord) > 2 && (strpos($nVersion, $nWord) !== false || strpos($nWord, $nVersion) !== false)) {
                return true;
            }
        }
        
        return false;
    }
}
