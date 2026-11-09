@extends('layouts.admin')

@section('title', 'মন্তব্য যাচাই ও মডারেশন')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 700;">পাঠকের মন্তব্য মডারেশন</h1>
    </div>

    <!-- Filter Tabs -->
    <div style="margin-bottom: 16px; display: flex; gap: 8px;">
        <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-secondary' }}">
            অপেক্ষমাণ (Pending)
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-secondary' }}">
            অনুমোদিত (Approved)
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-secondary' }}">
            প্রত্যাখ্যাত (Rejected)
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>মন্তব্যকারী</th>
                            <th>সংবাদ</th>
                            <th>মন্তব্যের বিষয়বস্তু</th>
                            <th>তারিখ</th>
                            <th>অবস্থা</th>
                            <th style="text-align: right; width: 180px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td>
                                    <strong>{{ $comment->user ? $comment->user->name : 'সাধারণ পাঠক' }}</strong>
                                    @if($comment->user)
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $comment->user->email }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($comment->news)
                                        <a href="{{ route('news.show', [$comment->news->public_id, $comment->news->slug]) }}" target="_blank" style="color: var(--primary); font-weight: 600;">
                                            {{ Str::limit($comment->news->title, 40) }} &nearr;
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <p style="margin: 0; line-height: 1.4;">{{ $comment->body }}</p>
                                    @if($comment->rejection_reason)
                                        <small style="color: #dc2626; display: block; margin-top: 4px;">কারণ: {{ $comment->rejection_reason }}</small>
                                    @endif
                                </td>
                                <td>{{ bn_date($comment->created_at) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $comment->status }}">
                                        @if($comment->status === 'approved') অনুমোদিত
                                        @elseif($comment->status === 'pending') অপেক্ষমাণ
                                        @else প্রত্যাখ্যাত @endif
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        @if($comment->status !== 'approved')
                                            <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">অনুমোদন</button>
                                            </form>
                                        @endif

                                        @if($comment->status !== 'rejected')
                                            <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #b45309;">বাতিল</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('মন্তব্যটি মুছে ফেলতে চান?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো মন্তব্য পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $comments->links() }}
    </div>
</div>
@endsection

