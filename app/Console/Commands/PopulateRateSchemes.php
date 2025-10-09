<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RateScheme;

class PopulateRateSchemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:rate-schemes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate rate schemes with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating rate schemes with sample data...');
        
        // Sample rate schemes
        $rateSchemes = [
            ['id' => 1, 'name' => 'Standard Rate', 'hourly_rate' => 25.00],
            ['id' => 2, 'name' => 'Beginner Rate', 'hourly_rate' => 20.00],
            ['id' => 3, 'name' => 'Intermediate Rate', 'hourly_rate' => 30.00],
            ['id' => 4, 'name' => 'Advanced Rate', 'hourly_rate' => 35.00],
            ['id' => 5, 'name' => 'Business Rate', 'hourly_rate' => 40.00],
            ['id' => 7, 'name' => 'Premium Rate', 'hourly_rate' => 45.00],
            ['id' => 9, 'name' => 'Corporate Rate', 'hourly_rate' => 50.00],
        ];
        
        foreach ($rateSchemes as $schemeData) {
            $rateScheme = RateScheme::find($schemeData['id']);
            if ($rateScheme) {
                $rateScheme->update([
                    'name' => $schemeData['name'],
                    'hourly_rate' => $schemeData['hourly_rate']
                ]);
                $this->info("Updated: {$rateScheme->name} - $" . number_format($rateScheme->hourly_rate, 2) . "/hour");
            }
        }
        
        $this->info('Rate schemes populated successfully!');
    }
}
