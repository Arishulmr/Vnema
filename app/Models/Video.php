<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['youtube_video_id', 'title', 'description', 'thumbnail_url', 'channel_id', 'vtuber_id'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function livestreams()
    {
        return $this->belongsToMany(Livestream::class, 'livestream_video');
    }

    public function vtubers()
    {
        return $this->belongsToMany(Vtuber::class, 'video_vtuber');
    }
}