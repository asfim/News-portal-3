@extends('layouts.admin')

@section('title', 'সংগৃহীত সংবাদ তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সংগৃহীত সংবাদসমূহ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">অনুমোদিত ফিড ও এপিআই থেকে সংগৃহীত সংবাদ ও সম্পাদকীয় অনুমোদন</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.duplicates.index') }}" class="btn btn-secondary btn-sm">ডুপ্লিকেট সংবাদ তালিকা →</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন কন্ট্রোল সেন্টার</a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 14px 20px;">
        <form method="GET" action="{{ route('admin.automation.imports.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <select name="source_id" class="form-control" onchange="this.form.submit()">
                    <option value="">সকল নিউজ সোর্স</option>
                    @foreach($sources as $s)
                        <option value="{{ $s->id }}" {{ $sourceId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 180px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">খসড়া ও প্রকাশিত</option>
                    <option value="draft_created" {{ $status === 'draft_created' ? 'selected' : '' }}>খসড়া (Draft)</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>প্রকাশিত</option>
                </select>
            </div>

            @if($sourceId || $status)
                <a href="{{ route('admin.automation.imports.index') }}" class="btn btn-secondary btn-sm">ফিল্টার মুছুন</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ছবি</th>
                        <th>শিরোনাম ও সোর্স</th>
                        <th>ক্যাটাগরি</th>
                        <th>অবস্থা</th>
                        <th>প্রকাশের তারিখ</th>
                        <th>সংগ্রহের তারিখ</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="importsTableBody">
                    @include('admin.automation.imports._rows', ['imports' => $imports])
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Load More Container -->
<div id="loadMoreContainer" style="margin-top: 24px; text-align: center; padding-bottom: 30px;">
    @if($imports->hasMorePages())
        @php
            $remainingCount = max(0, $imports->total() - $imports->count());
        @endphp
        <button id="btnLoadMore" type="button" class="btn btn-secondary" style="background: #ffffff; border: 1.5px solid #cbd5e1; color: #1e293b; padding: 11px 32px; font-weight: 700; font-size: 14px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <svg id="loadSpinner" style="display: none; animation: spin 0.8s linear infinite;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
            <span id="loadBtnText">আরও লোড করুন ({{ bn_num($remainingCount) }}টি বাকি)</span>
        </button>
    @else
        <div id="allLoadedMsg" style="color: #64748b; font-size: 13px; font-weight: 500;">
            ✓ সব সংগৃহীত সংবাদ লোড করা হয়েছে (মোট {{ bn_num($imports->total()) }}টি সংবাদ)
        </div>
    @endif
    <div id="allLoadedNotice" style="display: none; color: #64748b; font-size: 13px; font-weight: 500; margin-top: 10px;">
        ✓ সব সংগৃহীত সংবাদ লোড করা হয়েছে (মোট {{ bn_num($imports->total()) }}টি সংবাদ)
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
        let nextPage = {{ $imports->currentPage() + 1 }};
        let hasMore = {{ $imports->hasMorePages() ? 'true' : 'false' }};
        let isLoading = false;
        const btn = document.getElementById('btnLoadMore');
        const spinner = document.getElementById('loadSpinner');
        const btnText = document.getElementById('loadBtnText');
        const notice = document.getElementById('allLoadedNotice');
        const tableBody = document.getElementById('importsTableBody');

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
                    const loadedCount = document.querySelectorAll('#importsTableBody tr.import-row').length;
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
