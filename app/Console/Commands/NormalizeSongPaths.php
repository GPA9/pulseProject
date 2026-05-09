<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class NormalizeSongPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:normalize-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize song file paths to use forward slashes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Normalizando rutas de canciones...');

        $songs = Song::all();
        $updated = 0;

        foreach ($songs as $song) {
            // Reemplazar backslashes con forward slashes
            $normalizedPath = str_replace('\\', '/', $song->file_path);
            
            if ($normalizedPath !== $song->file_path) {
                $song->file_path = $normalizedPath;
                $song->save();
                $updated++;
            }
        }

        $this->info("✅ Rutas normalizadas: $updated de " . $songs->count());
        
        // Mostrar ejemplo
        $example = Song::first();
        if ($example) {
            $this->line('');
            $this->info('📁 Ejemplo de ruta normalizada:');
            $this->line('   ' . $example->file_path);
            $this->line('');
            $this->info('🎵 URL de reproducción:');
            $this->line('   ' . asset($example->file_path));
        }
    }
}
