@extends('layouts.admin')

@section('title', 'নতুন সংবাদ তৈরি')

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নতুন সংবাদ তৈরি করুন</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">এইচ টেক প্রোভিশন আইটি নিউজ পোর্টালের জন্য নতুন প্রতিবেদন লিখুন</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">&larr; সংবাদ তালিকায় ফিরে যান</a>
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

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm">
        @csrf

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Left Column: Main Content -->
            <div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">কিকার (ঐচ্ছিক শীর্ষ শিরোনাম)</label>
                            <input type="text" name="kicker" value="{{ old('kicker') }}" class="form-control" placeholder="যেমন: বিশেষ অনুসন্ধান / ব্রেকিং নিউজ">
                        </div>

                        <div class="form-group">
                            <label class="form-label">সংবাদের মূল শিরোনাম *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="form-control" style="font-size: 17px; font-weight: 700;" placeholder="এখানে সংবাদের আকর্ষণীয় শিরোনাম লিখুন...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">উপ-শিরোনাম (ঐচ্ছিক)</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="form-control" placeholder="সংবাদের অতিরিক্ত বিবরণ...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">সংক্ষিপ্ত সারসংক্ষেপ (Excerpt)</label>
                            <textarea name="excerpt" rows="3" class="form-control" placeholder="হোমপেজ ও সামাজিক মাধ্যমে প্রদর্শনের জন্য সংক্ষিপ্ত বিবরণ...">{{ old('excerpt') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">সংবাদের বিস্তারিত বিষয়বস্তু (Body HTML) *</label>
                            <textarea name="body_html" rows="15" required class="form-control" style="font-family: inherit; line-height: 1.6;" placeholder="এখানে সম্পূর্ণ প্রতিবেদন বা সংবাদ লিখুন...">{{ old('body_html') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Media & Video Details -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title">কভার ছবি ও মাল্টিমিডিয়া</h3>
                        <button type="button" id="openMediaModalBtn" class="btn btn-secondary btn-sm" style="font-size: 12px;">
                            🖼️ মিডিয়া লাইব্রেরি থেকে ছবি বেছে নিন
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Live Image Preview Box -->
                        <div id="coverPreviewContainer" style="margin-bottom: 16px; border: 2px dashed #ddd; border-radius: 6px; padding: 12px; text-align: center; background: #fafafa; position: relative;">
                            <div id="noImagePlaceholder" style="padding: 24px 0; color: #888;">
                                <svg style="width: 42px; height: 42px; fill: #bbb; margin-bottom: 8px; display: inline-block;" viewBox="0 0 24 24">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                <div style="font-size: 13px;">কোনো কভার ছবি নির্বাচন করা হয়নি</div>
                                <div style="font-size: 11px; color: #aaa; margin-top: 2px;">নিচের ফাইল আপলোড, মিডিয়া লাইব্রেরি বা সরাসরি লিঙ্ক দিন</div>
                            </div>
                            <div id="previewImageWrapper" style="display: none; position: relative; text-align: center;">
                                <img id="coverImagePreview" src="" alt="Cover Preview" style="max-height: 280px; max-width: 100%; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); object-fit: contain;">
                                <div style="margin-top: 10px;">
                                    <button type="button" id="removeImageBtn" class="btn btn-danger btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                        &times; এই ছবি বাদ দিন
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Media ID input (if picked from library) -->
                        <input type="hidden" name="cover_media_id" id="coverMediaIdInput" value="{{ old('cover_media_id') }}">

                        <!-- Option 1: File Upload -->
                        <div class="form-group">
                            <label class="form-label" style="display: flex; justify-content: space-between;">
                                <span>১. কম্পিউটার থেকে ছবি আপলোড করুন</span>
                                <span style="font-size: 11px; color: var(--text-muted);">JPG, PNG, WEBP, AVIF (সর্বোচ্চ ৫ মেগাবাইট)</span>
                            </label>
                            <input type="file" name="cover_image" id="coverImageFileInput" accept="image/*" class="form-control">
                        </div>

                        <!-- Option 2: Direct URL -->
                        <div class="form-group">
                            <label class="form-label">২. অথবা সরাসরি ছবির অনলাইন ইউআরএল (Image URL)</label>
                            <input type="url" name="cover_image_url_direct" id="coverUrlInput" value="{{ old('cover_image_url_direct') }}" class="form-control" placeholder="https://example.com/photo.jpg">
                        </div>

                        <!-- Caption & Credit -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">ছবির ক্যাপশন</label>
                                <input type="text" name="cover_caption" id="coverCaptionInput" value="{{ old('cover_caption') }}" class="form-control" placeholder="ছবির সংক্ষিপ্ত বিবরণ...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">ছবির ক্রেডিট / ফটোগ্রাফার</label>
                                <input type="text" name="cover_credit" id="coverCreditInput" value="{{ old('cover_credit') }}" class="form-control" placeholder="যেমন: ফাইল ছবি / রিয়াদুল করিম">
                            </div>
                        </div>

                        <!-- Video Option -->
                        <div id="videoFields" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
                            <label class="form-label">ভিডিও লিঙ্ক (যদি ভিডিও প্রতিবেদন হয়)</label>
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                                <select name="video_provider" class="form-control">
                                    <option value="youtube">YouTube</option>
                                    <option value="vimeo">Vimeo</option>
                                </select>
                                <input type="text" name="video_id" value="{{ old('video_id') }}" class="form-control" placeholder="YouTube Video ID (যেমন: dQw4w9WgXcQ)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Taxonomy, Metadata & Publishing Actions -->
            <div>
                <!-- Publishing Action Card -->
                <div class="card" style="border-top: 4px solid var(--primary);">
                    <div class="card-header">
                        <h3 class="card-title">প্রকাশনা অ্যাকশন</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">প্রকাশের শিডিউল তারিখ ও সময় (ঢাকা সময়)</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                            <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">শিডিউল বাটনে চাপ দিলে এই সময়ে স্বয়ংক্রিয়ভাবে প্রকাশিত হবে।</span>
                        </div>

                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="comments_enabled" value="1" checked> পাঠকদের মন্তব্য সক্রিয় থাকবে
                            </label>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                            <button type="submit" name="action" value="publish" class="btn btn-primary" style="justify-content: center; font-size: 14px; font-weight: 700; padding: 10px;">
                                অবিলম্বে প্রকাশ করুন
                            </button>
                            <button type="submit" name="action" value="schedule" class="btn btn-success" style="justify-content: center;">
                                নির্ধারিত সময়ে শিডিউল করুন
                            </button>
                            <button type="submit" name="action" value="submit_review" class="btn btn-secondary" style="justify-content: center; background: #fef3c7; color: #92400e;">
                                পর্যালোচনার জন্য জমা দিন
                            </button>
                            <button type="submit" name="action" value="draft" class="btn btn-secondary" style="justify-content: center;">
                                খসড়া (ড্রাফট) হিসেবে সংরক্ষণ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category & Type -->
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">সংবাদের ধরন *</label>
                            <select name="type" required class="form-control">
                                <option value="article">সাধারণ সংবাদ (Article)</option>
                                <option value="opinion">মতামত / কলাম (Opinion)</option>
                                <option value="video">ভিডিও প্রতিবেদন (Video)</option>
                                <option value="photo">ছবি অ্যালবাম (Photo)</option>
                                <option value="live">সরাসরি আপডেট (Live)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">প্রধান ক্যাটাগরি *</label>
                            <select name="primary_category_id" required class="form-control">
                                <option value="">-- ক্যাটাগরি নির্বাচন করুন --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('primary_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">লেখক / রিপোর্টার</label>
                            <select name="author_ids[]" multiple class="form-control" style="height: 100px;">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->designation ?? 'লেখক' }})</option>
                                @endforeach
                            </select>
                            <span style="font-size: 11px; color: var(--text-muted);">Ctrl চেপে একাধিক নির্বাচন করতে পারেন।</span>
                        </div>

                        <!-- Tags Section with Quick Create -->
                        <div class="form-group">
                            <label class="form-label" style="display: flex; justify-content: space-between;">
                                <span>সম্পর্কিত ট্যাগসমূহ</span>
                                <span style="font-size: 11px; color: var(--text-muted);">তালিকা থেকে বেছে নিন</span>
                            </label>
                            <select name="tag_ids[]" id="tagSelect" multiple class="form-control" style="height: 110px;">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', [])) ? 'selected' : '' }}>
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
                                    অথবা কমা দিয়ে একাধিক নতুন ট্যাগ লিখুন:
                                </label>
                                <input type="text" name="new_tags" value="{{ old('new_tags') }}" class="form-control" style="font-size: 12px;" placeholder="যেমন: প্রযুক্তি, এআই, এইচ টেক">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">এলাকা / বিভাগ / জেলা</label>
                            <select name="location_id" class="form-control">
                                <option value="">-- কোনো নির্দিষ্ট এলাকা নেই --</option>
                                @foreach($divisions as $div)
                                    <optgroup label="{{ $div->name }} বিভাগ">
                                        <option value="{{ $div->id }}">{{ $div->name }} (সমগ্র বিভাগ)</option>
                                        @foreach($div->children as $dist)
                                            <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
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
                    মিডিয়া লাইব্রেরিতে কোনো ছবি নেই। আপনি সরাসরি কম্পিউটার থেকে আপলোড করতে পারেন।
                </div>
            @endif
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #eee; text-align: right; background: #f9f9f9;">
            <button type="button" id="cancelMediaModalBtn" class="btn btn-secondary btn-sm">বন্ধ করুন</button>
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
                // Clear URL input and media id to avoid conflict
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
                // Check if already in select
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
});
</script>
@endsection
