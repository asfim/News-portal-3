<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsletterService;

class SendDailyNewsletterDigest extends Command
{
    protected $signature = 'news:send-daily-digest';
    protected $description = 'Generate and send daily email digest of published top news';

    public function handle(NewsletterService $newsletterService): int
    {
        $this->info("Generating daily newsletter digest...");
        $campaign = $newsletterService->sendDailyDigest();

        if ($campaign) {
            $this->info("✓ Daily digest campaign sent to {$campaign->total_recipients} subscribers.");
        } else {
            $this->warn("No published news found for today's digest.");
        }

        return Command::SUCCESS;
    }
}
