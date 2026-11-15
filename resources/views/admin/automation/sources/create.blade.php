@extends('layouts.admin')

@section('title', 'নতুন নিউজ সোর্স যুক্ত করুন')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নতুন নিউজ সোর্স যুক্ত করুন</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">অনুমোদিত আরএসএস বা গুগল নিউজ ফিডের তথ্য দিন</p>
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

    {{-- 100% Real Photo Verified News Feeds --}}
    <div class="card" style="margin-bottom: 20px; border-left: 4px solid #10b981; background: #f0fdf4;">
        <div class="card-body">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">📸</span>
                    <strong style="font-size: 15px; color: #065f46;">আসল ছবি সহ ভেরিফাইড বাংলা ফিড (100% Real HD Photo Feeds)</strong>
                </div>
                <span style="font-size: 11px; background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 4px; font-weight: 600;">আসল নিউজ ফটো সহ</span>
            </div>
            <p style="font-size: 12px; color: #047857; margin-bottom: 12px;">
                এই ফিডগুলোতে প্রতিটি খবরের সাথে ১২০০px হাই-রেজ্যুলেশন মূল সম্পাদকীয় ছবি সংযুক্ত থাকে। নিচের বাটনে ক্লিক করলেই এক ক্লিকে সেট হয়ে যাবে:
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <button type="button" class="btn btn-secondary btn-sm" style="border-color: #a7f3d0; background: #fff;" onclick="setNewsPreset('প্রথম আলো: শীর্ষ সংবাদ', 'https://www.prothomalo.com/feed/', 'প্রথম আলো ডেস্ক')">📰 প্রথম আলো: শীর্ষ সংবাদ</button>
                <button type="button" class="btn btn-secondary btn-sm" style="border-color: #a7f3d0; background: #fff;" onclick="setNewsPreset('বিবিসি বাংলা: বিশ্ব ও বাংলাদেশ', 'https://feeds.bbci.co.uk/bengali/rss.xml', 'বিবিসি বাংলা')">🇬🇧 বিবিসি বাংলা</button>
                <button type="button" class="btn btn-secondary btn-sm" style="border-color: #a7f3d0; background: #fff;" onclick="setNewsPreset('প্রথম আলো: আন্তর্জাতিক', 'https://www.prothomalo.com/feed/world', 'প্রথম আলো আন্তর্জাতিক ডেস্ক')">🌍 প্রথম আলো: আন্তর্জাতিক</button>
                <button type="button" class="btn btn-secondary btn-sm" style="border-color: #a7f3d0; background: #fff;" onclick="setNewsPreset('প্রথম আলো: খেলাধুলা', 'https://www.prothomalo.com/feed/sports', 'প্রথম আলো স্পোর্টস ডেস্ক')">🏏 প্রথম আলো: খেলাধুলা</button>
                <button type="button" class="btn btn-secondary btn-sm" style="border-color: #a7f3d0; background: #fff;" onclick="setNewsPreset('বিডিনিউজ২৪: বাংলাদেশ', 'https://bangla.bdnews24.com/?widgetName=rssfeed&widgetId=1150&getXmlFeed=true', 'বিডিনিউজ২৪')">🇧🇩 বিডিনিউজ২৪</button>
            </div>
        </div>
    </div>

    {{-- Google News 1-Click Quick Setup Presets --}}
    <div class="card" style="margin-bottom: 20px; border-left: 4px solid #1a73e8; background: #f8fafc;">
        <div class="card-body">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">⚡</span>
                    <strong style="font-size: 15px; color: #1e293b;">গুগল নিউজ আরএসএস সহকারী (Google News 1-Click Presets)</strong>
                </div>
                <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 600;">স্বয়ংক্রিয় সেটআপ</span>
            </div>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 12px;">
                নিচের যেকোনো বাটনে ক্লিক করলে গুগল নিউজের অফিশিয়াল বাংলা আরএসএস ফিড URL এবং উপযুক্ত সেটিংস স্বয়ংক্রিয়ভাবে পূরণ হয়ে যাবে:
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="setGoogleNewsPreset('গুগল নিউজ: বাংলাদেশ শীর্ষ সংবাদ', 'https://news.google.com/rss?hl=bn&gl=BD&ceid=BD:bn')">🇧🇩 বাংলাদেশ শীর্ষ খবর</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="setGoogleNewsPreset('গুগল নিউজ: খেলাধুলা', 'https://news.google.com/rss/headlines/section/topic/SPORTS?hl=bn&gl=BD&ceid=BD:bn')">🏏 খেলাধুলা</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="setGoogleNewsPreset('গুগল নিউজ: তথ্যপ্রযুক্তি', 'https://news.google.com/rss/headlines/section/topic/TECHNOLOGY?hl=bn&gl=BD&ceid=BD:bn')">💻 তথ্যপ্রযুক্তি</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="setGoogleNewsPreset('গুগল নিউজ: আন্তর্জাতিক', 'https://news.google.com/rss/headlines/section/topic/WORLD?hl=bn&gl=BD&ceid=BD:bn')">🌍 আন্তর্জাতিক</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="setGoogleNewsPreset('গুগল নিউজ: বাণিজ্য ও অর্থনীতি', 'https://news.google.com/rss/headlines/section/topic/BUSINESS?hl=bn&gl=BD&ceid=BD:bn')">💼 অর্থনীতি ও বাণিজ্য</button>
            </div>
            <div style="display: flex; gap: 8px; align-items: center; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                <span style="font-size: 13px; color: #64748b; white-space: nowrap;">🔍 নির্দিষ্ট বিষয়ে সার্চ ফিড:</span>
                <input type="text" id="customTopicInput" placeholder="যেমন: বাজেট / নির্বাচন / কৃত্রিম বুদ্ধিমত্তা" style="border: none; outline: none; flex: 1; font-size: 13px;">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyCustomTopicSearch()">ফিড লিংক সেট করুন</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.automation.sources.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">সোর্সের নাম *</label>
                    <input type="text" name="name" id="sourceNameInput" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: গুগল নিউজ শীর্ষ সংবাদ / প্রথম আলো">
                </div>

                <div class="form-group">
                    <label class="form-label">ফিডের সম্পূর্ণ URL *</label>
                    <input type="url" name="feed_url" id="feedUrlInput" value="{{ old('feed_url') }}" required class="form-control" placeholder="https://news.google.com/rss?hl=bn&gl=BD&ceid=BD:bn">
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">SSRF প্রটেকশন প্রযোজ্য: লোকালহোস্ট বা অভ্যন্তরীণ নেটওয়ার্কের ঠিকানা নিষিদ্ধ।</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">ফিড টাইপ *</label>
                        <select name="type" id="feedTypeSelect" class="form-control" required>
                            <option value="rss" {{ old('type', 'rss') === 'rss' ? 'selected' : '' }}>RSS 2.0 (Google News ইত্যাদি)</option>
                            <option value="atom" {{ old('type') === 'atom' ? 'selected' : '' }}>Atom 1.0</option>
                            <option value="json_api" {{ old('type') === 'json_api' ? 'selected' : '' }}>JSON Feed / API</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ভাষা *</label>
                        <select name="language" id="languageSelect" class="form-control" required>
                            <option value="bn" {{ old('language', 'bn') === 'bn' ? 'selected' : '' }}>বাংলা (Bengali)</option>
                            <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>ইংরেজি (English)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">ডিফল্ট ক্যাটাগরি</label>
                        <select name="default_category_id" id="categorySelect" class="form-control">
                            <option value="">স্বয়ংক্রিয় ট্যাগ ম্যাচিং</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('default_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">সংগ্রহ ব্যবধান (মিনিট) *</label>
                        <input type="number" name="fetch_interval_minutes" value="{{ old('fetch_interval_minutes', 60) }}" min="5" max="1440" required class="form-control">
                        <small style="color: #64748b; font-size: 11px;">কত মিনিট পর পর নতুন খবর চেক করা হবে (৫ থেকে ১৪৪০ মিনিট)</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">লেখক / সোর্স অ্যাট্রিবিউশন নাম</label>
                    <input type="text" name="author_attribution" id="authorAttributionInput" value="{{ old('author_attribution') }}" class="form-control" placeholder="যেমন: গুগল নিউজ ডেস্ক / বিশেষ প্রতিনিধি">
                </div>

                {{-- AI Paraphrasing & Editorial Review Configuration --}}
                <div style="margin: 20px 0; padding: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                        <input type="checkbox" name="ai_paraphrase" id="ai_paraphrase" value="1" {{ old('ai_paraphrase', true) ? 'checked' : '' }} style="width: 20px; height: 20px; margin-top: 2px;">
                        <div>
                            <label for="ai_paraphrase" style="font-weight: 700; font-size: 15px; color: #166534; cursor: pointer; margin: 0;">
                                🤖 স্বয়ংক্রিয় এআই প্যারাফ্রেজিং / রিরাইট (Auto AI Paraphrasing)
                            </label>
                            <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                                গুগল নিউজ বা আরএসএস থেকে সংবাদ সংগ্রহের সময় এআই স্বয়ংক্রিয়ভাবে প্রমিত বাংলায় শিরোনাম, সারসংক্ষেপ ও সম্পূর্ণ সংবাদ পুনর্লিখন (Paraphrase) করবে। এতে কপিরাইট ও ডুপ্লিকেট কন্টেন্টের ঝুঁকি দূর হবে।
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600; color: #166534;">সম্পাদকীয় অবস্থা (Editorial Status)</label>
                        <select name="editorial_status" id="editorialStatusSelect" class="form-control" style="background: #ffffff;">
                            <option value="review_pending" {{ old('editorial_status', 'review_pending') === 'review_pending' ? 'selected' : '' }}>
                                ✓ সম্পাদকীয় পর্যালোচনায় অপেক্ষমাণ (Review Pending) — [অনুমোদনের পর স্বয়ংক্রিয় কার্ড তৈরি হয়ে ফেসবুকে যাবে]
                            </option>
                            <option value="draft" {{ old('editorial_status') === 'draft' ? 'selected' : '' }}>
                                সাধারণ খসড়া (Draft)
                            </option>
                            <option value="published" {{ old('editorial_status') === 'published' ? 'selected' : '' }}>
                                সরাসরি প্রকাশনা (Direct Publish on Import)
                            </option>
                        </select>
                        <small style="color: #15803d; font-size: 11px; margin-top: 4px; display: block;">
                            সংবাদটি সংগৃহীত হওয়ার পর "সম্পাদকীয় পর্যালোচনায়" থাকবে। অ্যাডমিন বা সম্পাদক অনুমোদন দিলে স্বয়ংক্রিয়ভাবে সংবাদটির ছবি ও শিরোনাম যুক্ত সামাজিক মাধ্যম কার্ড তৈরি হবে এবং ফেসবুক পেজে পোস্ট হবে।
                        </small>
                    </div>
                </div>

                {{-- Status & Direct Publish Controls --}}
                <div style="margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">এই সোর্সটি সক্রিয় রাখুন</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="auto_publish" value="1" {{ old('auto_publish', false) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <div>
                            <span style="font-weight: 600; font-size: 14px; color: #b91c1c;">সরাসরি অটো পাবলিশ (বিনা পর্যালোচনায়)</span>
                            <div style="font-size: 11px; color: #64748b;">সতর্কতা: আনচেক থাকলে সংগৃহীত সংবাদ সম্পাদকের পর্যালোচনার জন্য জমা থাকবে।</div>
                        </div>
                    </label>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setNewsPreset(name, url, attribution) {
    document.getElementById('sourceNameInput').value = name;
    document.getElementById('feedUrlInput').value = url;
    document.getElementById('feedTypeSelect').value = 'rss';
    document.getElementById('languageSelect').value = 'bn';
    document.getElementById('authorAttributionInput').value = attribution || 'নিউজ ডেস্ক';
    document.getElementById('ai_paraphrase').checked = false;
    document.getElementById('editorialStatusSelect').value = 'published';
}

function setGoogleNewsPreset(name, url) {
    document.getElementById('sourceNameInput').value = name;
    document.getElementById('feedUrlInput').value = url;
    document.getElementById('feedTypeSelect').value = 'rss';
    document.getElementById('languageSelect').value = 'bn';
    document.getElementById('authorAttributionInput').value = 'গুগল নিউজ ডেস্ক';
    document.getElementById('ai_paraphrase').checked = true;
    document.getElementById('editorialStatusSelect').value = 'review_pending';
}

function applyCustomTopicSearch() {
    const topic = document.getElementById('customTopicInput').value.trim();
    if (!topic) {
        alert('দয়া করে সার্চ করার জন্য একটি বিষয় বা কি-ওয়ার্ড লিখুন');
        return;
    }
    const encoded = encodeURIComponent(topic);
    const feedUrl = `https://news.google.com/rss/search?q=${encoded}&hl=bn&gl=BD&ceid=BD:bn`;
    setGoogleNewsPreset(`গুগল নিউজ: ${topic}`, feedUrl);
}
</script>
@endsection
