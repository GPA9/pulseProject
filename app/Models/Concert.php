<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'venue',
        'address',
        'city',
        'province',
        'autonomous_community',
        'date',
        'price',
        'description',
        'capacity',
        'capacity_available',
        'genre',
        'latitude',
        'longitude',
        'ticketmaster_url',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Calculate distance from a given point using Haversine formula
     */
    public function getDistanceFrom(float $lat, float $lng): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $lat1 = deg2rad($lat);
        $lat2 = deg2rad($this->latitude);
        $deltaLat = deg2rad($this->latitude - $lat);
        $deltaLng = deg2rad($this->longitude - $lng);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLng / 2) * sin($deltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}
