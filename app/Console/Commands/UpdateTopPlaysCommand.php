<?php

namespace App\Console\Commands;

use App\Jobs\UpdateArtistPlayCounts;
use Illuminate\Console\Command;

class UpdateTopPlaysCommand extends Command
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
    protected $description = 'Update artist play counts from songs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating artist play counts...');
        
        dispatch(new UpdateArtistPlayCounts());
        
        $this->info('Play counts updated successfully!');
    }
}

