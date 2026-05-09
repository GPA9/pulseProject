<?php

namespace App\Console\Commands;

use App\Models\MusicianProfile;
use Illuminate\Console\Command;

class UpdateTopMusicians extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'musicians:update-top-plays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update total_plays cache for all musicians';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Actualizando reproducciones totales de artistas...');

        $musicians = MusicianProfile::with('songs')->get();
        $bar = $this->output->createProgressBar($musicians->count());

        $bar->start();

        foreach ($musicians as $musician) {
            $totalPlays = $musician->songs->sum('play_count');
            $musician->update(['total_plays' => $totalPlays]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ Actualización completada. ' . $musicians->count() . ' artistas actualizados.');

        return Command::SUCCESS;
    }
}