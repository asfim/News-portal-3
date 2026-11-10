<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsSource;
use App\Models\Category;
use App\Services\NewsImportService;
use Illuminate\Http\Request;

class NewsSourceController extends Controller
{
    public function index()
    {
        $sources = NewsSource::with(['defaultCategory'])
            ->withCount(['imports', 'logs'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.automation.sources.index', compact('sources'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.automation.sources.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'feed_url' => 'required|url|max:500',
            'type' => 'required|in:rss,atom,json_api',
            'default_category_id' => 'nullable|exists:categories,id',
            'language' => 'required|string|max:10',
            'fetch_interval_minutes' => 'required|integer|min:5|max:1440',
            'is_active' => 'boolean',
            'auto_publish' => 'boolean',
            'author_attribution' => 'nullable|string|max:150',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_publish'] = $request->boolean('auto_publish', false);

        $source = NewsSource::create($validated);

        return redirect()->route('admin.automation.sources.index')
            ->with('success', "নিউজ সোর্স '{$source->name}' সফলভাবে যুক্ত হয়েছে।");
    }

    public function edit(NewsSource $source)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.automation.sources.edit', compact('source', 'categories'));
    }

    public function update(Request $request, NewsSource $source)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'feed_url' => 'required|url|max:500',
            'type' => 'required|in:rss,atom,json_api',
            'default_category_id' => 'nullable|exists:categories,id',
            'language' => 'required|string|max:10',
            'fetch_interval_minutes' => 'required|integer|min:5|max:1440',
            'is_active' => 'boolean',
            'auto_publish' => 'boolean',
            'author_attribution' => 'nullable|string|max:150',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_publish'] = $request->boolean('auto_publish', false);

        $source->update($validated);

        return redirect()->route('admin.automation.sources.index')
            ->with('success', "নিউজ সোর্স '{$source->name}' সফলভাবে আপডেট হয়েছে।");
    }

    public function toggle(NewsSource $source)
    {
        $source->update(['is_active' => !$source->is_active]);

        $msg = $source->is_active ? 'সক্রিয় করা হয়েছে।' : 'নিষ্ক্রিয় করা হয়েছে।';
        return back()->with('success', "'{$source->name}' সোর্স {$msg}");
    }

    public function importNow(NewsSource $source, NewsImportService $importService)
    {
        $log = $importService->importFromSource($source);

        if ($log->status === 'failed') {
            return back()->with('error', $log->message);
        }

        return back()->with('success', $log->message);
    }

    public function destroy(NewsSource $source)
    {
        $name = $source->name;
        $source->delete();

        return redirect()->route('admin.automation.sources.index')
            ->with('success', "'{$name}' সোর্স সফলভাবে মুছে ফেলা হয়েছে।");
    }
}
