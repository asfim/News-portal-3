<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ড্যাশবোর্ড') | এইচ টেক প্রোভিশন অ্যাডমিন</title>

    <style>
        :root {
            --primary: #d60000;
            --primary-hover: #b00000;
            --sidebar-bg: #1e2229;
            --sidebar-active: #2b313a;
            --sidebar-text: #abb4be;
            --topbar-bg: #ffffff;
            --body-bg: #f4f6f9;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 16px 0;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-item {
            margin-bottom: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: var(--sidebar-active);
            border-left: 4px solid var(--primary);
        }

        /* Content Area */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: 64px;
            background-color: var(--topbar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .role-badge {
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .content-area {
            padding: 28px;
            flex: 1;
        }

        /* Common Elements */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-success {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background-color: #047857;
        }

        .btn-danger {
            background-color: #dc2626;
            color: #ffffff;
        }
        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            outline: none;
            font-family: inherit;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(214,0,0,0.1);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .table th {
            background-color: #f9fafb;
            padding: 12px 16px;
            font-weight: 600;
            color: #4b5563;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tr:hover td {
            background-color: #fbfbfb;
        }

        /* Alerts */
        .alert {
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        .status-published { background: #d1fae5; color: #065f46; }
        .status-draft { background: #f3f4f6; color: #4b5563; }
        .status-review_pending { background: #fef3c7; color: #92400e; }
        .status-scheduled { background: #e0e7ff; color: #3730a3; }
        .status-archived { background: #fee2e2; color: #991b1b; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span style="color: var(--primary); font-size: 22px;">■</span>
            <span>এইচ টেক CMS</span>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->is('admin') || request()->is('admin/dashboard') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                    <span>ড্যাশবোর্ড</span>
                </a>
            </li>
            
            <li style="padding: 14px 20px 6px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">সম্পাদকীয় ও সংবাদ</li>

            <li class="sidebar-item">
                <a href="{{ route('admin.news.index') }}" class="sidebar-link {{ request()->is('admin/news*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    <span>সংবাদ ব্যবস্থাপনা</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.breaking-news.index') }}" class="sidebar-link {{ request()->is('admin/breaking-news*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                    <span>ব্রেকিং নিউজ</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->is('admin/comments*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg>
                    <span>পাঠকের মন্তব্য</span>
                </a>
            </li>

            <li style="padding: 14px 20px 6px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">ফ্রন্টএন্ড ও বিন্যাস</li>

            <li class="sidebar-item">
                <a href="{{ route('admin.homepage-sections.index') }}" class="sidebar-link {{ request()->is('admin/homepage-sections*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M4 6h16v4H4zm0 8h16v4H4z"/></svg>
                    <span>হোমপেজ সেকশন</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.advertisements.index') }}" class="sidebar-link {{ request()->is('admin/advertisements*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8h16v10z"/></svg>
                    <span>বিজ্ঞাপন স্লট</span>
                </a>
            </li>

            <li style="padding: 14px 20px 6px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">ট্যাক্সোনমি ও মিডিয়া</li>

            <li class="sidebar-item">
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                    <span>ক্যাটাগরি মেনু</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.tags.index') }}" class="sidebar-link {{ request()->is('admin/tags*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>
                    <span>ট্রেন্ডিং টপিক ট্যাগ</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.locations.index') }}" class="sidebar-link {{ request()->is('admin/locations*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <span>বিভাগ ও জেলা</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.authors.index') }}" class="sidebar-link {{ request()->is('admin/authors*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <span>লেখক ও রিপোর্টার</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->is('admin/media*') ? 'active' : '' }}">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    <span>মিডিয়া লাইব্রেরি</span>
                </a>
            </li>

            <li class="sidebar-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 10px;">
                <a href="{{ route('home') }}" target="_blank" class="sidebar-link" style="color: #38bdf8;">
                    <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
                    <span>লাইভ ওয়েবসাইট দেখুন</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div>
                <span style="font-size: 13px; color: var(--text-muted);">{{ bn_date() }} | ঢাকা</span>
            </div>
            <div class="topbar-right">
                <span class="role-badge">
                    {{ auth()->user()->roles->first()?->name ?? 'Staff' }}
                </span>
                <span style="font-size: 14px; font-weight: 600;">
                    {{ auth()->user()->name }}
                </span>
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 5px 12px;">লগআউট</button>
                </form>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

