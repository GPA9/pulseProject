<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use App\Models\Song;
use Illuminate\Http\Request;

class RadioController extends Controller
{
    public function index()
    {
        $communities = ConcertController::getCommunitiesMap();
        return view('radio.index', compact('communities'));
    }

    public function getSongsByCommunity(Request $request, string $community)
    {
        $province = $request->query('province');

        $songs = Song::whereHas('musicianProfile', function ($query) use ($community, $province) {
            $query->where('autonomous_community', $community);
            if ($province) {
                $query->where('province', $province);
            }
        })->with('musicianProfile')->inRandomOrder()->get();

        return response()->json($songs);
    }

    // Keep backward-compatible city endpoint
    public function getSongsByCity($city)
    {
        $songs = Song::whereHas('musicianProfile', function ($query) use ($city) {
            $query->where('city', $city);
        })->with('musicianProfile')->inRandomOrder()->get();

        return response()->json($songs);
    }
}
