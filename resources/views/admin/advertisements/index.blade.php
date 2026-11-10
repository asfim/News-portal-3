@extends('layouts.admin')

@section('title', 'বিজ্ঞাপন স্লট ও ব্যানার ব্যবস্থাপনা')

@section('content')
<div class="admin-grid-sidebar-table">
    <!-- Add Advertisement Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">নতুন বিজ্ঞাপন যুক্ত করুন</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.advertisements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">বিজ্ঞাপনের শিরোনাম / ক্যাম্পেইন নাম *</label>
                    <input type="text" name="title" class="form-control" placeholder="যেমন: এইচ টেক প্রোভিশন ক্লাউড হোস্টিং" required>
                </div>

                <div class="form-group">
                    <label class="form-label">ক্লিক গন্তব্য লিংক (Destination URL) *</label>
                    <input type="url" name="destination_url" class="form-control" placeholder="https://..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">স্পনসর প্রতিষ্ঠানের নাম (ঐচ্ছিক)</label>
                    <input type="text" name="sponsor_name" class="form-control" placeholder="যেমন: Visa / H Tech Provision">
                </div>

                <div class="form-group">
                    <label class="form-label">বিজ্ঞাপনের স্লট নির্বাচন করুন *</label>
                    <div style="max-height: 140px; overflow-y: auto; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px;">
                        @foreach($slots as $slot)
                            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="ad_slot_ids[]" value="{{ $slot->id }}">
                                <span><strong>{{ $slot->name }}</strong> ({{ $slot->desktop_width }}×{{ $slot->desktop_height }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">বিজ্ঞাপনের ব্যানার ইমেজ (মিডিয়া লাইব্রেরি থেকে)</label>
                    <select name="media_id" class="form-control">
                        <option value="">-- টেক্সট অ্যাড / কোনো ছবি না থাকলে খালি রাখুন --</option>
                        @foreach($mediaList as $m)
                            <option value="{{ $m->id }}">{{ $m->original_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">স্ট্যাটাস</label>
                    <select name="status" class="form-control">
                        <option value="active">সক্রিয় (Active)</option>
                        <option value="paused">স্থগিত (Paused)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">বিজ্ঞাপন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>

    <!-- Active Advertisements List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">চলতি বিজ্ঞাপনের তালিকা</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ক্যাম্পেইন</th>
                            <th>স্লট</th>
                            <th>স্পনসর</th>
                            <th>স্ট্যাটাস</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                            <tr>
                                <td>
                                    <strong>{{ $ad->title }}</strong>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                                        <a href="{{ $ad->destination_url }}" target="_blank" style="color: #0284c7;">{{ Str::limit($ad->destination_url, 35) }}</a>
                                    </div>
                                </td>
                                <td>
                                    @foreach($ad->slots as $s)
                                        <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin: 2px;">
                                            {{ $s->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td style="font-size: 13px;">{{ $ad->sponsor_name ?? 'N/A' }}</td>
                                <td>
                                    @if($ad->status === 'active')
                                        <span class="status-badge status-published">সক্রিয়</span>
                                    @else
                                        <span class="status-badge status-draft">স্থগিত</span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <form action="{{ route('admin.advertisements.toggle', $ad->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $ad->status === 'active' ? 'স্থগিত' : 'চালু' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.advertisements.destroy', $ad->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">কোনো বিজ্ঞাপন কনফিগার করা নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px;">
                {{ $advertisements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

