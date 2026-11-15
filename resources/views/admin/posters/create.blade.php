@extends('layouts.admin')

@section('title', 'নতুন সোশ্যাল পোস্টার তৈরি')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সোশ্যাল পোস্টার জেনারেটর</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">সংবাদ ও কভার ছবি দিয়ে স্বয়ংক্রিয় প্রফেশনাল নিউজ ব্যানার তৈরি করুন</p>
        </div>
        <a href="{{ route('admin.posters.index') }}" class="btn btn-secondary">&larr; পোস্টার তালিকায় ফিরে যান</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.posters.store') }}" method="POST" id="posterForm">
                @csrf

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-weight: 600;">সংবাদ নির্বাচন করুন *</label>
                    <select name="news_id" id="newsSelect" required class="form-control" style="font-size: 14px;">
                        <option value="">সংবাদ নির্বাচন করুন...</option>
                        @foreach($publishedNews as $pNews)
                            <option value="{{ $pNews->id }}" 
                                    data-title="{{ $pNews->title }}"
                                    {{ ($selectedNews && $selectedNews->id == $pNews->id) ? 'selected' : '' }}>
                                [{{ $pNews->primaryCategory?->name ?? 'জাতীয়' }}] {{ Str::limit($pNews->title, 80) }} ({{ $pNews->created_at ? $pNews->created_at->format('d M') : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="font-weight: 600; margin: 0;">পোস্টারের হেডলাইন (কাস্টম বা মূল শিরোনাম)</label>
                        <button type="button" id="copyTitleBtn" style="background: none; border: none; color: #2563eb; font-size: 12px; cursor: pointer; text-decoration: underline;">
                            মূল শিরোনাম বসান
                        </button>
                    </div>
                    <textarea name="headline_text" id="headlineInput" rows="3" class="form-control" placeholder="খালি রাখলে মূল সংবাদের শিরোনাম ব্যবহৃত হবে..." maxlength="500" style="font-size: 14px; line-height: 1.5;">{{ $selectedNews ? $selectedNews->title : old('headline_text') }}</textarea>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                        <span>পোস্টারে সর্বোচ্চ ৩ লাইনে সুন্দরভাবে বসবে</span>
                        <span id="charCount">০ / ৫০০ অক্ষর</span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="font-weight: 600;">সাইজ ও অনুপাত নির্বাচন করুন *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 6px;">
                        <label style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; background: #fafafa;" id="dimOptionSquare">
                            <input type="radio" name="dimensions" value="1080x1080" checked style="width: 18px; height: 18px;">
                            <div>
                                <strong style="display: block; font-size: 14px;">স্কয়ার (1080 &times; 1080)</strong>
                                <span style="font-size: 11px; color: #64748b;">Facebook, Instagram Post ও WhatsApp-এর জন্য উপযুক্ত</span>
                            </div>
                        </label>

                        <label style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; background: #fafafa;" id="dimOptionLandscape">
                            <input type="radio" name="dimensions" value="1200x630" style="width: 18px; height: 18px;">
                            <div>
                                <strong style="display: block; font-size: 14px;">ল্যান্ডস্কেপ (1200 &times; 630)</strong>
                                <span style="font-size: 11px; color: #64748b;">Facebook Link Share & OpenGraph কার্ডের জন্য আদর্শ</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 24px; text-align: right;">
                    <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        🎨 পোস্টার তৈরি করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const newsSelect = document.getElementById('newsSelect');
    const headlineInput = document.getElementById('headlineInput');
    const copyTitleBtn = document.getElementById('copyTitleBtn');
    const charCount = document.getElementById('charCount');
    const posterForm = document.getElementById('posterForm');
    const submitBtn = document.getElementById('submitBtn');

    function updateCharCount() {
        if (!charCount || !headlineInput) return;
        const len = headlineInput.value.length;
        charCount.textContent = len + ' / ৫০০ অক্ষর';
    }

    function applySelectedTitle() {
        if (!newsSelect || !headlineInput) return;
        const opt = newsSelect.options[newsSelect.selectedIndex];
        const title = opt ? opt.getAttribute('data-title') : '';
        if (title) {
            headlineInput.value = title;
            updateCharCount();
        }
    }

    if (newsSelect) {
        newsSelect.addEventListener('change', applySelectedTitle);
    }

    if (copyTitleBtn) {
        copyTitleBtn.addEventListener('click', applySelectedTitle);
    }

    if (headlineInput) {
        headlineInput.addEventListener('input', updateCharCount);
        updateCharCount();
    }

    if (posterForm && submitBtn) {
        posterForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ পোস্টার তৈরি হচ্ছে...';
        });
    }
});
</script>
@endsection
