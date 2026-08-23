<?php

namespace App\Http\Controllers\Narasumber;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with(['parwa', 'user'])->latest()->paginate(10);
        return view('narasumber.video.index', compact('videos'));
    }

    public function create()
    {
        $parwas = \App\Models\Parwa::all();
        $versions = \App\Models\Version::pluck('name');
        return view('narasumber.video.create', compact('parwas', 'versions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:film,animasi,wayang,sendra_tari',
            'title' => 'required|string|max:255',
            'type' => 'required|in:youtube,upload',
            'url' => 'required_if:type,youtube|nullable|url',
            'video_file' => 'required_if:type,upload|nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:50000',
        ]);

        $url = $request->url;
        if ($request->type == 'upload' && $request->hasFile('video_file')) {
            $url = $request->file('video_file')->store('videos', 'public');
        }

        $video = Video::create([
            'parwa_id' => $request->parwa_id,
            'section' => $request->section,
            'version' => $request->version,
            'category' => $request->category,
            'title' => $request->title,
            'source' => auth()->user()->name,
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => 'approved', 
        ]);

        if ($request->type == 'upload' && $request->hasFile('video_file')) {
            \App\Jobs\OptimizeVideo::dispatch($video);
            return redirect()->route('narasumber.video.index')->with('success', 'Video berhasil ditambahkan dan sedang diproses untuk optimasi (faststart)!');
        }

        return redirect()->route('narasumber.video.index')->with('success', 'Video berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $parwas = \App\Models\Parwa::all();
        $versions = \App\Models\Version::pluck('name');
        return view('narasumber.video.edit', compact('video', 'parwas', 'versions'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
            'section' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:film,animasi,wayang,sendra_tari',
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
            'category' => $request->category,
            'title' => $request->title,
            'type' => $request->type,
            'url' => $url,
        ]);

        if ($request->type == 'upload' && $request->hasFile('video_file')) {
            \App\Jobs\OptimizeVideo::dispatch($video);
            return redirect()->route('narasumber.video.index')->with('success', 'Video berhasil diperbarui dan sedang diproses untuk optimasi!');
        }

        return redirect()->route('narasumber.video.index')->with('success', 'Video berhasil diperbarui!');
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
