@extends('layouts.frontend')

@section('title', 'আমার সংরক্ষিত সংবাদ (বুকমার্কস) - এইচ টেক প্রোভিশন আইটি')

@section('content')
<div class="content-wrapper" style="max-width: 1200px; margin: 30px auto; padding: 0 15px;">
    <div style="border-bottom: 2px solid #d60000; padding-bottom: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 24px; font-weight: 800; color: #1a202c; margin: 0;">
            <svg style="width: 22px; height: 22px; display: inline-block; vertical-align: middle; margin-right: 6px; fill: #d60000;" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
            আমার সংরক্ষিত সংবাদ (বুকমার্কস)
        </h1>
        <span style="font-size: 14px; color: #64748b; font-weight: 700;">মোট সংরক্ষিত: {{ bn_num($bookmarks->total()) }}টি</span>
    </div>

    @if(session('bookmark_status'))
        <div style="background: #effaf3; border: 1px solid #c3e6cb; color: #155724; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
            {{ session('bookmark_status') }}
        </div>
    @endif

    @if($bookmarks->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <p style="font-size: 16px; color: #64748b; margin-bottom: 15px;">আপনার কোনো সংবাদ সংরক্ষিত নেই।</p>
            <a href="{{ route('home') }}" style="display: inline-block; padding: 10px 20px; background: #d60000; color: #fff; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 14px;">প্রধান পাতায় যান</a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
            @foreach($bookmarks as $b)
                @if($b->news)
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                        @if($b->news->coverMedia)
                            <a href="{{ route('news.show', ['public_id' => $b->news->public_id, 'slug' => $b->news->slug]) }}">
                                <img src="{{ asset($b->news->coverMedia->path) }}" alt="{{ $b->news->title }}" style="width: 100%; height: 160px; object-fit: cover;">
                            </a>
                        @endif
                        <div style="padding: 15px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <span style="font-size: 12px; color: #d60000; font-weight: 700;">{{ $b->news->primaryCategory->name ?? '' }}</span>
                                <h2 style="font-size: 16px; font-weight: 700; line-height: 1.4; margin: 8px 0;">
                                    <a href="{{ route('news.show', ['public_id' => $b->news->public_id, 'slug' => $b->news->slug]) }}" style="color: #1a202c; text-decoration: none;">
                                        {{ $b->news->title }}
                                    </a>
                                </h2>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; pt-2; border-top: 1px solid #f1f5f9;">
                                <span style="font-size: 12px; color: #64748b;">{{ bn_time_ago($b->news->published_at) }}</span>
                                <form action="{{ route('news.bookmark.toggle', $b->news->public_id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                        মুছে ফেলুন
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div style="margin-top: 35px;">
            {{ $bookmarks->links() }}
        </div>
    @endif
</div>
@endsection

