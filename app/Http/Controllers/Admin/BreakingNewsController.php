<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakingItem;
use App\Models\News;
use Illuminate\Http\Request;

class BreakingNewsController extends Controller
{
    public function index()
    {
        $breakingItems = BreakingItem::with('news')->orderByDesc('priority')->orderByDesc('created_at')->paginate(15);
        $recentNews = News::published()->latest('published_at')->take(20)->get();

        return view('admin.breaking_news.index', compact('breakingItems', 'recentNews'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'news_id' => 'nullable|exists:news,id',
            'url' => 'nullable|url|max:255',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        BreakingItem::create([
            'label' => $validated['label'],
            'news_id' => $validated['news_id'] ?? null,
            'url' => $validated['url'] ?? null,
            'priority' => $validated['priority'] ?? 10,
            'is_active' => $request->has('is_active'),
            'starts_at' => now(),
        ]);

        return back()->with('success', 'ব্রেকিং নিউজ সফলভাবে যোগ করা হয়েছে এবং ফ্রন্টএন্ডে প্রদর্শিত হচ্ছে।');
    }

    public function toggle(BreakingItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return back()->with('success', 'ব্রেকিং নিউজের স্ট্যাটাস পরিবর্তন করা হয়েছে।');
    }

    public function destroy(BreakingItem $item)
    {
        $item->delete();

        return back()->with('success', 'ব্রেকিং নিউজ মুছে ফেলা হয়েছে।');
    }
}

