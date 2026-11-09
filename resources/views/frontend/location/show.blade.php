@extends('layouts.frontend')

@section('title', $location->name . ' - এলাকার সংবাদ | প্রথম আলো')

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; margin: 0;">{{ $location->name }}</h1>
            <span style="font-size: 13px; color: #888;">{{ $location->type === 'division' ? 'বিভাগীয় সংবাদ' : 'জেলা সংবাদ' }}</span>
        </div>
        <span style="font-size: 14px; color: #777;">মোট সংবাদ: {{ bn_num($newsList->total()) }} টি</span>
    </div>

    <!-- Sub-districts if division -->
    @if($location->children->isNotEmpty())
        <div style="background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <strong style="font-size: 14px; color: #555;">{{ $location->name }} বিভাগের জেলাসমূহ:</strong>
            @foreach($location->children as $dist)
                <a href="{{ route('location.show', $dist->slug) }}" style="background: #fff; border: 1px solid #ddd; padding: 4px 12px; border-radius: 14px; font-size: 13px; color: #333;">
                    {{ $dist->name }}
                </a>
            @endforeach
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        <div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                @forelse($newsList as $story)
                    <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
                @empty
                    <p style="color: #888; grid-column: 1 / -1;">এই এলাকার কোনো সংবাদ এখনো পাওয়া যায়নি।</p>
                @endforelse
            </div>

            <div style="margin-top: 30px;">
                {{ $newsList->links() }}
            </div>
        </div>

        <aside>
            <div style="background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #222; padding-bottom: 8px;">
                    অন্যান্য বিভাগ
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($divisions as $div)
                        <li>
                            <a href="{{ route('location.show', $div->slug) }}" style="display: flex; justify-content: space-between; color: #333; padding: 4px 0; border-bottom: 1px solid #f0f0f0;">
                                <span>{{ $div->name }}</span>
                                <span style="color: #999;">&rarr;</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>

</div>
@endsection
