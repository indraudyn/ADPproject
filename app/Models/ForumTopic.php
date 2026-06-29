<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'status',   // ← wajib ada agar update(['status'=>...]) bekerja
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(5);
            }
            // default status pending saat pertama dibuat
            if (!$model->status) {
                $model->status = 'pending';
            }
        });
    }

    // ── Scopes ──────────────────────────────────────

    /** Hanya topik yang sudah disetujui */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /** Hanya topik yang masih menunggu */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Relationships ────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ForumMessage::class, 'topic_id');
    }
}
