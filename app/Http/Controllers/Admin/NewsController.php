<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Models\AuthorProfile;
use App\Models\Location;
use App\Models\Media;
use App\Models\NewsRevision;
use App\Models\NewsStatusHistory;
use App\Models\NewsVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');

        $query = News::with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('primary_category_id', $categoryId);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // If Reporter, can only view own news or all news depending on permission
        $user = auth()->user();
        if ($user->hasRole('reporter') && !$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        }

        $newsList = $query->paginate(15)->withQueryString();
        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();

        return view('admin.news.index', compact('newsList', 'categories', 'status', 'categoryId', 'search'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $authors = AuthorProfile::orderBy('name')->get();
        $divisions = Location::divisions()->with('children')->get();
        $recentMedia = Media::latest()->take(24)->get();

        return view('admin.news.create', compact('categories', 'tags', 'authors', 'divisions', 'recentMedia'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'kicker' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'body_html' => 'required|string',
            'type' => 'required|in:article,opinion,photo,video,live',
            'primary_category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:author_profiles,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|max:5120',
            'cover_media_id' => 'nullable|exists:media,id',
            'cover_image_url_direct' => 'nullable|string|max:1000',
            'cover_caption' => 'nullable|string|max:255',
            'cover_credit' => 'nullable|string|max:150',
            'video_provider' => 'nullable|in:youtube,vimeo',
            'video_id' => 'nullable|string|max:100',
            'action' => 'required|in:draft,submit_review,publish,schedule',
            'scheduled_at' => 'nullable|date',
            'comments_enabled' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($request, $validated, $user) {
            // Slug generation
            $slug = !empty($validated['slug']) 
                ? Str::slug($validated['slug']) 
                : Str::slug($validated['title']);
            
            if (empty($slug)) {
                $slug = Str::lower((string) Str::ulid());
            }

            // Ensure unique slug
            $originalSlug = $slug;
            $count = 1;
            while (News::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            // Cover Image Selection or Direct Upload
            $coverMediaId = $request->input('cover_media_id') ?: null;
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/news', $filename, 'public');

                $media = Media::create([
                    'uploaded_by' => $user->id,
                    'disk' => 'public',
                    'path' => 'storage/' . $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                    'caption' => $request->input('cover_caption'),
                    'credit' => $request->input('cover_credit'),
                    'status' => 'active',
                ]);
                $coverMediaId = $media->id;
            } elseif ($request->filled('cover_image_url_direct')) {
                $directUrl = trim($request->input('cover_image_url_direct'));
                $media = Media::firstOrCreate(
                    ['path' => $directUrl],
                    [
                        'uploaded_by' => $user->id,
                        'disk' => 'public',
                        'original_name' => basename(parse_url($directUrl, PHP_URL_PATH) ?: 'external-image.jpg'),
                        'mime_type' => 'image/jpeg',
                        'size_bytes' => 0,
                        'caption' => $request->input('cover_caption'),
                        'credit' => $request->input('cover_credit'),
                        'status' => 'active',
                    ]
                );
                $coverMediaId = $media->id;
            }

            // Determine Status based on Action & Permissions
            $status = 'draft';
            $publishedAt = null;
            $scheduledAt = null;

            if ($validated['action'] === 'submit_review') {
                $status = 'review_pending';
            } elseif ($validated['action'] === 'publish') {
                // Reporter cannot directly publish; only editor/admin can
                if ($user->hasRole('reporter') && !$user->isSuperAdmin()) {
                    $status = 'review_pending';
                } else {
                    $status = 'published';
                    $publishedAt = now();
                }
            } elseif ($validated['action'] === 'schedule') {
                if ($user->hasRole('reporter') && !$user->isSuperAdmin()) {
                    $status = 'review_pending';
                } else {
                    $status = 'scheduled';
                    $scheduledAt = Carbon::parse($validated['scheduled_at'], 'Asia/Dhaka')->setTimezone('UTC');
                }
            }

            $news = News::create([
                'public_id' => (string) Str::ulid(),
                'slug' => $slug,
                'title' => $validated['title'],
                'kicker' => $validated['kicker'] ?? null,
                'subtitle' => $validated['subtitle'] ?? null,
                'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body_html']), 200),
                'body_html' => $validated['body_html'],
                'body_plain' => strip_tags($validated['body_html']),
                'type' => $validated['type'],
                'status' => $status,
                'primary_category_id' => $validated['primary_category_id'],
                'location_id' => $validated['location_id'] ?? null,
                'cover_media_id' => $coverMediaId,
                'cover_caption' => $validated['cover_caption'] ?? null,
                'cover_credit' => $validated['cover_credit'] ?? null,
                'created_by' => $user->id,
                'published_by' => $status === 'published' ? $user->id : null,
                'approved_by' => in_array($status, ['published', 'scheduled']) ? $user->id : null,
                'published_at' => $publishedAt,
                'scheduled_at' => $scheduledAt,
                'content_updated_at' => now(),
                'comments_enabled' => $request->boolean('comments_enabled', true),
                'lock_version' => 1,
            ]);

            // Sync Primary Category
            $news->categories()->sync([$validated['primary_category_id']]);

            // Sync Authors
            if (!empty($validated['author_ids'])) {
                $syncData = [];
                foreach ($validated['author_ids'] as $idx => $authId) {
                    $syncData[$authId] = ['sort_order' => $idx + 1];
                }
                $news->authors()->sync($syncData);
            }

            // Sync Tags & Create New Tags if provided
            $tagIds = $request->input('tag_ids', []) ?? [];
            if ($request->filled('new_tags')) {
                $rawTags = explode(',', $request->input('new_tags'));
                foreach ($rawTags as $rawName) {
                    $rawName = trim($rawName);
                    if ($rawName !== '') {
                        $tag = Tag::where('name', $rawName)->first();
                        if (!$tag) {
                            $tagSlug = Str::slug($rawName);
                            if (empty($tagSlug)) {
                                $tagSlug = 'tag-' . Str::lower(Str::random(6));
                            }
                            $origSlug = $tagSlug;
                            $c = 1;
                            while (Tag::where('slug', $tagSlug)->exists()) {
                                $tagSlug = "{$origSlug}-{$c}";
                                $c++;
                            }
                            $tag = Tag::create([
                                'name' => $rawName,
                                'slug' => $tagSlug,
                                'sort_order' => (Tag::max('sort_order') ?? 0) + 1,
                                'is_featured' => false,
                            ]);
                        }
                        $tagIds[] = $tag->id;
                    }
                }
            }
            if (!empty($tagIds)) {
                $news->tags()->sync(array_unique($tagIds));
            }

            // Video Provider Link
            if ($validated['type'] === 'video' && !empty($validated['video_id'])) {
                NewsVideo::create([
                    'news_id' => $news->id,
                    'provider' => $validated['video_provider'] ?? 'youtube',
                    'provider_video_id' => $validated['video_id'],
                ]);
            }

            // Create initial Revision snapshot
            NewsRevision::create([
                'news_id' => $news->id,
                'version' => 1,
                'snapshot' => [
                    'title' => $news->title,
                    'body_html' => $news->body_html,
                    'excerpt' => $news->excerpt,
                    'primary_category_id' => $news->primary_category_id,
                ],
                'status' => $status,
                'edited_by' => $user->id,
                'approved_by' => in_array($status, ['published', 'scheduled']) ? $user->id : null,
                'approved_at' => in_array($status, ['published', 'scheduled']) ? now() : null,
                'change_note' => 'প্রাথমিক খসড়া তৈরি',
            ]);

            // Status history
            NewsStatusHistory::create([
                'news_id' => $news->id,
                'from_status' => null,
                'to_status' => $status,
                'actor_id' => $user->id,
                'reason' => 'সংবাদ তৈরি',
            ]);

            return redirect()->route('admin.news.index')->with('success', 'সংবাদ সফলভাবে তৈরি হয়েছে।');
        });
    }

    public function edit(News $news)
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $authors = AuthorProfile::orderBy('name')->get();
        $divisions = Location::divisions()->with('children')->get();
        $revisions = $news->revisions()->with('editor')->orderByDesc('version')->get();
        $recentMedia = Media::latest()->take(24)->get();

        return view('admin.news.edit', compact('news', 'categories', 'tags', 'authors', 'divisions', 'revisions', 'recentMedia'));
    }

    public function update(Request $request, News $news)
    {
        $user = auth()->user();

        // Optimistic Locking Check
        if ($request->has('lock_version') && (int)$request->input('lock_version') !== $news->lock_version) {
            return back()->withErrors(['lock_version' => 'অন্য একজন সম্পাদক এই সংবাদটি ইতোমধ্যে সংশোধন করেছেন। আপনার পরিবর্তন সংরক্ষণ করার আগে পৃষ্ঠাটি রিফ্রেশ করুন।']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news,slug,' . $news->id,
            'kicker' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'body_html' => 'required|string',
            'type' => 'required|in:article,opinion,photo,video,live',
            'primary_category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:author_profiles,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|max:5120',
            'cover_media_id' => 'nullable|exists:media,id',
            'cover_image_url_direct' => 'nullable|string|max:1000',
            'cover_caption' => 'nullable|string|max:255',
            'cover_credit' => 'nullable|string|max:150',
            'action' => 'required|in:draft,submit_review,publish,schedule,archived',
            'scheduled_at' => 'nullable|date',
            'comments_enabled' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($request, $validated, $news, $user) {
            $coverMediaId = $request->input('cover_media_id') ?: $news->cover_media_id;
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/news', $filename, 'public');

                $media = Media::create([
                    'uploaded_by' => $user->id,
                    'disk' => 'public',
                    'path' => 'storage/' . $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                    'caption' => $request->input('cover_caption'),
                    'credit' => $request->input('cover_credit'),
                    'status' => 'active',
                ]);
                $coverMediaId = $media->id;
            } elseif ($request->filled('cover_image_url_direct')) {
                $directUrl = trim($request->input('cover_image_url_direct'));
                $media = Media::firstOrCreate(
                    ['path' => $directUrl],
                    [
                        'uploaded_by' => $user->id,
                        'disk' => 'public',
                        'original_name' => basename(parse_url($directUrl, PHP_URL_PATH) ?: 'external-image.jpg'),
                        'mime_type' => 'image/jpeg',
                        'size_bytes' => 0,
                        'caption' => $request->input('cover_caption'),
                        'credit' => $request->input('cover_credit'),
                        'status' => 'active',
                    ]
                );
                $coverMediaId = $media->id;
            }

            $oldStatus = $news->status;
            $newStatus = $oldStatus;
            $publishedAt = $news->published_at;
            $scheduledAt = $news->scheduled_at;

            if ($validated['action'] === 'publish') {
                if (!$user->hasRole('reporter') || $user->isSuperAdmin()) {
                    $newStatus = 'published';
                    if (!$publishedAt) {
                        $publishedAt = now();
                    }
                }
            } elseif ($validated['action'] === 'schedule') {
                $newStatus = 'scheduled';
                $scheduledAt = Carbon::parse($validated['scheduled_at'], 'Asia/Dhaka')->setTimezone('UTC');
            } elseif ($validated['action'] === 'archived') {
                $newStatus = 'archived';
            } elseif ($validated['action'] === 'draft') {
                $newStatus = 'draft';
            } elseif ($validated['action'] === 'submit_review') {
                $newStatus = 'review_pending';
            }

            $news->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'kicker' => $validated['kicker'] ?? null,
                'subtitle' => $validated['subtitle'] ?? null,
                'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body_html']), 200),
                'body_html' => $validated['body_html'],
                'body_plain' => strip_tags($validated['body_html']),
                'type' => $validated['type'],
                'status' => $newStatus,
                'primary_category_id' => $validated['primary_category_id'],
                'location_id' => $validated['location_id'] ?? null,
                'cover_media_id' => $coverMediaId,
                'cover_caption' => $validated['cover_caption'] ?? null,
                'cover_credit' => $validated['cover_credit'] ?? null,
                'published_at' => $publishedAt,
                'scheduled_at' => $scheduledAt,
                'content_updated_at' => now(),
                'comments_enabled' => $request->boolean('comments_enabled', true),
                'lock_version' => $news->lock_version + 1,
            ]);

            // Sync categories, authors, tags
            $news->categories()->sync([$validated['primary_category_id']]);
            if (isset($validated['author_ids'])) {
                $syncData = [];
                foreach ($validated['author_ids'] as $idx => $authId) {
                    $syncData[$authId] = ['sort_order' => $idx + 1];
                }
                $news->authors()->sync($syncData);
            }
            // Sync tags & create new tags if provided
            $tagIds = $request->input('tag_ids', []) ?? [];
            if ($request->filled('new_tags')) {
                $rawTags = explode(',', $request->input('new_tags'));
                foreach ($rawTags as $rawName) {
                    $rawName = trim($rawName);
                    if ($rawName !== '') {
                        $tag = Tag::where('name', $rawName)->first();
                        if (!$tag) {
                            $tagSlug = Str::slug($rawName);
                            if (empty($tagSlug)) {
                                $tagSlug = 'tag-' . Str::lower(Str::random(6));
                            }
                            $origSlug = $tagSlug;
                            $c = 1;
                            while (Tag::where('slug', $tagSlug)->exists()) {
                                $tagSlug = "{$origSlug}-{$c}";
                                $c++;
                            }
                            $tag = Tag::create([
                                'name' => $rawName,
                                'slug' => $tagSlug,
                                'sort_order' => (Tag::max('sort_order') ?? 0) + 1,
                                'is_featured' => false,
                            ]);
                        }
                        $tagIds[] = $tag->id;
                    }
                }
            }
            if ($request->has('tag_ids') || $request->filled('new_tags')) {
                $news->tags()->sync(array_unique($tagIds));
            }

            // Create revision record
            $nextVersion = ($news->revisions()->max('version') ?? 0) + 1;
            NewsRevision::create([
                'news_id' => $news->id,
                'version' => $nextVersion,
                'snapshot' => [
                    'title' => $news->title,
                    'body_html' => $news->body_html,
                    'excerpt' => $news->excerpt,
                ],
                'status' => $newStatus,
                'edited_by' => $user->id,
                'approved_by' => $newStatus === 'published' ? $user->id : null,
                'approved_at' => $newStatus === 'published' ? now() : null,
                'change_note' => $request->input('change_note', 'সংবাদ সংশোধন'),
            ]);

            if ($oldStatus !== $newStatus) {
                NewsStatusHistory::create([
                    'news_id' => $news->id,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus,
                    'actor_id' => $user->id,
                    'reason' => 'অবস্থা পরিবর্তন',
                ]);
            }

            return redirect()->route('admin.news.index')->with('success', 'সংবাদ সফলভাবে আপডেট করা হয়েছে।');
        });
    }

    public function destroy(News $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'সংবাদ সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
