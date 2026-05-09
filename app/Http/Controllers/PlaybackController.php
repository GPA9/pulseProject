<?php

namespace App\Http\Controllers;

use App\Models\SongPlayLog;
use App\Models\Song;
use App\Models\MusicianRanking;
use Illuminate\Http\Request;

class PlaybackController extends Controller
{
    /**
     * Registrar reproducción de canción (Route parameter binding)
     */
    public function recordPlay(Song $song)
    {
        try {
            // Registrar el play en el log
            SongPlayLog::create([
                'song_id' => $song->id,
            ]);

            // Incrementar play_count de la canción
            $song->increment('play_count');

            // Actualizar total_plays del artista en tiempo real
            $musician = $song->musicianProfile;
            if ($musician) {
                $totalPlays = $musician->songs()->sum('play_count');
                $musician->update(['total_plays' => $totalPlays]);

                // Actualizar o crear el ranking del artista
                MusicianRanking::updateOrCreate(
                    ['musician_profile_id' => $musician->id],
                    [
                        'total_plays'    => $totalPlays,
                        'calculated_at'  => now(),
                    ]
                );

                // Recalcular posición de todos los rankings
                $this->recalculateRanks();
            }

            $newPlayCount = $song->fresh()->play_count;

            return response()->json([
                'success'    => true,
                'play_count' => $newPlayCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recalcular la posición (rank) de todos los artistas en el ranking
     */
    private function recalculateRanks(): void
    {
        $rankings = MusicianRanking::orderByDesc('total_plays')->get();
        foreach ($rankings as $index => $ranking) {
            $ranking->rank = $index + 1;
            $ranking->save();
        }
    }
}
