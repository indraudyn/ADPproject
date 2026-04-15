<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parwa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ceritas()
    {
        return $this->hasMany(Cerita::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
