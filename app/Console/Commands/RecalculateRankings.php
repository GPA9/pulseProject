<?php

namespace App\Console\Commands;

use App\Models\MusicianRanking;
use App\Models\MusicianProfile;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use DB;

class RecalculateRankings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate musician rankings based on total plays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Recalculando rankings de artistas...');

        // Obtener todos los artistas con sus reproducciones totales
        $rankings = MusicianProfile::select('musician_profiles.id', 'musician_profiles.stage_name')
            ->selectRaw('COALESCE(SUM(songs.play_count), 0) as total_plays')
            ->leftJoin('songs', 'musician_profiles.id', '=', 'songs.musician_profile_id')
            ->groupBy('musician_profiles.id', 'musician_profiles.stage_name')
            ->orderByRaw('total_plays DESC')
            ->get();

        // Actualizar tabla de rankings con posiciones
        foreach ($rankings as $rank => $musician) {
            MusicianRanking::updateOrCreate(
                ['musician_profile_id' => $musician->id],
                [
                    'total_plays' => (int)$musician->total_plays,
                    'rank' => $rank + 1,
                    'calculated_at' => now(),
                ]
            );
        }

        // Guardar en caché para acceso rápido en frontend
        $top20 = MusicianRanking::with('musicianProfile')
            ->orderBy('rank')
            ->limit(20)
            ->get()
            ->map(function ($r) {
                return [
                    'rank' => $r->rank,
                    'id' => $r->musician_profile_id,
                    'name' => $r->musicianProfile->stage_name,
                    'plays' => $r->total_plays,
                    'genre' => $r->musicianProfile->genre,
                    'city' => $r->musicianProfile->city,
                    'image' => $r->musicianProfile->image_path,
                ];
            });

        Cache::put('top_musicians_ranking', $top20, now()->addMinutes(15));

        $this->info('✅ Rankings recalculados y cacheados');
        $this->info("🏆 Top 1: " . ($top20->first()['name'] ?? 'N/A') . " - " . ($top20->first()['plays'] ?? 0) . " reproducciones");
    }
}
