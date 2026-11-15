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

    .poster-modern-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s;
    }
    .poster-modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px -4px rgba(15, 23, 42, 0.12), 0 6px 12px -4px rgba(15, 23, 42, 0.06);
        border-color: #cbd5e1;
    }
    .poster-thumb-wrap {
        position: relative;
        background: #070b14;
        overflow: hidden;
    }
    .poster-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        transition: transform 0.4s ease;
    }
    .poster-modern-card:hover .poster-thumb-img {
        transform: scale(1.03);
    }
    .poster-dim-pill {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        color: #f8fafc;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        z-index: 2;
    }
    .poster-cat-pill {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(220, 38, 38, 0.9);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        color: #ffffff;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        z-index: 2;
    }
    .poster-quick-view-btn {
        position: absolute;
        inset: 0;
        background: rgba(7, 11, 20, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: 3;
        text-decoration: none;
    }
    .poster-quick-view-btn svg {
        background: rgba(0, 0, 0, 0.6);
        padding: 10px;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: transform 0.2s ease;
    }
    .poster-quick-view-btn:hover svg {
        transform: scale(1.1);
        background: rgba(220, 38, 38, 0.85);
    }
    .poster-modern-card:hover .poster-quick-view-btn {
        opacity: 1;
    }
    .poster-card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .poster-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.45;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .poster-card-meta {
        font-size: 12px;
        color: #64748b;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .poster-size-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .poster-card-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
    }
    .btn-poster-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 11px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .btn-poster-download {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        flex: 1;
        justify-content: center;
    }
    .btn-poster-download:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .btn-poster-copy {
        background: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }
    .btn-poster-copy:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-poster-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
        padding: 7px 9px;
    }
    .btn-poster-delete:hover {
        background: #dc2626;
        color: #ffffff;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyPosterLink(btn) {
        const url = btn.getAttribute('data-url');
        if (!url) return;
        navigator.clipboard.writeText(url).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span>✓ কপি হয়েছে!</span>';
            btn.style.background = '#dcfce7';
            btn.style.color = '#15803d';
            btn.style.borderColor = '#86efac';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.style.background = '';
                btn.style.color = '';
                btn.style.borderColor = '';
            }, 2000);
        }).catch(err => {
            console.error('Clipboard copy failed:', err);
        });
    }

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
