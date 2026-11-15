@forelse($newsList as $news)
    <tr class="news-row" data-id="{{ $news->id }}">
        <td>
            @if($news->cover_image_url)
                <img src="{{ $news->cover_image_url }}" alt="thumb" style="width: 48px; height: 34px; object-fit: cover; border-radius: 4px; display: block;" loading="lazy">
            @else
                <div style="width: 48px; height: 34px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">নো ছবি</div>
            @endif
        </td>
        <td>
            @if($news->kicker)
                <span style="font-size: 11px; color: var(--primary); font-weight: 700;">{{ $news->kicker }}</span><br>
            @endif
            <a href="{{ $news->url }}" target="_blank" style="color: #111; text-decoration: none; font-weight: 700;" onmouseover="this.style.color='#d60000'" onmouseout="this.style.color='#111'">
                {{ $news->title }}
            </a>
        </td>
        <td>{{ $news->primaryCategory ? $news->primaryCategory->name : '-' }}</td>
        <td>{{ $news->authors->first()?->name ?? 'ডেস্ক' }}</td>
        <td>
            <span class="status-badge status-{{ $news->status }}">
                @if($news->status === 'published') প্রকাশিত
                @elseif($news->status === 'draft') খসড়া
                @elseif($news->status === 'review_pending') পর্যালোচনায়
                @elseif($news->status === 'scheduled') নির্ধারিত
                @else আর্কাইভ @endif
            </span>
        </td>
        <td>
            <span style="font-size: 13px;">{{ bn_date($news->published_at ?? $news->created_at) }}</span>
        </td>
        <td style="text-align: right;">
            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                @if($news->status === 'review_pending' || $news->status === 'draft')
                    <form action="{{ route('admin.news.approve', $news->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" title="অনুমোদন করুন: সংবাদটি প্রকাশিত হবে, স্বয়ংক্রিয় কার্ড তৈরি হয়ে ফেসবুকে পোস্ট হবে" style="background: #16a34a; border-color: #16a34a; color: white; font-weight: 600;">
                            ✓ অনুমোদন
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                <form action="{{ route('admin.news.destroy', $news->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই সংবাদটি মুছে ফেলতে চান?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                </form>
            </div>
        </td>
    </tr>
@empty
    @if(!isset($isAppend) || !$isAppend)
        <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">কোনো সংবাদ পাওয়া যায়নি।</td>
        </tr>
    @endif
@endforelse
