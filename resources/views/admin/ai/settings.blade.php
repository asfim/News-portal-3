@extends('layouts.admin')

@section('title', 'এআই নিউজ অ্যাসিস্ট্যান্ট সেটিংস')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">এআই নিউজ অ্যাসিস্ট্যান্ট সেটিংস</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">গুগল জেমিনি বা ওপেনএআই মডেল ও দৈনিক ব্যবহার সীমা কনফিগার করুন</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.ai.history') }}" class="btn btn-secondary btn-sm">এআই হিস্টোরি ও লগ →</a>
            <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন সেন্টার</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ai.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">এআই প্রোভাইডার *</label>
                    <select name="provider" class="form-control" id="providerSelect" required>
                        <option value="gemini" {{ old('provider', $setting->provider) === 'gemini' ? 'selected' : '' }}>Google Gemini (প্রস্তাবিত)</option>
                        <option value="openai" {{ old('provider', $setting->provider) === 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                        <option value="mock" {{ old('provider', $setting->provider) === 'mock' ? 'selected' : '' }}>মক মোড (ডেভেলপমেন্ট ও টেস্ট)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">এআই মডেল নির্বাচন *</label>
                    <input type="text" name="model" value="{{ old('model', $setting->model) }}" required class="form-control" placeholder="যেমন: gemini-1.5-flash, gemini-1.5-pro, gpt-4o-mini">
                    <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">Gemini-এর জন্য <code>gemini-1.5-flash</code> অথবা OpenAI-এর জন্য <code>gpt-4o-mini</code> দ্রুতগতির ও মানসম্মত।</small>
                </div>

                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" name="api_key" value="" class="form-control" placeholder="{{ $setting->api_key ? '•••••••••••••••• (কী সংরক্ষিত রয়েছে, পরিবর্তন করতে চাইলে নতুন লিখুন)' : 'এখানে আপনার API কী পেস্ট করুন...' }}">
                    <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">কী ডাটাবেজে সম্পূর্ণ এনক্রিপ্ট করে সুরক্ষিত রাখা হয়।</small>
                </div>

                <div class="form-group">
                    <label class="form-label">দৈনিক রিকোয়েস্ট সীমা (Daily Limit) *</label>
                    <input type="number" name="daily_request_limit" value="{{ old('daily_request_limit', $setting->daily_request_limit) }}" min="10" max="5000" required class="form-control">
                    <small style="color: #64748b; font-size: 11px;">আজকের মোট রিকোয়েস্ট হয়েছে: {{ bn_num($setting->today_requests_count) }}টি</small>
                </div>

                <div style="margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $setting->is_enabled) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <div>
                            <span style="font-weight: 600; font-size: 14px;">এআই নিউজ অ্যাসিস্ট্যান্ট সক্রিয় রাখুন</span>
                            <div style="font-size: 11px; color: #64748b;">সক্রিয় থাকলে সংবাদ তৈরি ও সম্পাদনার পেজে এআই টুলবার প্রদর্শিত হবে।</div>
                        </div>
                    </label>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
