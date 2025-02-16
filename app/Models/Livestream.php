<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livestream extends Model
{
    use HasFactory;

    protected $fillable = ['youtube_livestream_id', 'title', 'description'];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}