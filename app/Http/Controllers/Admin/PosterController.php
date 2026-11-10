<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedPoster;
use App\Models\News;
use App\Models\PosterTemplate;
use App\Services\PosterGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosterController extends Controller
{
    public function __construct(
        protected PosterGeneratorService $posterService
    ) {}

    public function index()
    {
        $posters = GeneratedPoster::with(['news', 'generator'])
            ->orderByDesc('created_at')
            ->paginate(16);

        return view('admin.posters.index', compact('posters'));
    }

    public function create(Request $request)
    {
        $newsId = $request->query('news_id');
        $selectedNews = $newsId ? News::published()->find($newsId) : null;
        $publishedNews = News::published()->orderByDesc('published_at')->take(30)->get();

        return view('admin.posters.create', compact('selectedNews', 'publishedNews'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'news_id' => 'required|exists:news,id',
            'dimensions' => 'required|in:1080x1080,1200x630',
            'headline_text' => 'nullable|string|max:200',
        ]);

        $news = News::findOrFail($validated['news_id']);

        $poster = $this->posterService->generate(
            $news,
            $validated['dimensions'],
            $validated['headline_text'] ?? null,
            null,
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'poster_id' => $poster->id,
                'url' => $poster->url,
                'dimensions' => $poster->dimensions,
            ]);
        }

        return redirect()->route('admin.posters.index')
            ->with('success', 'সংবাদের সোশ্যাল পোস্টার সফলভাবে তৈরি হয়েছে।');
    }

    public function destroy(GeneratedPoster $poster)
    {
        if (Storage::disk('public')->exists($poster->file_path)) {
            Storage::disk('public')->delete($poster->file_path);
        }

        $poster->delete();

        return back()->with('success', 'পোস্টার সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
