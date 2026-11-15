<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\PushNotificationLog;
use App\Models\News;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct(
        protected PushNotificationService $pushService
    ) {}

    public function index()
    {
        $activeSubscribersCount = PushSubscription::where('is_active', true)->count();
        $totalSubscribersCount = PushSubscription::count();
        $totalNotificationsSent = PushNotificationLog::sum('successful_sent');
        $recentLogs = PushNotificationLog::with(['news', 'sender'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $recentNews = News::published()
            ->orderByDesc('published_at')
            ->take(20)
            ->get();

        return view('admin.notifications.index', compact(
            'activeSubscribersCount',
            'totalSubscribersCount',
            'totalNotificationsSent',
            'recentLogs',
            'recentNews'
        ));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:300',
            'url' => 'required|url|max:500',
            'image_url' => 'nullable|url|max:500',
            'news_id' => 'nullable|exists:news,id',
        ]);

        $log = $this->pushService->sendToAll(
            title: $validated['title'],
            body: $validated['body'],
            url: $validated['url'],
            imageUrl: $validated['image_url'] ?? null,
            newsId: $validated['news_id'] ?? null,
            sender: auth()->user()
        );

        return back()->with('success', "পুশ নোটিফিকেশন সফলভাবে পাঠানো হয়েছে। মোট লক্ষ্যমাত্রা: {$log->target_subscribers}, সফল ডেলিভারি: {$log->successful_sent}।");
    }

    public function subscribers()
    {
        $subscribers = PushSubscription::with('user')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.notifications.subscribers', compact('subscribers'));
    }

    public function toggleSubscriber(PushSubscription $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        $status = $subscriber->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়';
        return back()->with('success', "গ্রাহকের অবস্থা {$status} করা হয়েছে।");
    }

    public function logs()
    {
        $logs = PushNotificationLog::with(['news', 'sender'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.notifications.logs', compact('logs'));
    }
}

