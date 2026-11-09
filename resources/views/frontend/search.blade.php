@extends('layouts.frontend')

@section('title', 'খবর অনুসন্ধান | প্রথম আলো')

@section('content')
<style>
    .search-filter-grid {
        display: grid;
        grid-template-columns: 3fr 1.5fr 1.2fr 1.2fr auto;
        gap: 12px;
        align-items: flex-end;
    }
    @media (max-width: 900px) {
        .search-filter-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .search-filter-grid > div:last-child {
            grid-column: 1 / -1;
        }
        .search-filter-grid button {
            width: 100%;
        }
    }
    @media (max-width: 550px) {
        .search-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 24px 16px;">
    
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 24px;">
        <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 16px 0;">অনুসন্ধান</h1>

        <!-- Filter Form -->
        <form action="{{ route('search') }}" method="GET" style="background: #fafafa; border: 1px solid #e5e5e5; border-radius: 6px; padding: 20px;">
            <div class="search-filter-grid">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #555; margin-bottom: 4px;">শব্দ বা বাক্য:</label>
                    <input type="text" name="q" value="{{ $query }}" placeholder="অনুসন্ধান করতে লিখুন..." style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #555; margin-bottom: 4px;">বিভাগ:</label>
                    <select name="category" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 14px; background: #fff; box-sizing: border-box;">
                        <option value="">-- সব বিভাগ --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #555; margin-bottom: 4px;">তারিখ থেকে:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" style="width: 100%; padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 13px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #555; margin-bottom: 4px;">তারিখ পর্যন্ত:</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" style="width: 100%; padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 13px; box-sizing: border-box;">
                </div>
                <div>
                    <button type="submit" style="background: #d60000; color: #fff; border: none; padding: 9px 20px; border-radius: 4px; font-weight: 700; font-family: inherit; font-size: 15px; cursor: pointer;">
                        অনুসন্ধান
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="palo-main-grid-layout">
        <div>
            @if(!empty($query) || !empty($categoryId) || !empty($dateFrom))
                <div style="margin-bottom: 20px; font-size: 15px; color: #555;">
                    @if(method_exists($newsList, 'total'))
                        অনুসন্ধানের ফলাফল: <strong>{{ bn_num($newsList->total()) }}</strong> টি সংবাদ পাওয়া গেছে
                    @endif
                </div>

                <div class="palo-cards-grid-2">
                    @forelse($newsList as $story)
                        <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
                    @empty
                        <p style="color: #888; grid-column: 1 / -1; padding: 20px 0;">কোনো সংবাদ পাওয়া যায়নি। অনুগ্রহ করে ভিন্ন শব্দ দিয়ে অনুসন্ধান করুন।</p>
                    @endforelse
                </div>

                @if(method_exists($newsList, 'links'))
                    <div style="margin-top: 30px;">
                        {{ $newsList->links() }}
                    </div>
                @endif
            @else
                <p style="color: #666; font-size: 16px;">অনুসন্ধান করতে ওপরের বক্সে আপনার প্রয়োজনীয় বিষয় লিখুন।</p>
            @endif
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
