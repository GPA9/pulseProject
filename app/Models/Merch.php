<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merch extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'name',
        'description',
        'price',
        'image_path',
        'category',
        'city',
        'sales_count',
        'sizes',
        'merchbar_url',
    ];

    protected $casts = [
        'sizes' => 'array',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}
