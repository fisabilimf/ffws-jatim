<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeviceSensorImportService;

class ImportDevicesAndSensors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:devices-sensors 
                           {--source= : Import from specific source (arr-pusda, awlr-pusda, meteorologi-juanda)} 
                           {--dry-run : Show what would be imported without actually importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import devices and sensors from external APIs';

    protected $importService;

    public function __construct(DeviceSensorImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting device and sensor import from external APIs...');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        $source = $this->option('source');

        try {
            if (!$this->option('dry-run')) {
                $results = $this->importService->importAllDevicesAndSensors();
                $this->displayResults($results);
            } else {
                $this->performDryRun();
            }

            $this->newLine();
            $this->info('Import process completed successfully!');

        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Display import results
     */
    protected function displayResults($results)
    {
        $this->info('Import Results:');
        $this->line('================');

        foreach ($results['results'] as $source => $result) {
            if ($result['success']) {
                $this->info("✓ {$source}:");
                $this->line("  - Devices created: {$result['devices_created']}");
                $this->line("  - Devices updated: {$result['devices_updated']}");
                $this->line("  - Sensors created: {$result['sensors_created']}");
                $this->line("  - Sensors updated: {$result['sensors_updated']}");
                $this->line("  - Total processed: {$result['total_processed']}");
            } else {
                $this->error("✗ {$source}: {$result['error']}");
            }
            $this->newLine();
        }

        $summary = $results['summary'];
        $this->info('Overall Summary:');
        $this->line('================');
        $this->line("Successful sources: {$summary['successful_sources']}/3");
        $this->line("Total devices created: {$summary['total_devices_created']}");
        $this->line("Total devices updated: {$summary['total_devices_updated']}");
        $this->line("Total sensors created: {$summary['total_sensors_created']}");
        $this->line("Total sensors updated: {$summary['total_sensors_updated']}");

        if (!empty($summary['failed_sources'])) {
            $this->newLine();
            $this->error('Failed sources:');
            foreach ($summary['failed_sources'] as $failed) {
                $this->error("- {$failed['source']}: {$failed['error']}");
            }
        }
    }

    /**
     * Perform a dry run to show what would be imported
     */
    protected function performDryRun()
    {
        $this->info('Dry run - checking API endpoints...');
        
        $apis = [
            'cuaca-arr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-arr-pusda',
            'cuaca-awlr-pusda' => 'https://sih3.dpuair.jatimprov.go.id/api/cuaca-awlr-pusda',
            'meteorologi-juanda' => 'https://sih3.dpuair.jatimprov.go.id/api/meteorologi-juanda',
        ];

        foreach ($apis as $source => $url) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Extract the actual data count
                    $count = 0;
                    if ($source === 'cuaca-arr-pusda' && isset($data['Pos Duga Air Jam-jam an PU SDA'])) {
                        $count = count($data['Pos Duga Air Jam-jam an PU SDA']);
                    } elseif ($source === 'cuaca-awlr-pusda' && isset($data['Hujan Jam-Jam an PU SDA'])) {
                        $count = count($data['Hujan Jam-Jam an PU SDA']);
                    } elseif ($source === 'meteorologi-juanda' && isset($data['Data Meteorologi Juanda'])) {
                        $count = count($data['Data Meteorologi Juanda']);
                    }

                    $this->info("✓ {$source}: {$count} records available");
                } else {
                    $this->error("✗ {$source}: API returned status " . $response->status());
                }
            } catch (\Exception $e) {
                $this->error("✗ {$source}: " . $e->getMessage());
            }
        }
    }
}