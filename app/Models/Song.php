<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'album_id',
        'title',
        'file_path',
        'cover_path',
        'play_count',
        'royalties',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
