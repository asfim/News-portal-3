<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'এইচ টেক প্রোভিশন আইটি | নির্ভরযোগ্য বাংলা সংবাদ')</title>
    <meta name="description" content="@yield('meta_description', 'এইচ টেক প্রোভিশন আইটি - বাংলাদেশসহ বিশ্বের সর্বশেষ সংবাদ শিরোনাম, প্রতিবেদন, বিশ্লেষণ, খেলা, বিনোদন, চাকরি ও বাণিজ্যের বাংলা নিউজ।')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'এইচ টেক প্রোভিশন আইটি | নির্ভরযোগ্য বাংলা সংবাদ')">
    <meta property="og:description" content="@yield('og_description', 'এইচ টেক প্রোভিশন আইটি - বাংলাদেশসহ বিশ্বের সর্বশেষ সংবাদ শিরোনাম, প্রতিবেদন, বিশ্লেষণ, খেলা, বিনোদন, চাকরি ও বাণিজ্যের বাংলা নিউজ।')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('palo_bangla_og.png'))">
    <meta property="og:site_name" content="এইচ টেক প্রোভিশন আইটি">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'এইচ টেক প্রোভিশন আইটি | নির্ভরযোগ্য বাংলা সংবাদ')">
    <meta name="twitter:description" content="@yield('og_description', 'এইচ টেক প্রোভিশন আইটি - বাংলাদেশসহ বিশ্বের সর্বশেষ সংবাদ শিরোনাম, প্রতিবেদন, বিশ্লেষণ, খেলা, বিনোদন, চাকরি ও বাণিজ্যের বাংলা নিউজ।')">
    <meta name="twitter:image" content="@yield('og_image', asset('palo_bangla_og.png'))">

    <!-- JSON-LD Organization Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "NewsMediaOrganization",
        "name": "এইচ টেক প্রোভিশন আইটি",
        "url": "{{ url('/') }}",
        "logo": {
            "@@type": "ImageObject",
            "url": "https://media.prothomalo.com/prothomalo-bangla/2025-11-17/qix5skks/palo-bangla.svg"
        }
    }
    </script>
    @yield('schema')

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preconnect" href="https://assets.prothomalo.com">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    <style>
        @font-face {
            font-family: "Shurjo";
            font-display: swap;
            font-style: normal;
            font-weight: 400;
            src: url('https://assets.prothomalo.com/prothomalo/assets/ShurjoWeb_400_v5_1.woff2') format('woff2'),
                 url('https://assets.prothomalo.com/prothomalo/assets/ShurjoWeb_400_v5_1.woff') format('woff');
        }
        @font-face {
            font-family: "Shurjo";
            font-display: swap;
            font-style: normal;
            font-weight: 700;
            src: url('https://assets.prothomalo.com/prothomalo/assets/ShurjoWeb_700_v5_1.woff2') format('woff2'),
                 url('https://assets.prothomalo.com/prothomalo/assets/ShurjoWeb_700_v5_1.woff') format('woff');
        }

        :root {
            --title-font: "Shurjo", "Siyam Rupali", sans-serif;
            --content-font: "Shurjo", "Siyam Rupali", sans-serif;
        }

        /* Essential overrides and interactive elements */
        html {
            font-size: 112.5%;
            font-family: "Shurjo", "Siyam Rupali", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #fff;
            color: #111;
        }
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: inherit;
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        a:hover {
            color: #d60000;
        }
        @media (min-width: 1025px) {
            .ewX0K {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                min-height: 52px;
            }
        }
        @media (max-width: 1024px) {
            .ewX0K {
                display: none !important;
                visibility: hidden !important;
            }
        }
        .ewX0K .EL6od {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 12px;
            border-left: 1px solid #e5e5e5;
        }
        .ewX0K .EL6od:first-child {
            border-left: none;
        }
        .ewX0K .card-with-image-zoom {
            flex-shrink: 0;
            width: 55px;
            height: 38px;
            overflow: hidden;
            border-radius: 2px;
        }
        .ewX0K .card-with-image-zoom img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .ewX0K .headline {
            font-size: 14px;
            line-height: 1.35;
            font-weight: 700;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Search Dropdown / Modal */
        #searchDrawer {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            border-top: 2px solid #d60000;
            z-index: 9999;
            padding: 20px 0;
        }
        #searchDrawer.active {
            display: block;
        }
        .search-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            gap: 10px;
        }
        .search-input {
            flex: 1;
            padding: 12px 18px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            font-family: inherit;
        }
        .search-input:focus {
            border-color: #d60000;
        }
        .search-btn {
            background: #d60000;
            color: #fff;
            border: none;
            padding: 0 24px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }

        /* Slide-out Mobile / Mega Drawer */
        #sideMenuDrawer {
            position: fixed;
            top: 0;
            right: -360px;
            width: 340px;
            height: 100vh;
            background: #fff;
            box-shadow: -4px 0 20px rgba(0,0,0,0.25);
            z-index: 100000;
            transition: right 0.3s ease;
            overflow-y: auto;
            padding: 24px 20px;
        }
        #sideMenuDrawer.open {
            right: 0;
        }
        #sideMenuBackdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99999;
        }
        #sideMenuBackdrop.open {
            display: block;
        }
        .drawer-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #555;
        }

        /* Trending Tags Bar */
        .trending-topics-bar {
            background: #f8f8f8;
            border-bottom: 1px solid #eaeaea;
            padding: 8px 16px;
            font-size: 14px;
        }
        .trending-container {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .trending-label {
            font-weight: 700;
            color: #d60000;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .trending-tags-list {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .trending-tags-list a {
            color: #333;
            text-decoration: none;
        }
        .trending-tags-list a:hover {
            color: #d60000;
            text-decoration: underline;
        }

        /* Top utility bar */
        .top-utility-bar {
            border-bottom: 1px solid #f0f0f0;
            padding: 6px 16px;
            font-size: 13px;
            color: #666;
            background: #fff;
        }
        .top-utility-container {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Shared Responsive Layout Utilities */
        .palo-main-grid-layout {
            display: grid;
            grid-template-columns: 8fr 4fr;
            gap: 32px;
        }
        @media (max-width: 991px) {
            .palo-main-grid-layout {
                grid-template-columns: 1fr !important;
                gap: 24px;
            }
        }

        .palo-cards-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 640px) {
            .palo-cards-grid-2 {
                grid-template-columns: 1fr !important;
                gap: 14px;
            }
        }

        .palo-video-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        @media (max-width: 1024px) {
            .palo-video-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }
        }
        @media (max-width: 580px) {
            .palo-video-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }

        /* ========================================================
           HEADER & NAVBAR DEFINITIVE RESPONSIVE ARCHITECTURE
           ======================================================== */
        #header.header {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100vw !important;
            box-sizing: border-box !important;
            position: relative !important;
            background: #fff !important;
            z-index: 100 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
        }
        .header .null-wrapper,
        .header .QXmSm,
        .header .headroom-wrapper,
        .header .headroom {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: unset !important;
        }
        .wr1sa {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            max-width: 1240px !important;
            margin: 0 auto !important;
            padding: 10px 16px !important;
            box-sizing: border-box !important;
        }
        .desktop-logo img {
            height: 36px;
            width: auto;
            display: block;
        }

        /* Continuous category navigation row (.CrI-C) */
        .CrI-C {
            width: 100% !important;
            max-width: 100vw !important;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #dfdfdf;
            background: #fff;
            box-sizing: border-box !important;
            position: relative;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
        }
        .u0ucN {
            display: grid !important;
            grid-template-columns: 1fr auto !important;
            width: 100% !important;
            max-width: 1240px !important;
            margin: 0 auto !important;
            height: 44px;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }
        .smrJE {
            min-width: 0 !important;
            overflow: hidden !important;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .smrJE .navbar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .smrJE .navbar-list {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            height: 100%;
            align-items: center;
            margin: 0;
            padding: 0 12px;
            list-style: none;
            width: 100%;
            gap: 0;
        }
        .smrJE .navbar-list::-webkit-scrollbar {
            display: none !important;
        }
        .smrJE .menu-item {
            flex-shrink: 0 !important;
            white-space: nowrap !important;
            padding: 0 11px;
            font-size: 14.5px;
            font-weight: 600;
        }
        .smrJE .menu-item a {
            color: #222;
            text-decoration: none;
            padding: 12px 2px;
            display: block;
            line-height: 1;
            transition: color 0.15s ease;
        }
        .smrJE .menu-item:hover a,
        .smrJE .menu-item.active a {
            color: #d60000;
            font-weight: 700;
        }

        /* Right-pinned utilities in navbar */
        .v1dsw {
            display: flex !important;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            background: #fff;
            box-shadow: -4px 0 8px -2px rgba(0,0,0,0.1) !important;
            z-index: 5;
            flex-shrink: 0 !important;
            height: 100%;
            box-sizing: border-box;
        }
        .v1dsw .search {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #444;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .v1dsw ._3AN8h {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            color: #222;
        }
        .v1dsw ._3AN8h:hover {
            background: #f1f5f9;
        }
        .v1dsw ._3AN8h svg {
            width: 22px;
            height: 22px;
        }

        /* Header Login Button */
        .header-login-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 6px 14px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            line-height: 1 !important;
            white-space: nowrap !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-sizing: border-box !important;
            height: 36px !important;
        }
        .header-login-btn:hover {
            border-color: #d60000 !important;
            color: #d60000 !important;
        }
        .header-login-btn svg {
            width: 17px !important;
            height: 17px !important;
            fill: currentColor !important;
            flex-shrink: 0 !important;
        }

        /* Responsive Breakpoints for Header, Navbar & Footer */
        @media (max-width: 1024px) {
            .ewX0K {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .desktop-only-inline {
                display: none !important;
            }
            .top-utility-bar {
                display: none !important;
            }
            .v1dsw .search {
                display: none !important;
            }
            .v1dsw {
                padding: 0 10px;
                box-shadow: -3px 0 6px -2px rgba(0,0,0,0.15) !important;
            }
            .wr1sa {
                padding: 8px 12px !important;
            }
            .desktop-logo img {
                height: 28px !important;
                max-width: 120px;
            }
            .smrJE .menu-item {
                padding: 0 8px;
                font-size: 14px;
            }
            .header-login-btn {
                padding: 5px 10px !important;
                font-size: 13px !important;
                height: 32px !important;
                gap: 4px !important;
            }
            .header-login-btn svg {
                width: 15px !important;
                height: 15px !important;
            }
            .trending-container {
                overflow-x: auto;
                white-space: nowrap;
                flex-wrap: nowrap;
                scrollbar-width: none;
                padding-bottom: 2px;
            }
            .trending-container::-webkit-scrollbar {
                display: none;
            }
            .trending-tags-list {
                flex-wrap: nowrap;
            }
            .trending-label {
                font-size: 13px;
            }
            .print-footer-row1 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 16px;
            }
            .print-footer-row2 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 16px;
            }
            .flA-8 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 8px;
            }
            #sideMenuDrawer {
                width: 85vw;
                max-width: 320px;
            }
            .search-container {
                padding: 0 12px;
            }
            .search-input {
                font-size: 14px;
                padding: 10px 14px;
            }
            .search-btn {
                font-size: 14px;
                padding: 0 16px;
            }
        }

        /* Image fallbacks */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Utility Bar: Date, Location, Edition -->
    <div class="top-utility-bar print-none">
        <div class="top-utility-container">
            <div class="top-date-info">
                <span>{{ bn_date() }}</span>
                <span style="margin: 0 8px;">|</span>
                <span>ঢাকা</span>
            </div>
            <div class="top-utility-right" style="display: flex; align-items: center; gap: 16px;">
                <a href="{{ route('news.latest') }}" style="color: #d60000; font-weight: 700;">সর্বশেষ সংবাদ</a>
                <a href="{{ route('page.show', 'about') }}">আমাদের কথা</a>
                <a href="{{ route('page.show', 'contact') }}">যোগাযোগ</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header id="header" class="header" style="margin-top: 0px; position: relative;">
        <div class="null-wrapper vrm-K">
            <div class="QXmSm">
                <div class="headroom-wrapper" style="min-height: 120px;">
                    <div class="headroom headroom--unfixed">
                        <!-- Logo & Pinned Top Stories Row -->
                        <div class="wr1sa">
                            <!-- Logo -->
                            <a class="VDuHf sGZHG" aria-label="Prothom alo" href="{{ route('home') }}">
                                <h1 class="desktop-logo kWicl" style="margin: 0;">
                                    <img class="DxXuy" src="https://media.prothomalo.com/prothomalo-bangla/2025-11-17/qix5skks/palo-bangla.svg" alt="prothomalo.com logo" style="height: 38px; width: auto;">
                                </h1>
                            </a>

                            <!-- Top Pinned Headlines Strip (.ewX0K) -->
                            <div class="ewX0K">
                                @forelse($topHeaderStories as $hStory)
                                    <div class="EL6od">
                                        <a href="{{ route('news.show', [$hStory->public_id, $hStory->slug]) }}" aria-label="{{ $hStory->title }}">
                                            <div class="card-with-image-zoom">
                                                <img src="{{ $hStory->cover_image_url }}" alt="{{ $hStory->cover_alt ?? $hStory->title }}" loading="lazy">
                                            </div>
                                        </a>
                                        <a href="{{ route('news.show', [$hStory->public_id, $hStory->slug]) }}" style="flex: 1;">
                                            <p class="headline">
                                                @if($hStory->type === 'video')
                                                    <svg class="kma-J" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none"><path d="M14.5607 19.9999L19.7607 16.6666C20.0095 16.5066 20.134 16.2844 20.134 15.9999C20.134 15.7155 20.0095 15.4933 19.7607 15.3333L14.5607 11.9999C14.294 11.8221 14.0229 11.8088 13.7473 11.9599C13.4718 12.111 13.334 12.3466 13.334 12.6666V19.3333C13.334 19.6533 13.4718 19.8888 13.7473 20.0399C14.0229 20.191 14.294 20.1777 14.5607 19.9999ZM16.0007 26.6666C14.5251 26.6666 13.1384 26.3866 11.8407 25.8266C10.5429 25.2666 9.41398 24.5066 8.45398 23.5466C7.49398 22.5866 6.73398 21.4577 6.17398 20.1599C5.61398 18.8621 5.33398 17.4755 5.33398 15.9999C5.33398 14.5244 5.61398 13.1377 6.17398 11.8399C6.73398 10.5421 7.49398 9.41325 8.45398 8.45325C9.41398 7.49325 10.5429 6.73325 11.8407 6.17325C13.1384 5.61325 14.5251 5.33325 16.0007 5.33325C17.4762 5.33325 18.8629 5.61325 20.1607 6.17325C21.4584 6.73325 22.5873 7.49325 23.5473 8.45325C24.5073 9.41325 25.2673 10.5421 25.8273 11.8399C26.3873 13.1377 26.6673 14.5244 26.6673 15.9999C26.6673 17.4755 26.3873 18.8621 25.8273 20.1599C25.2673 21.4577 24.5073 22.5866 23.5473 23.5466C22.5873 24.5066 21.4584 25.2666 20.1607 25.8266C18.8629 26.3866 17.4762 26.6666 16.0007 26.6666ZM16.0007 24.5333C18.3829 24.5333 20.4007 23.7066 22.054 22.0533C23.7073 20.3999 24.534 18.3821 24.534 15.9999C24.534 13.6177 23.7073 11.5999 22.054 9.94659C20.4007 8.29325 18.3829 7.46659 16.0007 7.46659C13.6184 7.46659 11.6007 8.29325 9.94732 9.94659C8.29398 11.5999 7.46732 13.6177 7.46732 15.9999C7.46732 18.3821 8.29398 20.3999 9.94732 22.0533C11.6007 23.7066 13.6184 24.5333 16.0007 24.5333Z" fill="#D60000"></path></svg>
                                                @else
                                                    <svg class="kma-J" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none"><path fill="#D60000" d="M7.408 25.333c-.57 0-1.059-.203-1.465-.609a1.997 1.997 0 0 1-.609-1.465V10.815c0-.57.203-1.059.61-1.465.405-.406.894-.61 1.464-.61h3.267l1.296-1.4c.19-.224.42-.392.687-.505.268-.112.549-.168.843-.168h4.407c.294 0 .575.056.843.168.268.113.497.281.687.506l1.296 1.4h3.267c.57 0 1.058.203 1.464.61.407.405.61.893.61 1.464v12.444c0 .57-.203 1.059-.61 1.465-.406.406-.894.61-1.464.61H7.408zm16.593-2.074V10.815H7.408v12.444h16.593z"></path><circle cx="15.734" cy="17.067" r="3.333" fill="#D60000"></circle></svg>
                                                @endif
                                                {{ $hStory->title }}
                                            </p>
                                        </a>
                                    </div>
                                @empty
                                    <div class="EL6od">
                                        <p class="headline">সর্বশেষ খবর পড়তে আমাদের সঙ্গেই থাকুন</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Right search & login action -->
                            <div class="print-none ykk4R" style="display: flex; align-items: center; gap: 12px;">
                                <a href="javascript:void(0)" onclick="toggleSearch()" class="search _5ndd-" aria-label="Search">
                                    <div class="TDAsr">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="DSRqc" style="width: 20px; height: 20px;"><path fill-opacity="0.68" d="M25.2,27.067L17.733,19.6c-0.667,0.533-1.433,0.956-2.3,1.267c-0.867,0.311-1.789,0.467-2.767,0.467 c-2.422,0-4.472-0.839-6.15-2.517C4.839,17.139,4,15.089,4,12.667c0-2.422,0.839-4.472,2.517-6.15C8.194,4.839,10.244,4,12.667,4 c2.422,0,4.472,0.839,6.15,2.517c1.678,1.678,2.517,3.728,2.517,6.15c0,0.978-0.156,1.9-0.467,2.767 c-0.311,0.867-0.733,1.633-1.267,2.3l7.5,7.5c0.244,0.245,0.367,0.545,0.367,0.9c0,0.356-0.133,0.667-0.4,0.933 c-0.244,0.244-0.556,0.367-0.933,0.367C25.756,27.433,25.444,27.311,25.2,27.067z M12.667,18.667c1.667,0,3.083-0.583,4.25-1.75 c1.167-1.167,1.75-2.583,1.75-4.25c0-1.667-0.583-3.083-1.75-4.25c-1.167-1.167-2.583-1.75-4.25-1.75 c-1.667,0-3.083,0.583-4.25,1.75C7.25,9.583,6.667,11,6.667,12.667c0,1.667,0.583,3.083,1.75,4.25 C9.583,18.083,11,18.667,12.667,18.667z"></path></svg>
                                    </div>
                                </a>
                                <div class="_4XyKZ">
                                    @auth
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            @if(auth()->user()->isStaff())
                                                <a href="{{ route('admin.dashboard') }}" style="background: #d60000; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 3px rgba(214,0,0,0.3);">
                                                    <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                                                    <span class="desktop-only-inline">ড্যাশবোর্ড</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('bookmarks.index') }}" title="সংরক্ষিত সংবাদ" style="color: #475569; display: flex; align-items: center; text-decoration: none; padding: 6px;">
                                                <svg style="width: 18px; height: 18px; fill: #d60000;" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                                            </a>
                                            <span class="desktop-only-inline" style="font-size: 13px; font-weight: 700; color: #1e293b; max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</span>
                                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;" class="desktop-only-inline">
                                                @csrf
                                                <button type="submit" style="border: 1px solid #e2e8f0; background: #f8fafc; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; color: #64748b; cursor: pointer;">
                                                    লগআউট
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('login') }}" class="header-login-btn" aria-label="লগইন করুন">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                            <span>লগইন</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Main Navigation Bar (.CrI-C) -->
                        <div class="print-none CrI-C">
                            <div class="u0ucN" id="navbar">
                                <div class="smrJE">
                                    <nav class="navbar Xh8NA">
                                        <ul class="navbar-list jAzil">
                                            <li class="menu-item _1G1J7">
                                                <div><a href="{{ route('home') }}">প্রচ্ছদ</a></div>
                                            </li>
                                            <li class="menu-item _1G1J7">
                                                <div><a href="{{ route('news.latest') }}">সর্বশেষ</a></div>
                                            </li>
                                            @foreach($navCategories as $navCat)
                                                @if($navCat->slug !== 'latest')
                                                    <li class="menu-item _1G1J7 {{ request()->is('category/'.$navCat->slug) ? 'active' : '' }}">
                                                        <div><a href="{{ route('category.show', $navCat->slug) }}">{{ $navCat->name }}</a></div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </nav>
                                </div>
                                <div class="v1dsw">
                                    <a href="javascript:void(0)" onclick="toggleSearch()" class="search _5ndd-">
                                        <div class="TDAsr">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="DSRqc" style="width: 18px; height: 18px;"><path fill-opacity="0.68" d="M25.2,27.067L17.733,19.6c-0.667,0.533-1.433,0.956-2.3,1.267c-0.867,0.311-1.789,0.467-2.767,0.467 c-2.422,0-4.472-0.839-6.15-2.517C4.839,17.139,4,15.089,4,12.667c0-2.422,0.839-4.472,2.517-6.15C8.194,4.839,10.244,4,12.667,4 c2.422,0,4.472,0.839,6.15,2.517c1.678,1.678,2.517,3.728,2.517,6.15c0,0.978-0.156,1.9-0.467,2.767 c-0.311,0.867-0.733,1.633-1.267,2.3l7.5,7.5c0.244,0.245,0.367,0.545,0.367,0.9c0,0.356-0.133,0.667-0.4,0.933 c-0.244,0.244-0.556,0.367-0.933,0.367C25.756,27.433,25.444,27.311,25.2,27.067z M12.667,18.667c1.667,0,3.083-0.583,4.25-1.75 c1.167-1.167,1.75-2.583,1.75-4.25c0-1.667-0.583-3.083-1.75-4.25c-1.167-1.167-2.583-1.75-4.25-1.75 c-1.667,0-3.083,0.583-4.25,1.75C7.25,9.583,6.667,11,6.667,12.667c0,1.667,0.583,3.083,1.75,4.25 C9.583,18.083,11,18.667,12.667,18.667z"></path></svg>
                                            <span class="OAIj1">খুঁজুন</span>
                                        </div>
                                    </a>
                                    <div class="_3AN8h" onclick="toggleSideMenu()" style="cursor: pointer;" title="সকল বিভাগ">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="_3ylg7"><path fill-opacity="0.87" d="M4,18c-0.283,0-0.521-0.096-0.713-0.288C3.096,17.521,3,17.283,3,17s0.096-0.521,0.287-0.712 C3.479,16.096,3.717,16,4,16h16c0.283,0,0.521,0.096,0.712,0.288S21,16.717,21,17s-0.096,0.521-0.288,0.712S20.283,18,20,18H4z M4,13c-0.283,0-0.521-0.096-0.713-0.288C3.096,12.521,3,12.283,3,12s0.096-0.521,0.287-0.712C3.479,11.096,3.717,11,4,11h16 c0.283,0,0.521,0.096,0.712,0.288S21,11.717,21,12s-0.096,0.521-0.288,0.712S20.283,13,20,13H4z M4,8 C3.717,8,3.479,7.904,3.287,7.713C3.096,7.521,3,7.283,3,7s0.096-0.521,0.287-0.713C3.479,6.096,3.717,6,4,6h16 c0.283,0,0.521,0.096,0.712,0.287S21,6.717,21,7s-0.096,0.521-0.288,0.713S20.283,8,20,8H4z"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Search Form -->
                        <div id="searchDrawer">
                            <form action="{{ route('search') }}" method="GET" class="search-container">
                                <input type="text" name="q" class="search-input" placeholder="যেকোনো খবর খুঁজতে এখানে লিখুন..." value="{{ request('q') }}" required>
                                <button type="submit" class="search-btn">অনুসন্ধান</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Trending Tags Bar -->
    @if(isset($trendingTags) && $trendingTags->isNotEmpty())
        <div class="trending-topics-bar print-none">
            <div class="trending-container">
                <div class="trending-label">
                    <span>আলোচিত বিষয়:</span>
                </div>
                <ul class="trending-tags-list">
                    @foreach($trendingTags as $tag)
                        <li>
                            <a href="{{ route('tag.show', $tag->slug) }}">#{{ $tag->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Breaking News Ticker (if active) -->
    @if(isset($breakingNews) && $breakingNews)
        <div class="breaking-news-bar" style="background: #fff5f5; border-bottom: 1px solid #fed7d7; padding: 8px 15px;">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 12px; font-size: 14px;">
                <span style="background: #d60000; color: #fff; padding: 2px 8px; border-radius: 3px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                    <span style="width: 6px; height: 6px; background: #fff; border-radius: 50%; display: inline-block;"></span>
                    ব্রেকিং
                </span>
                <a href="{{ $breakingNews->url ?? ($breakingNews->news ? route('news.show', ['public_id' => $breakingNews->news->public_id, 'slug' => $breakingNews->news->slug]) : '#') }}" style="color: #9b2c2c; font-weight: 700; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $breakingNews->label ?? ($breakingNews->news ? $breakingNews->news->title : '') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main id="container" class="main-content-wrapper">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer id="footer" class="show-anchor" style="margin-top: 40px; background: #fff; border-top: 1px solid #e0e0e0;">
        <div class="_2XFBv" style="max-width: 1240px; margin: 0 auto; padding: 32px 16px;">
            <!-- Row 1: Logo & Network Links -->
            <div class="print-footer-row1 _0twTb" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 24px;">
                <div class="bGsjq">
                    <a aria-label="Prothom alo logo" href="{{ route('home') }}">
                        <img src="https://media.prothomalo.com/prothomalo-bangla/2025-11-17/qix5skks/palo-bangla.svg" alt="logo" style="height: 32px; width: auto;">
                    </a>
                </div>
                <div class="print-footer-links fjiQ1" style="border-bottom: none; padding: 0;">
                    <div class="footer-menu-group" style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <a href="{{ route('category.show', 'bangladesh') }}" class="footer-list-menu-item jxJws">বাংলাদেশ</a>
                        <a href="{{ route('category.show', 'international') }}" class="footer-list-menu-item jxJws">আন্তর্জাতিক</a>
                        <a href="{{ route('category.show', 'sports') }}" class="footer-list-menu-item jxJws">খেলা</a>
                        <a href="{{ route('category.show', 'entertainment') }}" class="footer-list-menu-item jxJws">বিনোদন</a>
                        <a href="{{ route('category.show', 'opinion') }}" class="footer-list-menu-item jxJws">মতামত</a>
                        <a href="{{ route('category.show', 'business') }}" class="footer-list-menu-item jxJws">বাণিজ্য</a>
                        <a href="{{ route('category.show', 'technology') }}" class="footer-list-menu-item jxJws">বিজ্ঞান ও প্রযুক্তি</a>
                        <a href="{{ route('news.latest') }}" class="footer-list-menu-item jxJws">সর্বশেষ খবর</a>
                    </div>
                </div>
            </div>

            <!-- Row 2: Social Links & App Links -->
            <div class="print-footer-row2 mRqzu" style="display: flex; justify-content: space-between; align-items: center; padding: 24px 0; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; gap: 20px;">
                <div class="ehJja" style="display: flex; align-items: center; gap: 16px;">
                    <span class="_4xWff" style="font-weight: 700;">অনুসরণ করুন:</span>
                    <div class="social-links__items YD17W" style="display: flex; gap: 12px;">
                        <a aria-label="Facebook" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com" class="ppHjf">
                            <svg xmlns="http://www.w3.org/2000/svg" height="28" width="28" viewBox="0 0 24 24"><circle fill="#1877F2" cx="12" cy="12" r="10"></circle><path fill="#FFFFFF" d="M13.25,12.938h1.562l0.625-2.5H13.25v-1.25c0-0.644,0-1.25,1.25-1.25h0.938v-2.1 c-0.204-0.027-0.973-0.088-1.786-0.088c-1.697,0-2.902,1.036-2.902,2.937v1.75H8.875v2.5h1.875v5.313h2.5V12.938z"></path></svg>
                        </a>
                        <a aria-label="Twitter" target="_blank" rel="noopener noreferrer" href="https://twitter.com" class="ppHjf">
                            <svg xmlns="http://www.w3.org/2000/svg" height="28" width="28" viewBox="0 0 24 24"><circle fill="#0F1419" cx="12" cy="12" r="10"></circle><path fill="#FFFFFF" d="M15.531,7h1.662l-3.63,4.236L17.833,17h-3.343l-2.62-3.495L8.876,17H7.212l3.882-4.531L7,7h3.427 l2.366,3.195L15.531,7z M14.947,15.986h0.92L9.926,7.962H8.937L14.947,15.986z"></path></svg>
                        </a>
                        <a aria-label="YouTube" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com" class="ppHjf">
                            <svg xmlns="http://www.w3.org/2000/svg" height="28" width="28" viewBox="0 0 24 24"><path fill="#FF0000" d="M12,2c5.523,0,10,4.477,10,10c0,5.523-4.477,10-10,10C6.477,22,2,17.523,2,12C2,6.477,6.477,2,12,2z"></path><path fill="#FFFFFF" d="M17.964,8.561C18.25,9.675,18.25,12,18.25,12s0,2.325-0.286,3.439c-0.159,0.616-0.623,1.1-1.211,1.264 C15.685,17,12,17,12,17s-3.683,0-4.753-0.298c-0.591-0.166-1.054-0.65-1.211-1.264C5.75,14.325,5.75,12,5.75,12s0-2.325,0.286-3.439 c0.159-0.616,0.623-1.1,1.211-1.264C8.317,7,12,7,12,7s3.685,0,4.753,0.298C17.344,7.464,17.808,7.948,17.964,8.561z M10.75,14.188 L14.5,12l-3.75-2.188V14.188z"></path></svg>
                        </a>
                    </div>
                </div>
                <div class="PTRef">
                    <div class="footer-menu-group" style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 14px;">
                        <a href="{{ route('page.show', 'about') }}" class="footer-list-menu-item jxJws">আমাদের সম্পর্কে</a>
                        <a href="{{ route('page.show', 'terms') }}" class="footer-list-menu-item jxJws">নীতি ও শর্ত</a>
                        <a href="{{ route('page.show', 'privacy') }}" class="footer-list-menu-item jxJws">গোপনীয়তা নীতি</a>
                        <a href="{{ route('page.show', 'contact') }}" class="footer-list-menu-item jxJws">যোগাযোগ</a>
                    </div>
                </div>
            </div>

            <!-- Row 3: Editorial & Copyright Attribution -->
            <div class="flA-8 _2SGVH" style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; font-size: 13px; color: #777; flex-wrap: wrap; gap: 10px;">
                <div class="_37nmw">স্বত্ব © ২০২৬ এইচ টেক প্রোভিশন আইটি (H Tech Provision IT)</div>
                <div class="print-default-editor AQWAe"><span>সম্পাদক ও প্রকাশক: এইচ টেক প্রোভিশন আইটি</span></div>
            </div>
        </div>
    </footer>

    <!-- Slide-out Side Menu Drawer -->
    <div id="sideMenuBackdrop" onclick="toggleSideMenu()"></div>
    <div id="sideMenuDrawer">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <img src="https://media.prothomalo.com/prothomalo-bangla/2025-11-17/qix5skks/palo-bangla.svg" alt="logo" style="height: 30px; width: auto;">
            <button class="drawer-close" onclick="toggleSideMenu()" style="position: static; font-size: 26px; line-height: 1; padding: 4px;">&times;</button>
        </div>

        <div style="font-size: 13px; color: #64748b; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
            📅 {{ bn_date() }} | ঢাকা
        </div>

        <!-- In-drawer Search Form -->
        <form action="{{ route('search') }}" method="GET" style="display: flex; gap: 8px; margin-bottom: 18px;">
            <input type="text" name="q" placeholder="খবর খুঁজুন..." style="flex: 1; min-width: 0; padding: 8px 12px; font-size: 14px; border: 1px solid #ddd; border-radius: 4px; outline: none; font-family: inherit;" required>
            <button type="submit" style="background: #d60000; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-weight: 700; cursor: pointer; font-family: inherit;">খুঁজুন</button>
        </form>

        @auth
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 20px;">
                <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                    👤 {{ auth()->user()->name }}
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @if(auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" style="background: #d60000; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; text-decoration: none;">ড্যাশবোর্ড</a>
                    @endif
                    <a href="{{ route('bookmarks.index') }}" style="background: #e2e8f0; color: #334155; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; text-decoration: none;">সংরক্ষিত সংবাদ</a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" style="background: #fee2e2; color: #991b1b; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">লগআউট</button>
                    </form>
                </div>
            </div>
        @else
            <div style="margin-bottom: 20px;">
                <a href="{{ route('login') }}" style="display: block; text-align: center; background: #d60000; color: #fff; padding: 10px; border-radius: 4px; font-weight: 700; text-decoration: none;">লগইন করুন</a>
            </div>
        @endauth

        <h3 style="font-size: 17px; border-bottom: 2px solid #d60000; padding-bottom: 6px; margin-bottom: 14px; color: #111;">সকল বিভাগ</h3>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 15px;">
            <li><a href="{{ route('home') }}" style="font-weight: 700; color: #d60000;">প্রচ্ছদ</a></li>
            <li><a href="{{ route('news.latest') }}" style="font-weight: 700;">সর্বশেষ</a></li>
            @foreach($navCategories as $cat)
                @if($cat->slug !== 'latest')
                    <li>
                        <a href="{{ route('category.show', $cat->slug) }}" style="display: block; padding: 3px 0; color: #333;">
                            {{ $cat->name }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #eee; font-size: 13.5px;">
            <a href="{{ route('page.show', 'about') }}" style="display: block; margin-bottom: 8px; color: #666;">আমাদের কথা</a>
            <a href="{{ route('page.show', 'contact') }}" style="display: block; margin-bottom: 8px; color: #666;">যোগাযোগ</a>
            <a href="{{ route('page.show', 'terms') }}" style="display: block; color: #666;">ব্যবহারের শর্তাবলি</a>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSearch() {
            var drawer = document.getElementById('searchDrawer');
            if (drawer) {
                drawer.classList.toggle('active');
                if (drawer.classList.contains('active')) {
                    drawer.querySelector('input').focus();
                }
            }
        }

        function toggleSideMenu() {
            var menu = document.getElementById('sideMenuDrawer');
            var backdrop = document.getElementById('sideMenuBackdrop');
            if (menu && backdrop) {
                menu.classList.toggle('open');
                backdrop.classList.toggle('open');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
