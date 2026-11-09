@props([
    'news',
    'variant' => 'horizontal', // 'lead', 'horizontal', 'vertical', 'compact', 'video', 'opinion'
    'showExcerpt' => true,
    'showImage' => true,
    'hideBorder' => false,
])

@php
    $detailUrl = route('news.show', [$news->public_id, $news->slug]);
@endphp

@if($variant === 'lead')
    <article class="palo-lead-card" style="position: relative; display: block; width: 100%;">
        @if($showImage && $news->cover_image_url)
            <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; margin-bottom: 12px; text-decoration: none;">
                <div style="position: relative; width: 100%; aspect-ratio: 16/9; border-radius: 2px; overflow: hidden; background: #eee;">
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" fetchpriority="high">
                    @if($news->type === 'video')
                        <span style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.75); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                            <svg width="14" height="14" fill="#d60000" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> ভিডিও
                        </span>
                    @endif
                </div>
            </a>
        @endif

        <h2 style="font-size: 28px; line-height: 1.32; font-weight: 700; margin: 0 0 10px 0;">
            <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                @if($news->kicker)
                    <span style="color: #d60000; margin-right: 6px;">{{ $news->kicker }}</span>
                    <span style="color: #999; margin-right: 6px;">•</span>
                @endif
                <span>{{ $news->title }}</span>
            </a>
        </h2>

        @if($showExcerpt && $news->excerpt)
            <p style="font-size: 15px; line-height: 1.55; color: #444; margin: 0 0 10px 0;">
                {{ $news->excerpt }}
            </p>
        @endif

        <div style="font-size: 12px; color: #757575;">
            <time>{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'horizontal')
    <article class="palo-horizontal-card" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; {{ $hideBorder ? '' : 'border-bottom: 1px solid #dfdfdf; padding-bottom: 12px; margin-bottom: 12px;' }} width: 100%;">
        <div style="flex: 1; min-width: 0;">
            <h3 style="font-size: 15px; line-height: 1.38; font-weight: 700; margin: 0 0 6px 0;">
                <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                    @if($news->kicker)
                        <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                        <span style="color: #999; margin-right: 4px;">•</span>
                    @endif
                    <span>{{ $news->title }}</span>
                </a>
            </h3>
            @if($showExcerpt && $news->excerpt)
                <p style="font-size: 13px; line-height: 1.45; color: #555; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $news->excerpt }}
                </p>
            @endif
            <div style="font-size: 12px; color: #757575;">
                <time>{{ bn_time_ago($news->published_at) }}</time>
            </div>
        </div>
        @if($showImage && $news->cover_image_url)
            <div style="flex-shrink: 0; width: 100px;">
                <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="text-decoration: none; display: block;">
                    <div style="width: 100px; height: 68px; border-radius: 2px; overflow: hidden; background: #eee;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="eager">
                    </div>
                </a>
            </div>
        @endif
    </article>

@elseif($variant === 'vertical')
    <article class="palo-vertical-card" style="width: 100%; {{ $hideBorder ? '' : 'margin-bottom: 16px;' }}">
        @if($showImage && $news->cover_image_url)
            <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; margin-bottom: 8px; text-decoration: none;">
                <div style="width: 100%; aspect-ratio: 16/10; border-radius: 2px; overflow: hidden; background: #eee;">
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.25s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'" loading="eager">
                </div>
            </a>
        @endif
        <h3 style="font-size: 16px; line-height: 1.35; font-weight: 700; margin: 0 0 6px 0;">
            <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                @if($news->kicker)
                    <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                    <span style="color: #999; margin-right: 4px;">•</span>
                @endif
                <span>{{ $news->title }}</span>
            </a>
        </h3>
        @if($showExcerpt && $news->excerpt)
            <p style="font-size: 13px; line-height: 1.45; color: #555; margin: 0 0 6px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $news->excerpt }}
            </p>
        @endif
        <div style="font-size: 12px; color: #757575;">
            <time>{{ bn_time_ago($news->published_at) }}</time>
        </div>
    </article>

@elseif($variant === 'compact')
    <article class="palo-compact-card" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; {{ $hideBorder ? '' : 'border-bottom: 1px solid #dfdfdf; padding-bottom: 10px; margin-bottom: 10px;' }} width: 100%;">
        <div style="flex: 1; min-width: 0;">
            <h3 style="font-size: 15px; line-height: 1.35; font-weight: 700; margin: 0 0 4px 0;">
                <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                    @if($news->kicker)
                        <span style="color: #d60000; margin-right: 4px;">{{ $news->kicker }}</span>
                        <span style="color: #999; margin-right: 4px;">•</span>
                    @endif
                    <span>{{ $news->title }}</span>
                </a>
            </h3>
            <div style="font-size: 12px; color: #757575;">
                <time>{{ bn_time_ago($news->published_at) }}</time>
            </div>
        </div>
        @if($showImage && $news->cover_image_url)
            <div style="flex-shrink: 0; width: 85px;">
                <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="text-decoration: none; display: block;">
                    <div style="width: 85px; height: 58px; border-radius: 2px; overflow: hidden; background: #eee;">
                        <img src="{{ $news->cover_image_url }}" alt="{{ $news->cover_alt ?? $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="eager">
                    </div>
                </a>
            </div>
        @endif
    </article>

@elseif($variant === 'video')
    <article class="palo-video-card" style="position: relative; background: #000; border-radius: 3px; overflow: hidden; color: #fff;">
        <a href="{{ $detailUrl }}" aria-label="{{ $news->title }}" style="display: block; text-decoration: none;">
            <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9; display: block;" loading="eager">
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25);">
                    <div style="width: 44px; height: 44px; background: rgba(214,0,0,0.92); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.4);">
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
    <article class="palo-opinion-card" style="padding: 12px 0; {{ $hideBorder ? '' : 'border-bottom: 1px solid #dfdfdf;' }}">
        @php
            $author = $news->authors->first();
        @endphp
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <div style="width: 46px; height: 46px; border-radius: 50%; background: #e0e0e0; overflow: hidden; flex-shrink: 0; border: 1px solid #ddd;">
                @if($author && $author->avatarMedia)
                    <img src="{{ $author->avatarMedia->path }}" alt="{{ $author->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @elseif($news->cover_image_url)
                    <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @else
                    <svg viewBox="0 0 24 24" fill="#888" style="width: 100%; height: 100%;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                @endif
            </div>
            <div>
                <strong style="font-size: 15px; display: block; color: #d60000; font-weight: 700;">{{ $author ? $author->name : 'সম্পাদকীয়' }}</strong>
                <span style="font-size: 12px; color: #777;">{{ $author->designation ?? 'কলামিস্ট' }}</span>
            </div>
        </div>
        <h3 style="font-size: 16px; font-weight: 700; line-height: 1.35; margin: 0 0 4px 0;">
            <a href="{{ $detailUrl }}" style="color: #111; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                {{ $news->title }}
            </a>
        </h3>
        <span style="font-size: 12px; color: #757575;">{{ bn_time_ago($news->published_at) }}</span>
    </article>
@endif
