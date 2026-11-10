<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\NewsDailyStat;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function show(Request $request, string $public_id, ?string $slug = null)
    {
        $news = News::published()
            ->where('public_id', $public_id)
            ->with(['primaryCategory', 'categories', 'tags', 'authors', 'coverMedia', 'video', 'approvedComments.user'])
            ->firstOrFail();

        // 301 Permanent Redirect if the slug in URL does not match canonical slug
        if ($slug !== $news->slug) {
            return redirect()->to(url("/news/{$news->public_id}/{$news->slug}"), 301);
        }

        // Accurate Analytics with Bot Filtering & Session View Deduplication
        $userAgent = $request->userAgent() ?? '';
        $isBot = (bool) preg_match('/bot|crawl|slurp|spider|mediapartners|curl|wget|facebookexternalhit|whatsapp|twitterbot|pinterest|slackbot/i', $userAgent);

        if (!$isBot) {
            $sessionKey = 'viewed_news_' . $news->id;
            $isUnique = false;

            if ($request->hasSession() && !$request->session()->has($sessionKey)) {
                $request->session()->put($sessionKey, now()->timestamp);
                $isUnique = true;
            } elseif (!$request->hasSession()) {
                $isUnique = true;
            }

            $today = now()->toDateString();
            $stat = NewsDailyStat::firstOrCreate(
                ['news_id' => $news->id, 'stat_date' => $today],
                ['views' => 0, 'unique_views' => 0]
            );

            $stat->increment('views');
            if ($isUnique) {
                $stat->increment('unique_views');
            }

            // Track reader activity for personalized recommendations
            $sessionId = $request->hasSession() ? $request->session()->getId() : 'guest_' . md5($request->ip());
            app(\App\Services\NewsRecommendationService::class)->recordReading(
                $news,
                auth()->user(),
                $sessionId
            );
        }

        // Related News
        $relatedNews = $news->relatedNews()->published()->with(['primaryCategory', 'coverMedia'])->take(4)->get();
        if ($relatedNews->isEmpty()) {
            $relatedNews = News::published()
                ->where('primary_category_id', $news->primary_category_id)
                ->where('id', '!=', $news->id)
                ->with(['primaryCategory', 'coverMedia'])
                ->orderByDesc('published_at')
                ->take(4)
                ->get();
        }

        // Latest & Most Read for sidebar
        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();

        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.news.show', compact('news', 'relatedNews', 'latestNews', 'navCategories'));
    }

    public function latest()
    {
        $newsList = News::published()
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->paginate(15);

        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.news.latest', compact('newsList', 'navCategories'));
    }

    public function storeComment(Request $request, string $public_id)
    {
        $news = News::published()->where('public_id', $public_id)->firstOrFail();
        if (!$news->comments_enabled) {
            return back()->with('error', 'এই সংবাদের জন্য মন্তব্য বন্ধ রয়েছে।');
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        if (!empty($validated['parent_id'])) {
            $parent = \App\Models\Comment::where('id', $validated['parent_id'])->where('news_id', $news->id)->first();
            if (!$parent) {
                return back()->with('error', 'অবৈধ মন্তব্য উত্তর।');
            }
        }

        \App\Models\Comment::create([
            'news_id' => $news->id,
            'user_id' => auth()->id() ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => strip_tags($validated['body']),
            'status' => 'pending',
        ]);

        return back()->with('comment_success', 'আপনার মন্তব্য পর্যালোচনার জন্য জমা হয়েছে। অনুমোদনের পর তা প্রদর্শিত হবে।');
    }
}
