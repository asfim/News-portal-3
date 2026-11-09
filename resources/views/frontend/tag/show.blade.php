@extends('layouts.frontend')

@section('title', '#' . $tag->name . ' - সম্পর্কিত সংবাদ | প্রথম আলো')

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 28px; font-weight: 700; margin: 0;">#{{ $tag->name }}</h1>
        <span style="font-size: 14px; color: #777;">মোট সংবাদ: {{ bn_num($newsList->total()) }} টি</span>
    </div>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        <div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                @forelse($newsList as $story)
                    <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
                @empty
                    <p style="color: #888; grid-column: 1 / -1;">এই বিষয়ের কোনো সংবাদ এখনো পাওয়া যায়নি।</p>
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
