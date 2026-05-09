<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MusicianRanking extends Model
{
    protected $fillable = ['musician_profile_id', 'total_plays', 'rank', 'calculated_at'];
    public $timestamps = false;

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}
