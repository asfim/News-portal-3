@extends('layouts.frontend')

@section('title', ($category->seo_title ?? $category->name) . ' | প্রথম আলো')
@section('meta_description', $category->seo_description ?? $category->description)

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">

    <!-- Category Header -->
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin: 0; color: #111;">{{ $category->name }}</h1>
            @if($category->description)
                <p style="font-size: 14px; color: #666; margin: 6px 0 0 0;">{{ $category->description }}</p>
            @endif
        </div>

        @if($category->children->isNotEmpty())
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                @foreach($category->children as $child)
                    <a href="{{ route('category.show', $child->slug) }}" style="background: #f2f2f2; color: #333; padding: 4px 12px; border-radius: 16px; font-size: 13px;">
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 40px;">
        <div>
            @if($leadNews)
                <div style="margin-bottom: 30px;">
                    <x-news-card :news="$leadNews" variant="lead" :showExcerpt="true" />
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                @foreach($otherNews as $story)
                    <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
                @endforeach
            </div>

            @if($newsList->isEmpty())
                <p style="color: #888;">এই বিভাগে কোনো সংবাদ এখনো প্রকাশিত হয়নি।</p>
            @endif

            <div style="margin-top: 30px;">
                {{ $newsList->links() }}
            </div>
        </div>

        <aside>
            <div style="background: #fafafa; border: 1px solid #ebebeb; border-radius: 6px; padding: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; border-bottom: 2px solid #d60000; padding-bottom: 8px;">
                    সর্বশেষ
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
        </aside>
    </div>

</div>
@endsection
