<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicianProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stage_name',
        'bio',
        'city',
        'province',
        'autonomous_community',
        'genre',
        'image_path',
        'total_plays',
        'social_networks',
        'streaming_platforms',
    ];

    protected $casts = [
        'total_plays' => 'integer',
        'social_networks' => 'array',
        'streaming_platforms' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Calculate total plays from all songs
     */
    public function calculateTotalPlays(): int
    {
        return $this->songs()->sum('play_count');
    }

    /**
     * Update cached total_plays from songs
     */
    public function updateTotalPlays(): void
    {
        $this->total_plays = $this->calculateTotalPlays();
        $this->save();
    }

    public function concerts()
    {
        return $this->hasMany(Concert::class);
    }

    public function merch()
    {
        return $this->hasMany(Merch::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
