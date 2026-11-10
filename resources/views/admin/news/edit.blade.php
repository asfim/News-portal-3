@extends('layouts.admin')

@section('title', 'সংবাদ সম্পাদনা: ' . $news->title)

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সংবাদ সম্পাদনা</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">পাবলিক আইডি: <code>{{ $news->public_id }}</code> | সংস্করণ: v{{ $news->lock_version }}</p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('news.show', [$news->public_id, $news->slug]) }}" target="_blank" class="btn btn-secondary">
                <span>👁️ ওয়েবসাইটে দেখুন</span>
            </a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">&larr; সংবাদ তালিকা</a>
        </div>
    </div>

    @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px;">
            <strong style="display: block; margin-bottom: 6px;">দয়া করে নিচের ত্রুটিগুলো সংশোধন করুন:</strong>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="lock_version" value="{{ $news->lock_version }}">

        <div class="admin-grid-2col">
            <!-- Left Column: Main Content -->
            <div>
                <!-- AI Editorial Assistant Box -->
                <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; color: #fff; box-shadow: 0 4px 14px rgba(49, 46, 129, 0.15);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 18px;">⚡</span>
                            <span style="font-weight: 700; font-size: 14px; letter-spacing: 0.3px;">এআই সম্পাদকীয় সহকারী (AI News Assistant)</span>
                        </div>
                        <span style="font-size: 11px; color: #c7d2fe; background: rgba(255,255,255,0.1); padding: 3px 8px; border-radius: 9999px;">
                            মডেল: {{ \App\Models\AiSetting::current()->model }}
                        </span>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="headline" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">🪄 বিকল্প শিরোনাম</button>
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="summary" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">📝 সারসংক্ষেপ</button>
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="improve" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">✨ ভাষা পরিমার্জন</button>
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="seo" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">🔍 এসইও তৈরি</button>
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="tags" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">🏷️ ট্যাগ প্রস্তাব</button>
                        <button type="button" class="btn btn-sm ai-action-btn" data-action="social_caption" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px; padding: 5px 10px;">📢 সোশ্যাল ক্যাপশন</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">কিকার (শীর্ষ শিরোনাম)</label>
                            <input type="text" name="kicker" value="{{ old('kicker', $news->kicker) }}" class="form-control" placeholder="যেমন: বিশেষ অনুসন্ধান">
                        </div>

                        <div class="form-group">
                            <label class="form-label">সংবাদের মূল শিরোনাম *</label>
                            <input type="text" name="title" value="{{ old('title', $news->title) }}" required class="form-control" style="font-size: 17px; font-weight: 700;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ইউআরএল স্লাগ (Slug) *</label>
                            <input type="text" name="slug" value="{{ old('slug', $news->slug) }}" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">উপ-শিরোনাম</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $news->subtitle) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">সংক্ষিপ্ত সারসংক্ষেপ (Excerpt)</label>
                            <textarea name="excerpt" rows="3" class="form-control">{{ old('excerpt', $news->excerpt) }}</textarea>
                        </div>

                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="form-label" style="margin-bottom: 0;">সংবাদের বিস্তারিত বিষয়বস্তু (Body HTML) *</label>
                                <div id="wordCountBadge" style="font-size: 12px; color: #64748b; font-weight: 500;">
                                    শব্দ: <span id="editorWordCount">০</span> | অক্ষর: <span id="editorCharCount">০</span>
                                </div>
                            </div>
                            <textarea name="body_html" id="bodyHtmlEditor" rows="15" class="form-control" style="font-family: inherit; line-height: 1.6;">{{ old('body_html', $news->body_html) }}</textarea>
                        </div>

                        <div class="form-group" style="padding-top: 14px; border-top: 1px solid #eee;">
                            <label class="form-label">সংশোধনের বিবরণ (Change Note) - রিভিশন ট্র্যাকিংয়ের জন্য</label>
                            <input type="text" name="change_note" value="{{ old('change_note') }}" class="form-control" placeholder="যেমন: বানান সংশোধন / তথ্যের সংযোজন">
                        </div>
                    </div>
                </div>

                <!-- Media Details -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title">কভার ছবি ও মাল্টিমিডিয়া</h3>
                        <button type="button" id="openMediaModalBtn" class="btn btn-secondary btn-sm" style="font-size: 12px;">
                            🖼️ মিডিয়া লাইব্রেরি থেকে ছবি বেছে নিন
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Live Image Preview Box -->
                        @php $hasCover = !empty($news->cover_image_url); @endphp
                        <div id="coverPreviewContainer" style="margin-bottom: 16px; border: 2px dashed #ddd; border-radius: 6px; padding: 12px; text-align: center; background: #fafafa; position: relative;">
                            <div id="noImagePlaceholder" style="{{ $hasCover ? 'display: none;' : '' }} padding: 24px 0; color: #888;">
                                <svg style="width: 42px; height: 42px; fill: #bbb; margin-bottom: 8px; display: inline-block;" viewBox="0 0 24 24">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                <div style="font-size: 13px;">কোনো কভার ছবি নির্বাচন করা হয়নি</div>
                            </div>
                            <div id="previewImageWrapper" style="{{ $hasCover ? '' : 'display: none;' }} position: relative; text-align: center;">
                                <img id="coverImagePreview" src="{{ $news->cover_image_url ?? '' }}" alt="Cover Preview" style="max-height: 280px; max-width: 100%; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); object-fit: contain;">
                                <div style="margin-top: 10px;">
                                    <button type="button" id="removeImageBtn" class="btn btn-danger btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                        &times; এই ছবি বাদ দিন
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Media ID input (if picked from library or preserved) -->
                        <input type="hidden" name="cover_media_id" id="coverMediaIdInput" value="{{ old('cover_media_id', $news->cover_media_id) }}">

                        <!-- Option 1: File Upload -->
                        <div class="form-group">
                            <label class="form-label" style="display: flex; justify-content: space-between;">
                                <span>১. কম্পিউটার থেকে নতুন ছবি আপলোড করুন</span>
                                <span style="font-size: 11px; color: var(--text-muted);">JPG, PNG, WEBP, AVIF (সর্বোচ্চ ৫ মেগাবাইট)</span>
                            </label>
                            <input type="file" name="cover_image" id="coverImageFileInput" accept="image/*" class="form-control">
                        </div>

                        <!-- Option 2: Direct URL -->
                        <div class="form-group">
                            <label class="form-label">২. অথবা সরাসরি ছবির অনলাইন ইউআরএল (Image URL)</label>
                            <input type="url" name="cover_image_url_direct" id="coverUrlInput" value="{{ old('cover_image_url_direct') }}" class="form-control" placeholder="https://example.com/photo.jpg">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">ছবির ক্যাপশন</label>
                                <input type="text" name="cover_caption" id="coverCaptionInput" value="{{ old('cover_caption', $news->cover_caption) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">ছবির ক্রেডিট</label>
                                <input type="text" name="cover_credit" id="coverCreditInput" value="{{ old('cover_credit', $news->cover_credit) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Taxonomy & Publishing -->
            <div>
                <!-- Publishing Actions -->
                <div class="card" style="border-top: 4px solid var(--primary);">
                    <div class="card-header">
                        <h3 class="card-title">প্রকাশনা নিয়ন্ত্রণ</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">বর্তমান অবস্থা পরিবর্তন করুন:</label>
                            <select name="action" class="form-control" style="font-weight: 700;">
                                <option value="publish" {{ $news->status === 'published' ? 'selected' : '' }}>প্রকাশিত (Published)</option>
                                <option value="draft" {{ $news->status === 'draft' ? 'selected' : '' }}>খসড়া (Draft)</option>
                                <option value="submit_review" {{ $news->status === 'review_pending' ? 'selected' : '' }}>পর্যালোচনায় (Review Pending)</option>
                                <option value="schedule" {{ $news->status === 'scheduled' ? 'selected' : '' }}>শিডিউল্ড (Scheduled)</option>
                                <option value="archived" {{ $news->status === 'archived' ? 'selected' : '' }}>আর্কাইভ (Archived)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">শিডিউল প্রকাশের তারিখ ও সময়</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ $news->scheduled_at ? $news->scheduled_at->setTimezone('Asia/Dhaka')->format('Y-m-d\TH:i') : '' }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="comments_enabled" value="1" {{ $news->comments_enabled ? 'checked' : '' }}> মন্তব্য সক্রিয় রাখুন
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 14px; font-weight: 700; padding: 10px; margin-top: 10px;">
                            পরিবর্তন সংরক্ষণ করুন
                        </button>
                    </div>
                </div>

                <!-- Taxonomies -->
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">সংবাদের ধরন *</label>
                            <select name="type" required class="form-control">
                                <option value="article" {{ $news->type === 'article' ? 'selected' : '' }}>সাধারণ সংবাদ (Article)</option>
                                <option value="opinion" {{ $news->type === 'opinion' ? 'selected' : '' }}>মতামত / কলাম (Opinion)</option>
                                <option value="video" {{ $news->type === 'video' ? 'selected' : '' }}>ভিডিও প্রতিবেদন (Video)</option>
                                <option value="photo" {{ $news->type === 'photo' ? 'selected' : '' }}>ছবি অ্যালবাম (Photo)</option>
                                <option value="live" {{ $news->type === 'live' ? 'selected' : '' }}>সরাসরি আপডেট (Live)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">প্রধান ক্যাটাগরি *</label>
                            <select name="primary_category_id" required class="form-control">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $news->primary_category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">লেখক / রিপোর্টার</label>
                            @php $selectedAuthorIds = $news->authors->pluck('id')->toArray(); @endphp
                            <select name="author_ids[]" multiple class="form-control" style="height: 100px;">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ in_array($author->id, $selectedAuthorIds) ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span style="font-size: 11px; color: var(--text-muted);">Ctrl চেপে একাধিক নির্বাচন করতে পারেন।</span>
                        </div>

                        <!-- Tags Section with Quick Create -->
                        <div class="form-group">
                            <label class="form-label" style="display: flex; justify-content: space-between;">
                                <span>ট্যাগসমূহ</span>
                                <span style="font-size: 11px; color: var(--text-muted);">চিহ্নিত ট্যাগ সক্রিয় থাকবে</span>
                            </label>
                            @php $selectedTagIds = $news->tags->pluck('id')->toArray(); @endphp
                            <select name="tag_ids[]" id="tagSelect" multiple class="form-control" style="height: 110px;">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTagIds) ? 'selected' : '' }}>
                                        #{{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Quick Tag Create Box -->
                            <div style="margin-top: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px;">
                                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: #334155;">
                                    + নতুন ট্যাগ তৈরি ও যুক্ত করুন:
                                </label>
                                <div style="display: flex; gap: 6px;">
                                    <input type="text" id="quickTagNameInput" class="form-control" style="font-size: 12px; padding: 6px 10px;" placeholder="যেমন: চ্যাম্পিয়ন্স ট্রফি">
                                    <button type="button" id="quickTagSubmitBtn" class="btn btn-secondary btn-sm" style="white-space: nowrap; font-size: 12px;">
                                        + যোগ করুন
                                    </button>
                                </div>
                                <div id="quickTagFeedback" style="font-size: 11px; margin-top: 4px; display: none;"></div>
                            </div>

                            <!-- Comma Separated Tags Option -->
                            <div style="margin-top: 8px;">
                                <label class="form-label" style="font-size: 11px; color: var(--text-muted); margin-bottom: 3px;">
                                    অথবা কমা দিয়ে অতিরিক্ত নতুন ট্যাগ লিখুন:
                                </label>
                                <input type="text" name="new_tags" value="{{ old('new_tags') }}" class="form-control" style="font-size: 12px;" placeholder="যেমন: প্রযুক্তি, এআই, এইচ টেক">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">লোকেশন / এলাকা</label>
                            <select name="location_id" class="form-control">
                                <option value="">-- কোনো নির্দিষ্ট এলাকা নেই --</option>
                                @foreach($divisions as $div)
                                    <optgroup label="{{ $div->name }} বিভাগ">
                                        <option value="{{ $div->id }}" {{ $news->location_id == $div->id ? 'selected' : '' }}>{{ $div->name }} (সমগ্র বিভাগ)</option>
                                        @foreach($div->children as $dist)
                                            <option value="{{ $dist->id }}" {{ $news->location_id == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Revisions History -->
                @if($revisions->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">পূর্ববর্তী রিভিশন রেকর্ড ({{ bn_num($revisions->count()) }} টি)</h3>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <ul style="list-style: none; margin: 0; padding: 0;">
                                @foreach($revisions as $rev)
                                    <li style="padding: 10px 16px; border-bottom: 1px solid #f0f0f0; font-size: 12px;">
                                        <div style="display: flex; justify-content: space-between; font-weight: 700;">
                                            <span>v{{ $rev->version }} ({{ $rev->editor ? $rev->editor->name : 'অজানা' }})</span>
                                            <span style="color: var(--text-muted);">{{ bn_time_ago($rev->created_at) }}</span>
                                        </div>
                                        @if($rev->change_note)
                                            <div style="color: #666; margin-top: 2px;">{{ $rev->change_note }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Modal: Media Library Selector -->
<div id="mediaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 800px; max-height: 85vh; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 700; margin: 0;">মিডিয়া লাইব্রেরি থেকে ছবি নির্বাচন করুন</h3>
            <button type="button" id="closeMediaModalBtn" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #888;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex: 1;">
            @if(isset($recentMedia) && $recentMedia->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px;">
                    @foreach($recentMedia as $m)
                        <div class="media-pick-item" 
                             data-id="{{ $m->id }}" 
                             data-url="{{ $m->url }}"
                             data-caption="{{ $m->caption ?? '' }}"
                             data-credit="{{ $m->credit ?? '' }}"
                             style="cursor: pointer; border: 2px solid #eee; border-radius: 6px; overflow: hidden; position: relative; transition: border-color 0.15s; background: #fafafa;">
                            <div style="aspect-ratio: 16/10; overflow: hidden;">
                                <img src="{{ $m->url }}" alt="{{ $m->original_name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                            </div>
                            <div style="padding: 6px 8px; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #555;">
                                {{ $m->original_name ?: 'Media #'.$m->id }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; color: var(--text-muted); padding: 40px;">
                    মিডিয়া লাইব্রেরিতে কোনো ছবি নেই।
                </div>
            @endif
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #eee; text-align: right; background: #f9f9f9;">
            <button type="button" id="cancelMediaModalBtn" class="btn btn-secondary btn-sm">বন্ধ করুন</button>
        </div>
    </div>
</div>

<!-- Modal: AI Assistant Side-by-Side Review -->
<div id="aiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10000; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 950px; max-height: 90vh; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: 16px 22px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">⚡</span>
                <h3 id="aiModalTitle" style="font-size: 16px; font-weight: 700; margin: 0; color: #1e1b4b;">এআই ফলাফল ও সম্পাদকীয় পর্যালোচনা</h3>
            </div>
            <button type="button" id="closeAiModalBtn" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1;">&times;</button>
        </div>

        <div style="padding: 20px; overflow-y: auto; flex: 1;">
            <div id="aiLoadingIndicator" style="display: none; text-align: center; padding: 40px 20px;">
                <div style="display: inline-block; width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <div style="margin-top: 14px; font-weight: 600; color: #4338ca; font-size: 14px;">এআই প্রসেসিং হচ্ছে, অনুগ্রহ করে অপেক্ষা করুন...</div>
            </div>

            <div id="aiErrorAlert" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;"></div>

            <!-- Side by Side Preview Layout -->
            <div id="aiComparisonContainer" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label class="form-label" style="font-weight: 700; color: #475569;">১. মূল লেখা (Original Content)</label>
                    <textarea id="aiOriginalContent" class="form-control" rows="12" readonly style="background: #f8fafc; font-size: 13px; line-height: 1.6;"></textarea>
                </div>
                <div>
                    <label class="form-label" style="font-weight: 700; color: #4338ca;">২. এআই প্রস্তাবিত সংস্করণ (AI Suggested)</label>
                    <textarea id="aiSuggestedContent" class="form-control" rows="12" style="font-size: 13px; line-height: 1.6; border-color: #818cf8;"></textarea>
                    <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">আপনি চাইলে গ্রহণের পূর্বে এখানেও প্রয়োজনীয় সম্পাদনা করতে পারেন।</small>
                </div>
            </div>
        </div>

        <div style="padding: 14px 22px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <span style="font-size: 12px; color: #64748b;">
                🛡️ সম্পাদকীয় সুরক্ষা: কোনো লেখা স্বয়ংক্রিয়ভাবে প্রতিস্থাপন হয় না।
            </span>
            <div style="display: flex; gap: 10px;">
                <button type="button" id="cancelAiModalBtn" class="btn btn-secondary btn-sm">বাতিল করুন</button>
                <button type="button" id="acceptAiChangesBtn" class="btn btn-primary btn-sm" style="background: #4338ca; border-color: #4338ca;">
                    ✓ এই পরিবর্তন গ্রহণ ও প্রতিস্থাপন করুন
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // Cover Image Live Preview & Handling
    // ----------------------------------------------------
    const fileInput = document.getElementById('coverImageFileInput');
    const urlInput = document.getElementById('coverUrlInput');
    const mediaIdInput = document.getElementById('coverMediaIdInput');
    const previewWrapper = document.getElementById('previewImageWrapper');
    const previewImg = document.getElementById('coverImagePreview');
    const noImagePlaceholder = document.getElementById('noImagePlaceholder');
    const removeBtn = document.getElementById('removeImageBtn');
    const captionInput = document.getElementById('coverCaptionInput');
    const creditInput = document.getElementById('coverCreditInput');

    function showImagePreview(src) {
        if (!src) return;
        previewImg.src = src;
        previewWrapper.style.display = 'block';
        noImagePlaceholder.style.display = 'none';
    }

    function clearImagePreview() {
        previewImg.src = '';
        previewWrapper.style.display = 'none';
        noImagePlaceholder.style.display = 'block';
        fileInput.value = '';
        urlInput.value = '';
        mediaIdInput.value = '';
    }

    // 1. On File Select
    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (evt) {
                showImagePreview(evt.target.result);
                urlInput.value = '';
                mediaIdInput.value = '';
            };
            reader.readAsDataURL(file);
        }
    });

    // 2. On Direct URL Type/Paste
    urlInput.addEventListener('input', function (e) {
        const val = e.target.value.trim();
        if (val && (val.startsWith('http://') || val.startsWith('https://'))) {
            showImagePreview(val);
            fileInput.value = '';
            mediaIdInput.value = '';
        } else if (!val && !fileInput.files.length && !mediaIdInput.value) {
            clearImagePreview();
        }
    });

    // 3. Remove Image Button
    removeBtn.addEventListener('click', function () {
        clearImagePreview();
    });

    // ----------------------------------------------------
    // Media Library Modal
    // ----------------------------------------------------
    const modal = document.getElementById('mediaModal');
    const openModalBtn = document.getElementById('openMediaModalBtn');
    const closeModalBtn = document.getElementById('closeMediaModalBtn');
    const cancelModalBtn = document.getElementById('cancelMediaModalBtn');

    function openModal() {
        modal.style.display = 'flex';
    }
    function closeModal() {
        modal.style.display = 'none';
    }

    if (openModalBtn) openModalBtn.addEventListener('click', openModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

    // Pick item from modal
    document.querySelectorAll('.media-pick-item').forEach(function (item) {
        item.addEventListener('mouseenter', function () {
            this.style.borderColor = 'var(--primary)';
        });
        item.addEventListener('mouseleave', function () {
            this.style.borderColor = '#eee';
        });
        item.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            const caption = this.getAttribute('data-caption');
            const credit = this.getAttribute('data-credit');

            mediaIdInput.value = id;
            fileInput.value = '';
            urlInput.value = '';

            if (caption && !captionInput.value) captionInput.value = caption;
            if (credit && !creditInput.value) creditInput.value = credit;

            showImagePreview(url);
            closeModal();
        });
    });

    // ----------------------------------------------------
    // Quick Tag Creation via AJAX
    // ----------------------------------------------------
    const quickTagInput = document.getElementById('quickTagNameInput');
    const quickTagBtn = document.getElementById('quickTagSubmitBtn');
    const quickTagFeedback = document.getElementById('quickTagFeedback');
    const tagSelect = document.getElementById('tagSelect');

    function createQuickTag() {
        const tagName = quickTagInput.value.trim();
        if (!tagName) {
            quickTagFeedback.style.display = 'block';
            quickTagFeedback.style.color = '#dc2626';
            quickTagFeedback.textContent = 'ট্যাগের নাম লিখুন।';
            return;
        }

        quickTagBtn.disabled = true;
        quickTagBtn.textContent = 'তৈরি হচ্ছে...';

        fetch('{{ route('admin.tags.quick-store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: tagName })
        })
        .then(response => response.json())
        .then(data => {
            quickTagBtn.disabled = false;
            quickTagBtn.textContent = '+ যোগ করুন';

            if (data.success && data.tag) {
                let existingOpt = tagSelect.querySelector(`option[value="${data.tag.id}"]`);
                if (!existingOpt) {
                    const opt = document.createElement('option');
                    opt.value = data.tag.id;
                    opt.textContent = '#' + data.tag.name;
                    opt.selected = true;
                    tagSelect.appendChild(opt);
                } else {
                    existingOpt.selected = true;
                }

                quickTagFeedback.style.display = 'block';
                quickTagFeedback.style.color = '#16a34a';
                quickTagFeedback.textContent = '✓ ট্যাগ যুক্ত হয়েছে: #' + data.tag.name;
                quickTagInput.value = '';

                setTimeout(() => {
                    quickTagFeedback.style.display = 'none';
                }, 3000);
            } else {
                quickTagFeedback.style.display = 'block';
                quickTagFeedback.style.color = '#dc2626';
                quickTagFeedback.textContent = data.message || 'ট্যাগ তৈরি করা সম্ভব হয়নি।';
            }
        })
        .catch(err => {
            quickTagBtn.disabled = false;
            quickTagBtn.textContent = '+ যোগ করুন';
            quickTagFeedback.style.display = 'block';
            quickTagFeedback.style.color = '#dc2626';
            quickTagFeedback.textContent = 'সার্ভার যোগাযোগে ত্রুটি হয়েছে।';
        });
    }

    if (quickTagBtn) {
        quickTagBtn.addEventListener('click', createQuickTag);
    }
    if (quickTagInput) {
        quickTagInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                createQuickTag();
            }
        });
    }

    // ----------------------------------------------------
    // Bengali Number Formatter & Word/Char Counter
    // ----------------------------------------------------
    function toBnNum(num) {
        const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return num.toString().replace(/\d/g, d => bnDigits[d]);
    }

    function updateLiveCounters() {
        let content = '';
        if (typeof tinymce !== 'undefined' && tinymce.get('bodyHtmlEditor')) {
            content = tinymce.get('bodyHtmlEditor').getContent({ format: 'text' });
        } else {
            const el = document.getElementById('bodyHtmlEditor');
            content = el ? el.value : '';
        }

        const cleanText = content.trim();
        const words = cleanText ? cleanText.split(/\s+/).filter(Boolean).length : 0;
        const chars = cleanText.length;

        const wEl = document.getElementById('editorWordCount');
        const cEl = document.getElementById('editorCharCount');
        if (wEl) wEl.textContent = toBnNum(words);
        if (cEl) cEl.textContent = toBnNum(chars);
    }

    const form = document.getElementById('newsForm');
    const editorTextarea = document.getElementById('bodyHtmlEditor');
    if (form) {
        form.addEventListener('submit', function () {
            if (typeof tinymce !== 'undefined' && tinymce.get('bodyHtmlEditor')) {
                tinymce.get('bodyHtmlEditor').save();
            }
        });
    }

    // ----------------------------------------------------
    // Initialize TinyMCE Rich Text Editor
    // ----------------------------------------------------
    function initTinyMCE() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#bodyHtmlEditor',
                height: 480,
                menubar: 'file edit view insert format tools table',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | link image media table | ' +
                    'blockquote code removeformat | fullscreen preview',
                content_style: `
                    body { 
                        font-family: 'SolaimanLipi', 'Noto Sans Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                        font-size: 16px; 
                        line-height: 1.7; 
                        color: #202638; 
                        padding: 12px;
                    }
                    img { max-width: 100%; height: auto; border-radius: 4px; }
                    blockquote { border-left: 3px solid #c62828; margin: 1em 0; padding-left: 14px; font-style: italic; color: #555; }
                    figure { margin: 1em 0; text-align: center; }
                    figcaption { font-size: 13px; color: #777; margin-top: 4px; }
                `,
                images_upload_url: '{{ route('admin.media.editor-upload') }}',
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise(function (resolve, reject) {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '{{ route('admin.media.editor-upload') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = function (e) {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = function () {
                            if (xhr.status === 403 || xhr.status < 200 || xhr.status >= 300) {
                                reject('HTTP Error: ' + xhr.status);
                                return;
                            }
                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject('Invalid JSON response');
                                return;
                            }
                            resolve(json.location);
                        };

                        xhr.onerror = function () {
                            reject('Image upload failed');
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });
                },
                setup: function (editor) {
                    editor.on('init', function () {
                        updateLiveCounters();
                    });
                    editor.on('change keyup NodeChange', function () {
                        editor.save();
                        updateLiveCounters();
                    });
                }
            });
        } else if (editorTextarea) {
            editorTextarea.addEventListener('input', updateLiveCounters);
            updateLiveCounters();
        }
    }

    if (window.tinymce) {
        initTinyMCE();
    } else {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js';
        script.referrerPolicy = 'origin';
        script.onload = initTinyMCE;
        script.onerror = function () {
            if (editorTextarea) {
                editorTextarea.addEventListener('input', updateLiveCounters);
                updateLiveCounters();
            }
        };
        document.head.appendChild(script);
    }

    // ----------------------------------------------------
    // AI Editorial Assistant Integration
    // ----------------------------------------------------
    const aiModal = document.getElementById('aiModal');
    const closeAiBtn = document.getElementById('closeAiModalBtn');
    const cancelAiBtn = document.getElementById('cancelAiModalBtn');
    const acceptAiBtn = document.getElementById('acceptAiChangesBtn');
    const aiOriginalText = document.getElementById('aiOriginalContent');
    const aiSuggestedText = document.getElementById('aiSuggestedContent');
    const aiLoading = document.getElementById('aiLoadingIndicator');
    const aiError = document.getElementById('aiErrorAlert');
    const aiContainer = document.getElementById('aiComparisonContainer');
    const aiModalTitle = document.getElementById('aiModalTitle');

    let currentAiAction = null;

    function openAiModal(action, title) {
        currentAiAction = action;
        if (aiModalTitle) aiModalTitle.textContent = 'এআই ফলাফল: ' + title;
        if (aiError) aiError.style.display = 'none';
        if (aiSuggestedText) aiSuggestedText.value = '';
        if (aiContainer) aiContainer.style.display = 'none';
        if (aiLoading) aiLoading.style.display = 'block';
        if (acceptAiBtn) acceptAiBtn.disabled = true;
        if (aiModal) aiModal.style.display = 'flex';
    }

    function closeAiModal() {
        if (aiModal) aiModal.style.display = 'none';
        currentAiAction = null;
    }

    if (closeAiBtn) closeAiBtn.addEventListener('click', closeAiModal);
    if (cancelAiBtn) cancelAiBtn.addEventListener('click', closeAiModal);

    document.querySelectorAll('.ai-action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            const actionTitle = this.textContent.trim();

            let inputText = '';
            let editorText = '';
            if (typeof tinymce !== 'undefined' && tinymce.get('bodyHtmlEditor')) {
                editorText = tinymce.get('bodyHtmlEditor').getContent({ format: 'text' }).trim();
            } else if (editorTextarea) {
                editorText = editorTextarea.value.trim();
            }

            const titleVal = document.querySelector('input[name="title"]')?.value.trim() || '';
            const excerptVal = document.querySelector('textarea[name="excerpt"]')?.value.trim() || '';

            if (action === 'headline') {
                inputText = editorText || excerptVal || titleVal;
            } else if (action === 'summary' || action === 'improve' || action === 'social_caption') {
                inputText = editorText || excerptVal;
            } else if (action === 'seo' || action === 'tags') {
                inputText = (titleVal ? "শিরোনাম: " + titleVal + "\n\n" : "") + (editorText || excerptVal);
            }

            if (!inputText) {
                alert('দয়া করে প্রথমে সংবাদের শিরোনাম অথবা বিস্তারিত বিষয়বস্তু লিখুন যাতে এআই তা বিশ্লেষণ করতে পারে।');
                return;
            }

            openAiModal(action, actionTitle);
            if (aiOriginalText) aiOriginalText.value = inputText;

            fetch('{{ route('admin.ai.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    input_text: inputText,
                    news_id: {{ $news->id }}
                })
            })
            .then(res => res.json())
            .then(data => {
                if (aiLoading) aiLoading.style.display = 'none';
                if (data.success && data.text) {
                    if (aiSuggestedText) aiSuggestedText.value = data.text;
                    if (aiContainer) aiContainer.style.display = 'grid';
                    if (acceptAiBtn) acceptAiBtn.disabled = false;
                } else {
                    if (aiError) {
                        aiError.textContent = data.error || 'এআই প্রক্রিয়াকরণ ব্যর্থ হয়েছে।';
                        aiError.style.display = 'block';
                    }
                }
            })
            .catch(err => {
                if (aiLoading) aiLoading.style.display = 'none';
                if (aiError) {
                    aiError.textContent = 'সার্ভার যোগাযোগে ত্রুটি হয়েছে। অনুগ্রহ করে ইন্টারনেট ও এআই কনফিগারেশন চেক করুন।';
                    aiError.style.display = 'block';
                }
            });
        });
    });

    if (acceptAiBtn) {
        acceptAiBtn.addEventListener('click', function () {
            const acceptedText = aiSuggestedText ? aiSuggestedText.value.trim() : '';
            if (!acceptedText) return;

            if (currentAiAction === 'headline') {
                const lines = acceptedText.split('\n').filter(Boolean);
                const firstHeadline = lines[0].replace(/^[১-৩1-3০-৯0-9\.\-\s]+/, '').trim();
                const titleInput = document.querySelector('input[name="title"]');
                if (titleInput) titleInput.value = firstHeadline || acceptedText;
            } else if (currentAiAction === 'summary') {
                const excerptInput = document.querySelector('textarea[name="excerpt"]');
                if (excerptInput) excerptInput.value = acceptedText;
            } else if (currentAiAction === 'improve') {
                if (typeof tinymce !== 'undefined' && tinymce.get('bodyHtmlEditor')) {
                    tinymce.get('bodyHtmlEditor').setContent(acceptedText);
                } else if (editorTextarea) {
                    editorTextarea.value = acceptedText;
                }
            } else if (currentAiAction === 'seo') {
                const seoTitleMatch = acceptedText.match(/SEO Title:\s*(.*)/i);
                const metaDescMatch = acceptedText.match(/Meta Description:\s*(.*)/i);
                if (seoTitleMatch) {
                    const seoTitleInput = document.querySelector('input[name="seo_title"]');
                    if (seoTitleInput) seoTitleInput.value = seoTitleMatch[1].trim();
                }
                if (metaDescMatch) {
                    const seoDescInput = document.querySelector('textarea[name="seo_description"]');
                    if (seoDescInput) seoDescInput.value = metaDescMatch[1].trim();
                }
            } else if (currentAiAction === 'tags') {
                const tagInput = document.querySelector('input[name="new_tags"]');
                if (tagInput) tagInput.value = (tagInput.value ? tagInput.value + ', ' : '') + acceptedText;
            }

            closeAiModal();
            updateLiveCounters();
        });
    }
});
</script>
@endsection
