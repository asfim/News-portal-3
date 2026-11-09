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
    <article class="lead-news-card" style="margin-bottom: 24px; position: relative; display: block; width: 100%;">
        @if($showImage && $news->cover_image_url)
            <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; margin-bottom: 14px; text-decoration: none;">
                <div style="position: relative; width: 100%; aspect-ratio: 16/9; border-radius: 4px; overflow: hidden; background: #e5e7eb;">
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" fetchpriority="high">
                    @if($news->type === 'video')
                        <span style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.75); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                            <svg width="14" height="14" fill="#d60000" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> ভিডিও
                        </span>
                    @endif
                </div>
            </a>
        @endif

        <div style="margin-bottom: 8px;">
            <h2 style="font-size: 26px; line-height: 1.35; font-weight: 700; margin: 0 0 10px 0;">
                <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                    @if($news->kicker)
                        <span style="color: #d60000; margin-right: 6px;">{{ $news->kicker }}</span>
                    @endif
                    <span>{{ $news->title }}</span>
                </a>
            </h2>
        </div>

        @if($showExcerpt && $news->excerpt)
            <p style="font-size: 15px; line-height: 1.55; color: #444; margin: 0 0 10px 0;">
                {{ $news->excerpt }}
            </p>
        @endif

        <div style="font-size: 13px; color: #888;">
            <time>{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'horizontal')
    <article class="horizontal-news-card" style="display: flex; gap: 14px; margin-bottom: 18px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; width: 100%;">
        @if($showImage && $news->cover_image_url)
            <div style="flex-shrink: 0; width: 110px;">
                <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="text-decoration: none;">
                    <div style="width: 110px; height: 75px; border-radius: 4px; overflow: hidden; background: #eee;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                    </div>
                </a>
            </div>
        @endif
        <div style="flex: 1; min-width: 0;">
            <h3 style="font-size: 15px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
                <a href="{{ $detailUrl }}" style="color: #222; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#222'">
                    @if($news->kicker)
                        <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                    @endif
                    <span>{{ $news->title }}</span>
                </a>
            </h3>
            @if($showExcerpt && $news->excerpt)
                <p style="font-size: 13px; line-height: 1.45; color: #555; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $news->excerpt }}
                </p>
            @endif
            <div style="font-size: 12px; color: #888;">
                <time>{{ bn_time_ago($news->published_at) }}</time>
            </div>
        </div>
    </article>

@elseif($variant === 'vertical')
    <article class="vertical-news-card" style="margin-bottom: 16px; width: 100%;">
        @if($showImage && $news->cover_image_url)
            <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; margin-bottom: 8px; text-decoration: none;">
                <div style="width: 100%; aspect-ratio: 16/10; border-radius: 4px; overflow: hidden; background: #eee;">
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.25s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'" loading="lazy">
                </div>
            </a>
        @endif
        <h3 style="font-size: 15px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
            <a href="{{ $detailUrl }}" style="color: #222; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#222'">
                @if($news->kicker)
                    <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                @endif
                <span>{{ $news->title }}</span>
            </a>
        </h3>
        @if($showExcerpt && $news->excerpt)
            <p style="font-size: 13px; line-height: 1.45; color: #555; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $news->excerpt }}
            </p>
        @endif
        <div style="font-size: 12px; color: #888;">
            <time>{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'compact')
    <article class="compact-news-card" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f4f4f4; width: 100%;">
        <div style="flex: 1; min-width: 0;">
            <h3 style="font-size: 15px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
                <a href="{{ $detailUrl }}" style="color: #222; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#222'">
                    @if($news->kicker)
                        <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                    @endif
                    <span>{{ $news->title }}</span>
                </a>
            </h3>
            <div style="font-size: 12px; color: #888;">
                <time>{{ bn_time_ago($news->published_at) }}</time>
            </div>
        </div>
        @if($showImage && $news->cover_image_url)
            <div style="flex-shrink: 0; width: 85px;">
                <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="text-decoration: none; display: block;">
                    <div style="width: 85px; height: 58px; border-radius: 4px; overflow: hidden; background: #eee;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                    </div>
                </a>
            </div>
        @endif
    </article>

@elseif($variant === 'video')
    <article class="video-news-card" style="position: relative; background: #000; border-radius: 4px; overflow: hidden; color: #fff;">
        <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; text-decoration: none;">
            <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9; display: block;" loading="lazy">
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
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #e0e0e0; overflow: hidden; flex-shrink: 0; border: 1px solid #ddd;">
                @if($author && $author->avatarMedia)
                    <img src="{{ $author->avatarMedia->path }}" alt="{{ $author->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @elseif($news->cover_image_url)
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
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
            <a href="{{ $detailUrl }}" style="color: #222; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#222'">
                {{ $news->title }}
            </a>
        </h3>
        <span style="font-size: 12px; color: #999;">{{ bn_time_ago($news->published_at) }}</span>
    </article>
@endif
