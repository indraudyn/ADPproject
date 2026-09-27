<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'message',
        'reply_to_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(ForumMessage::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumMessage::class, 'reply_to_id');
    }
}
