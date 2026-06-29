<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
use App\Models\Audio;
use App\Services\BackendApiService;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function upload()
    {
        $audios = Audio::where('user_id', auth()->id())->latest()->get();
        return view('audio.upload', compact('audios'));
    }

    public function create(BackendApiService $apiService)
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
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk audio create: " . $e->getMessage());
        }
        return view('audio.create', compact('parwas', 'versions'));
    }

    public function storeUser(Request $request)
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
            'status' => in_array(auth()->user()->role, ['admin', 'narasumber']) ? 'approved' : 'pending',
        ]);

        return redirect()->route('audio.upload')->with('success', 'Audio berhasil diupload!');
    }

    public function edit(Audio $audio, BackendApiService $apiService)
    {
        if ($audio->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }
        $parwas = Parwa::all();
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk audio edit: " . $e->getMessage());
        }
        return view('audio.edit', compact('audio', 'parwas', 'versions'));
    }

    public function update(Request $request, Audio $audio)
    {
        if ($audio->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }

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
            'status' => 'pending', // Reset status to pending to require admin re-approval
        ]);

        return redirect()->route('audio.upload')->with('success', 'Audio berhasil diperbarui!');
    }

    public function destroy(Audio $audio)
    {
        if ($audio->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }
        
        $audio->delete();
        return back()->with('success', 'Audio berhasil dihapus.');
    }
}
