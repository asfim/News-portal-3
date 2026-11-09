<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন | এইচ টেক প্রোভিশন আইটি</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 36px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 700;
            color: #111;
            margin-top: 8px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .form-control:focus {
            border-color: #d60000;
            box-shadow: 0 0 0 3px rgba(214,0,0,0.1);
        }
        .btn-submit {
            width: 100%;
            background: #d60000;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover {
            background: #b00000;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <span style="color: #d60000; font-size: 32px; font-weight: 900;">■</span>
            <h1 class="brand-title">সম্পাদকীয় প্যানেল লগইন</h1>
            <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">এইচ টেক প্রোভিশন আইটি নিউজ পোর্টাল</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">ইমেইল ঠিকানা</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control" placeholder="admin@htechprovision.com">
            </div>

            <div class="form-group">
                <label class="form-label">পাসওয়ার্ড</label>
                <input type="password" name="password" required class="form-control" placeholder="••••••••">
            </div>

            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember"> মনে রাখুন
                </label>
            </div>

            <button type="submit" class="btn-submit">প্রবেশ করুন</button>
        </form>
    </div>
</body>
</html>

