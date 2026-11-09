@extends('layouts.admin')

@section('title', 'ব্রেকিং নিউজ ব্যবস্থাপনা')

@section('content')
<div style="display: grid; grid-template-columns: 4fr 8fr; gap: 24px;">
    <!-- Add Breaking News Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">নতুন ব্রেকিং নিউজ যুক্ত করুন</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.breaking-news.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">ব্রেকিং শিরোনাম / হেডলাইন *</label>
                    <textarea name="label" rows="3" class="form-control" placeholder="যেমন: পদ্মা সেতুতে নতুন রেকর্ড, মাত্র ১ মিনিটে ৫ হাজার গাড়ি পারাপার" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">সংশ্লিষ্ট প্রকাশিত সংবাদ (ঐচ্ছিক)</label>
                    <select name="news_id" class="form-control">
                        <option value="">-- কোনো সংবাদ লিংক না থাকলে খালি রাখুন --</option>
                        @foreach($recentNews as $rn)
                            <option value="{{ $rn->id }}">{{ Str::limit($rn->title, 50) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">কাস্টম বহিঃস্থ লিংক (ঐচ্ছিক)</label>
                    <input type="url" name="url" class="form-control" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label class="form-label">অগ্রাধিকার (Priority)</label>
                    <input type="number" name="priority" value="10" class="form-control">
                    <small style="color: #64748b;">যেটির মান বেশি সেটি আগে দেখাবে।</small>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label for="is_active" style="font-size: 14px; font-weight: 600; cursor: pointer;">এখনই লাইভ সাইটে সক্রিয় করুন</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">ব্রেকিং নিউজ প্রচার করুন</button>
            </form>
        </div>
    </div>

    <!-- Active Breaking News List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">সকল ব্রেকিং নিউজ তালিকা</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>শিরোনাম</th>
                            <th>অগ্রাধিকার</th>
                            <th>স্ট্যাটাস</th>
                            <th>তারিখ</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($breakingItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->label }}</strong>
                                    @if($item->news)
                                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                            নিউজ লিংক: <a href="{{ route('news.show', [$item->news->public_id, $item->news->slug]) }}" target="_blank" style="color: #d60000;">{{ Str::limit($item->news->title, 40) }}</a>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->priority }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="status-badge status-published">লাইভ সক্রিয়</span>
                                    @else
                                        <span class="status-badge status-draft">নিষ্ক্রিয়</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #64748b;">{{ bn_time_ago($item->created_at) }}</td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <form action="{{ route('admin.breaking-news.toggle', $item->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $item->is_active ? 'বন্ধ করুন' : 'চালু করুন' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.breaking-news.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">কোনো ব্রেকিং নিউজ যুক্ত নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px;">
                {{ $breakingItems->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

