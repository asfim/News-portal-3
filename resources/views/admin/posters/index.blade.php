@extends('layouts.admin')

@section('title', 'তৈরিকৃত সোশ্যাল পোস্টারসমূহ')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সোশ্যাল মিডিয়া পোস্টার ও ব্যানার</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ফেসবুক ও ইনস্টাগ্রামের জন্য প্রস্তুতকৃত ডায়নামিক বাংলা নিউজ পোস্টার</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.posters.create') }}" class="btn btn-primary btn-sm">＋ নতুন পোস্টার তৈরি করুন</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন সেন্টার</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div id="postersGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
    @include('admin.posters._cards', ['posters' => $posters])
</div>

<!-- Load More Container -->
<div id="loadMoreContainer" style="margin-top: 28px; text-align: center; padding-bottom: 30px;">
    @if($posters->hasMorePages())
        @php
            $remainingCount = max(0, $posters->total() - $posters->count());
        @endphp
        <button id="btnLoadMore" type="button" class="btn btn-secondary" style="background: #ffffff; border: 1.5px solid #cbd5e1; color: #1e293b; padding: 11px 32px; font-weight: 700; font-size: 14px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <svg id="loadSpinner" style="display: none; animation: spin 0.8s linear infinite;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
            <span id="loadBtnText">আরও লোড করুন ({{ bn_num($remainingCount) }}টি বাকি)</span>
        </button>
    @else
        <div id="allLoadedMsg" style="color: #64748b; font-size: 13px; font-weight: 500;">
            ✓ সব পোস্টার লোড করা হয়েছে (মোট {{ bn_num($posters->total()) }}টি পোস্টার)
        </div>
    @endif
    <div id="allLoadedNotice" style="display: none; color: #64748b; font-size: 13px; font-weight: 500; margin-top: 10px;">
        ✓ সব পোস্টার লোড করা হয়েছে (মোট {{ bn_num($posters->total()) }}টি পোস্টার)
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    #btnLoadMore:hover { background: #f8fafc !important; border-color: #94a3b8 !important; transform: translateY(-1px); }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let nextPage = {{ $posters->currentPage() + 1 }};
        let hasMore = {{ $posters->hasMorePages() ? 'true' : 'false' }};
        let isLoading = false;
        const btn = document.getElementById('btnLoadMore');
        const spinner = document.getElementById('loadSpinner');
        const btnText = document.getElementById('loadBtnText');
        const notice = document.getElementById('allLoadedNotice');
        const grid = document.getElementById('postersGrid');

        if (!btn) return;

        btn.addEventListener('click', function () {
            if (isLoading || !hasMore) return;

            isLoading = true;
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'লোড হচ্ছে...';

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('page', nextPage);
            currentUrl.searchParams.set('load_more', '1');

            fetch(currentUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP error ' + res.status);
                return res.json();
            })
            .then(data => {
                if (data.html) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                }

                hasMore = data.has_more;
                nextPage = data.next_page;

                if (hasMore) {
                    const loadedCount = document.querySelectorAll('#postersGrid .poster-card').length;
                    const remaining = Math.max(0, data.total - loadedCount);
                    btnText.textContent = `আরও লোড করুন (${remaining}টি বাকি)`;
                    btn.disabled = false;
                } else {
                    btn.style.display = 'none';
                    notice.style.display = 'block';
                }
            })
            .catch(err => {
                console.error('Load more failed:', err);
                btnText.textContent = 'ত্রুটি হয়েছে, আবার চেষ্টা করুন';
                btn.disabled = false;
            })
            .finally(() => {
                isLoading = false;
                spinner.style.display = 'none';
            });
        });
    });
</script>
@endpush
