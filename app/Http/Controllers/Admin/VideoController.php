<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with(['parwa', 'user'])->latest()->paginate(10);
        return view('admin.video.index', compact('videos'));
    }

    public function create(\App\Services\BackendApiService $apiService)
    {
        $parwas = \App\Models\Parwa::all();
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk admin video create: " . $e->getMessage());
        }
        return view('admin.video.create', compact('parwas', 'versions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|in:youtube,upload',
            'url' => 'required_if:type,youtube|nullable|url',
            'video_file' => 'required_if:type,upload|nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:50000',
        ]);

        $url = $request->url;
        if ($request->type == 'upload' && $request->hasFile('video_file')) {
            $url = $request->file('video_file')->store('videos', 'public');
        }

        Video::create([
            'parwa_id' => $request->parwa_id,
            'section' => $request->section,
            'version' => $request->version,
            'title' => $request->title,
            'source' => auth()->user()->name,
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => 'approved', 
        ]);

        return redirect()->route('admin.video.index')->with('success', 'Video berhasil ditambahkan!');
    }

    public function edit($id, \App\Services\BackendApiService $apiService)
    {
        $video = Video::findOrFail($id);
        $parwas = \App\Models\Parwa::all();
        $versions = [];
        try {
            $versionsResponse = $apiService->getVersions();
            if ($versionsResponse->successful()) {
                $versionsData = $versionsResponse->json();
                $versions = $versionsData['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal mengambil versions untuk admin video edit: " . $e->getMessage());
        }
        return view('admin.video.edit', compact('video', 'parwas', 'versions'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|in:youtube,upload',
            'url' => 'required_if:type,youtube|nullable|url',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:50000',
        ]);

        $url = $video->url;
        if ($request->type == 'youtube') {
            $url = $request->url;
        } elseif ($request->type == 'upload' && $request->hasFile('video_file')) {
            $url = $request->file('video_file')->store('videos', 'public');
        }

        $video->update([
            'parwa_id' => $request->parwa_id,
            'section' => $request->section,
            'version' => $request->version,
            'title' => $request->title,
            'type' => $request->type,
            'url' => $url,
        ]);

        return redirect()->route('admin.video.index')->with('success', 'Video berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        Video::where('id', $id)->update(['status' => $request->status]);
        return back()->with('success', 'Status video berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return back()->with('success', 'Video berhasil dihapus.');
    }
}
