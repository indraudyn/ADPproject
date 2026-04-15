<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function parwa()
    {
        return $this->belongsTo(Parwa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getYoutubeIdAttribute()
    {
        if ($this->type === 'youtube') {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $this->url, $match);
            return $match[1] ?? null;
        }
        return null;
    }
}
