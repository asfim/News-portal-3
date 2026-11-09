@extends('layouts.admin')

@section('title', 'হোমপেজ সেকশন কনফিগারেশন')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">হোমপেজ সেকশন ও গ্রিড নিয়ন্ত্রণ</h3>
            <p style="font-size: 13px; color: #64748b; margin-top: 4px;">হোমপেজে কোন সেকশনটি দৃশ্যমান থাকবে, কয়টি সংবাদ লোড হবে এবং কোন ক্রমানুসারে দেখাবে তা এখান থেকে নিয়ন্ত্রণ করুন।</p>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>সেকশনের নাম</th>
                        <th>লেআউট প্রিসেট</th>
                        <th>উৎস (Source)</th>
                        <th>সংবাদ সীমা (Item Limit)</th>
                        <th>ক্রম (Sort Order)</th>
                        <th>স্ট্যাটাস</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $sec)
                        <tr>
                            <form action="{{ route('admin.homepage-sections.update', $sec->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td>
                                    <input type="text" name="title" value="{{ $sec->title }}" class="form-control" style="font-weight: 700; width: 180px;">
                                    <small style="color: #94a3b8; font-family: monospace;">key: {{ $sec->key }}</small>
                                </td>
                                <td style="font-size: 13px; color: #475569;">
                                    <code>{{ $sec->layout }}</code>
                                </td>
                                <td style="font-size: 13px;">
                                    <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-weight: 600;">{{ $sec->source_type }}</span>
                                </td>
                                <td>
                                    <input type="number" name="item_limit" value="{{ $sec->item_limit }}" class="form-control" style="width: 70px;">
                                </td>
                                <td>
                                    <input type="number" name="sort_order" value="{{ $sec->sort_order }}" class="form-control" style="width: 60px;">
                                </td>
                                <td>
                                    @if($sec->is_active)
                                        <span class="status-badge status-published">চালু রয়েছে</span>
                                    @else
                                        <span class="status-badge status-draft">লুকানো রয়েছে</span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <button type="submit" class="btn btn-primary btn-sm" title="পরিবর্তন সংরক্ষণ করুন">আপডেট</button>
                            </form>
                                    <form action="{{ route('admin.homepage-sections.toggle', $sec->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $sec->is_active ? 'হাইড করুন' : 'শো করুন' }}
                                        </button>
                                    </form>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

