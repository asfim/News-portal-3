@extends('layouts.admin')

@section('title', 'নতুন ফেসবুক পেজ সংযোগ')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ফেসবুক পেজ সংযোগ করুন</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">অফিসিয়াল মেটা প্ল্যাটফর্ম টোকেন ও পেজ আইডি কনফিগার করুন</p>
        </div>
        <a href="{{ route('admin.social.accounts.index') }}" class="btn btn-secondary">&larr; অ্যাকাউন্ট তালিকায় ফিরে যান</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.social.accounts.store') }}" method="POST">
                @csrf
                <input type="hidden" name="provider" value="facebook">

                <div class="form-group">
                    <label class="form-label">ফেসবুক পেজের নাম *</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" required class="form-control" placeholder="যেমন: এইচ টেক প্রোভিশন নিউজ / অফিসিয়াল পেজ">
                </div>

                <div class="form-group">
                    <label class="form-label">ফেসবুক পেজ আইডি (Page ID) *</label>
                    <input type="text" name="account_id" value="{{ old('account_id') }}" required class="form-control" placeholder="যেমন: 104829104819201">
                    <small style="color: #64748b; font-size: 11px;">পেজের About সেকশন থেকে পেজ আইডি কপি করুন।</small>
                </div>

                <div class="form-group">
                    <label class="form-label">পেজ এক্সেস টোকেন (Page Access Token) *</label>
                    <textarea name="access_token" rows="4" required class="form-control" placeholder="EAA... (Meta Developers Portal থেকে সংগৃহীত Page Access Token)">{{ old('access_token') }}</textarea>
                    <small style="color: #64748b; font-size: 11px;">টোকেন ডাটাবেজে সম্পূর্ণ এনক্রিপ্ট করে সংরক্ষণ করা হয়।</small>
                </div>

                <div style="margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">এই অ্যাকাউন্টটি সক্রিয় রাখুন</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="auto_publish" value="1" {{ old('auto_publish', false) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <div>
                            <span style="font-weight: 600; font-size: 14px; color: #1e40af;">নতুন সংবাদ প্রকাশের সাথে সাথে স্বয়ংক্রিয় পোস্ট (Auto Publish)</span>
                            <div style="font-size: 11px; color: #64748b;">অনুমোদিত নতুন সংবাদ লাইভ হওয়ার সাথে সাথেই ব্যাকগ্রাউন্ড কিউ-এর মাধ্যমে ফেসবুকে চলে যাবে।</div>
                        </div>
                    </label>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        পেজ সংযুক্ত করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
