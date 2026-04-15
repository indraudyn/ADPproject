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
            'title' => 'required|string|max:255',
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
            'title' => $request->title,
            'source' => $request->type == 'youtube' ? 'YouTube' : 'User Upload',
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
        return view('video.create', compact('parwas'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'parwa_id' => 'required|exists:parwas,id',
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
            'title' => $request->title,
            'source' => $request->type == 'youtube' ? 'YouTube' : auth()->user()->name,
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => in_array(auth()->user()->role, ['admin', 'narasumber']) ? 'approved' : 'pending', 
        ]);

        // Redirect without flash message as requested
        return redirect()->route('video.upload');
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
