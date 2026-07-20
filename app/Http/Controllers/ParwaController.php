<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
use App\Services\BackendApiService;
use App\Services\ParwaCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParwaController extends Controller
{
    /**
     * Convert any string to a URL-friendly slug.
     * e.g. "Adi Parva" => "adi-parva", "Bab I" => "bab-i"
     */
    public static function toSlug(string $text): string
    {
        return \Illuminate\Support\Str::slug($text);
    }

    /**
     * Convert a slug back to its original title case form.
     * e.g. "adi-parva" => "Adi Parva", "bab-i" => "Bab I"
     */
    public static function fromSlug(string $slug): string
    {
        return ucwords(str_replace('-', ' ', $slug));
    }

    public static function getBookNameBySlug(string $slug): string
    {
        $map = [
            'adi-parwa' => 'Adi Parva',
            'sabha-parwa' => 'Sabha Parva',
            'vana-parwa' => 'Vana Parva',
            'virata-parwa' => 'Virata Parva',
            'udyoga-parwa' => 'Udyoga Parva',
            'bhishma-parwa' => 'Bhishma Parva',
            'drona-parwa' => 'Drona Parva',
            'karna-parwa' => 'Karna Parva',
            'shalya-parwa' => 'Shalya Parva',
            'sauptika-parwa' => 'Sauptika Parva',
            'stri-parwa' => 'Stri Parva',
            'shanti-parwa' => 'Shanti Parva',
            'anushasana-parwa' => 'Anushasana Parva',
            'ashvamedhika-parwa' => 'Ashvamedhika Parva',
            'ashramavasika-parwa' => 'Ashramavasika Parva',
            'mausala-parwa' => 'Mausala Parva',
            'mahaprasthanika-parwa' => 'Mahaprasthanika Parva',
            'svargarohana-parwa' => 'Swargarohanika Parva',
        ];

        return $map[$slug] ?? ucwords(str_replace('-', ' ', $slug));
    }

    public function index()
    {
        $parwas = Parwa::all();
        return view('parwa', compact('parwas'));
    }

    public function show($slug, BackendApiService $apiService)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $bookName = self::getBookNameBySlug($slug);
        
        $selectedVersion = request()->query('version');

        // ── Sections (cached for 5 minutes) ──
        $sections = ParwaCacheService::rememberSections($bookName, $selectedVersion, function () use ($apiService, $bookName, $selectedVersion) {
            try {
                if ($selectedVersion && $selectedVersion !== 'all') {
                    $response = $apiService->getSectionsByBook($bookName, $selectedVersion);
                } else {
                    $response = $apiService->getSectionsByBook($bookName);
                }
                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
            } catch (\Exception $e) {
                Log::warning("Gagal mengambil sections dari backend: " . $e->getMessage());
            }
            return [];
        });

        // ── Local ceritas fallback (cached for 5 minutes) ──
        $ceritas = [];
        if (empty($sections)) {
            $ceritas = ParwaCacheService::rememberCeritas($parwa->id, function () use ($parwa) {
                return $parwa->ceritas()
                    ->where('status', 'approved')
                    ->oldest()
                    ->get()
                    ->groupBy('sub_parwa')
                    ->map(fn ($group) => $group->first());
            });
        }

        // ── Active versions for this book (cached for 5 minutes) ──
        $versions = ParwaCacheService::rememberVersions($bookName, function () use ($apiService, $bookName) {
            try {
                $versionsResponse = $apiService->getVersions();
                if ($versionsResponse->successful()) {
                    $allVersions = $versionsResponse->json()['data'] ?? [];
                    // Check active versions concurrently using Http::pool
                    return $apiService->checkActiveVersions($bookName, $allVersions);
                }
            } catch (\Exception $e) {
                Log::warning("Gagal mengambil versions dari backend: " . $e->getMessage());
            }
            return [];
        });

        // ── Parwa-level media (section is null) ──
        $videos = \App\Models\Video::where('parwa_id', $parwa->id)
            ->whereNull('section')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $audios = \App\Models\Audio::where('parwa_id', $parwa->id)
            ->whereNull('section')
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('parwa.detail', compact('parwa', 'sections', 'bookName', 'ceritas', 'versions', 'videos', 'audios'));
    }

    public function read($bookSlug, $sectionSlug, BackendApiService $apiService)
    {
        // Convert slugs back to real names for API calls
        $book = self::fromSlug($bookSlug);
        $section = self::fromSlug($sectionSlug);
        
        // Get version from URL param first, then from session as fallback
        $version = request()->query('version') ?: session('selected_parwa_version');
        
        $content = ParwaCacheService::rememberContentBySection($book, $section, $version, function () use ($apiService, $book, $section, $version) {
            try {
                $response = $apiService->getContentBySection($book, $section, $version);
                if ($response->successful()) {
                    $apiData = $response->json();
                    return $apiData['data'] ?? $apiData;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengambil konten bab dari backend: " . $e->getMessage());
            }
            return null;
        });

        if (!$content || empty($content)) {
            abort(404, 'Konten bab tidak ditemukan atau server backend tidak merespon.');
        }

        // Apply language switcher to static parwa content dynamically
        $locale = session('locale', 'id');
        $content = collect($content)
            ->map(function ($item) use ($locale) {
            $displayedContent = $item['isi'] ?? '';

            if ($locale === 'id') {
                if (isset($item['isi_id']) && strlen($item['isi_id']) > 15) {
                    $displayedContent = $item['isi_id'];
                }
            } else {
                if (isset($item['isi']) && $item['isi'] !== '-' && strlen($item['isi']) > 1) {
                    $displayedContent = $item['isi'];
                } elseif (isset($item['isi_id'])) {
                    $displayedContent = $item['isi_id'];
                }
            }

            $item['isi'] = $displayedContent;

            // Extract and clean version title from raw judul
            $title = trim($item['judul'] ?? 'Terjemahan Resmi');
            
            // Remove parwa name from the beginning of the title
            $titleParts = explode(' - ', $title);
            if (count($titleParts) >= 2 && (stripos($titleParts[0], 'parva') !== false || stripos($titleParts[0], 'parwa') !== false)) {
                array_shift($titleParts);
                $title = implode(' - ', $titleParts);
                $item['judul'] = $title;
            }

            $versiClean = 'Terjemahan Resmi';
            if (strpos($title, ', tr.') !== false) {
                $parts = explode(' - ', $title);
                if (count($parts) >= 1) {
                    // After removing parwa, the translator is usually the first part
                    $verPart = $parts[0]; 
                    $verSubParts = explode(', tr.', $verPart);
                    $versiClean = 'Versi ' . trim($verSubParts[0]);
                }
            } else {
                $parts = explode(' - ', $title);
                if (count($parts) >= 1) {
                    // Try to extract version name
                    $versionName = trim($parts[count($parts) - 1]);
                    // If it's something like "Bab 1", fallback to first part if possible, otherwise keep original logic
                    if (stripos($versionName, 'bab') === 0 || stripos($versionName, 'section') === 0) {
                        $versionName = trim($parts[0]);
                    }
                    $versiClean = stripos($versionName, 'versi') === 0 ? ucwords($versionName) : 'Versi ' . $versionName;
                }
            }
            $item['versi_clean'] = $versiClean;

            return $item;
        })->toArray();

        // Cari data Parwa lokal untuk tampilan navigasi / header kembali
        // Cari parwa berdasarkan nama buku backend
        $slugMap = [
            'Adi Parva' => 'adi-parwa',
            'Sabha Parva' => 'sabha-parwa',
            'Vana Parva' => 'vana-parwa',
            'Virata Parva' => 'virata-parwa',
            'Udyoga Parva' => 'udyoga-parwa',
            'Bhishma Parva' => 'bhishma-parwa',
            'Drona Parva' => 'drona-parwa',
            'Karna Parva' => 'karna-parwa',
            'Shalya Parva' => 'shalya-parwa',
            'Sauptika Parva' => 'sauptika-parwa',
            'Stri Parva' => 'stri-parwa',
            'Shanti Parva' => 'shanti-parwa',
            'Anushasana Parva' => 'anushasana-parwa',
            'Ashvamedhika Parva' => 'ashvamedhika-parwa',
            'Ashramavasika Parva' => 'ashramavasika-parwa',
            'Mausala Parva' => 'mausala-parwa',
            'Mahaprasthanika Parva' => 'mahaprasthanika-parwa',
            'Swargarohanika Parva' => 'svargarohana-parwa',
        ];
        $slug = $slugMap[$book] ?? 'adi-parwa';
        $parwa = Parwa::where('slug', $slug)->first();

        // Query sections to find adjacent chapters for next/prev navigation
        $prevSection = null;
        $nextSection = null;
        
        $sectionsList = ParwaCacheService::rememberSections($book, $version, function () use ($apiService, $book, $version) {
            try {
                if ($version && $version !== 'all') {
                    $sectionsResponse = $apiService->getSectionsByBook($book, $version);
                } else {
                    $sectionsResponse = $apiService->getSectionsByBook($book);
                }
                if ($sectionsResponse->successful()) {
                    return $sectionsResponse->json()['data'] ?? [];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengambil daftar sections untuk navigasi: " . $e->getMessage());
            }
            return [];
        });

        if (!empty($sectionsList)) {
            $currentIdx = -1;
            foreach ($sectionsList as $idx => $sec) {
                if (trim($sec['section']) === trim($section)) {
                    $currentIdx = $idx;
                    break;
                }
            }
            
            if ($currentIdx !== -1) {
                if (isset($sectionsList[$currentIdx - 1])) {
                    $prevSection = self::toSlug($sectionsList[$currentIdx - 1]['section']);
                }
                if (isset($sectionsList[$currentIdx + 1])) {
                    $nextSection = self::toSlug($sectionsList[$currentIdx + 1]['section']);
                }
            }
        }

        $videos = collect();
        $audios = collect();
        if ($parwa) {
            // Filter by URL version param. If no version, show all approved media.
            $videosQuery = \App\Models\Video::where('parwa_id', $parwa->id)
                ->where('status', 'approved')
                ->whereRaw('TRIM(LOWER(section)) = ?', [trim(strtolower($section))]);

            if ($version && $version !== 'all') {
                $videosQuery->whereRaw('TRIM(LOWER(version)) = ?', [trim(strtolower($version))]);
            }
            $videos = $videosQuery->latest()->get();

            $audiosQuery = \App\Models\Audio::where('parwa_id', $parwa->id)
                ->where('status', 'approved')
                ->whereRaw('TRIM(LOWER(section)) = ?', [trim(strtolower($section))]);

            if ($version && $version !== 'all') {
                $audiosQuery->whereRaw('TRIM(LOWER(version)) = ?', [trim(strtolower($version))]);
            }
            $audios = $audiosQuery->latest()->get();

            \Illuminate\Support\Facades\Log::info('ParwaController@read media filter', [
                'section_url' => $section,
                'section_lower' => trim(strtolower($section)),
                'url_version' => $version,
                'videos_count' => $videos->count(),
                'audios_count' => $audios->count(),
            ]);
        }

        $bookSlug = self::toSlug($book);
        $sectionSlug = self::toSlug($section);
        return view('parwa.read', compact('content', 'book', 'section', 'bookSlug', 'sectionSlug', 'parwa', 'prevSection', 'nextSection', 'videos', 'audios'));
    }

    public function video($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $videos = $parwa->videos()->where('status', 'approved')->get();
        
        return view('parwa.video', compact('parwa', 'videos'));
    }


}
