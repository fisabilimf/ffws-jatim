<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncExternalApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external-api:sync 
                            {--source= : Specific API source to sync (cuaca-arr-pusda, meteorologi-juanda, cuaca-awlr-pusda)}
                            {--test : Test connectivity without syncing data}
                            {--cleanup : Cleanup old records after sync}
                            {--days=30 : Days to keep for cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from external APIs (DPU Air Jatimprov)';

    protected $externalApiService;

    /**
     * Create a new command instance.
     */
    public function __construct(ExternalApiService $externalApiService)
    {
        parent::__construct();
        $this->externalApiService = $externalApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $this->info("🌊 FFWS External API Sync Started at {$startTime->format('Y-m-d H:i:s')}");
        $this->newLine();

        try {
            // Test connectivity if requested
            if ($this->option('test')) {
                $this->testConnectivity();
                return Command::SUCCESS;
            }

            // Get specific source or sync all
            $source = $this->option('source');
            
            if ($source) {
                $this->syncSpecificSource($source);
            } else {
                $this->syncAllSources();
            }

            // Cleanup old records if requested
            if ($this->option('cleanup')) {
                $this->cleanupOldRecords();
            }

            $duration = now()->diffInSeconds($startTime);
            $this->newLine();
            $this->info("✅ External API sync completed successfully in {$duration} seconds");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ External API sync failed: " . $e->getMessage());
            Log::error('External API sync command failed: ' . $e->getMessage(), [
                'exception' => $e,
                'options' => $this->options(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Test API connectivity
     */
    protected function testConnectivity()
    {
        $this->info("🔍 Testing API connectivity...");
        $this->newLine();

        $results = $this->externalApiService->testConnectivity();

        foreach ($results as $source => $result) {
            $status = $result['success'] ? '✅' : '❌';
            $responseTime = $result['response_time_ms'] ?? 'N/A';
            
            $this->line("{$status} {$source}: {$result['status_code']} ({$responseTime}ms)");
            
            if (!$result['success'] && $result['error']) {
                $this->error("   Error: " . $result['error']);
            }
        }

        $healthyCount = collect($results)->where('success', true)->count();
        $totalCount = count($results);

        $this->newLine();
        $this->info("📊 Connectivity Summary: {$healthyCount}/{$totalCount} sources healthy");
    }

    /**
     * Sync all sources
     */
    protected function syncAllSources()
    {
        $this->info("📡 Syncing data from all external API sources...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(3);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Preparing...');
        $progressBar->start();

        $result = $this->externalApiService->fetchAllData();

        $progressBar->setMessage('Completed');
        $progressBar->finish();
        $this->newLine(2);

        // Display summary
        $summary = $result['summary'];
        $this->displaySyncSummary($summary);

        // Display details for each source
        if ($result['details']) {
            $this->displaySourceDetails($result['details']);
        }
    }

    /**
     * Sync specific source
     */
    protected function syncSpecificSource($source)
    {
        $validSources = ['cuaca-arr-pusda', 'meteorologi-juanda', 'cuaca-awlr-pusda'];
        
        if (!in_array($source, $validSources)) {
            $this->error("Invalid source: {$source}");
            $this->line("Valid sources: " . implode(', ', $validSources));
            return;
        }

        $this->info("📡 Syncing data from {$source}...");
        $this->newLine();

        $result = $this->externalApiService->fetchDataFromSource($source);

        if ($result['success']) {
            $this->info("✅ Successfully synced from {$source}");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Records', $result['total_records']],
                    ['New Records', $result['new_records']],
                    ['Updated Records', $result['updated_records']],
                    ['Latest Record', $result['latest_record_time'] ?? 'N/A'],
                    ['Sync Time', $result['sync_time']->format('Y-m-d H:i:s')],
                ]
            );
        } else {
            $this->error("❌ Failed to sync from {$source}: " . $result['error']);
        }
    }

    /**
     * Display sync summary
     */
    protected function displaySyncSummary($summary)
    {
        $this->info("📊 Sync Summary:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Sources', $summary['total_sources']],
                ['Successful Sources', $summary['successful_sources']],
                ['Total Records', $summary['total_records']],
                ['New Records', $summary['new_records']],
                ['Updated Records', $summary['updated_records']],
                ['Failed Sources', count($summary['failed_sources'])],
                ['Sync Time', $summary['sync_time']->format('Y-m-d H:i:s')],
            ]
        );

        if (!empty($summary['failed_sources'])) {
            $this->newLine();
            $this->error("❌ Failed Sources:");
            foreach ($summary['failed_sources'] as $failed) {
                $this->line("   • {$failed['source']}: {$failed['error']}");
            }
        }
    }

    /**
     * Display source details
     */
    protected function displaySourceDetails($details)
    {
        $this->newLine();
        $this->info("📋 Source Details:");

        foreach ($details as $source => $detail) {
            $status = $detail['success'] ? '✅' : '❌';
            $this->line("{$status} {$source}:");
            
            if ($detail['success']) {
                $this->line("   • Total: {$detail['total_records']} records");
                $this->line("   • New: {$detail['new_records']} records");
                $this->line("   • Updated: {$detail['updated_records']} records");
                if ($detail['latest_record_time']) {
                    $this->line("   • Latest: {$detail['latest_record_time']->format('Y-m-d H:i:s')}");
                }
            } else {
                $this->line("   • Error: {$detail['error']}");
            }
            $this->newLine();
        }
    }

    /**
     * Cleanup old records
     */
    protected function cleanupOldRecords()
    {
        $days = (int) $this->option('days');
        
        $this->info("🧹 Cleaning up records older than {$days} days...");
        
        $deletedCount = $this->externalApiService->cleanupOldRecords($days);
        
        if ($deletedCount > 0) {
            $this->info("✅ Cleaned up {$deletedCount} old records");
        } else {
            $this->info("✅ No old records to cleanup");
        }
    }
}
