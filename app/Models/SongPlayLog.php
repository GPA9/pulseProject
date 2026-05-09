<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongPlayLog extends Model
{
    protected $fillable = ['song_id', 'played_at'];
    public $timestamps = false;

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
