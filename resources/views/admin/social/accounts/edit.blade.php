@extends('layouts.admin')

@section('title', 'ফেসবুক পেজ সম্পাদনা')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ফেসবুক পেজ অ্যাকাউন্ট সম্পাদনা</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">পেজের নাম, পেজ আইডি, মেটা গ্রাফ এপিআই অ্যাক্সেস টোকেন ও অটো পাবলিশ সেটিংস আপডেট করুন</p>
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
            <form action="{{ route('admin.social.accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">ফেসবুক পেজের নাম *</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $account->account_name) }}" required class="form-control" placeholder="যেমন: এইচ টেক প্রোভিশন নিউজ / অফিসিয়াল পেজ">
                </div>

                <div class="form-group">
                    <label class="form-label">ফেসবুক পেজ আইডি (Page ID) *</label>
                    <input type="text" name="account_id" value="{{ old('account_id', $account->account_id) }}" required class="form-control" placeholder="যেমন: 104829104819201">
                    <small style="color: #64748b; font-size: 11px;">পেজের About সেকশন থেকে পেজ আইডি কপি করুন।</small>
                </div>

                <div class="form-group">
                    <label class="form-label">পেজ এক্সেস টোকেন (Page Access Token)</label>
                    <textarea name="access_token" rows="4" class="form-control" placeholder="টোকেন পরিবর্তন করতে চাইলে এখানে নতুন Token পেস্ট করুন (অন্যথায় ফাঁকা রাখুন)">{{ old('access_token') }}</textarea>
                    <small style="color: #64748b; font-size: 11px;">টোকেন অপরিবর্তিত রাখতে চাইলে এটি ফাঁকা রাখুন। নতুন টোকেন দিলে তা নিরাপদভাবে এনক্রিপ্ট হয়ে সংরক্ষণ হবে।</small>
                </div>

                <div style="margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">এই অ্যাকাউন্টটি সক্রিয় রাখুন</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="auto_publish" value="1" {{ old('auto_publish', $account->auto_publish) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <div>
                            <span style="font-weight: 600; font-size: 14px; color: #1e40af;">নতুন সংবাদ প্রকাশের সাথে সাথে স্বয়ংক্রিয় পোস্ট (Auto Publish)</span>
                            <div style="font-size: 11px; color: #64748b;">অনুমোদিত নতুন সংবাদ লাইভ হওয়ার সাথে সাথেই ব্যাকগ্রাউন্ড কিউ-এর মাধ্যমে ফেসবুকে চলে যাবে।</div>
                        </div>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('admin.social.accounts.index') }}" class="btn btn-secondary">বাতিল</a>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">
                        পরিবর্তন সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
