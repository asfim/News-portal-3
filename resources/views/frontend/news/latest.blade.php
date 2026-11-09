@extends('layouts.frontend')

@section('title', 'সর্বশেষ সংবাদ | প্রথম আলো')

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 28px; font-weight: 700; margin: 0;">সর্বশেষ সংবাদ</h1>
        <span style="font-size: 14px; color: #777;">মোট সংবাদ: {{ bn_num($newsList->total()) }} টি</span>
    </div>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        <div>
            @forelse($newsList as $news)
                <x-news-card :news="$news" variant="horizontal" :showExcerpt="true" />
            @empty
                <p style="color: #888;">কোনো সংবাদ পাওয়া যায়নি।</p>
            @endforelse

            <div style="margin-top: 30px;">
                {{ $newsList->links() }}
            </div>
        </div>

        <aside>
            <div style="background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #222; padding-bottom: 8px;">
                    বিভাগসমূহ
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($navCategories as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat->slug) }}" style="display: flex; justify-content: space-between; color: #333; padding: 4px 0; border-bottom: 1px solid #f0f0f0;">
                                <span>{{ $cat->name }}</span>
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
