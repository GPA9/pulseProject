<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Models\MusicianProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PopulateSongsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:populate-songs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate songs from public/music folder to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎵 Iniciando población de canciones...');

        // Eliminar todas las canciones existentes
        $this->info('Limpiando canciones existentes...');
        Song::truncate();

        // Obtener todos los archivos MP3
        $musicPath = public_path('music');
        $mp3Files = File::allFiles($musicPath);
        
        // Filtrar solo archivos MP3
        $mp3Files = array_filter($mp3Files, function ($file) {
            return strtolower($file->getExtension()) === 'mp3';
        });

        if (count($mp3Files) === 0) {
            $this->error('❌ No se encontraron archivos MP3 en ' . $musicPath);
            return;
        }

        $this->info("✅ Se encontraron " . count($mp3Files) . " canciones MP3");

        // Obtener todos los artistas
        $musicians = MusicianProfile::all();
        $musicianCount = $musicians->count();

        if ($musicianCount === 0) {
            $this->error('❌ No hay artistas en la base de datos');
            return;
        }

        $this->info("✅ Se encontraron " . $musicianCount . " artistas");

        // Distribuir canciones entre artistas
        $mp3Array = array_values($mp3Files);
        $songsCreated = 0;

        foreach ($musicians as $index => $musician) {
            // Asignar 2-3 canciones por artista
            $songsPerMusician = rand(2, 3);
            
            for ($i = 0; $i < $songsPerMusician; $i++) {
                // Seleccionar un MP3 aleatorio
                $mp3File = $mp3Array[array_rand($mp3Array)];
                $relativePath = str_replace(public_path(), '', $mp3File->getRealPath());
                $relativePath = ltrim($relativePath, '\\');

                // Obtener nombre sin extensión como título
                $songTitle = pathinfo($mp3File->getFilename(), PATHINFO_FILENAME);

                Song::create([
                    'musician_profile_id' => $musician->id,
                    'title' => $songTitle,
                    'file_path' => $relativePath,
                    'cover_path' => null,
                    'play_count' => rand(100, 10000),
                ]);

                $songsCreated++;
            }

            // Mostrar progreso
            if (($index + 1) % 10 === 0) {
                $this->line("  Procesados " . ($index + 1) . " de " . $musicianCount . " artistas");
            }
        }

        // Actualizar total_plays en cada artista
        $this->info('📊 Actualizando estadísticas de artistas...');
        foreach ($musicians as $musician) {
            $musician->updateTotalPlays();
        }

        $this->info('');
        $this->info('✨ ¡Población completada exitosamente!');
        $this->info("📝 Canciones creadas: $songsCreated");
        $this->info("🎤 Artistas actualizados: $musicianCount");
        $this->info('🎵 Escucha en: http://pulse.project/radio');
    }
}
