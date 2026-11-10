<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialPost;
use App\Models\SocialAccount;
use App\Models\News;
use App\Services\FacebookPublisherService;
use Illuminate\Http\Request;

class SocialPostController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = SocialPost::with(['account', 'news.primaryCategory'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $posts = $query->paginate(20)->withQueryString();

        return view('admin.social.posts.index', compact('posts', 'status'));
    }

    public function create(Request $request)
    {
        $newsId = $request->query('news_id');
        $selectedNews = $newsId ? News::published()->find($newsId) : null;
        $publishedNews = News::published()->orderByDesc('published_at')->take(30)->get();
        $accounts = SocialAccount::where('is_active', true)->get();

        return view('admin.social.posts.create', compact('selectedNews', 'publishedNews', 'accounts'));
    }

    public function publish(Request $request, FacebookPublisherService $publisherService)
    {
        $validated = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'news_id' => 'required|exists:news,id',
            'custom_message' => 'nullable|string|max:2000',
        ]);

        $account = SocialAccount::findOrFail($validated['social_account_id']);
        $news = News::findOrFail($validated['news_id']);

        $post = $publisherService->publishArticle(
            $account,
            $news,
            $validated['custom_message'] ?? null
        );

        if ($post->status === 'published') {
            return redirect()->route('admin.social.posts.index')
                ->with('success', "সংবাদটি সফলভাবে ফেসবুকে পোস্ট করা হয়েছে (ID: {$post->external_post_id})।");
        }

        return redirect()->route('admin.social.posts.index')
            ->with('error', "ফেসবুকে পোস্ট ব্যর্থ হয়েছে: " . $post->last_error);
    }
}
