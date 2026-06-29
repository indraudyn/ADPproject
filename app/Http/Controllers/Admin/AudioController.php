<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\Parwa;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function index()
    {
        $audios = Audio::with(['parwa', 'user'])->latest()->paginate(10);
        return view('admin.audio.index', compact('audios'));
    }

    public function create(\App\Services\BackendApiService $apiService)
    {
        $parwas = Parwa::all();
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk admin audio create: " . $e->getMessage());
        }
        return view('admin.audio.create', compact('parwas', 'versions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|in:link,upload',
            'url' => 'required_if:type,link|nullable|url',
            'audio_file' => 'required_if:type,upload|nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-m4a,audio/m4a,audio/mpga,audio/x-mpeg|max:20000',
        ]);

        $url = $request->url;
        if ($request->type == 'upload' && $request->hasFile('audio_file')) {
            $url = $request->file('audio_file')->store('audios', 'public');
        }

        Audio::create([
            'parwa_id' => $request->parwa_id,
            'section' => $request->section,
            'version' => $request->version,
            'title' => $request->title,
            'source' => $request->type === 'link' ? 'Audio Link' : auth()->user()->name,
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => 'approved', 
        ]);

        return redirect()->route('admin.audio.index')->with('success', 'Audio berhasil ditambahkan!');
    }

    public function edit($id, \App\Services\BackendApiService $apiService)
    {
        $audio = Audio::findOrFail($id);
        $parwas = Parwa::all();
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk admin audio edit: " . $e->getMessage());
        }
        return view('admin.audio.edit', compact('audio', 'parwas', 'versions'));
    }

    public function update(Request $request, $id)
    {
        $audio = Audio::findOrFail($id);

        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|in:link,upload',
            'url' => 'required_if:type,link|nullable|url',
            'audio_file' => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-m4a,audio/m4a,audio/mpga,audio/x-mpeg|max:20000',
        ]);

        $url = $audio->url;
        if ($request->type == 'link') {
            $url = $request->url;
        } elseif ($request->type == 'upload' && $request->hasFile('audio_file')) {
            $url = $request->file('audio_file')->store('audios', 'public');
        }

        $audio->update([
            'parwa_id' => $request->parwa_id,
            'section' => $request->section,
            'version' => $request->version,
            'title' => $request->title,
            'type' => $request->type,
            'url' => $url,
        ]);

        return redirect()->route('admin.audio.index')->with('success', 'Audio berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        Audio::where('id', $id)->update(['status' => $request->status]);
        return back()->with('success', 'Status audio berhasil diubah.');
    }

    public function destroy($id)
    {
        $audio = Audio::findOrFail($id);
        $audio->delete();

        return back()->with('success', 'Audio berhasil dihapus.');
    }
}
