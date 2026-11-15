@forelse($posters as $poster)
    <div class="poster-modern-card poster-card" data-id="{{ $poster->id }}">
        <!-- Thumbnail Container -->
        <div class="poster-thumb-wrap" style="aspect-ratio: {{ $poster->dimensions === '1200x630' ? '1200/630' : '1/1' }};">
            <img src="{{ $poster->url }}" alt="{{ $poster->headline_text }}" class="poster-thumb-img" loading="lazy">
            
            <!-- Dimension Tag -->
            <span class="poster-dim-pill">
                {{ $poster->dimensions === '1200x630' ? '১২০০ × ৬৩০' : '১০৮০ × ১০৮০' }}
            </span>

            @if($poster->news && $poster->news->primaryCategory)
                <span class="poster-cat-pill">
                    {{ $poster->news->primaryCategory->name }}
                </span>
            @endif

            <!-- Hover Preview Overlay Button -->
            <a href="{{ $poster->url }}" target="_blank" rel="noopener noreferrer" class="poster-quick-view-btn" title="পুরো পোস্টার দেখুন">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
        </div>

        <!-- Card Body -->
        <div class="poster-card-body">
            <div>
                <h4 class="poster-card-title" title="{{ $poster->headline_text }}">
                    {{ Str::limit($poster->headline_text, 70) }}
                </h4>
                
                <div class="poster-card-meta">
                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ bn_date($poster->created_at) }}
                    </span>
                    <span class="poster-size-badge">
                        {{ round($poster->file_size_bytes / 1024) }} KB
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="poster-card-actions">
                <a href="{{ $poster->url }}" download class="btn-poster-action btn-poster-download" title="পোস্টার ডাউনলোড করুন">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>ডাউনলোড</span>
                </a>

                <button type="button" class="btn-poster-action btn-poster-copy" data-url="{{ $poster->url }}" title="ইমেজ লিংক কপি করুন" onclick="copyPosterLink(this)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span>কপি লিংক</span>
                </button>

                <form action="{{ route('admin.posters.destroy', $poster->id) }}" method="POST" onsubmit="return confirm('পোস্টারটি মুছে ফেলতে চান?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-poster-action btn-poster-delete" title="মুছে ফেলুন">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
@empty
    @if(!isset($isAppend) || !$isAppend)
        <div class="card" style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; border-radius: 12px; border: 1.5px dashed #cbd5e1; background: #fafafa;">
            <div style="font-size: 38px; margin-bottom: 12px;">🎨</div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">এখনো কোনো সোশ্যাল পোস্টার তৈরি করা হয়নি</h3>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 18px; max-width: 480px; margin-left: auto; margin-right: auto;">
                যেকোনো প্রকাশিত সংবাদ ও ছবি দিয়ে ফেসবুক বা ইনস্টাগ্রামের জন্য স্বয়ংক্রিয় প্রফেশনাল নিউজ কার্ড ব্যানার তৈরি করুন।
            </p>
            <a href="{{ route('admin.posters.create') }}" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">
                ＋ প্রথম পোস্টার তৈরি করুন
            </a>
        </div>
    @endif
@endforelse
