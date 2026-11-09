@extends('layouts.frontend')

@section('title', 'প্রথম আলো | বাংলা নিউজ পেপার')

@push('styles')
<style>
    /* ========================================================
       PROTHOM ALO AUTHENTIC NEWSPAPER GRID & BORDER SYSTEM
       ======================================================== */
    :root {
        --palo-border: #dfdfdf;
        --palo-border-dark: #222222;
        --palo-red: #d60000;
        --palo-text-primary: #111111;
        --palo-text-secondary: #555555;
        --palo-text-muted: #757575;
    }

    .palo-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 16px;
        background: #ffffff;
    }

    /* Common Newspaper Divider Lines */
    .palo-hr {
        border: none;
        border-top: 1px solid var(--palo-border);
        margin: 20px 0;
    }
    .palo-vr {
        width: 1px;
        background-color: var(--palo-border);
        align-self: stretch;
    }

    /* Section Header (Iconic Prothom Alo) */
    .palo-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--palo-border);
        padding-bottom: 8px;
        margin-bottom: 16px;
        position: relative;
    }
    .palo-section-header::after {
        content: "";
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 60px;
        height: 3px;
        background-color: var(--palo-red);
    }
    .palo-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .palo-section-title {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: var(--palo-text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .palo-section-title a {
        color: inherit;
        text-decoration: none;
    }
    .palo-section-title a:hover {
        color: var(--palo-red);
    }
    .palo-section-title .arrow {
        color: var(--palo-red);
        font-weight: 700;
        font-size: 18px;
    }
    .palo-subnav {
        display: flex;
        align-items: center;
        gap: 14px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .palo-subnav a {
        color: var(--palo-text-secondary);
        font-size: 14px;
        text-decoration: none;
        transition: color 0.15s;
    }
    .palo-subnav a:hover {
        color: var(--palo-red);
    }
    .palo-view-all {
        color: var(--palo-red);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    /* ==================== 1. HERO 3-COLUMN EDITORIAL GRID ==================== */
    .hero-editorial-grid {
        display: grid;
        grid-template-columns: 3.1fr 5.8fr 3.1fr;
        padding: 20px 0;
        border-bottom: 1px solid var(--palo-border);
    }
    .hero-col-left {
        padding-right: 20px;
        border-right: 1px solid var(--palo-border);
    }
    .hero-col-center {
        padding: 0 20px;
        border-right: 1px solid var(--palo-border);
    }
    .hero-col-right {
        padding-left: 20px;
    }
    @media (max-width: 1024px) {
        .hero-editorial-grid {
            grid-template-columns: 1fr;
        }
        .hero-col-left,
        .hero-col-center,
        .hero-col-right {
            border-right: none;
            padding: 0;
            border-bottom: 1px solid var(--palo-border);
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        .hero-col-right {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    }

    .hero-substories-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid var(--palo-border);
        margin-top: 16px;
        padding-top: 16px;
    }
    .hero-substories-grid > div:first-child {
        padding-right: 14px;
        border-right: 1px solid var(--palo-border);
    }
    .hero-substories-grid > div:last-child {
        padding-left: 14px;
    }
    @media (max-width: 600px) {
        .hero-substories-grid {
            grid-template-columns: 1fr;
        }
        .hero-substories-grid > div:first-child {
            padding-right: 0;
            border-right: none;
            border-bottom: 1px solid var(--palo-border);
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .hero-substories-grid > div:last-child {
            padding-left: 0;
        }
    }

    /* ==================== 2. SEAMLESS 4-COLUMN NEWSPAPER TABLE ==================== */
    .palo-newspaper-table-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border-top: 1px solid var(--palo-border);
        border-bottom: 1px solid var(--palo-border);
        margin: 24px 0;
    }
    .palo-newspaper-table-4 > div {
        border-right: 1px solid var(--palo-border);
        padding: 16px;
    }
    .palo-newspaper-table-4 > div:first-child {
        padding-left: 0;
    }
    .palo-newspaper-table-4 > div:last-child {
        border-right: none;
        padding-right: 0;
    }
    @media (max-width: 900px) {
        .palo-newspaper-table-4 {
            grid-template-columns: repeat(2, 1fr);
        }
        .palo-newspaper-table-4 > div:nth-child(2) {
            border-right: none;
        }
        .palo-newspaper-table-4 > div:nth-child(3) {
            border-top: 1px solid var(--palo-border);
            padding-left: 0;
        }
        .palo-newspaper-table-4 > div:nth-child(4) {
            border-top: 1px solid var(--palo-border);
        }
    }
    @media (max-width: 500px) {
        .palo-newspaper-table-4 {
            grid-template-columns: 1fr;
        }
        .palo-newspaper-table-4 > div {
            border-right: none;
            border-bottom: 1px solid var(--palo-border);
            padding: 14px 0 !important;
        }
        .palo-newspaper-table-4 > div:last-child {
            border-bottom: none;
        }
    }

    /* ==================== 3. BANGLADESH 3-COLUMN GRID ==================== */
    .palo-bd-grid {
        display: grid;
        grid-template-columns: 4fr 4.2fr 3.8fr;
        border-top: 1px solid var(--palo-border);
        border-bottom: 1px solid var(--palo-border);
        padding: 20px 0;
        margin: 20px 0;
    }
    .palo-bd-col1 {
        padding-right: 20px;
        border-right: 1px solid var(--palo-border);
    }
    .palo-bd-col2 {
        padding: 0 20px;
        border-right: 1px solid var(--palo-border);
    }
    .palo-bd-col3 {
        padding-left: 20px;
    }
    @media (max-width: 1024px) {
        .palo-bd-grid {
            grid-template-columns: 1fr;
        }
        .palo-bd-col1, .palo-bd-col2, .palo-bd-col3 {
            border-right: none;
            padding: 0;
            border-bottom: 1px solid var(--palo-border);
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        .palo-bd-col3 {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    }

    /* Location Selector Box */
    .location-bar {
        background: #fbfbfb;
        border: 1px solid var(--palo-border);
        border-radius: 3px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .location-bar label {
        font-weight: 700;
        font-size: 14px;
        color: #333;
    }
    .location-bar select {
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
    }
    .location-bar button {
        background: var(--palo-red);
        color: #fff;
        border: none;
        padding: 7px 16px;
        border-radius: 3px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
    }

    /* ==================== 4. TABBED LATEST & MOST READ WIDGET ==================== */
    .palo-tabs-container {
        display: grid;
        grid-template-columns: 7fr 5fr;
        border-top: 1px solid var(--palo-border);
        border-bottom: 1px solid var(--palo-border);
        padding: 24px 0;
        margin: 32px 0;
    }
    .palo-tabs-left {
        padding-right: 24px;
        border-right: 1px solid var(--palo-border);
    }
    .palo-tabs-right {
        padding-left: 24px;
    }
    @media (max-width: 900px) {
        .palo-tabs-container {
            grid-template-columns: 1fr;
        }
        .palo-tabs-left {
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid var(--palo-border);
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .palo-tabs-right {
            padding-left: 0;
        }
    }

    .palo-tab-nav {
        display: flex;
        gap: 24px;
        border-bottom: 1px solid var(--palo-border);
        margin-bottom: 16px;
    }
    .palo-tab-btn {
        background: none;
        border: none;
        font-size: 18px;
        font-weight: 700;
        font-family: inherit;
        color: #777;
        padding-bottom: 8px;
        cursor: pointer;
        position: relative;
        transition: color 0.15s;
    }
    .palo-tab-btn.active {
        color: var(--palo-text-primary);
    }
    .palo-tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: var(--palo-red);
    }

    .palo-num-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid var(--palo-border);
    }
    .palo-num-item:last-child {
        border-bottom: none;
    }
    .palo-num-glyph {
        font-size: 32px;
        line-height: 1;
        font-weight: 700;
        color: #999;
        flex-shrink: 0;
        width: 28px;
    }
    .palo-num-body {
        flex: 1;
        min-width: 0;
    }
    .palo-num-body h4 {
        font-size: 15px;
        line-height: 1.35;
        font-weight: 700;
        margin: 0 0 4px 0;
    }
    .palo-num-body h4 a {
        color: var(--palo-text-primary);
        text-decoration: none;
    }
    .palo-num-body h4 a:hover {
        color: var(--palo-red);
    }
    .palo-num-body time {
        font-size: 12px;
        color: var(--palo-text-muted);
    }
    .palo-num-thumb {
        flex-shrink: 0;
        width: 80px;
        height: 54px;
        border-radius: 2px;
        overflow: hidden;
        background: #eee;
    }
    .palo-num-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ==================== 5. TWO-COLUMN SPLIT (SPORTS & OPINION, ENTERTAINMENT & WORLD) ==================== */
    .palo-split-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid var(--palo-border);
        border-bottom: 1px solid var(--palo-border);
        padding: 24px 0;
        margin: 32px 0;
    }
    .palo-split-left {
        padding-right: 24px;
        border-right: 1px solid var(--palo-border);
    }
    .palo-split-right {
        padding-left: 24px;
    }
    @media (max-width: 900px) {
        .palo-split-2 {
            grid-template-columns: 1fr;
        }
        .palo-split-left {
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid var(--palo-border);
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .palo-split-right {
            padding-left: 0;
        }
    }

    /* Subgrid for Sports */
    .palo-sports-subgrid {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
    }
    .palo-sports-subgrid > div:first-child {
        padding-right: 16px;
        border-right: 1px solid var(--palo-border);
    }
    .palo-sports-subgrid > div:last-child {
        padding-left: 16px;
    }
    @media (max-width: 600px) {
        .palo-sports-subgrid {
            grid-template-columns: 1fr;
        }
        .palo-sports-subgrid > div:first-child {
            padding-right: 0;
            border-right: none;
            border-bottom: 1px solid var(--palo-border);
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .palo-sports-subgrid > div:last-child {
            padding-left: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="palo-wrapper">

    <!-- =========================================================================
         SECTION 1: HERO 3-COLUMN NEWSPAPER EDITORIAL GRID
         ========================================================================= -->
    <section class="hero-editorial-grid">
        
        <!-- Left Column: 5 Horizontal News Cards -->
        <div class="hero-col-left">
            @forelse($leftStories as $idx => $story)
                <x-news-card :news="$story" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last" />
            @empty
                @if(!$leadStory)
                    <p style="color: #888;">সংবাদ লোড হচ্ছে...</p>
                @endif
            @endforelse
        </div>

        <!-- Center Column: Grand Lead Story + 2 Sub-Stories with Divider -->
        <div class="hero-col-center">
            @if($leadStory)
                <x-news-card :news="$leadStory" variant="lead" :showExcerpt="true" />
            @endif

            <!-- 2 Center Sub-Stories (Divided by crisp vertical line) -->
            <div class="hero-substories-grid">
                @foreach($centerSubStories->take(2) as $subStory)
                    <div>
                        <x-news-card :news="$subStory" variant="vertical" :showExcerpt="false" :hideBorder="true" />
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Column: 4 Horizontal Cards + Ad Widget Placement -->
        <div class="hero-col-right">
            @forelse($rightStories->take(3) as $rStory)
                <x-news-card :news="$rStory" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last && !$adSlots->has('home_right_rectangle')" />
            @empty
            @endforelse

            <!-- Ad / Promo Box (With Subtle 1px Newspaper Border) -->
            <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--palo-border);">
                <x-ad-slot slotKey="home_right_rectangle" />
            </div>
        </div>
    </section>

    <!-- Leaderboard Ad Slot -->
    <div style="margin: 16px 0; text-align: center;">
        <x-ad-slot slotKey="header_leaderboard" />
    </div>

    <!-- =========================================================================
         SECTION 2: আপনার জন্য (FOR YOU) - 4-COLUMN NEWSPAPER GRID
         ========================================================================= -->
    @if($forYouStories->isNotEmpty())
        <section style="margin: 28px 0;">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <span>আপনার জন্য</span>
                        <span class="arrow">&gt;</span>
                    </h2>
                </div>
            </div>

            <!-- Continuous Newspaper 4-Column Table with Vertical Borders -->
            <div class="palo-newspaper-table-4">
                @foreach($forYouStories->take(4) as $fyStory)
                    <div>
                        <x-news-card :news="$fyStory" variant="vertical" :showExcerpt="false" :hideBorder="true" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- =========================================================================
         SECTION 3: ভিডিও (VIDEO) SECTION
         ========================================================================= -->
    @if($videoStories->isNotEmpty())
        <section style="margin: 32px 0; background: #111; padding: 22px 20px; border-radius: 4px; color: #fff;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; padding-bottom: 12px; margin-bottom: 18px;">
                <h3 style="color: #fff; font-size: 22px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 6px;">
                    <span>ভিডিও</span>
                    <span style="color: #d60000; font-size: 18px;">&gt;</span>
                </h3>
                <a href="{{ route('category.show', 'video') }}" style="color: #d60000; font-size: 14px; font-weight: 700; text-decoration: none;">সব ভিডিও &gt;</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                @foreach($videoStories->take(4) as $vStory)
                    <x-news-card :news="$vStory" variant="video" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- =========================================================================
         SECTION 4: বাংলাদেশ (BANGLADESH) - 3-COLUMN NEWSPAPER GRID
         ========================================================================= -->
    <section style="margin: 32px 0;">
        <div class="palo-section-header">
            <div class="palo-header-left">
                <h2 class="palo-section-title">
                    <a href="{{ route('category.show', 'bangladesh') }}">বাংলাদেশ</a>
                    <span class="arrow">&gt;</span>
                </h2>
                <ul class="palo-subnav">
                    <li><a href="{{ route('category.show', 'politics') }}">রাজনীতি</a></li>
                    <li><a href="{{ route('category.show', 'bangladesh') }}">রাজধানী</a></li>
                    <li><a href="{{ route('category.show', 'bangladesh') }}">জেলা সংবাদ</a></li>
                    <li><a href="{{ route('category.show', 'special-news') }}">বিশেষ সংবাদ</a></li>
                </ul>
            </div>
            <a href="{{ route('category.show', 'bangladesh') }}" class="palo-view-all">সব খবর &gt;</a>
        </div>

        <!-- Location Search Bar -->
        <div class="location-bar">
            <label>আপনার এলাকার খবর জানতে জেলা বেছে নিন:</label>
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

        <!-- 3-Column Newspaper Grid with Divider Lines -->
        <div class="palo-bd-grid">
            <!-- Col 1: Lead Story -->
            <div class="palo-bd-col1">
                @if($bdNews->isNotEmpty())
                    <x-news-card :news="$bdNews->first()" variant="lead" :showExcerpt="true" />
                @endif
            </div>

            <!-- Col 2: 2 Middle Stories -->
            <div class="palo-bd-col2">
                @foreach($bdNews->slice(1, 2) as $bStory)
                    <x-news-card :news="$bStory" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last" />
                @endforeach
            </div>

            <!-- Col 3: 3 District News Items -->
            <div class="palo-bd-col3">
                @foreach($bdNews->slice(3, 3) as $bStory)
                    <x-news-card :news="$bStory" variant="compact" :hideBorder="$loop->last" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 5: সর্বশেষ ও সর্বাধিক পঠিত (LATEST & MOST READ WIDGET)
         ========================================================================= -->
    <section class="palo-tabs-container">
        <!-- Left Side: Interactive Tabs + Numbered List 1-5 -->
        <div class="palo-tabs-left">
            <div class="palo-tab-nav">
                <button type="button" id="tabBtnMostRead" class="palo-tab-btn active" onclick="switchTab('mostRead')">
                    পঠিত
                </button>
                <button type="button" id="tabBtnLatest" class="palo-tab-btn" onclick="switchTab('latest')">
                    সর্বশেষ
                </button>
                <a href="{{ route('news.latest') }}" style="margin-left: auto; color: var(--palo-red); font-size: 13px; font-weight: 700; text-decoration: none; align-self: center;">
                    সব &gt;
                </a>
            </div>

            <!-- Tab Pane 1: Most Read (পঠিত) -->
            <div id="tabPaneMostRead">
                @foreach($mostReadNews->take(5) as $idx => $mNews)
                    <div class="palo-num-item">
                        <span class="palo-num-glyph">{{ bn_num($idx + 1) }}</span>
                        <div class="palo-num-body">
                            <h4>
                                <a href="{{ route('news.show', [$mNews->public_id, $mNews->slug]) }}">
                                    {{ $mNews->title }}
                                </a>
                            </h4>
                            <time>{{ bn_time_ago($mNews->published_at) }}</time>
                        </div>
                        @if($mNews->cover_image_url)
                            <a href="{{ route('news.show', [$mNews->public_id, $mNews->slug]) }}" class="palo-num-thumb">
                                <img src="{{ $mNews->cover_image_url }}" alt="{{ $mNews->title }}" loading="eager">
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Tab Pane 2: Latest (সর্বশেষ) -->
            <div id="tabPaneLatest" style="display: none;">
                @foreach($latestNews->take(5) as $idx => $lNews)
                    <div class="palo-num-item">
                        <span class="palo-num-glyph" style="color: var(--palo-red);">{{ bn_num($idx + 1) }}</span>
                        <div class="palo-num-body">
                            <h4>
                                <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}">
                                    {{ $lNews->title }}
                                </a>
                            </h4>
                            <time>{{ bn_time_ago($lNews->published_at) }}</time>
                        </div>
                        @if($lNews->cover_image_url)
                            <a href="{{ route('news.show', [$lNews->public_id, $lNews->slug]) }}" class="palo-num-thumb">
                                <img src="{{ $lNews->cover_image_url }}" alt="{{ $lNews->title }}" loading="eager">
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Big Feature Visual Story with Left Border -->
        <div class="palo-tabs-right">
            @if($latestNews->isNotEmpty())
                @php $featureStory = $latestNews->first(); @endphp
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span style="display: inline-block; background: #fee2e2; color: #b91c1c; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 2px; margin-bottom: 10px;">
                            {{ $featureStory->primaryCategory->name ?? 'শীর্ষ সংবাদ' }}
                        </span>
                        <h3 style="font-size: 22px; line-height: 1.35; font-weight: 700; margin: 0 0 12px 0;">
                            <a href="{{ route('news.show', [$featureStory->public_id, $featureStory->slug]) }}" style="color: #111; text-decoration: none;">
                                {{ $featureStory->title }}
                            </a>
                        </h3>
                        @if($featureStory->cover_image_url)
                            <a href="{{ route('news.show', [$featureStory->public_id, $featureStory->slug]) }}" style="display: block; aspect-ratio: 16/10; border-radius: 2px; overflow: hidden; margin-bottom: 12px;">
                                <img src="{{ $featureStory->cover_image_url }}" alt="{{ $featureStory->title }}" style="width: 100%; height: 100%; object-fit: cover;" loading="eager">
                            </a>
                        @endif
                        <p style="font-size: 14px; line-height: 1.55; color: #555; margin: 0 0 10px 0;">
                            {{ $featureStory->excerpt }}
                        </p>
                    </div>
                    <div style="font-size: 12px; color: #888; border-top: 1px solid var(--palo-border); padding-top: 8px;">
                        <time>{{ bn_time_ago($featureStory->published_at) }}</time>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- =========================================================================
         SECTION 6: খেলা (SPORTS) & মতামত (OPINION) SPLIT WITH VERTICAL BORDER
         ========================================================================= -->
    <div class="palo-split-2">
        
        <!-- Left Side: Sports (খেলা) with Subgrid -->
        <div class="palo-split-left">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <a href="{{ route('category.show', 'sports') }}">খেলা</a>
                        <span class="arrow">&gt;</span>
                    </h2>
                    <ul class="palo-subnav">
                        <li><a href="{{ route('category.show', 'sports') }}">ক্রিকেট</a></li>
                        <li><a href="{{ route('category.show', 'sports') }}">ফুটবল</a></li>
                    </ul>
                </div>
                <a href="{{ route('category.show', 'sports') }}" class="palo-view-all">আরও &gt;</a>
            </div>

            <div class="palo-sports-subgrid">
                <!-- Left Subcol: Lead Sports Story -->
                <div>
                    @if($sportsNews->isNotEmpty())
                        <x-news-card :news="$sportsNews->first()" variant="lead" :showExcerpt="true" />
                    @endif
                </div>

                <!-- Right Subcol: 3 Sports Stories -->
                <div>
                    @foreach($sportsNews->slice(1, 3) as $sStory)
                        <x-news-card :news="$sStory" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last" />
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: Opinion (মতামত) -->
        <div class="palo-split-right">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <a href="{{ route('category.show', 'opinion') }}">মতামত</a>
                        <span class="arrow">&gt;</span>
                    </h2>
                </div>
                <a href="{{ route('category.show', 'opinion') }}" class="palo-view-all">আরও &gt;</a>
            </div>

            <div>
                @forelse($opinionNews->take(3) as $opNews)
                    <x-news-card :news="$opNews" variant="opinion" :hideBorder="$loop->last" />
                @empty
                    <p style="color: #888;">মতামত কলাম এখনো প্রকাশিত হয়নি।</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 7: বিনোদন (ENTERTAINMENT) & বিশ্ব (WORLD) SPLIT
         ========================================================================= -->
    <div class="palo-split-2">
        <!-- Left Side: Entertainment (বিনোদন) -->
        <div class="palo-split-left">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <a href="{{ route('category.show', 'entertainment') }}">বিনোদন</a>
                        <span class="arrow">&gt;</span>
                    </h2>
                    <ul class="palo-subnav">
                        <li><a href="{{ route('category.show', 'entertainment') }}">বলিউড</a></li>
                        <li><a href="{{ route('category.show', 'entertainment') }}">ঢালিউড</a></li>
                        <li><a href="{{ route('category.show', 'entertainment') }}">ওটিটি</a></li>
                    </ul>
                </div>
                <a href="{{ route('category.show', 'entertainment') }}" class="palo-view-all">আরও &gt;</a>
            </div>

            <div>
                @if($entNews->isNotEmpty())
                    <x-news-card :news="$entNews->first()" variant="lead" :showExcerpt="false" />
                    <div style="border-top: 1px solid var(--palo-border); margin-top: 12px; padding-top: 12px;">
                        @foreach($entNews->slice(1, 2) as $eStory)
                            <x-news-card :news="$eStory" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last" />
                        @endforeach
                    </div>
                @else
                    <p style="color: #888;">বিনোদন সংবাদ এখনো প্রকাশিত হয়নি।</p>
                @endif
            </div>
        </div>

        <!-- Right Side: World (বিশ্ব) -->
        <div class="palo-split-right">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <a href="{{ route('category.show', 'world') }}">বিশ্ব</a>
                        <span class="arrow">&gt;</span>
                    </h2>
                    <ul class="palo-subnav">
                        <li><a href="{{ route('category.show', 'world') }}">যুক্তরাষ্ট্র</a></li>
                        <li><a href="{{ route('category.show', 'world') }}">ইউরোপ</a></li>
                        <li><a href="{{ route('category.show', 'world') }}">মধ্যপ্রাচ্য</a></li>
                    </ul>
                </div>
                <a href="{{ route('category.show', 'world') }}" class="palo-view-all">আরও &gt;</a>
            </div>

            <div>
                @if($worldNews->isNotEmpty())
                    <x-news-card :news="$worldNews->first()" variant="lead" :showExcerpt="false" />
                    <div style="border-top: 1px solid var(--palo-border); margin-top: 12px; padding-top: 12px;">
                        @foreach($worldNews->slice(1, 2) as $wStory)
                            <x-news-card :news="$wStory" variant="horizontal" :showExcerpt="false" :hideBorder="$loop->last" />
                        @endforeach
                    </div>
                @else
                    <p style="color: #888;">আন্তর্জাতিক সংবাদ এখনো প্রকাশিত হয়নি।</p>
                @endif
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 8: বিজ্ঞান ও প্রযুক্তি (TECHNOLOGY) - 4-COLUMN NEWSPAPER TABLE
         ========================================================================= -->
    @if($techNews->isNotEmpty())
        <section style="margin: 32px 0;">
            <div class="palo-section-header">
                <div class="palo-header-left">
                    <h2 class="palo-section-title">
                        <a href="{{ route('category.show', 'technology') }}">বিজ্ঞান ও প্রযুক্তি</a>
                        <span class="arrow">&gt;</span>
                    </h2>
                    <ul class="palo-subnav">
                        <li><a href="{{ route('category.show', 'technology') }}">মহাকাশ</a></li>
                        <li><a href="{{ route('category.show', 'technology') }}">গ্যাজেট</a></li>
                        <li><a href="{{ route('category.show', 'technology') }}">এআই</a></li>
                    </ul>
                </div>
                <a href="{{ route('category.show', 'technology') }}" class="palo-view-all">সব &gt;</a>
            </div>

            <div class="palo-newspaper-table-4">
                @foreach($techNews->take(4) as $tStory)
                    <div>
                        <x-news-card :news="$tStory" variant="vertical" :showExcerpt="false" :hideBorder="true" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // Tab switching for Latest / Most Read
    function switchTab(tab) {
        var btnMostRead = document.getElementById('tabBtnMostRead');
        var btnLatest = document.getElementById('tabBtnLatest');
        var paneMostRead = document.getElementById('tabPaneMostRead');
        var paneLatest = document.getElementById('tabPaneLatest');

        if (tab === 'mostRead') {
            btnMostRead.classList.add('active');
            btnLatest.classList.remove('active');
            paneMostRead.style.display = 'block';
            paneLatest.style.display = 'none';
        } else {
            btnLatest.classList.add('active');
            btnMostRead.classList.remove('active');
            paneLatest.style.display = 'block';
            paneMostRead.style.display = 'none';
        }
    }

    // Location selector logic
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
