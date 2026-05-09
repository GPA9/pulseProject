<?php

namespace App\Console\Commands;

use App\Models\MusicianProfile;
use Illuminate\Console\Command;

class FixBandLogoPaths extends Command
{
    protected $signature = 'bands:fix-logo-paths';
    protected $description = 'Fix band logo paths in database to match public/images/band-logos location';

    public function handle()
    {
        $this->info('Fixing band logo paths...');

        $musicians = MusicianProfile::whereNotNull('image_path')->get();

        foreach ($musicians as $musician) {
            // Extract just the filename if it has path
            $filename = basename($musician->image_path);
            $musician->update(['image_path' => $filename]);
            $this->line("✓ {$musician->stage_name} → {$filename}");
        }

        $this->info('✓ All paths fixed!');
    }
}
