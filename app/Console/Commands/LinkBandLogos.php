<?php

namespace App\Console\Commands;

use App\Models\MusicianProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LinkBandLogos extends Command
{
    protected $signature = 'bands:link-logos';
    protected $description = 'Link band logos from public/images/band-logos to musicians and delete those without logos';

    public function handle()
    {
        // Mapeo de nombres de archivos a nombres de artistas (normalizado)
        $logoMap = [
            'alisio.png' => 'Alisio',
            'barceloneta sound.png' => 'Barceloneta Sound',
            'brisa_valenciana_logo.svg' => 'Brisa Valenciana',
            'celtia.png' => 'Celtia',
            'copla andaluza.png' => 'Copla Andaluza',
            'dehesa sound.png' => 'Dehesa Sound',
            'duero sessions.png' => 'Duero Sessions',
            'flamenco sur.png' => 'Flamenco Sur',
            'gaita electrica.png' => 'Gaita Eléctrica',
            'girona folk.png' => 'Girona Folk',
            'ibero rock.png' => 'Ibero Rock',
            'jota brava.png' => 'Jota Brava',
            'la_mancha_sound_logo.svg' => 'La Mancha Sound',
            'luis jazz.png' => 'Luis Jazz',
            'luna barcelona.png' => 'Luna Barcelona',
            'mar_cantabrico_logo.svg' => 'Mar Cantábrico',
            'meseta club.png' => 'Meseta Club',
            'quijote_beats_logo.svg' => 'Quijote Beats',
            'rumba granadina.png' => 'Rumba Granadina',
            'solea project.png' => 'Solea Project',
            'soria noise.png' => 'Soria Noise',
            'tarragona blues.png' => 'Tarragona Blues',
            'timple sessions.png' => 'Timple Sessions',
            'trueno madrid.png' => 'Trueno Madrid',
        ];

        $this->info('Linking band logos...');
        $linked = 0;
        $notFound = 0;

        // Vincular logos
        foreach ($logoMap as $filename => $stageName) {
            $musician = MusicianProfile::where('stage_name', $stageName)->first();
            if ($musician) {
                $musician->update(['image_path' => "band-logos/{$filename}"]);
                $this->line("✓ {$stageName} → band-logos/{$filename}");
                $linked++;
            } else {
                $this->warn("✗ No encontrado: {$stageName}");
                $notFound++;
            }
        }

        $this->newLine();
        $this->info("Linked: {$linked} logos");

        // Eliminar artistas sin logo
        $this->newLine();
        $this->info('Deleting musicians without logos...');
        
        $allMusicians = MusicianProfile::all();
        $deleted = 0;

        foreach ($allMusicians as $musician) {
            if (!in_array($musician->stage_name, $logoMap)) {
                $this->line("🗑 Deleting: {$musician->stage_name}");
                $musician->delete();
                $deleted++;
            }
        }

        $this->newLine();
        $this->info("✓ Deleted: {$deleted} musicians without logos");
        $this->info("✓ Total remaining: " . MusicianProfile::count());
    }
}
