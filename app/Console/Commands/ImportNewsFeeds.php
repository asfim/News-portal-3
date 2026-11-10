<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsSource;
use App\Services\NewsImportService;

class ImportNewsFeeds extends Command
{
    protected $signature = 'news:import-feeds {--source= : Specific source ID to fetch} {--force : Fetch even if interval not elapsed}';
    protected $description = 'Automatically collect news from authorized RSS and API sources';

    public function handle(NewsImportService $importService): int
    {
        $sourceId = $this->option('source');
        $force = $this->option('force');

        $query = NewsSource::active();

        if ($sourceId) {
            $query->where('id', $sourceId);
        } elseif (!$force) {
            $query->where(function ($q) {
                $q->whereNull('last_fetched_at')
                  ->orWhereRaw('TIMESTAMPDIFF(MINUTE, last_fetched_at, NOW()) >= fetch_interval_minutes');
            });
        }

        $sources = $query->get();

        if ($sources->isEmpty()) {
            $this->info("No news sources due for import at this time.");
            return Command::SUCCESS;
        }

        $this->info("Processing " . $sources->count() . " news sources...");

        foreach ($sources as $source) {
            $this->line("Fetching from: [{$source->name}] ({$source->feed_url})...");
            $log = $importService->importFromSource($source);

            if ($log->status === 'success') {
                $this->info("  ✓ {$log->message} ({$log->execution_time_seconds}s)");
            } elseif ($log->status === 'warning') {
                $this->warn("  ⚠ {$log->message} ({$log->items_failed} failed)");
            } else {
                $this->error("  ✗ {$log->message}");
            }
        }

        return Command::SUCCESS;
    }
}
