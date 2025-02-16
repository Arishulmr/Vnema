<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vtuber extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'agency'];

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}