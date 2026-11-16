@extends('layouts.frontend')

@section('title', 'লগইন - এইচ টেক প্রোভিশন আইটি')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card">
        <!-- Brand & Welcoming Header -->
        <div class="auth-header">
            <div class="auth-brand-badge">
                <span>HT</span>
            </div>
            <span class="auth-kicker">অ্যাকাউন্ট অ্যাক্সেস</span>
            <h1 class="auth-title">পোর্টালে লগইন করুন</h1>
            <p class="auth-subtitle">এইচ টেক প্রোভিশন নিউজ পোর্টালে আপনার অ্যাকাউন্টে প্রবেশ করে সংবাদ বিশ্লেষণ, বুকমার্ক ও সম্পাদকীয় সুবিধা উপভোগ করুন।</p>
        </div>

        @if(session('success'))
            <div class="auth-alert auth-alert-success" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="auth-alert auth-alert-error" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Quick 1-Click Demo Login Section -->
        <div class="demo-section">
            <div class="demo-section-header">
                <span class="demo-bolt">⚡</span>
                <span class="demo-section-title">এক ক্লিকেই ডেমো লগইন</span>
            </div>
            <p class="demo-section-desc">যেকোনো একটি অ্যাকাউন্টে ক্লিক করলেই স্বয়ংক্রিয়ভাবে লগইন হয়ে যাবে:</p>

            <div class="demo-grid">
                <!-- Demo 1: Super Admin -->
                <button type="button" class="demo-card demo-card-admin" onclick="quickLogin('admin@htechprovision.com', 'Admin@123456', this)">
                    <div class="demo-card-content">
                        <div class="demo-card-head">
                            <span class="demo-badge demo-badge-admin">👑 অ্যাডমিন</span>
                            <span class="demo-arrow">লগইন →</span>
                        </div>
                        <div class="demo-role-name">সুপার অ্যাডমিনিস্ট্রেটর</div>
                        <div class="demo-email">admin@htechprovision.com</div>
                    </div>
                </button>

                <!-- Demo 2: Editor -->
                <button type="button" class="demo-card demo-card-editor" onclick="quickLogin('editor@htechprovision.com', 'Editor@123456', this)">
                    <div class="demo-card-content">
                        <div class="demo-card-head">
                            <span class="demo-badge demo-badge-editor">✍️ সম্পাদক</span>
                            <span class="demo-arrow">লগইন →</span>
                        </div>
                        <div class="demo-role-name">প্রধান বার্তা সম্পাদক</div>
                        <div class="demo-email">editor@htechprovision.com</div>
                    </div>
                </button>

                <!-- Demo 3: Reader -->
                <button type="button" class="demo-card demo-card-reader" onclick="quickLogin('reader_test@htechprovision.com', 'Secret@123', this)">
                    <div class="demo-card-content">
                        <div class="demo-card-head">
                            <span class="demo-badge demo-badge-reader">👤 পাঠক</span>
                            <span class="demo-arrow">লগইন →</span>
                        </div>
                        <div class="demo-role-name">নিবন্ধিত সাধারণ পাঠক</div>
                        <div class="demo-email">reader_test@htechprovision.com</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Divider -->
        <div class="auth-divider">
            <span>অথবা তথ্য দিয়ে প্রবেশ করুন</span>
        </div>

        <!-- Main Login Form -->
        <form id="frontendLoginForm" action="{{ route('login.submit') }}" method="POST" novalidate>
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">
                    ইমেইল ঠিকানা <span class="required-star">*</span>
                </label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path d="m3 7 9 6 9-6"></path>
                    </svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="form-control" placeholder="নাম@উদাহরণ.কম">
                </div>
            </div>

            <!-- Password Field with Interactive Eye Toggle Button -->
            <div class="form-group">
                <div class="label-row">
                    <label for="password" class="form-label">
                        পাসওয়ার্ড <span class="required-star">*</span>
                    </label>
                </div>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        <circle cx="12" cy="15.5" r="1.5"></circle>
                    </svg>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="form-control form-control-pw" placeholder="আপনার গোপন পাসওয়ার্ড দিন">
                    
                    <!-- Eye Toggle Button -->
                    <button type="button" id="togglePasswordBtn" class="password-toggle-btn"
                            aria-label="পাসওয়ার্ড দেখুন" title="পাসওয়ার্ড প্রদর্শন / গোপন করুন" onclick="togglePasswordVisibility()">
                        <!-- Eye Icon (shown when masked) -->
                        <svg id="eyeIcon" class="toggle-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Eye-Off Icon (shown when revealed) -->
                        <svg id="eyeOffIcon" class="toggle-svg" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-options">
                <label class="remember-label">
                    <input type="checkbox" name="remember" value="1" id="remember" class="custom-checkbox">
                    <span>আমাকে মনে রাখুন</span>
                </label>
            </div>

            <!-- Primary Submit Button -->
            <button type="submit" id="submitBtn" class="submit-btn">
                <span id="submitBtnText">লগইন করুন</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>

        <!-- Register & Help Footer -->
        <div class="auth-footer">
            <p class="register-prompt">
                এখনও কোনো অ্যাকাউন্ট নেই? 
                <a href="{{ route('register') }}" class="register-link">বিনামূল্যে নিবন্ধন করুন</a>
            </p>
            <div class="security-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>নিরাপদ ও এনক্রিপ্ট করা সংযোগ</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* ==========================================================================
       MODERN BEAUTIFUL AUTHENTICATION STYLING
       ========================================================================== */
    .auth-page-wrapper {
        min-height: calc(100vh - 280px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 16px;
        background: radial-gradient(circle at 50% 10%, rgba(214, 0, 0, 0.04) 0%, rgba(248, 250, 252, 1) 70%);
    }

    .auth-card {
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(15, 23, 42, 0.02);
        padding: 40px 36px;
        box-sizing: border-box;
    }

    /* Header */
    .auth-header {
        text-align: center;
        margin-bottom: 26px;
    }

    .auth-brand-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #d60000 0%, #991b1b 100%);
        color: #ffffff;
        font-size: 19px;
        font-weight: 900;
        letter-spacing: -0.5px;
        box-shadow: 0 8px 18px -4px rgba(214, 0, 0, 0.4);
        margin-bottom: 14px;
    }

    .auth-kicker {
        display: block;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #d60000;
        margin-bottom: 6px;
    }

    .auth-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px 0;
        line-height: 1.3;
    }

    .auth-subtitle {
        font-size: 13.5px;
        color: #64748b;
        margin: 0 auto;
        line-height: 1.55;
        max-width: 420px;
    }

    /* Alerts */
    .auth-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13.5px;
        line-height: 1.45;
        margin-bottom: 22px;
    }

    .auth-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .auth-alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    /* Quick Demo Login */
    .demo-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 26px;
    }

    .demo-section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .demo-bolt {
        font-size: 16px;
    }

    .demo-section-title {
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
    }

    .demo-section-desc {
        font-size: 12px;
        color: #64748b;
        margin: 0 0 14px 0;
        line-height: 1.4;
    }

    .demo-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .demo-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .demo-card:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .demo-card-admin:hover {
        border-color: #fca5a5;
        background: #fffafa;
    }

    .demo-card-editor:hover {
        border-color: #c7d2fe;
        background: #fafafe;
    }

    .demo-card-reader:hover {
        border-color: #a7f3d0;
        background: #f6fdfa;
    }

    .demo-card-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .demo-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .demo-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 5px;
        display: inline-block;
    }

    .demo-badge-admin {
        background: #fee2e2;
        color: #b91c1c;
    }

    .demo-badge-editor {
        background: #e0e7ff;
        color: #4338ca;
    }

    .demo-badge-reader {
        background: #d1fae5;
        color: #047857;
    }

    .demo-arrow {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        transition: color 0.15s, transform 0.15s;
    }

    .demo-card:hover .demo-arrow {
        color: #d60000;
        transform: translateX(2px);
    }

    .demo-role-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .demo-email {
        font-size: 11.5px;
        color: #64748b;
        font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
    }

    /* Divider */
    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 24px 0 20px 0;
        color: #94a3b8;
        font-size: 12.5px;
        font-weight: 600;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .auth-divider span {
        padding: 0 14px;
        background: #ffffff;
    }

    /* Form */
    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .required-star {
        color: #dc2626;
        font-weight: 700;
    }

    .label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        width: 18px;
        height: 18px;
        color: #94a3b8;
        pointer-events: none;
        transition: color 0.15s;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px 12px 42px;
        font-size: 14.5px;
        font-family: inherit;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        outline: none;
        color: #0f172a;
        background: #ffffff;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control-pw {
        padding-right: 46px; /* Room for eye button */
    }

    .form-control:focus {
        border-color: #d60000;
        box-shadow: 0 0 0 3px rgba(214, 0, 0, 0.12);
    }

    .input-wrap:focus-within .input-icon {
        color: #d60000;
    }

    /* Eye Toggle Button */
    .password-toggle-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 6px 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        border-radius: 6px;
        transition: color 0.15s, background-color 0.15s;
    }

    .password-toggle-btn:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }

    .password-toggle-btn:focus {
        outline: 2px solid #d60000;
        outline-offset: 1px;
    }

    .toggle-svg {
        width: 18px;
        height: 18px;
    }

    /* Options (Remember me) */
    .form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 6px 0 22px 0;
    }

    .remember-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        color: #475569;
        cursor: pointer;
        user-select: none;
    }

    .custom-checkbox {
        width: 16px;
        height: 16px;
        accent-color: #d60000;
        cursor: pointer;
    }

    /* Submit Button */
    .submit-btn {
        width: 100%;
        padding: 13px 20px;
        background: linear-gradient(135deg, #d60000 0%, #b91c1c 100%);
        color: #ffffff;
        border: none;
        border-radius: 9px;
        font-size: 15.5px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(214, 0, 0, 0.28);
        transition: transform 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
    }

    .submit-btn:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 6px 20px rgba(214, 0, 0, 0.36);
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Footer */
    .auth-footer {
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        text-align: center;
    }

    .register-prompt {
        font-size: 13.5px;
        color: #475569;
        margin: 0 0 10px 0;
    }

    .register-link {
        color: #d60000;
        font-weight: 800;
        text-decoration: none;
        transition: color 0.15s;
    }

    .register-link:hover {
        text-decoration: underline;
        color: #b91c1c;
    }

    .security-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        color: #94a3b8;
    }

    /* Responsive adjustments */
    @media (max-width: 540px) {
        .auth-card {
            padding: 28px 20px;
            border-radius: 14px;
        }

        .auth-title {
            font-size: 21px;
        }

        .demo-section {
            padding: 14px 12px;
        }
    }
</style>

<script>
    /**
     * Interactive Show / Hide Password Toggle
     */
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        const toggleBtn = document.getElementById('togglePasswordBtn');

        if (!passwordInput) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.style.display = 'none';
            eyeOffIcon.style.display = 'block';
            toggleBtn.setAttribute('aria-label', 'পাসওয়ার্ড লুকান');
        } else {
            passwordInput.type = 'password';
            eyeIcon.style.display = 'block';
            eyeOffIcon.style.display = 'none';
            toggleBtn.setAttribute('aria-label', 'পাসওয়ার্ড দেখুন');
        }
        passwordInput.focus();
    }

    /**
     * 1-Click Quick Demo Login Action
     * Automatically fills in credentials and submits the login form
     */
    function quickLogin(email, password, triggerElement) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('frontendLoginForm');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtn = document.getElementById('submitBtn');

        if (!emailInput || !passwordInput || !loginForm) return;

        // Fill credentials
        emailInput.value = email;
        passwordInput.value = password;

        // Visual feedback on card
        if (triggerElement) {
            const arrow = triggerElement.querySelector('.demo-arrow');
            if (arrow) {
                arrow.textContent = 'লগইন হচ্ছে... ⏳';
                arrow.style.color = '#d60000';
            }
        }

        // Visual feedback on submit button
        if (submitBtnText) {
            submitBtnText.textContent = 'লগইন হচ্ছে...';
        }
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        // Immediate submission
        loginForm.requestSubmit();
    }
</script>
@endsection
