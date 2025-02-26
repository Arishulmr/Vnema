<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vtuber extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'agency', 'channelUrl', 'thumbnails'];

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'video_vtuber');
    }
}
