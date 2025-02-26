<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livestream extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_livestream_id',
        'title',
        'description',
        'event_id',
        'vtuber_id'
    ];

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'livestream_video');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vtuber()
    {
        return $this->belongsTo(Vtuber::class);
    }
}