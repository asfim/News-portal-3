<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#171d2b">
    <title>অ্যাডমিন লগইন | এইচ টেক প্রোভিশন নিউজ</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #202638;
            --muted: #788196;
            --red: #c62828;
            --line: #e8eaf0;
        }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 12% 10%, rgba(198,40,40,.14), transparent 30%),
                radial-gradient(ellipse at 88% 90%, rgba(57,82,132,.12), transparent 28%),
                #f4f5f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        .login-shell {
            display: grid;
            grid-template-columns: minmax(300px, .92fr) minmax(380px, 1.08fr);
            width: min(100%, 980px);
            min-height: 590px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.8);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 28px 80px rgba(27,34,50,.16);
        }
        .login-aside {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 38px;
            color: #fff;
            background: linear-gradient(145deg, #242c3e 0%, #171d2b 68%, #351b29 100%);
        }
        .login-aside::before,
        .login-aside::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            right: -180px;
            bottom: 30px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
            box-shadow: 0 0 0 30px rgba(255,255,255,.035), 0 0 0 62px rgba(255,255,255,.025);
        }
        .login-aside::after {
            width: 220px;
            height: 220px;
            right: -110px;
            bottom: 80px;
            border-color: rgba(235,81,81,.28);
        }
        .brand-lockup, .aside-copy, .aside-footer { position: relative; z-index: 1; }
        .brand-lockup { display: flex; align-items: center; gap: 12px; }
        .brand-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(145deg, #ef6262, #c62828);
            box-shadow: 0 8px 18px rgba(198,40,40,.28);
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -.06em;
        }
        .brand-name { font-size: 15px; font-weight: 750; letter-spacing: .01em; }
        .brand-caption { margin-top: 2px; color: #aab4c6; font-size: 11px; }
        .aside-copy { max-width: 360px; margin: auto 0; padding: 48px 0; }
        .aside-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #ffb6b6;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .aside-label::before {
            content: "";
            width: 18px;
            height: 2px;
            background: #ed6464;
        }
        .aside-copy h1 { margin: 0 0 14px; font-size: clamp(29px, 3.8vw, 40px); line-height: 1.2; }
        .aside-copy p { margin: 0; color: #bdc5d2; font-size: 14px; line-height: 1.8; }
        .aside-footer { color: #8792a6; font-size: 11px; }
        .login-main { display: flex; align-items: center; justify-content: center; padding: 52px; }
        .login-content { width: 100%; max-width: 370px; }
        .mobile-brand { display: none; }
        .welcome-kicker { margin-bottom: 8px; color: var(--red); font-size: 12px; font-weight: 750; }
        .login-title { margin: 0; color: var(--ink); font-size: 28px; letter-spacing: -.03em; }
        .login-description { margin: 9px 0 27px; color: var(--muted); font-size: 13px; line-height: 1.65; }
        .alert-error {
            display: flex;
            gap: 9px;
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            color: #991b1b;
            background: #fff2f2;
            font-size: 13px;
        }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 7px; color: #384155; font-size: 12px; font-weight: 700; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            width: 17px;
            height: 17px;
            transform: translateY(-50%);
            color: #9aa2b2;
            pointer-events: none;
        }
        .form-control {
            display: block;
            width: 100%;
            min-height: 48px;
            padding: 12px 14px 12px 42px;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            outline: none;
            color: var(--ink);
            background: #fff;
            font: inherit;
            font-size: 13px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .form-control::placeholder { color: #b0b6c2; }
        .form-control:focus {
            border-color: #d76666;
            box-shadow: 0 0 0 4px rgba(198,40,40,.09);
        }
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin: 2px 0 21px; }
        .remember-label { display: inline-flex; align-items: center; gap: 8px; color: #5c6577; font-size: 12px; cursor: pointer; }
        .remember-label input { width: 15px; height: 15px; margin: 0; accent-color: var(--red); }
        .btn {
            display: inline-flex;
            width: 100%;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 10px;
            font: inherit;
            font-size: 13px;
            font-weight: 750;
            text-decoration: none;
            cursor: pointer;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(100deg, #b9252e, #d33b3b);
            box-shadow: 0 8px 18px rgba(198,40,40,.19);
        }
        .btn-primary:hover { box-shadow: 0 11px 22px rgba(198,40,40,.25); }
        .btn-demo {
            margin-top: 12px;
            border: 1px solid #e8dada;
            color: #9e2930;
            background: #fff;
        }
        .btn-demo:hover { background: #fff7f6; }
        .demo-note { margin: 12px 0 0; color: #9299a8; font-size: 11px; line-height: 1.6; text-align: center; }
        .login-bottom { margin-top: 26px; padding-top: 18px; border-top: 1px solid #eef0f4; color: #9aa1af; font-size: 11px; text-align: center; }
        @media (max-width: 760px) {
            body { padding: 18px; }
            .login-shell { display: block; width: min(100%, 470px); min-height: auto; border-radius: 18px; }
            .login-aside { display: none; }
            .login-main { padding: 34px 28px; }
            .mobile-brand { display: flex; align-items: center; gap: 11px; margin-bottom: 34px; }
            .mobile-brand .brand-name { color: var(--ink); }
            .mobile-brand .brand-caption { color: var(--muted); }
        }
        @media (max-width: 380px) {
            body { padding: 12px; }
            .login-main { padding: 27px 20px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <aside class="login-aside" aria-label="এইচ টেক প্রোভিশন নিউজ">
            <div class="brand-lockup">
                <span class="brand-mark" aria-hidden="true">HT</span>
                <div>
                    <div class="brand-name">এইচ টেক প্রোভিশন</div>
                    <div class="brand-caption">নিউজ পোর্টাল · অ্যাডমিন</div>
                </div>
            </div>
            <div class="aside-copy">
                <div class="aside-label">সম্পাদকীয় ব্যবস্থাপনা</div>
                <h1>খবরের পেছনের<br>কন্ট্রোল রুম।</h1>
                <p>সংবাদ, পাঠক ও প্রকাশনার সবকিছু পরিচালনা করুন একটি নিরাপদ ও সহজ ড্যাশবোর্ড থেকে।</p>
            </div>
            <div class="aside-footer">© {{ date('Y') }} এইচ টেক প্রোভিশন নিউজ</div>
        </aside>

        <section class="login-main" aria-labelledby="login-title">
            <div class="login-content">
                <div class="mobile-brand">
                    <span class="brand-mark" aria-hidden="true">HT</span>
                    <div>
                        <div class="brand-name">এইচ টেক প্রোভিশন</div>
                        <div class="brand-caption">নিউজ পোর্টাল · অ্যাডমিন</div>
                    </div>
                </div>
                <div class="welcome-kicker">স্বাগতম</div>
                <h2 class="login-title" id="login-title">আপনার অ্যাকাউন্টে প্রবেশ করুন</h2>
                <p class="login-description">অ্যাডমিন প্যানেলে যেতে আপনার নিবন্ধিত ইমেইল ও পাসওয়ার্ড দিন।</p>

                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <span aria-hidden="true">!</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form id="adminLoginForm" action="{{ route('admin.login.submit') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">ইমেইল ঠিকানা</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control" placeholder="admin@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">পাসওয়ার্ড</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v3"/></svg>
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" placeholder="আপনার পাসওয়ার্ড লিখুন">
                        </div>
                    </div>
                    <div class="remember-row">
                        <label class="remember-label"><input type="checkbox" name="remember" value="1"> আমাকে মনে রাখুন</label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        প্রবেশ করুন
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <button type="button" class="btn btn-demo" id="demoLoginButton">
                        ডেমো অ্যাডমিন হিসেবে প্রবেশ
                    </button>
                    <p class="demo-note">ডেমো বোতামটি seeded অ্যাডমিন অ্যাকাউন্ট দিয়ে সাধারণ লগইন সম্পন্ন করবে।</p>
                </form>

                <div class="login-bottom">শুধুমাত্র অনুমোদিত সম্পাদক ও প্রশাসকদের জন্য</div>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('demoLoginButton').addEventListener('click', function () {
            document.getElementById('email').value = 'admin@htechprovision.com';
            document.getElementById('password').value = 'Admin@123456';
            document.getElementById('adminLoginForm').requestSubmit();
        });
    </script>
</body>
</html>
