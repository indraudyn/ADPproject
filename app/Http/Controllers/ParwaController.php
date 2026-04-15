<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
use Illuminate\Http\Request;

class ParwaController extends Controller
{
    public function index()
    {
        $parwas = Parwa::all();
        return view('parwa', compact('parwas'));
    }

    public function show($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();

        // Ambil cerita approved, urutkan dari yang paling pertama diupload.
        // Group by sub_parwa dan ambil yang pertama (tertua) sebagai representasi section card.
        // Cerita lain di section yang sama akan muncul di "Lainnya" pada halaman baca.
        $ceritas = $parwa->ceritas()
            ->where('status', 'approved')
            ->oldest()              // urutkan dari yang pertama diupload
            ->get()
            ->groupBy('sub_parwa')
            ->map(fn ($group) => $group->first()); // wakil terambil adalah yang pertama

        return view('parwa.detail', compact('parwa', 'ceritas'));
    }

    public function video($slug)
    {
        $parwa = Parwa::where('slug', $slug)->firstOrFail();
        $videos = $parwa->videos()->where('status', 'approved')->get();
        return view('parwa.video', compact('parwa', 'videos'));
    }
}
