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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.posters.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">সংবাদ নির্বাচন করুন *</label>
                    <select name="news_id" id="newsSelect" required class="form-control" style="font-size: 14px;">
                        <option value="">সংবাদ নির্বাচন করুন...</option>
                        @foreach($publishedNews as $pNews)
                            <option value="{{ $pNews->id }}" 
                                    data-title="{{ $pNews->title }}"
                                    {{ ($selectedNews && $selectedNews->id == $pNews->id) ? 'selected' : '' }}>
                                [{{ $pNews->primaryCategory?->name ?? 'জাতীয়' }}] {{ Str::limit($pNews->title, 70) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">পোস্টারের হেডলাইন (ঐচ্ছিক কাস্টম শিরোনাম)</label>
                    <input type="text" name="headline_text" id="headlineInput" value="{{ $selectedNews ? $selectedNews->title : old('headline_text') }}" class="form-control" placeholder="খালি রাখলে মূল সংবাদের শিরোনাম ব্যবহৃত হবে...">
                </div>

                <div class="form-group">
                    <label class="form-label">সাইজ ও অনুপাত নির্বাচন করুন *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 6px;">
                        <label style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; background: #fafafa;">
                            <input type="radio" name="dimensions" value="1080x1080" checked style="width: 18px; height: 18px;">
                            <div>
                                <strong style="display: block; font-size: 14px;">স্কয়ার (1080 &times; 1080)</strong>
                                <span style="font-size: 11px; color: #64748b;">Facebook, Instagram Post ও WhatsApp-এর জন্য উপযুক্ত</span>
                            </div>
                        </label>

                        <label style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; background: #fafafa;">
                            <input type="radio" name="dimensions" value="1200x630" style="width: 18px; height: 18px;">
                            <div>
                                <strong style="display: block; font-size: 14px;">ল্যান্ডস্কেপ (1200 &times; 630)</strong>
                                <span style="font-size: 11px; color: #64748b;">Facebook Link Share & OpenGraph কার্ডের জন্য আদর্শ</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 24px; text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
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

    if (newsSelect && headlineInput) {
        newsSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const title = opt ? opt.getAttribute('data-title') : '';
            if (title && !headlineInput.value) {
                headlineInput.value = title;
            }
        });
    }
});
</script>
@endsection
