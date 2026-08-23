<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Str;

class OptimizeVideo implements ShouldQueue
{
    use Queueable;

    public $video;

    /**
     * Create a new job instance.
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->video->type !== 'file') {
            return;
        }

        $originalPath = $this->video->url;
        
        // If file doesn't exist, ignore
        if (!Storage::disk('public')->exists($originalPath)) {
            return;
        }

        // Create a new filename
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $newFilename = 'videos/opt_' . Str::random(10) . '_' . time() . '.mp4';

        // Optimize video: 720p maximum, AAC audio, and faststart for web
        $format = new X264();
        $format->setAudioCodec('aac')
               ->setVideoCodec('libx264')
               ->setAdditionalParameters(['-movflags', 'faststart']);

        FFMpeg::fromDisk('public')
            ->open($originalPath)
            ->export()
            ->toDisk('public')
            ->inFormat($format)
            ->save($newFilename);

        // Update video URL and delete old file
        $this->video->update(['url' => $newFilename]);
        Storage::disk('public')->delete($originalPath);
    }
}
