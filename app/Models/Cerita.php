<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cerita extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'parwa_id',
        'sub_parwa',
        'sumber',
        'cerita',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parwa(): BelongsTo
    {
        return $this->belongsTo(Parwa::class);
    }
}
