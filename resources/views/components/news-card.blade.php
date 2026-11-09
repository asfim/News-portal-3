@props([
    'news',
    'variant' => 'horizontal', // 'lead', 'horizontal', 'compact', 'video', 'opinion'
    'showExcerpt' => true,
    'showImage' => true,
])

@php
    $detailUrl = route('news.show', [$news->public_id, $news->slug]);
@endphp

@if($variant === 'lead')
    <article class="news_with_item haBBZ EqaDF _004hA" style="margin-bottom: 24px;">
        @if($showImage && $news->cover_image_url)
            <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; margin-bottom: 12px;">
                <div class="card-with-image-zoom qt-image-dynamic image3x2 card-image-wrapper JCQpo _8mCoa UUq0z" style="position: relative; width: 100%; border-radius: 4px; overflow: hidden;">
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; display: block;" fetchpriority="high">
                    @if($news->type === 'video')
                        <span style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                            <svg width="14" height="14" fill="#d60000" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> ভিডিও
                        </span>
                    @endif
                </div>
            </a>
        @endif
        <div class="card-with-image-zoom">
            <h2 class="headline-title _1d6-d" style="font-size: 26px; line-height: 1.35; font-weight: 700; margin: 0 0 10px 0;">
                <a href="{{ $detailUrl }}" class="title-link">
                    @if($news->kicker)
                        <span class="sub-title _4ckrT" style="color: #d60000; margin-right: 6px;">{{ $news->kicker }}</span>
                    @endif
                    <span class="tilte-no-link-parent">{{ $news->title }}</span>
                </a>
            </h2>
        </div>
        @if($showExcerpt && $news->excerpt)
            <p class="excerpt _4Nuxp" style="font-size: 16px; line-height: 1.5; color: #444; margin: 0 0 10px 0;">
                {{ $news->excerpt }}
            </p>
        @endif
        <div class="story-meta-data sONJ0" style="font-size: 13px; color: #888;">
            <time class="published-time fw8bp">{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'horizontal')
    <article class="left_image_right_news news_item haBBZ QA-72 wMFhj" style="display: flex; gap: 14px; margin-bottom: 18px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0;">
        @if($showImage && $news->cover_image_url)
            <div class="left-image _6HEAn" style="flex-shrink: 0; width: 110px;">
                <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}">
                    <div class="card-with-image-zoom" style="width: 110px; height: 75px; border-radius: 3px; overflow: hidden;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                    </div>
                </a>
            </div>
        @endif
        <div class="content-area" style="flex: 1;">
            <h3 class="headline-title _1d6-d" style="font-size: 16px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
                <a href="{{ $detailUrl }}" class="title-link">
                    @if($news->kicker)
                        <span class="sub-title _4ckrT" style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                    @endif
                    <span class="tilte-no-link-parent">{{ $news->title }}</span>
                </a>
            </h3>
            @if($showExcerpt && $news->excerpt)
                <p class="excerpt _4Nuxp" style="font-size: 13px; line-height: 1.45; color: #555; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $news->excerpt }}
                </p>
            @endif
            <div class="story-meta-data sONJ0" style="font-size: 12px; color: #888;">
                <time class="published-time fw8bp">{{ bn_time_ago($news->published_at) }}</time>
            </div>
        </div>
    </article>

@elseif($variant === 'compact')
    <article class="news_with_item SSPtv _004hA" style="margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f4f4f4;">
        <h3 class="headline-title _1d6-d" style="font-size: 15px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
            <a href="{{ $detailUrl }}" class="title-link">
                @if($news->kicker)
                    <span class="sub-title _4ckrT" style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                @endif
                <span class="tilte-no-link-parent">{{ $news->title }}</span>
            </a>
        </h3>
        <div class="story-meta-data sONJ0" style="font-size: 12px; color: #888;">
            <time class="published-time fw8bp">{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'video')
    <article class="video-news-card" style="position: relative; background: #000; border-radius: 4px; overflow: hidden; color: #fff;">
        <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block;">
            <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;" loading="lazy">
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25);">
                    <div style="width: 44px; height: 44px; background: rgba(214,0,0,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <div style="padding: 12px; background: #1a1a1a;">
                <h3 style="font-size: 15px; line-height: 1.35; font-weight: 700; color: #fff; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $news->title }}
                </h3>
                <span style="font-size: 12px; color: #aaa;">{{ bn_time_ago($news->published_at) }}</span>
            </div>
        </a>
    </article>

@elseif($variant === 'opinion')
    <article class="opinion-card" style="padding: 14px; background: #fafafa; border-radius: 4px; border: 1px solid #eee; margin-bottom: 14px;">
        @php
            $author = $news->authors->first();
        @endphp
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; overflow: hidden; flex-shrink: 0;">
                @if($author && $author->avatarMedia)
                    <img src="{{ $author->avatarMedia->path }}" alt="{{ $author->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <svg viewBox="0 0 24 24" fill="#888" style="width: 100%; height: 100%;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                @endif
            </div>
            <div>
                <strong style="font-size: 14px; display: block; color: #d60000;">{{ $author ? $author->name : 'সম্পাদকীয়' }}</strong>
                <span style="font-size: 12px; color: #777;">{{ $author->designation ?? 'লেখক' }}</span>
            </div>
        </div>
        <h3 style="font-size: 16px; font-weight: 700; line-height: 1.35; margin: 0 0 6px 0;">
            <a href="{{ $detailUrl }}" style="color: #222;">
                {{ $news->title }}
            </a>
        </h3>
        <span style="font-size: 12px; color: #999;">{{ bn_time_ago($news->published_at) }}</span>
    </article>
@endif
