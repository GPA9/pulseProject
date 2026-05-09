<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use Illuminate\Http\Request;

class TopMusiciansController extends Controller
{
    /**
     * Display top 20 musicians by total plays (calculated in real-time)
     */
    public function index()
    {
        $musicians = MusicianProfile::withSum('songs', 'play_count')
            ->orderByDesc('songs_sum_play_count')
            ->limit(20)
            ->get()
            ->map(fn($m) => (object)[
                'id'          => $m->id,
                'stage_name'  => $m->stage_name,
                'total_plays' => $m->songs_sum_play_count ?? 0,
                'genre'       => $m->genre,
                'city'        => $m->city,
                'image_path'  => $m->image_path,
            ]);

        return view('top-musicians.index', compact('musicians'));
    }

    /**
     * Get top musicians data as JSON (for AJAX) - always fresh from songs table
     */
    public function data()
    {
        $musicians = MusicianProfile::withSum('songs', 'play_count')
            ->orderByDesc('songs_sum_play_count')
            ->limit(20)
            ->get()
            ->map(fn($m) => [
                'id'    => $m->id,
                'name'  => $m->stage_name,
                'plays' => $m->songs_sum_play_count ?? 0,
                'genre' => $m->genre,
                'city'  => $m->city,
                'image' => $m->image_path,
            ])
            ->toArray();

        return response()->json(['musicians' => $musicians]);
    }
}
