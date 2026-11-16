<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#171d2b">
    <title>লগইন | এইচ টেক প্রোভিশন নিউজ</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #202638;
            --muted: #788196;
            --red: #c62828;
            --red-hover: #b91c1c;
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
        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
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
        .aside-copy h1 { margin: 0 0 14px; font-size: clamp(28px, 3.6vw, 38px); line-height: 1.25; }
        .aside-copy p { margin: 0; color: #bdc5d2; font-size: 14px; line-height: 1.8; }
        .aside-footer {
            color: #8792a6;
            font-size: 11.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .aside-footer a {
            color: #ffb6b6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }
        .aside-footer a:hover { color: #fff; }
        .login-main { display: flex; align-items: center; justify-content: center; padding: 52px; }
        .login-content { width: 100%; max-width: 380px; }
        .mobile-brand { display: none; }
        .welcome-kicker { margin-bottom: 8px; color: var(--red); font-size: 12px; font-weight: 750; }
        .login-title { margin: 0; color: var(--ink); font-size: 28px; letter-spacing: -.03em; }
        .login-description { margin: 9px 0 24px; color: var(--muted); font-size: 13px; line-height: 1.65; }
        
        .alert-error {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            color: #991b1b;
            background: #fff2f2;
            font-size: 13px;
        }
        .alert-success {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            color: #047857;
            background: #ecfdf5;
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
        .input-wrap-pw .form-control { padding-right: 44px; }
        .pw-eye-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #8892a4;
            padding: 6px;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .15s ease, background .15s ease;
        }
        .pw-eye-btn:hover { color: var(--ink); background: #f0f2f6; }
        .pw-eye-btn:focus { outline: 2px solid var(--red); outline-offset: 1px; }

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
            font-size: 13.5px;
            font-weight: 750;
            text-decoration: none;
            cursor: pointer;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(100deg, #b9252e, #d33b3b);
            box-shadow: 0 8px 18px rgba(198,40,40,.19);
        }
        .btn-primary:hover { box-shadow: 0 11px 22px rgba(198,40,40,.25); }
        
        /* Demo Super Admin Button */
        .btn-demo {
            margin-top: 14px;
            border: 1px solid #fecaca;
            color: #991b1b;
            background: #fffafa;
            font-weight: 700;
        }
        .btn-demo:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            box-shadow: 0 4px 12px rgba(198,40,40,0.1);
        }
        .demo-note { margin: 10px 0 0; color: #9299a8; font-size: 11px; line-height: 1.6; text-align: center; }

        .login-bottom {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #eef0f4;
            color: #64748b;
            font-size: 12.5px;
            text-align: center;
        }
        .register-link {
            color: var(--red);
            font-weight: 750;
            text-decoration: none;
            transition: color 0.15s;
        }
        .register-link:hover {
            color: var(--red-hover);
            text-decoration: underline;
        }

        @media (max-width: 760px) {
            body { padding: 18px; }
            .login-shell { display: block; width: min(100%, 470px); min-height: auto; border-radius: 18px; }
            .login-aside { display: none; }
            .login-main { padding: 34px 28px; }
            .mobile-brand { display: flex; align-items: center; gap: 11px; margin-bottom: 30px; }
            .mobile-brand .brand-name { color: var(--ink); }
            .mobile-brand .brand-caption { color: var(--muted); }
        }
        @media (max-width: 380px) {
            body { padding: 12px; }
            .login-main { padding: 26px 18px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <!-- Left Side: Dark Aside Panel (Prothom Alo / H Tech Style) -->
        <aside class="login-aside" aria-label="এইচ টেক প্রোভিশন নিউজ">
            <a href="{{ route('home') }}" class="brand-lockup">
                <span class="brand-mark" aria-hidden="true">HT</span>
                <div>
                    <div class="brand-name">এইচ টেক প্রোভিশন</div>
                    <div class="brand-caption">অনলাইন নিউজ পোর্টাল</div>
                </div>
            </a>
            <div class="aside-copy">
                <div class="aside-label">খবরের সাথে থাকুন</div>
                <h1>আপনার পছন্দের সংবাদ,<br>সবার আগে।</h1>
                <p>বিশ্বাসযোগ্য সংবাদ বিশ্লেষণ, বুকমার্ক এবং নিজস্ব সম্পাদকীয় অভিজ্ঞতার জন্য লগইন করে সংযুক্ত থাকুন।</p>
            </div>
            <div class="aside-footer">
                <span>© {{ date('Y') }} এইচ টেক প্রোভিশন</span>
                <a href="{{ route('home') }}">← মূল পাতা</a>
            </div>
        </aside>

        <!-- Right Side: White Login Form Section -->
        <section class="login-main" aria-labelledby="login-title">
            <div class="login-content">
                <div class="mobile-brand">
                    <span class="brand-mark" aria-hidden="true">HT</span>
                    <div>
                        <div class="brand-name">এইচ টেক প্রোভিশন</div>
                        <div class="brand-caption">অনলাইন নিউজ পোর্টাল</div>
                    </div>
                </div>
                
                <div class="welcome-kicker">স্বাগতম</div>
                <h2 class="login-title" id="login-title">অ্যাকাউন্টে প্রবেশ করুন</h2>
                <p class="login-description">পোর্টালে প্রবেশ করতে আপনার নিবন্ধিত ইমেইল ও পাসওয়ার্ড লিখুন।</p>

                @if(session('success'))
                    <div class="alert-success" role="alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form id="frontendLoginForm" action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label" for="email">ইমেইল ঠিকানা</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control" placeholder="admin@htechprovision.com">
                        </div>
                    </div>

                    <!-- Password Field with Interactive Eye Toggle Button -->
                    <div class="form-group">
                        <label class="form-label" for="password">পাসওয়ার্ড</label>
                        <div class="input-wrap input-wrap-pw">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                <circle cx="12" cy="15.5" r="1.5"></circle>
                            </svg>
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" placeholder="আপনার গোপন পাসওয়ার্ড দিন">
                            
                            <button type="button" class="pw-eye-btn" id="togglePasswordBtn" aria-label="পাসওয়ার্ড দেখুন" title="পাসওয়ার্ড দেখুন / লুকান">
                                <svg id="userEye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="userEyeOff" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="remember-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" value="1"> আমাকে মনে রাখুন
                        </label>
                    </div>

                    <!-- Primary Submit Button -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">প্রবেশ করুন</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"></path>
                        </svg>
                    </button>

                    <!-- Single 1-Click Super Admin Demo Login -->
                    <button type="button" class="btn btn-demo" id="demoAdminBtn" onclick="quickAdminLogin()">
                        👑 সুপার অ্যাডমিন হিসেবে ডেমো প্রবেশ
                    </button>
                    <p class="demo-note">ডেমো বোতামে ক্লিক করলেই সুপার অ্যাডমিন অ্যাকাউন্টে (admin@htechprovision.com) স্বয়ংক্রিয়ভাবে লগইন হবে।</p>
                </form>

                <!-- Register Link for Readers -->
                <div class="login-bottom">
                    এখনও কোনো অ্যাকাউন্ট নেই? 
                    <a href="{{ route('register') }}" class="register-link">বিনামূল্যে নিবন্ধন করুন</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Password Eye Toggle
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function () {
                const pwInput = document.getElementById('password');
                const eyeIcon = document.getElementById('userEye');
                const eyeOffIcon = document.getElementById('userEyeOff');
                
                if (pwInput.type === 'password') {
                    pwInput.type = 'text';
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                    togglePasswordBtn.setAttribute('aria-label', 'পাসওয়ার্ড লুকান');
                } else {
                    pwInput.type = 'password';
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                    togglePasswordBtn.setAttribute('aria-label', 'পাসওয়ার্ড দেখুন');
                }
                pwInput.focus();
            });
        }

        // 1-Click Super Admin Demo Login
        function quickAdminLogin() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const form = document.getElementById('frontendLoginForm');
            const demoBtn = document.getElementById('demoAdminBtn');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');

            emailInput.value = 'admin@htechprovision.com';
            passwordInput.value = 'Admin@123456';

            if (demoBtn) {
                demoBtn.textContent = 'লগইন হচ্ছে... ⏳';
                demoBtn.disabled = true;
            }
            if (submitBtnText) {
                submitBtnText.textContent = 'লগইন হচ্ছে...';
            }
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            form.requestSubmit();
        }
    </script>
</body>
</html>
