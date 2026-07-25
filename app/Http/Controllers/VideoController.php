<?php

namespace App\Http\Controllers;

use App\Models\Parwa;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'type' => 'required|in:youtube,upload',
            'url' => 'required_if:type,youtube|nullable|url',
            'video_file' => 'required_if:type,upload|nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:50000', // 50MB max
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
            'source' => $request->type == 'youtube' ? 'YouTube' : ($request->source ?: auth()->user()->name),
            'url' => $url,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Video berhasil ditambahkan!');
    }

    public function upload()
    {
        $videos = Video::where('user_id', auth()->id())->latest()->get();
        return view('video.upload', compact('videos'));
    }

    public function create()
    {
        $parwas = Parwa::all();
        $versions = \App\Models\Version::pluck('name');
        return view('video.create', compact('parwas', 'versions'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
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
            'source' => $request->type == 'youtube' ? 'YouTube' : ($request->source ?: auth()->user()->name),
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => in_array(auth()->user()->role, ['admin', 'narasumber']) ? 'approved' : 'pending', 
        ]);

        return redirect()->route('video.upload');
    }

    public function edit(Video $video)
    {
        if ($video->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }
        $parwas = Parwa::all();
        $versions = \App\Models\Version::pluck('name');
        return view('video.edit', compact('video', 'parwas', 'versions'));
    }

    public function update(Request $request, Video $video)
    {
        if ($video->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }

        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
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
            'source' => $request->type == 'youtube' ? 'YouTube' : ($request->source ?: auth()->user()->name),
            'type' => $request->type,
            'url' => $url,
            'status' => 'pending', // Reset status to pending to require admin re-approval
        ]);

        return redirect()->route('video.upload')->with('success', 'Video berhasil diperbarui!');
    }

    public function destroy(Video $video)
    {
        if ($video->user_id !== auth()->id() && auth()->user()->role !== 'admin' && auth()->user()->role !== 'narasumber') {
            abort(403);
        }
        
        $video->delete();
        return back()->with('success', 'Video berhasil dihapus.');
    }
}
