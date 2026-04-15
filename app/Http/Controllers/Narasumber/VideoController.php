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
        return view('narasumber.video.create', compact('parwas'));
    }

    public function store(Request $request)
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
            'source' => auth()->user()->name,
            'url' => $url,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => 'approved', 
        ]);

        return redirect()->route('narasumber.video.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        Video::where('id', $id)->update(['status' => $request->status]);
        return back();
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return back();
    }
}
