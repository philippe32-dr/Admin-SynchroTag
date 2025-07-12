<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializePuceCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puces:init-coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize longitude and latitude to 0 for all puces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = \App\Models\Puce::where(function($query) {
            $query->whereNull('longitude')
                  ->orWhere('longitude', '!=', 0)
                  ->orWhereNull('latitude')
                  ->orWhere('latitude', '!=', 0);
        })->update([
            'longitude' => 0,
            'latitude' => 0
        ]);

        $this->info("Initialized coordinates for {$updated} puce(s).");
    }
}
