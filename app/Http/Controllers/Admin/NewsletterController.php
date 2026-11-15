<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterDelivery;
use App\Models\News;
use App\Services\NewsletterService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        protected NewsletterService $newsletterService
    ) {}

    public function index()
    {
        $verifiedCount = NewsletterSubscriber::whereNotNull('verified_at')->where('is_active', true)->count();
        $totalSubscribers = NewsletterSubscriber::count();
        $campaignsCount = NewsletterCampaign::count();
        $totalDeliveries = NewsletterDelivery::where('status', 'sent')->count();

        $campaigns = NewsletterCampaign::with('creator')
            ->withCount('deliveries')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.newsletter.index', compact(
            'verifiedCount',
            'totalSubscribers',
            'campaignsCount',
            'totalDeliveries',
            'campaigns'
        ));
    }

    public function create()
    {
        $recentNews = News::published()->orderByDesc('published_at')->take(10)->get();
        return view('admin.newsletter.create', compact('recentNews'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'subject' => 'required|string|max:200',
            'content_html' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = NewsletterCampaign::create([
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'content_html' => $validated['content_html'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => !empty($validated['scheduled_at']) ? 'scheduled' : 'draft',
            'created_by' => auth()->id(),
        ]);

        if (empty($validated['scheduled_at']) && $request->input('action') === 'send_now') {
            $this->newsletterService->sendCampaign($campaign);
            return redirect()->route('admin.newsletter.index')
                ->with('success', "'{$campaign->title}' নিউজলেটার ক্যাম্পেইন সফলভাবে পাঠানো হয়েছে।");
        }

        return redirect()->route('admin.newsletter.index')
            ->with('success', "'{$campaign->title}' ক্যাম্পেইন সংরক্ষণ করা হয়েছে।");
    }

    public function sendDailyDigest()
    {
        $publishedNews = News::published()
            ->whereDate('published_at', today())
            ->orderByDesc('published_at')
            ->take(8)
            ->get();

        if ($publishedNews->isEmpty()) {
            $publishedNews = News::published()
                ->orderByDesc('published_at')
                ->take(8)
                ->get();
        }

        $campaign = NewsletterCampaign::create([
            'title' => 'দৈনিক সংবাদ বুলেটিন - ' . bn_date(),
            'subject' => 'এইচ টেক প্রোভিশন আজকের শীর্ষ সংবাদ - ' . bn_date(),
            'content_html' => view('emails.newsletter_digest', ['newsItems' => $publishedNews])->render(),
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        $sentCount = $this->newsletterService->sendCampaign($campaign);

        return back()->with('success', "দৈনিক সংবাদ ডাইজেস্ট সফলভাবে পাঠানো হয়েছে ({$sentCount}টি ডেলিভারি)।");
    }

    public function subscribers()
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->paginate(25);
        return view('admin.newsletter.subscribers', compact('subscribers'));
    }

    public function toggleSubscriber(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        $status = $subscriber->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়';
        return back()->with('success', "গ্রাহক {$subscriber->email} {$status} করা হয়েছে।");
    }

    public function deliveries()
    {
        $deliveries = NewsletterDelivery::with(['campaign', 'subscriber'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.newsletter.deliveries', compact('deliveries'));
    }
}

