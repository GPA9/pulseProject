<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'title',
        'cover_path',
        'description',
        'release_year',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
