<?php

namespace App\Jobs;

use App\Models\ArtistPlayCount;
use App\Models\MusicianProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class UpdateArtistPlayCounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();
        
        // Obtener todos los músicos y sus reproducciones totales
        $musicians = MusicianProfile::with('songs')->get();
        
        foreach ($musicians as $musician) {
            $totalPlays = $musician->calculateTotalPlays();
            
            // Crear o actualizar el registro de hoy
            ArtistPlayCount::updateOrCreate(
                [
                    'musician_profile_id' => $musician->id,
                    'recorded_date' => $today,
                ],
                [
                    'play_count' => $totalPlays,
                ]
            );
        }
    }
}
