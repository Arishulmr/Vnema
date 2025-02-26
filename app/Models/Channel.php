<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();

        static::created(function ($channel) {
            // Run VTuber update function whenever a new channel is added
            self::updateVtuberVideos();
        });
    }

    public static function updateVtuberVideos()
    {
        $vtubers = Vtuber::all();

        foreach ($vtubers as $vtuber) {
            $videos = Video::where('title', 'LIKE', "%{$vtuber->name}%")->get();
            foreach ($videos as $video) {
                $video->vtubers()->syncWithoutDetaching([$vtuber->id]);
            }

        }

    }

    protected $fillable = ['youtube_channel_id', 'name', 'vtuber_id'];

    public function vtuber()
    {
        return $this->belongsTo(Vtuber::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}