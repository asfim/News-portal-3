<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutomationDashboardController extends Controller
{
    public function index()
    {
        // 1. RSS / API News Sources Stats
        $activeSourcesCount = NewsSource::where('is_active', true)->count();
        $totalSourcesCount = NewsSource::count();
        $todayImportsCount = NewsImport::whereDate('created_at', today())->where('status', 'draft_created')->count();
        $todayDuplicatesCount = NewsImport::whereDate('created_at', today())->where('status', 'duplicate_skipped')->count();

        // 2. AI Assistant Stats
        $aiSetting = AiSetting::current();
        $aiRequestsToday = AiGenerationLog::whereDate('created_at', today())->count();

        // 3. Social Media Auto-Publishing Stats
        $socialAccountsCount = SocialAccount::where('is_active', true)->count();
        $socialPostsPublished = SocialPost::where('status', 'published')->count();
        $socialPostsPending = SocialPost::whereIn('status', ['draft', 'scheduled'])->count();

        // 4. Posters Generated
        $postersCount = GeneratedPoster::count();

        // 5. Web Push Stats
        $pushSubscribersCount = PushSubscription::where('is_active', true)->count();
        $pushNotificationsSent = PushNotificationLog::sum('successful_sent');

        // 6. Newsletter Stats
        $newsletterSubscribersCount = NewsletterSubscriber::where('is_active', true)->count();
        $newsletterCampaignsCount = NewsletterCampaign::count();

        // Recent Import Logs
        $recentImportLogs = NewsImportLog::with('source')->orderByDesc('created_at')->take(5)->get();

        // Recent Imported News
        $recentImports = NewsImport::with(['source', 'news'])->whereIn('status', ['draft_created', 'published'])->orderByDesc('created_at')->take(6)->get();

        return view('admin.automation.dashboard', compact(
            'activeSourcesCount',
            'totalSourcesCount',
            'todayImportsCount',
            'todayDuplicatesCount',
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
            'recentImportLogs',
            'recentImports'
        ));
    }
}
