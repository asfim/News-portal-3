@forelse($imports as $imp)
    <tr class="import-row" data-id="{{ $imp->id }}">
        <td>
            @php
                $imgUrl = $imp->news?->cover_image_url ?: $imp->image_url;
            @endphp
            @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="thumb" style="width: 60px; height: 42px; object-fit: cover; border-radius: 4px; display: block; border: 1px solid #e2e8f0;" loading="lazy">
            @else
                <div style="width: 60px; height: 42px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; border: 1px dashed #cbd5e1;">নো ইমেজ</div>
            @endif
        </td>
        <td>
            <div style="font-weight: 700; font-size: 14px; color: #1e293b; line-height: 1.4;">
                {{ $imp->title }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px; display: flex; gap: 8px;">
                <span style="font-weight: 600; color: #c62828;">{{ $imp->source?->name ?? 'সোর্স' }}</span>
                <span>•</span>
                <a href="{{ $imp->source_url }}" target="_blank" rel="noopener" style="color: #64748b; text-decoration: none;">মূল লিংক ↗</a>
            </div>
        </td>
        <td>
            {{ $imp->news?->primaryCategory?->name ?? '-' }}
        </td>
        <td>
            @if($imp->news?->status === 'review_pending' || $imp->status === 'review_pending')
                <span class="status-badge" style="background: #fef3c7; color: #92400e; font-weight: 600;">পর্যালোচনায় অপেক্ষমাণ</span>
            @elseif($imp->status === 'draft_created' || $imp->news?->status === 'draft')
                <span class="status-badge status-draft">খসড়া (ড্রাফট)</span>
            @elseif($imp->status === 'published' || $imp->news?->status === 'published')
                <span class="status-badge status-published">প্রকাশিত</span>
            @else
                <span class="status-badge status-archived">{{ $imp->status }}</span>
            @endif
        </td>
        <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
            {{ $imp->published_at ? bn_date($imp->published_at) : '-' }}
        </td>
        <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
            {{ bn_date($imp->created_at) }}
        </td>
        <td style="text-align: right; white-space: nowrap;">
            @if($imp->news)
                <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                    @if($imp->news->status === 'review_pending' || $imp->news->status === 'draft')
                        <form action="{{ route('admin.news.approve', $imp->news->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" title="অনুমোদন করুন: সংবাদটি প্রকাশিত হবে, সোশ্যাল পোস্টার তৈরি হবে এবং ফেসবুক পেজে পোস্ট হবে" style="background: #16a34a; border-color: #16a34a; color: white; font-weight: 600;">
                                ✓ অনুমোদন
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.news.edit', $imp->news->id) }}" class="btn btn-primary btn-sm">সম্পাদনা</a>
                    @if($imp->news->status === 'published')
                        <a href="{{ route('news.show', [$imp->news->public_id, $imp->news->slug]) }}" target="_blank" class="btn btn-secondary btn-sm">লাইভ</a>
                    @endif
                </div>
            @endif
        </td>
    </tr>
@empty
    @if(!isset($isAppend) || !$isAppend)
        <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                কোনো সংগৃহীত সংবাদ পাওয়া যায়নি।
            </td>
        </tr>
    @endif
@endforelse

