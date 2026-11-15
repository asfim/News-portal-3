<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsSource;
use App\Models\NewsImport;
use App\Models\NewsImportLog;
use App\Models\AiSetting;
use App\Models\AiGenerationLog;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\GeneratedPoster;
use App\Models\PushSubscription;
use App\Models\PushNotificationLog;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AutomationDashboardController extends Controller
{
    public function index()
    {
        // 1. RSS / API News Sources Stats
        $activeSourcesCount = NewsSource::where('is_active', true)->count();
        $totalSourcesCount = NewsSource::count();
        $todayImportsCount = NewsImport::whereDate('created_at', today())->where('status', 'draft_created')->count();
        $todayDuplicatesCount = NewsImport::whereDate('created_at', today())->where('status', 'duplicate_skipped')->count();

        // 2. Editorial Metrics
        $pendingEditorialReview = News::where('status', 'review_pending')->count();
        $publishedToday = News::where('status', 'published')->whereDate('published_at', today())->count();

        // 3. AI Assistant Stats
        $aiSetting = AiSetting::current();
        $aiRequestsToday = AiGenerationLog::whereDate('created_at', today())->count();

        // 4. Social Media Auto-Publishing Stats
        $socialAccountsCount = SocialAccount::where('is_active', true)->count();
        $socialPostsPublished = SocialPost::where('status', 'published')->count();
        $socialPostsPending = SocialPost::whereIn('status', ['draft', 'scheduled'])->count();

        // 5. Posters Generated
        $postersCount = GeneratedPoster::count();

        // 6. Web Push Stats
        $pushSubscribersCount = PushSubscription::where('is_active', true)->count();
        $pushNotificationsSent = (int) PushNotificationLog::sum('successful_sent');

        // 7. Newsletter Stats
        $newsletterSubscribersCount = NewsletterSubscriber::where('is_active', true)->count();
        $newsletterCampaignsCount = NewsletterCampaign::count();
        $newsletterDeliveriesSent = (int) NewsletterDelivery::where('status', 'sent')->count();

        // 8. Queue & Failed Jobs Status
        $queueJobsCount = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedJobsCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->orderByDesc('failed_at')->take(5)->get() : collect();

        // Recent Activity
        $recentImportLogs = NewsImportLog::with('source')->orderByDesc('created_at')->take(6)->get();
        $recentImports = NewsImport::with(['source', 'news'])->whereIn('status', ['draft_created', 'published'])->orderByDesc('created_at')->take(6)->get();

        return view('admin.automation.dashboard', compact(
            'activeSourcesCount',
            'totalSourcesCount',
            'todayImportsCount',
            'todayDuplicatesCount',
            'pendingEditorialReview',
            'publishedToday',
            'aiSetting',
            'aiRequestsToday',
            'socialAccountsCount',
            'socialPostsPublished',
            'socialPostsPending',
            'postersCount',
            'pushSubscribersCount',
            'pushNotificationsSent',
            'newsletterSubscribersCount',
            'newsletterCampaignsCount',
            'newsletterDeliveriesSent',
            'queueJobsCount',
            'failedJobsCount',
            'failedJobs',
            'recentImportLogs',
            'recentImports'
        ));
    }

    public function triggerImportFeeds()
    {
        Artisan::call('news:import-feeds');
        $output = Artisan::output();
        return back()->with('success', 'স্বয়ংক্রিয় ফিড সংগ্রহ কমান্ড সফলভাবে সম্পন্ন হয়েছে: ' . trim($output));
    }

    public function triggerPublishScheduled()
    {
        Artisan::call('news:publish-scheduled');
        $output = Artisan::output();
        return back()->with('success', 'নির্ধারিত সংবাদ প্রকাশের কমান্ড সম্পন্ন হয়েছে: ' . trim($output));
    }

    public function retryFailedJob(string $id)
    {
        Artisan::call("queue:retry {$id}");
        return back()->with('success', "ব্যর্থ জব #{$id} পুনরায় কিউতে যোগ করা হয়েছে।");
    }

    public function retryAllFailed()
    {
        Artisan::call('queue:retry all');
        return back()->with('success', 'সকল ব্যর্থ জব পুনরায় কিউতে যুক্ত করা হয়েছে।');
    }

    public function flushFailed()
    {
        Artisan::call('queue:flush');
        return back()->with('success', 'ব্যর্থ জব টেবিল পরিষ্কার করা হয়েছে।');
    }
}
