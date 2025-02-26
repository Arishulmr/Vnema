<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
    ];

    public function livestreams()
    {
        return $this->hasMany(Livestream::class);
    }
}
