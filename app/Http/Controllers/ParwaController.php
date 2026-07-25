<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
use App\Models\Cerita;
use App\Models\Video;
use App\Models\Audio;
use Illuminate\Http\Request;

class ParwaController extends Controller
{
    public static function toSlug(string $text): string
    {
        return \Illuminate\Support\Str::slug($text);
    }

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

    public function show($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $bookName = self::getBookNameBySlug($slug);
        $versionName = request()->query('version') ?: session('selected_parwa_version');
        
        $query = Cerita::where('book', $bookName)->where('status', 'approved');
        
        if ($versionName && $versionName !== 'all') {
            $version = \App\Models\Version::where('name', $versionName)->first();
            if ($version) {
                $query->where('versionId', $version->id);
            }
        }
        
        $ceritas = $query->orderBy('id', 'asc')->get();

        $sections = $ceritas->map(function ($c) {
            return [
                'section' => $c->section ?? 'Bab 1',
                'sub_parva' => $c->sub_parwa ?? '-',
            ];
        })->unique('section')->values()->toArray();
        
        $versions = \App\Models\Version::pluck('name')->toArray();

        // Parwa-level media (section is null)
        $videos = Video::where('parwa_id', $parwa->id)
            ->whereNull('section')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $audios = Audio::where('parwa_id', $parwa->id)
            ->whereNull('section')
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('parwa.detail', compact('parwa', 'sections', 'bookName', 'ceritas', 'versions', 'videos', 'audios'));
    }

    public function read($bookSlug, $sectionSlug)
    {
        $book = self::fromSlug($bookSlug);
        // Special case handling for 'bab-1' -> 'Bab 1'
        $section = ucwords(str_replace('-', ' ', $sectionSlug));
        
        $versionName = request()->query('version') ?: session('selected_parwa_version');
        $version = \App\Models\Version::where('name', $versionName)->first();
        $versionId = $version ? $version->id : 1;

        $contentQuery = Cerita::where('book', $book)->where('section', $section)->where('status', 'approved');
        
        // Try specific version first
        $content = (clone $contentQuery)->where('versionId', $versionId)->get();
        if ($content->isEmpty()) {
            // Fallback to any version
            $content = $contentQuery->get();
        }

        if ($content->isEmpty()) {
            abort(404, 'Konten bab tidak ditemukan.');
        }

        $locale = session('locale', 'id');
        $content = $content->map(function ($item) use ($locale) {
            $displayedContent = $item->isi ?? '';

            if ($locale === 'id') {
                if (!empty($item->isi_id) && strlen($item->isi_id) > 15) {
                    $displayedContent = $item->isi_id;
                }
            } else {
                if (!empty($item->isi) && $item->isi !== '-' && strlen($item->isi) > 1) {
                    $displayedContent = $item->isi;
                } elseif (!empty($item->isi_id)) {
                    $displayedContent = $item->isi_id;
                }
            }
            $item->isi = $displayedContent;
            
            $title = trim($item->judul ?? 'Terjemahan Resmi');
            $titleParts = explode(' - ', $title);
            if (count($titleParts) >= 2 && (stripos($titleParts[0], 'parva') !== false || stripos($titleParts[0], 'parwa') !== false)) {
                array_shift($titleParts);
                $title = implode(' - ', $titleParts);
                $item->judul = $title;
            }
            return $item;
        });

        $allSections = Cerita::where('book', $book)->where('status', 'approved')->orderBy('id', 'asc')->pluck('section')->unique()->values()->toArray();
        $currentIndex = array_search($section, $allSections);
        $prevSection = ($currentIndex > 0) ? \Illuminate\Support\Str::slug($allSections[$currentIndex - 1]) : null;
        $nextSection = ($currentIndex !== false && $currentIndex < count($allSections) - 1) ? \Illuminate\Support\Str::slug($allSections[$currentIndex + 1]) : null;

        $parwa = Parwa::where('slug', $bookSlug)->first();
        if (!$parwa) {
            // Fallback: try to find by name generated from slug
            $possibleName = self::fromSlug($bookSlug);
            $parwa = Parwa::where('name', 'LIKE', '%' . str_replace('va', 'wa', strtolower($possibleName)) . '%')
                ->orWhere('name', 'LIKE', '%' . str_replace('wa', 'va', strtolower($possibleName)) . '%')
                ->first();
        }
        
        $parwaId = $parwa ? $parwa->id : 0;

        $videos = Video::where('parwa_id', $parwaId)->where('section', $section)->where('status', 'approved')->get();
        $audios = Audio::where('parwa_id', $parwaId)->where('section', $section)->where('status', 'approved')->get();

        return view('parwa.read', compact('content', 'book', 'section', 'bookSlug', 'sectionSlug', 'parwa', 'prevSection', 'nextSection', 'videos', 'audios'));
    }

    public function video($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $videos = $parwa->videos()->where('status', 'approved')->get();
        
        return view('parwa.video', compact('parwa', 'videos'));
    }

    public function sectionsByBook(Request $request)
    {
        $book = $request->query('book');
        if (!$book) {
            return response()->json(['data' => []]);
        }

        $sections = Cerita::where('book', $book)
            ->where('status', 'approved')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'section' => $c->section ?? 'Bab 1',
                    'sub_parva' => $c->sub_parwa ?? '-',
                ];
            })
            ->unique('section')
            ->values();

        return response()->json(['data' => $sections]);
    }
}
