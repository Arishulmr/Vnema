<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['youtube_video_id', 'title', 'description', 'thumbnail_url', 'channel_id', 'livestream_id', 'vtuber_id'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function livestream()
    {
        return $this->belongsTo(Livestream::class);
    }

    public function vtubers()
    {
        return $this->hasMany(Vtuber::class);
    }
}