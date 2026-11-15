@extends('layouts.admin')

@section('title', 'নিউজ সোর্স সম্পাদনা')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজ সোর্স সম্পাদনা: {{ $source->name }}</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">সোর্সের কনফিগারেশন পরিবর্তন করুন</p>
        </div>
        <a href="{{ route('admin.automation.sources.index') }}" class="btn btn-secondary">&larr; সোর্স তালিকায় ফিরে যান</a>
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
            <form action="{{ route('admin.automation.sources.update', $source->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">সোর্সের নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $source->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ফিডের সম্পূর্ণ URL *</label>
                    <input type="url" name="feed_url" value="{{ old('feed_url', $source->feed_url) }}" required class="form-control">
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">SSRF প্রটেকশন প্রযোজ্য: লোকালহোস্ট বা অভ্যন্তরীণ নেটওয়ার্কের ঠিকানা নিষিদ্ধ।</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">ফিড টাইপ *</label>
                        <select name="type" class="form-control" required>
                            <option value="rss" {{ old('type', $source->type) === 'rss' ? 'selected' : '' }}>RSS 2.0</option>
                            <option value="atom" {{ old('type', $source->type) === 'atom' ? 'selected' : '' }}>Atom 1.0</option>
                            <option value="json_api" {{ old('type', $source->type) === 'json_api' ? 'selected' : '' }}>JSON Feed / API</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ভাষা *</label>
                        <select name="language" class="form-control" required>
                            <option value="bn" {{ old('language', $source->language) === 'bn' ? 'selected' : '' }}>বাংলা (Bengali)</option>
                            <option value="en" {{ old('language', $source->language) === 'en' ? 'selected' : '' }}>ইংরেজি (English)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">ডিফল্ট ক্যাটাগরি</label>
                        <select name="default_category_id" class="form-control">
                            <option value="">স্বয়ংক্রিয় ট্যাগ ম্যাচিং</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('default_category_id', $source->default_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">সংগ্রহ ব্যবধান (মিনিট) *</label>
                        <input type="number" name="fetch_interval_minutes" value="{{ old('fetch_interval_minutes', $source->fetch_interval_minutes) }}" min="5" max="1440" required class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">লেখক / সোর্স অ্যাট্রিবিউশন নাম</label>
                    <input type="text" name="author_attribution" value="{{ old('author_attribution', $source->author_attribution) }}" class="form-control">
                </div>

                {{-- AI Paraphrasing & Editorial Review Configuration --}}
                @php
                    $sourceSettings = $source->settings ?? [];
                    $isAiEnabled = old('ai_paraphrase', $sourceSettings['ai_paraphrase'] ?? true);
                    $editorialStatus = old('editorial_status', $sourceSettings['editorial_status'] ?? 'review_pending');
                @endphp
                <div style="margin: 20px 0; padding: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                        <input type="checkbox" name="ai_paraphrase" id="ai_paraphrase" value="1" {{ $isAiEnabled ? 'checked' : '' }} style="width: 20px; height: 20px; margin-top: 2px;">
                        <div>
                            <label for="ai_paraphrase" style="font-weight: 700; font-size: 15px; color: #166534; cursor: pointer; margin: 0;">
                                🤖 স্বয়ংক্রিয় এআই প্যারাফ্রেজিং / রিরাইট (Auto AI Paraphrasing)
                            </label>
                            <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                                গুগল নিউজ বা আরএসএস থেকে সংবাদ সংগ্রহের সময় এআই স্বয়ংক্রিয়ভাবে প্রমিত বাংলায় শিরোনাম, সারসংক্ষেপ ও সম্পূর্ণ সংবাদ পুনর্লিখন (Paraphrase) করবে।
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600; color: #166534;">সম্পাদকীয় অবস্থা (Editorial Status)</label>
                        <select name="editorial_status" class="form-control" style="background: #ffffff;">
                            <option value="review_pending" {{ $editorialStatus === 'review_pending' ? 'selected' : '' }}>
                                ✓ সম্পাদকীয় পর্যালোচনায় অপেক্ষমাণ (Review Pending) — [অনুমোদনের পর স্বয়ংক্রিয় কার্ড তৈরি হয়ে ফেসবুকে যাবে]
                            </option>
                            <option value="draft" {{ $editorialStatus === 'draft' ? 'selected' : '' }}>
                                সাধারণ খসড়া (Draft)
                            </option>
                            <option value="published" {{ $editorialStatus === 'published' ? 'selected' : '' }}>
                                সরাসরি প্রকাশনা (Direct Publish on Import)
                            </option>
                        </select>
                        <small style="color: #15803d; font-size: 11px; margin-top: 4px; display: block;">
                            সংবাদটি সংগৃহীত হওয়ার পর "সম্পাদকীয় পর্যালোচনায়" থাকবে। অ্যাডমিন বা সম্পাদক অনুমোদন দিলে স্বয়ংক্রিয়ভাবে সংবাদটির ছবি ও শিরোনাম যুক্ত সামাজিক মাধ্যম কার্ড তৈরি হবে এবং ফেসবুক পেজে পোস্ট হবে।
                        </small>
                    </div>
                </div>

                <div style="margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $source->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">এই সোর্সটি সক্রিয় রাখুন</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="auto_publish" value="1" {{ old('auto_publish', $source->auto_publish) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <div>
                            <span style="font-weight: 600; font-size: 14px; color: #b91c1c;">সরাসরি প্রকাশ করুন (Auto Publish)</span>
                            <div style="font-size: 11px; color: #64748b;">সতর্কতা: আনচেক থাকলে সংগৃহীত সংবাদ সম্পাদকের পর্যালোচনার জন্য "খসড়া" (Draft) হিসেবে জমা থাকবে।</div>
                        </div>
                    </label>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        আপডেট সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
