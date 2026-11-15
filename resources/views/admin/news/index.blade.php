@extends('layouts.admin')

@section('title', 'সংবাদ ব্যবস্থাপনা')

@section('content')
<div>
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">সংবাদ তালিকা</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <span>+ নতুন সংবাদ তৈরি করুন</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px;">
            <form action="{{ route('admin.news.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="শিরোনাম দিয়ে খুঁজুন...">
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="status" class="form-control">
                        <option value="">-- সকল অবস্থা --</option>
                        <option value="published" {{ $status === 'published' ? 'selected' : '' }}>প্রকাশিত</option>
                        <option value="review_pending" {{ $status === 'review_pending' ? 'selected' : '' }}>পর্যালোচনায়</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>খসড়া</option>
                        <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>নির্ধারিত</option>
                        <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>আর্কাইভ</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="category_id" class="form-control">
                        <option value="">-- সকল ক্যাটাগরি --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary">ফিল্টার</button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary" style="background: transparent; border: 1px solid #d1d5db;">রিসেট</a>
            </form>
        </div>
    </div>

    <!-- News Table -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ছবি</th>
                            <th>শিরোনাম</th>
                            <th>ক্যাটাগরি</th>
                            <th>লেখক</th>
                            <th>অবস্থা</th>
                            <th>তারিখ</th>
                            <th style="text-align: right; width: 140px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody id="newsTableBody">
                        @include('admin.news._rows', ['newsList' => $newsList])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Load More Container -->
    <div id="loadMoreContainer" style="margin-top: 24px; text-align: center; padding-bottom: 30px;">
        @if($newsList->hasMorePages())
            @php
                $remainingCount = max(0, $newsList->total() - $newsList->count());
            @endphp
            <button id="btnLoadMore" type="button" class="btn btn-secondary" style="background: #ffffff; border: 1.5px solid #cbd5e1; color: #1e293b; padding: 11px 32px; font-weight: 700; font-size: 14px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <svg id="loadSpinner" style="display: none; animation: spin 0.8s linear infinite;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                <span id="loadBtnText">আরও লোড করুন ({{ bn_num($remainingCount) }}টি বাকি)</span>
            </button>
        @else
            <div id="allLoadedMsg" style="color: #64748b; font-size: 13px; font-weight: 500;">
                ✓ সব সংবাদ লোড করা হয়েছে (মোট {{ bn_num($newsList->total()) }}টি সংবাদ)
            </div>
        @endif
        <div id="allLoadedNotice" style="display: none; color: #64748b; font-size: 13px; font-weight: 500; margin-top: 10px;">
            ✓ সব সংবাদ লোড করা হয়েছে (মোট {{ bn_num($newsList->total()) }}টি সংবাদ)
        </div>
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
        let nextPage = {{ $newsList->currentPage() + 1 }};
        let hasMore = {{ $newsList->hasMorePages() ? 'true' : 'false' }};
        let isLoading = false;
        const btn = document.getElementById('btnLoadMore');
        const spinner = document.getElementById('loadSpinner');
        const btnText = document.getElementById('loadBtnText');
        const notice = document.getElementById('allLoadedNotice');
        const tableBody = document.getElementById('newsTableBody');

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
                    tableBody.insertAdjacentHTML('beforeend', data.html);
                }

                hasMore = data.has_more;
                nextPage = data.next_page;

                if (hasMore) {
                    const loadedCount = document.querySelectorAll('#newsTableBody tr.news-row').length;
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

