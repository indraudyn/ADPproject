<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audios';
    protected $guarded = ['id'];

    public function parwa()
    {
        return $this->belongsTo(Parwa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
