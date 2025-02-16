<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

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
