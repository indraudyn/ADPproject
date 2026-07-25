<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
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
        
        $sections = []; // No API sections anymore
        $versions = []; // No API versions anymore

        // Local ceritas fallback
        $ceritas = $parwa->ceritas()
            ->where('status', 'approved')
            ->oldest()
            ->get();

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

    public function video($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $videos = $parwa->videos()->where('status', 'approved')->get();
        
        return view('parwa.video', compact('parwa', 'videos'));
    }
}
