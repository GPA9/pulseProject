<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistPlayCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'play_count',
        'recorded_date',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'play_count' => 'integer',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}

