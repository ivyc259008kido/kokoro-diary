<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    protected $fillable = ['user_id', 'body', 'ai_reply', 'mood', 'summary', 'encouragement', 'themes'];
    protected $casts = [
        'themes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
