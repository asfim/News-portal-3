@extends('layouts.frontend')

@section('title', 'লগইন - এইচ টেক প্রোভিশন আইটি')

@section('content')
<div class="content-wrapper" style="max-width: 480px; margin: 40px auto; padding: 25px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div style="text-align: center; margin-bottom: 25px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #1a202c; margin-bottom: 8px;">পাঠক লগইন</h1>
        <p style="color: #64748b; font-size: 14px;">আপনার অ্যাকাউন্টে লগইন করে সংবাদ বুকমার্ক ও মন্তব্য করুন</p>
    </div>

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">ইমেইল ঠিকানা</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; outline: none; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">পাসওয়ার্ড</label>
            <input type="password" name="password" required
                   style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; outline: none; box-sizing: border-box;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer;">
                <input type="checkbox" name="remember" value="1"> আমাকে মনে রাখুন
            </label>
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #d60000; color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: 700; cursor: pointer;">লগইন করুন</button>
    </form>

    <div style="text-align: center; margin-top: 25px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 14px; color: #64748b;">
        নতুন পাঠক? <a href="{{ route('register') }}" style="color: #d60000; font-weight: 700; text-decoration: none;">এখানে নিবন্ধন করুন</a>
    </div>
</div>
@endsection

