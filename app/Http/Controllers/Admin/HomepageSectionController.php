<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\Category;
use App\Models\Collection;
use App\Models\News;
use App\Services\HomepageBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageSectionController extends Controller
{
    public function __construct(
        protected HomepageBuilderService $homepageBuilderService
    ) {}

    public function index()
    {
        $sections = HomepageSection::with(['category', 'items.news'])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();

        return view('admin.homepage_sections.index', compact('sections', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();
        $collections = Collection::where('is_active', true)->orderBy('name')->get();
        $layouts = $this->getAvailableLayouts();

        return view('admin.homepage_sections.create', compact('categories', 'collections', 'layouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'key' => 'nullable|string|max:50|unique:homepage_sections,key',
            'layout' => 'required|string|max:50',
            'source_type' => 'required|in:latest,category,collection,popular,manual',
            'category_id' => 'nullable|required_if:source_type,category|exists:categories,id',
            'collection_id' => 'nullable|required_if:source_type,collection|exists:collections,id',
            'item_limit' => 'required|integer|min:1|max:50',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);

        $validated['key'] = !empty($validated['key']) ? Str::slug($validated['key'], '_') : Str::slug($validated['title'], '_') . '_' . Str::random(4);
        $validated['sort_order'] = $validated['sort_order'] ?? (HomepageSection::max('sort_order') + 1);
        $validated['is_active'] = $request->boolean('is_active', true);

        $section = HomepageSection::create($validated);
        $this->homepageBuilderService->invalidateCache();

        return redirect()->route('admin.homepage-sections.edit', $section->id)
            ->with('success', "'{$section->title}' সেকশন তৈরি হয়েছে। এখন পিন্ড নিউজ ও বিস্তারিত কনফিগার করুন।");
    }

    public function edit(HomepageSection $section)
    {
        $section->load(['category', 'collection', 'items.news.primaryCategory']);
        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();
        $collections = Collection::where('is_active', true)->orderBy('name')->get();
        $layouts = $this->getAvailableLayouts();

        // Recent published news for manual pin selector
        $recentNews = News::published()
            ->orderByDesc('published_at')
            ->take(30)
            ->get();

        return view('admin.homepage_sections.edit', compact('section', 'categories', 'collections', 'layouts', 'recentNews'));
    }

    public function update(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'layout' => 'required|string|max:50',
            'source_type' => 'required|in:latest,category,collection,popular,manual',
            'category_id' => 'nullable|exists:categories,id',
            'collection_id' => 'nullable|exists:collections,id',
            'item_limit' => 'required|integer|min:1|max:50',
            'sort_order' => 'required|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $section->update($validated);
        $this->homepageBuilderService->invalidateCache();

        return back()->with('success', "'{$section->title}' সেকশন সফলভাবে আপডেট করা হয়েছে।");
    }

    public function toggle(HomepageSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);
        $this->homepageBuilderService->invalidateCache();

        $state = $section->is_active ? 'চালু' : 'বন্ধ';
        return back()->with('success', "'{$section->title}' সেকশনটি {$state} করা হয়েছে।");
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $id => $order) {
            HomepageSection::where('id', $id)->update(['sort_order' => (int)$order]);
        }

        $this->homepageBuilderService->invalidateCache();
        return response()->json(['success' => true, 'message' => 'সেকশনের ক্রম সফলভাবে সাজানো হয়েছে।']);
    }

    public function addItem(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'news_id' => 'required|exists:news,id',
            'position' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['section_id'] = $section->id;
        $validated['position'] = $validated['position'] ?? ($section->items()->max('position') + 1);

        HomepageSectionItem::updateOrCreate(
            ['section_id' => $section->id, 'news_id' => $validated['news_id']],
            $validated
        );

        $this->homepageBuilderService->invalidateCache();
        return back()->with('success', 'সংবাদটি সেকশনে সফলভাবে যুক্ত/পিন করা হয়েছে।');
    }

    public function removeItem(HomepageSection $section, HomepageSectionItem $item)
    {
        if ($item->section_id === $section->id) {
            $item->delete();
            $this->homepageBuilderService->invalidateCache();
        }

        return back()->with('success', 'পিন্ড সংবাদটি সফলভাবে সরানো হয়েছে।');
    }

    public function destroy(HomepageSection $section)
    {
        $title = $section->title;
        $section->items()->delete();
        $section->delete();

        $this->homepageBuilderService->invalidateCache();
        return redirect()->route('admin.homepage-sections.index')
            ->with('success', "'{$title}' সেকশনটি সফলভাবে মুছে ফেলা হয়েছে।");
    }

    protected function getAvailableLayouts(): array
    {
        return [
            'hero_3col' => 'প্রধান সংবাদ (Hero 3-Column Editorial Grid)',
            'tags_bar' => 'আলোচিত বিষয় / ট্রেন্ডিং ট্যাগস স্ট্রিপ (Tags Bar)',
            'grid_4col' => 'আপনার জন্য / ৪-কলাম গ্রিড (4-Column Newspaper Grid)',
            'video_carousel' => 'ভিডিও সেকশন (Video Carousel / Grid)',
            'mid_3col' => 'সর্বশেষ ও সর্বাধিক পঠিত ট্যাব উইজেট (Latest & Most Read Tabbed)',
            'section_lead_quad' => 'বাংলাদেশ / লিড ও সাব-স্টোরি গ্রিড (Section Lead + 4 Stories)',
            'opinion_split' => 'মতামত / কলামিস্ট স্প্লিট (Opinion / Columnist Split)',
            'beige_tech' => 'বিজ্ঞান ও প্রযুক্তি ৪-কলাম টেবিল (Technology 4-Column Table)',
            'dark_video_hub' => 'ভিডিও হাব ডার্ক কার্ড (Dark Video Hub)',
            'pubs_grid' => 'অন্যান্য প্রকাশনা ও উদ্যোগ (Publications Grid)',
        ];
    }
}
