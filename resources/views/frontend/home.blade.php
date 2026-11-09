@extends('layouts.frontend')

@section('title', 'প্রথম আলো | বাংলা নিউজ পেপার')

@push('styles')
<style>
    /* Hero Grid Layout */
    .hero-grid-wrapper {
        display: grid;
        grid-template-columns: 2.8fr 4.4fr 2.8fr;
        gap: 24px;
        padding: 24px 0;
        border-bottom: 1px solid #e5e5e5;
    }
    @media (max-width: 1024px) {
        .hero-grid-wrapper {
            grid-template-columns: 1fr;
        }
    }
    .hero-col {
        min-width: 0;
    }
    .hero-center {
        border-left: 1px solid #eee;
        border-right: 1px solid #eee;
        padding: 0 20px;
    }
    @media (max-width: 1024px) {
        .hero-center {
            border-left: none;
            border-right: none;
            padding: 0;
        }
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        border-bottom: 2px solid #222;
        padding-bottom: 8px;
    }
    .section-title {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #111;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title::before {
        content: "";
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: #d60000;
        border-radius: 2px;
    }
    .section-title a {
        color: inherit;
        text-decoration: none;
    }
    .section-title a:hover {
        color: #d60000;
    }

    /* Latest & Most Read Numbered List */
    .numbered-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .numbered-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .numbered-item:last-child {
        border-bottom: none;
    }
    .num-badge {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        background: #f0f0f0;
        color: #d60000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
    }
    .numbered-content {
        flex: 1;
    }
    .numbered-content h4 {
        margin: 0 0 4px 0;
        font-size: 15px;
        line-height: 1.35;
        font-weight: 700;
    }
    .numbered-content h4 a {
        color: #222;
        text-decoration: none;
        transition: color 0.15s;
    }
    .numbered-content h4 a:hover {
        color: #d60000;
    }
    .numbered-content span {
        font-size: 12px;
        color: #888;
    }

    /* For You & Video Grid */
    .grid-4-col {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }
    @media (max-width: 900px) {
        .grid-4-col {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 500px) {
        .grid-4-col {
            grid-template-columns: 1fr;
        }
    }

    /* Location selector box */
    .location-box {
        background: #fbfbfb;
        border: 1px solid #eaeaea;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .location-box select {
        padding: 8px 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 15px;
        font-family: inherit;
        outline: none;
        background: #fff;
    }
    .location-box button {
        background: #d60000;
        color: #fff;
        border: none;
        padding: 9px 18px;
        border-radius: 4px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background-color 0.15s;
    }
    .location-box button:hover {
        background: #b50000;
    }

    /* Card image zoom hover */
    .zoom-hover {
        overflow: hidden;
        border-radius: 4px;
    }
    .zoom-hover img {
        transition: transform 0.25s ease-out;
    }
    .zoom-hover:hover img {
        transform: scale(1.03);
    }
</style>
@endpush

@section('content')
<div class="site-container" style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">

    <!-- ==================== HERO 3-COLUMN EDITORIAL GRID ==================== -->
    <div class="hero-grid-wrapper">
        
        <!-- Left Column: 4-5 Horizontal Cards -->
        <div class="hero-col hero-left">
            @forelse($leftStories as $story)
                <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" />
            @empty
                @if(!$leadStory)
                    <p style="color: #888;">সংবাদ লোড হচ্ছে...</p>
                @endif
            @endforelse
        </div>

        <!-- Center Column: Lead Story + 2 Sub-stories -->
        <div class="hero-col hero-center">
            @if($leadStory)
                <x-news-card :news="$leadStory" variant="lead" :showExcerpt="true" />
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
                @foreach($centerSubStories->take(2) as $subStory)
                    <x-news-card :news="$subStory" variant="vertical" :showExcerpt="false" />
                @endforeach
            </div>
        </div>

        <!-- Right Column: 4 Horizontal Cards + Ad Slot / Widget -->
        <div class="hero-col hero-right">
            @forelse($rightStories as $rStory)
                <x-news-card :news="$rStory" variant="horizontal" :showExcerpt="false" />
            @empty
            @endforelse

            <!-- Ad / Promo Widget Placement -->
            <x-ad-slot slotKey="home_right_rectangle" />
        </div>
    </div>

    <!-- Leaderboard Ad Slot -->
    <x-ad-slot slotKey="header_leaderboard" />

    <!-- ==================== FOR YOU SECTION (আপনার জন্য) ==================== -->
    @if($forYouStories->isNotEmpty())
        <section style="margin: 36px 0;">
            <div class="section-header">
                <h3 class="section-title">
                    <span>আপনার জন্য</span>
                </h3>
            </div>
            <div class="grid-4-col">
                @foreach($forYouStories as $fyStory)
                    <article class="zoom-hover" style="border: 1px solid #eee; border-radius: 4px; overflow: hidden; background: #fff;">
                        <a href="{{ route('news.show', [$fyStory->public_id, $fyStory->slug]) }}" style="text-decoration: none;">
                            @if($fyStory->cover_image_url)
                                <div style="aspect-ratio: 16/10; overflow: hidden; background: #eee;">
                                    <img src="{{ $fyStory->cover_image_url }}" alt="{{ $fyStory->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                                </div>
                            @endif
                            <div style="padding: 12px;">
                                <h4 style="font-size: 16px; font-weight: 700; line-height: 1.35; margin: 0 0 6px 0; color: #111;">
                                    {{ $fyStory->title }}
                                </h4>
                                <span style="font-size: 12px; color: #888;">{{ bn_time_ago($fyStory->published_at) }}</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ==================== VIDEO STORIES (ভিডিও) ==================== -->
    @if($videoStories->isNotEmpty())
        <section style="margin: 36px 0; background: #111; padding: 24px; border-radius: 6px; color: #fff;">
            <div class="section-header" style="border-bottom-color: #333; margin-bottom: 20px;">
                <h3 class="section-title" style="color: #fff;">
                    <span>ভিডিও</span>
                </h3>
                <a href="{{ route('category.show', 'video') }}" style="color: #d60000; font-size: 14px; font-weight: 700; text-decoration: none;">সব ভিডিও &rarr;</a>
            </div>
            <div class="grid-4-col">
                @foreach($videoStories->take(4) as $vStory)
                    <x-news-card :news="$vStory" variant="video" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- ==================== BANGLADESH (বাংলাদেশ ও জেলা সংবাদ) ==================== -->
    <section style="margin: 36px 0;">
        <div class="section-header">
            <h3 class="section-title">
                <a href="{{ route('category.show', 'bangladesh') }}">বাংলাদেশ</a>
            </h3>
            <a href="{{ route('category.show', 'bangladesh') }}" style="color: #d60000; font-weight: 700; font-size: 14px; text-decoration: none;">সব খবর &rarr;</a>
        </div>

        <!-- Division / District Selector Bar -->
        <div class="location-box">
            <label style="font-weight: 700; color: #333;">আপনার এলাকার খবর জানতে জেলা বেছে নিন:</label>
            <select id="divisionSelect" onchange="onDivisionChange(this.value)">
                <option value="">-- বিভাগ নির্বাচন করুন --</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}" data-slug="{{ $div->slug }}">{{ $div->name }}</option>
                @endforeach
            </select>
            <select id="districtSelect" style="display: none;">
                <option value="">-- জেলা নির্বাচন করুন --</option>
            </select>
            <button type="button" onclick="goToLocation()">খুঁজুন</button>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                @foreach($bdNews->take(4) as $bStory)
                    <x-news-card :news="$bStory" variant="horizontal" :showExcerpt="false" />
                @endforeach
            </div>
            <div>
                @foreach($bdNews->slice(4, 3) as $bStory)
                    <x-news-card :news="$bStory" variant="compact" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- ==================== LATEST & MOST READ TABS (সর্বশেষ ও সর্বাধিক পঠিত) ==================== -->
    <section style="margin: 40px 0; background: #fafafa; border: 1px solid #ebebeb; border-radius: 6px; padding: 24px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
            <!-- Column 1: Latest -->
            <div>
                <div class="section-header">
                    <h3 class="section-title">সর্বশেষ</h3>
                    <a href="{{ route('news.latest') }}" style="color: #d60000; font-size: 14px; font-weight: 700; text-decoration: none;">সব &rarr;</a>
                </div>
                <ul class="numbered-list">
                    @foreach($latestNews as $idx => $lNews)
                        <li class="numbered-item" style="display: flex; gap: 12px; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span class="num-badge">{{ bn_num($idx + 1) }}</span>
                            <div class="numbered-content" style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 4px 0; font-size: 15px; line-height: 1.35; font-weight: 700;">
                                    <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}">
                                        {{ $lNews->title }}
                                    </a>
                                </h4>
                                <span>{{ bn_time_ago($lNews->published_at) }}</span>
                            </div>
                            @if($lNews->cover_image_url)
                                <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}" style="flex-shrink: 0; width: 75px; height: 52px; border-radius: 4px; overflow: hidden; display: block; background: #eee;">
                                    <img src="{{ $lNews->cover_image_url }}" alt="{{ $lNews->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Column 2: Most Read -->
            <div>
                <div class="section-header">
                    <h3 class="section-title">পঠিত</h3>
                </div>
                <ul class="numbered-list">
                    @foreach($mostReadNews as $idx => $mNews)
                        <li class="numbered-item" style="display: flex; gap: 12px; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span class="num-badge" style="background: #fee2e2; color: #b91c1c;">{{ bn_num($idx + 1) }}</span>
                            <div class="numbered-content" style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 4px 0; font-size: 15px; line-height: 1.35; font-weight: 700;">
                                    <a href="{{ route('news.show', [$mNews->public_id, $mNews->slug]) }}">
                                        {{ $mNews->title }}
                                    </a>
                                </h4>
                                <span>{{ bn_time_ago($mNews->published_at) }}</span>
                            </div>
                            @if($mNews->cover_image_url)
                                <a href="{{ route('news.show', [$mNews->public_id, $mNews->slug]) }}" style="flex-shrink: 0; width: 75px; height: 52px; border-radius: 4px; overflow: hidden; display: block; background: #eee;">
                                    <img src="{{ $mNews->cover_image_url }}" alt="{{ $mNews->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <!-- ==================== SPORTS & OPINION SECTION ==================== -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin: 36px 0;">
        <!-- Sports (খেলা) -->
        <div>
            <div class="section-header">
                <h3 class="section-title">
                    <a href="{{ route('category.show', 'sports') }}">খেলা</a>
                </h3>
                <a href="{{ route('category.show', 'sports') }}" style="color: #d60000; font-weight: 700; font-size: 14px; text-decoration: none;">আরও &rarr;</a>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @if($sportsNews->isNotEmpty())
                    <div>
                        <x-news-card :news="$sportsNews->first()" variant="lead" :showExcerpt="true" />
                    </div>
                    <div>
                        @foreach($sportsNews->slice(1, 4) as $sStory)
                            <x-news-card :news="$sStory" variant="horizontal" :showExcerpt="false" />
                        @endforeach
                    </div>
                @else
                    <p style="color: #888;">খেলার সংবাদ এখনো প্রকাশিত হয়নি।</p>
                @endif
            </div>
        </div>

        <!-- Opinion (মতামত) -->
        <div>
            <div class="section-header">
                <h3 class="section-title">
                    <a href="{{ route('category.show', 'opinion') }}">মতামত</a>
                </h3>
                <a href="{{ route('category.show', 'opinion') }}" style="color: #d60000; font-weight: 700; font-size: 14px; text-decoration: none;">আরও &rarr;</a>
            </div>
            <div>
                @forelse($opinionNews as $opNews)
                    <x-news-card :news="$opNews" variant="opinion" />
                @empty
                    <p style="color: #888;">মতামত কলাম এখনো প্রকাশিত হয়নি।</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==================== TECHNOLOGY (বিজ্ঞান ও প্রযুক্তি) ==================== -->
    @if($techNews->isNotEmpty())
        <section style="margin: 36px 0; background: #fdfbf7; border: 1px solid #f0eae1; border-radius: 6px; padding: 24px;">
            <div class="section-header">
                <h3 class="section-title">
                    <a href="{{ route('category.show', 'technology') }}">বিজ্ঞান ও প্রযুক্তি</a>
                </h3>
                <a href="{{ route('category.show', 'technology') }}" style="color: #d60000; font-weight: 700; font-size: 14px; text-decoration: none;">সব &rarr;</a>
            </div>
            <div class="grid-4-col">
                @foreach($techNews->take(4) as $tStory)
                    <article class="zoom-hover" style="border: 1px solid #e8e2d8; border-radius: 4px; overflow: hidden; background: #fff;">
                        <a href="{{ route('news.show', [$tStory->public_id, $tStory->slug]) }}" style="text-decoration: none;">
                            @if($tStory->cover_image_url)
                                <div style="aspect-ratio: 16/10; overflow: hidden; background: #eee;">
                                    <img src="{{ $tStory->cover_image_url }}" alt="{{ $tStory->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                                </div>
                            @endif
                            <div style="padding: 12px;">
                                <h4 style="font-size: 16px; font-weight: 700; line-height: 1.35; margin: 0 0 6px 0; color: #111;">
                                    {{ $tStory->title }}
                                </h4>
                                <span style="font-size: 12px; color: #888;">{{ bn_time_ago($tStory->published_at) }}</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
    var divisionsData = @json($divisions);

    function onDivisionChange(divId) {
        var districtSelect = document.getElementById('districtSelect');
        if (!divId) {
            districtSelect.style.display = 'none';
            return;
        }

        var selectedDiv = divisionsData.find(d => d.id == divId);
        if (selectedDiv && selectedDiv.children && selectedDiv.children.length > 0) {
            districtSelect.innerHTML = '<option value="">-- জেলা নির্বাচন করুন --</option>';
            selectedDiv.children.forEach(function(district) {
                var opt = document.createElement('option');
                opt.value = district.slug;
                opt.text = district.name;
                districtSelect.appendChild(opt);
            });
            districtSelect.style.display = 'inline-block';
        } else {
            districtSelect.style.display = 'none';
        }
    }

    function goToLocation() {
        var districtSelect = document.getElementById('districtSelect');
        var divisionSelect = document.getElementById('divisionSelect');
        
        var slug = '';
        if (districtSelect.style.display !== 'none' && districtSelect.value) {
            slug = districtSelect.value;
        } else if (divisionSelect.value) {
            var selectedDiv = divisionsData.find(d => d.id == divisionSelect.value);
            if (selectedDiv) {
                slug = selectedDiv.slug;
            }
        }

        if (slug) {
            window.location.href = "{{ url('/location') }}/" + slug;
        } else {
            alert('দয়া করে বিভাগ বা জেলা নির্বাচন করুন');
        }
    }
</script>
@endpush
