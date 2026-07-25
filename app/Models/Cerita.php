<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cerita extends Model
{
    use HasFactory;

    protected $table = 'cerita';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;

    protected $fillable = [
        'userId', 'user_id',
        'judul',
        'book', 'parwa_id',
        'sub_parva', 'sub_parwa',
        'url', 'sumber',
        'isi',
        'isi_id',
        'status',
        'versionId',
        'section',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function parwa(): BelongsTo
    {
        return $this->belongsTo(Parwa::class, 'book', 'name');
    }

    // Accessors for Legacy Columns
    public function getUserIdAttribute() {
        return $this->attributes['userId'] ?? null;
    }
    public function setUserIdAttribute($value) {
        $this->attributes['userId'] = $value;
    }
    
    public function getParwaIdAttribute() {
        return $this->attributes['book'] ?? null;
    }
    public function setParwaIdAttribute($value) {
        $this->attributes['book'] = $value;
    }
    
    public function getSubParwaAttribute() {
        return $this->attributes['sub_parva'] ?? null;
    }
    public function setSubParwaAttribute($value) {
        $this->attributes['sub_parva'] = $value;
    }
    
    public function getSumberAttribute() {
        return $this->attributes['url'] ?? null;
    }
    public function setSumberAttribute($value) {
        $this->attributes['url'] = $value;
    }

    public function getCreatedAtAttribute() {
        $val = $this->attributes['createdAt'] ?? null;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }
}
