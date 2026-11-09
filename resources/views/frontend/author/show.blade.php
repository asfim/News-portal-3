@extends('layouts.frontend')

@section('title', $author->name . ' | প্রথম আলো')

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    
    <!-- Author Profile Header -->
    <div style="background: #fafafa; border: 1px solid #ebebeb; border-radius: 6px; padding: 24px; margin-bottom: 30px; display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #e0e0e0; overflow: hidden; flex-shrink: 0;">
            @if($author->avatarMedia)
                <img src="{{ $author->avatarMedia->path }}" alt="{{ $author->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <svg viewBox="0 0 24 24" fill="#888" style="width: 100%; height: 100%;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            @endif
        </div>
        <div>
            <h1 style="font-size: 26px; font-weight: 700; margin: 0 0 4px 0;">{{ $author->name }}</h1>
            @if($author->designation)
                <div style="font-size: 15px; color: #d60000; font-weight: 600; margin-bottom: 8px;">{{ $author->designation }}</div>
            @endif
            @if($author->bio)
                <p style="font-size: 14px; color: #555; margin: 0; max-width: 600px;">{{ $author->bio }}</p>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #222; padding-bottom: 8px;">
                {{ $author->name }}-এর লেখা প্রতিবেদন ও বিশ্লেষণ
            </h2>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                @forelse($newsList as $story)
                    <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
                @empty
                    <p style="color: #888; grid-column: 1 / -1;">এই লেখকের কোনো প্রতিবেদন পাওয়া যায়নি।</p>
                @endforelse
            </div>

            <div style="margin-top: 30px;">
                {{ $newsList->links() }}
            </div>
        </div>

        <aside>
            <div style="background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #222; padding-bottom: 8px;">
                    সর্বশেষ সংবাদ
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($latestNews as $idx => $lNews)
                        <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                            <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}" style="font-size: 14px; font-weight: 700; color: #111;">
                                {{ $lNews->title }}
                            </a>
                            <div style="font-size: 12px; color: #888; margin-top: 4px;">{{ bn_time_ago($lNews->published_at) }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>

</div>
@endsection
