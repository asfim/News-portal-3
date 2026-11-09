@extends('layouts.frontend')

@section('title', $news->seo_title ?? ($news->title . ' | এইচ টেক প্রোভিশন আইটি'))
@section('meta_description', $news->seo_description ?? $news->excerpt)
@section('og_type', 'article')
@section('og_title', $news->title)
@section('og_description', $news->excerpt)
@section('og_image', $news->cover_image_url)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "NewsArticle",
    "headline": "{{ $news->title }}",
    "image": [
        "{{ $news->cover_image_url }}"
    ],
    "datePublished": "{{ $news->published_at ? $news->published_at->toIso8601String() : '' }}",
    "dateModified": "{{ $news->content_updated_at ? $news->content_updated_at->toIso8601String() : ($news->published_at ? $news->published_at->toIso8601String() : '') }}",
    "author": [
        @foreach($news->authors as $author)
        {
            "@@type": "Person",
            "name": "{{ $author->name }}",
            "url": "{{ route('author.show', $author->slug) }}"
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    "publisher": {
        "@@type": "Organization",
        "name": "এইচ টেক প্রোভিশন আইটি",
        "logo": {
            "@@type": "ImageObject",
            "url": "https://media.prothomalo.com/prothomalo-bangla/2025-11-17/qix5skks/palo-bangla.svg"
        }
    },
    "description": "{{ $news->excerpt }}"
}
</script>
@endsection

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 20px 16px;">
    
    <!-- Breadcrumb -->
    <nav style="font-size: 13px; color: #888; margin-bottom: 16px;">
        <a href="{{ route('home') }}" style="color: #666;">প্রচ্ছদ</a>
        @if($news->primaryCategory)
            <span style="margin: 0 6px;">/</span>
            <a href="{{ route('category.show', $news->primaryCategory->slug) }}" style="color: #d60000; font-weight: 700;">
                {{ $news->primaryCategory->name }}
            </a>
        @endif
    </nav>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        
        <!-- Main Article Column -->
        <article class="article-main">
            @if($news->kicker)
                <div style="color: #d60000; font-weight: 700; font-size: 18px; margin-bottom: 6px;">
                    {{ $news->kicker }}
                </div>
            @endif

            <h1 style="font-size: 34px; line-height: 1.3; font-weight: 700; margin: 0 0 16px 0; color: #111;">
                {{ $news->title }}
            </h1>

            @if($news->subtitle)
                <p style="font-size: 18px; line-height: 1.45; color: #555; margin: 0 0 16px 0;">
                    {{ $news->subtitle }}
                </p>
            @endif

            <!-- Author & Time Row -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 14px 0; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    @php $firstAuthor = $news->authors->first(); @endphp
                    @if($firstAuthor)
                        <div style="font-size: 15px;">
                            <a href="{{ route('author.show', $firstAuthor->slug) }}" style="font-weight: 700; color: #111;">
                                {{ $firstAuthor->name }}
                            </a>
                            @if($firstAuthor->designation)
                                <span style="color: #888; font-size: 13px;">({{ $firstAuthor->designation }})</span>
                            @endif
                        </div>
                    @else
                        <div style="font-size: 15px; font-weight: 700; color: #111;">
                            নিজস্ব প্রতিবেদক
                        </div>
                    @endif
                </div>

                <div style="font-size: 13px; color: #777;">
                    <span>প্রকাশ: {{ bn_date($news->published_at) }}</span>
                    @if($news->content_updated_at && $news->content_updated_at->gt($news->published_at))
                        <span style="margin: 0 6px;">|</span>
                        <span>আপডেট: {{ bn_time_ago($news->content_updated_at) }}</span>
                    @endif
                </div>
            </div>

            <!-- Social Share Bar & Bookmark -->
            <div style="display: flex; gap: 10px; margin-bottom: 24px; align-items: center; flex-wrap: wrap;">
                <span style="font-size: 13px; color: #777; font-weight: 700;">শেয়ার করুন:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" style="background: #1877f2; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                    ফেসবুক
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" target="_blank" rel="noopener noreferrer" style="background: #000; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                    এক্স (টুইটার)
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($news->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" style="background: #25d366; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                    হোয়াটসঅ্যাপ
                </a>

                @auth
                    @php
                        $isBookmarked = \App\Models\Bookmark::where('user_id', auth()->id())->where('news_id', $news->id)->exists();
                    @endphp
                    <form action="{{ route('news.bookmark.toggle', $news->public_id) }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" style="background: {{ $isBookmarked ? '#d60000' : '#f1f5f9' }}; color: {{ $isBookmarked ? '#fff' : '#334155' }}; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                            <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                            {{ $isBookmarked ? 'সংরক্ষিত' : 'বুকমার্ক' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" title="বুকমার্ক করতে লগইন করুন" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                        বুকমার্ক
                    </a>
                @endauth
            </div>

            @if(session('bookmark_status'))
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 10px 14px; border-radius: 6px; margin-bottom: 20px; font-size: 13px;">
                    {{ session('bookmark_status') }}
                </div>
            @endif

            <!-- Cover Photo / Video Player -->
            @if($news->type === 'video' && $news->video)
                <div style="margin-bottom: 24px; background: #000; border-radius: 6px; overflow: hidden; aspect-ratio: 16/9;">
                    @if($news->video->provider === 'youtube')
                        <iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/{{ $news->video->provider_video_id }}" frameborder="0" allowfullscreen></iframe>
                    @else
                        <div style="color: #fff; display: flex; align-items: center; justify-content: center; height: 100%;">
                            ভিডিও প্লেয়ার
                        </div>
                    @endif
                </div>
            @elseif($news->cover_image_url)
                <figure style="margin: 0 0 24px 0;">
                    <div style="border-radius: 4px; overflow: hidden; width: 100%;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: auto; display: block;" fetchpriority="high">
                    </div>
                    @if($news->cover_caption || $news->cover_credit)
                        <figcaption style="font-size: 13px; color: #666; margin-top: 8px; line-height: 1.4;">
                            {{ $news->cover_caption }}
                            @if($news->cover_credit)
                                <span style="color: #999; font-weight: 700;">ছবি: {{ $news->cover_credit }}</span>
                            @endif
                        </figcaption>
                    @endif
                </figure>
            @endif

            <!-- Article Body -->
            <div class="article-content-body" style="font-size: 18px; line-height: 1.75; color: #222; margin-bottom: 32px;">
                {!! $news->body_html !!}
            </div>

            <!-- Tags -->
            @if($news->tags->isNotEmpty())
                <div style="margin: 32px 0 24px 0; padding-top: 16px; border-top: 1px solid #eee;">
                    <strong style="font-size: 15px; color: #333; margin-right: 8px;">ট্যাগ:</strong>
                    @foreach($news->tags as $t)
                        <a href="{{ route('tag.show', $t->slug) }}" style="display: inline-block; background: #f2f2f2; color: #333; padding: 4px 12px; border-radius: 16px; font-size: 13px; margin: 4px 4px 4px 0;">
                            #{{ $t->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- In-Article Advertisement -->
            <x-ad-slot slotKey="in_article_banner" />

            <!-- Comments Section -->
            @if($news->comments_enabled)
                <section style="margin-top: 40px; padding-top: 24px; border-top: 2px solid #222;">
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;">
                        মন্তব্য করুন ({{ bn_num($news->approvedComments->count()) }})
                    </h3>

                    @if(session('comment_success'))
                        <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;">
                            {{ session('comment_success') }}
                        </div>
                    @endif

                    <!-- Comment Submission Form -->
                    <form action="{{ route('news.comment.store', $news->public_id) }}" method="POST" style="margin-bottom: 30px;">
                        @csrf
                        <textarea name="body" rows="4" style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 12px; font-family: inherit; font-size: 15px; outline: none; margin-bottom: 10px;" placeholder="আপনার গঠনমূলক মন্তব্য লিখুন..." required></textarea>
                        <button type="submit" style="background: #d60000; color: #fff; border: none; padding: 10px 24px; border-radius: 4px; font-weight: 700; font-size: 15px; cursor: pointer; font-family: inherit;">
                            মন্তব্য জমা দিন
                        </button>
                    </form>

                    <!-- Approved Comments List -->
                    <div class="comments-list">
                        @forelse($news->approvedComments as $comment)
                            <div style="padding: 14px 0; border-bottom: 1px solid #eee;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <strong style="font-size: 14px; color: #111;">{{ $comment->user ? $comment->user->name : 'পাঠক' }}</strong>
                                    <span style="font-size: 12px; color: #888;">{{ bn_time_ago($comment->created_at) }}</span>
                                </div>
                                <p style="font-size: 15px; line-height: 1.5; color: #333; margin: 0;">
                                    {{ $comment->body }}
                                </p>
                            </div>
                        @empty
                            <p style="font-size: 14px; color: #888;">এখনো কোনো মন্তব্য নেই। প্রথম মন্তব্যটি আপনিই করুন!</p>
                        @endforelse
                    </div>
                </section>
            @endif

        </article>

        <!-- Sidebar Column -->
        <aside class="article-sidebar">
            <!-- Latest News Widget -->
            <div style="background: #fafafa; border: 1px solid #ebebeb; border-radius: 6px; padding: 20px; margin-bottom: 30px;">
                <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; border-bottom: 2px solid #d60000; padding-bottom: 8px;">
                    সর্বশেষ সংবাদ
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($latestNews as $idx => $lNews)
                        <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; gap: 10px; align-items: flex-start;">
                            <span style="color: #d60000; font-weight: 700; font-size: 15px;">{{ bn_num($idx + 1) }}.</span>
                            <div>
                                <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}" style="font-size: 14px; font-weight: 700; line-height: 1.35; color: #111;">
                                    {{ $lNews->title }}
                                </a>
                                <div style="font-size: 12px; color: #888; margin-top: 4px;">{{ bn_time_ago($lNews->published_at) }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Ad Placeholder -->
            <div style="background: #fdfdfd; border: 1px dashed #ccc; border-radius: 4px; padding: 24px; text-align: center;">
                <span style="font-size: 11px; color: #999;">বিজ্ঞাপন</span>
                <div style="height: 250px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 4px; color: #666; margin-top: 8px;">
                    ৩০০ × ২৫০ বিজ্ঞাপন
                </div>
            </div>
        </aside>

    </div>

    <!-- Related Stories Section -->
    @if(isset($relatedNews) && $relatedNews->isNotEmpty())
        <section style="margin-top: 50px; padding-top: 30px; border-top: 2px solid #222;">
            <h3 style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">
                সম্পর্কিত খবর
            </h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                @foreach($relatedNews as $relNews)
                    <article style="border: 1px solid #eee; border-radius: 4px; overflow: hidden; background: #fff;">
                        <a href="{{ route('news.show', [$relNews->public_id, $relNews->slug]) }}">
                            @if($relNews->cover_image_url)
                                <div style="aspect-ratio: 16/10; overflow: hidden;">
                                    <img src="{{ $relNews->cover_image_url }}" alt="{{ $relNews->title }}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                </div>
                            @endif
                            <div style="padding: 12px;">
                                <h4 style="font-size: 15px; font-weight: 700; line-height: 1.35; margin: 0 0 6px 0;">
                                    {{ $relNews->title }}
                                </h4>
                                <span style="font-size: 12px; color: #888;">{{ bn_time_ago($relNews->published_at) }}</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
