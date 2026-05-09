<?php

namespace App\Console\Commands;

use App\Models\Concert;
use Illuminate\Console\Command;

class PurgeExpiredConcerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'concerts:purge-expired';

    /**
     * The console command description.
     */
    protected $description = 'Elimina automáticamente los conciertos cuya fecha ya ha pasado';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = Concert::where('date', '<', now())->delete();

        $this->info("✓ Conciertos pasados eliminados: {$deleted}");

        return Command::SUCCESS;
    }
}
