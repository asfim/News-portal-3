@extends('layouts.admin')

@section('title', 'লোকেশন ব্যবস্থাপনা')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 700;">লোকেশন ও জেলা তালিকা</h1>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
            <span>+ নতুন লোকেশন তৈরি করুন</span>
        </a>
    </div>

    <!-- Filter by Type -->
    <div style="margin-bottom: 16px; display: flex; gap: 8px;">
        <a href="{{ route('admin.locations.index') }}" class="btn btn-sm {{ !$type ? 'btn-primary' : 'btn-secondary' }}">সব</a>
        <a href="{{ route('admin.locations.index', ['type' => 'division']) }}" class="btn btn-sm {{ $type === 'division' ? 'btn-primary' : 'btn-secondary' }}">বিভাগ</a>
        <a href="{{ route('admin.locations.index', ['type' => 'district']) }}" class="btn btn-sm {{ $type === 'district' ? 'btn-primary' : 'btn-secondary' }}">জেলা</a>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>নাম</th>
                            <th>ধরন</th>
                            <th>স্লাগ</th>
                            <th>মূল বিভাগ (Parent)</th>
                            <th>সংবাদ সংখ্যা</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $loc)
                            <tr>
                                <td><strong>{{ $loc->name }}</strong></td>
                                <td>
                                    <span class="status-badge" style="background: #e0f2fe; color: #0369a1;">
                                        {{ $loc->type === 'division' ? 'বিভাগ' : ($loc->type === 'district' ? 'জেলা' : 'উপজেলা') }}
                                    </span>
                                </td>
                                <td><code>{{ $loc->slug }}</code></td>
                                <td>{{ $loc->parent ? $loc->parent->name : '-' }}</td>
                                <td>{{ bn_num($loc->news_count) }} টি</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.locations.edit', $loc->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                    <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই লোকেশনটি মুছে ফেলতে চান?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো লোকেশন পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $locations->links() }}
    </div>
</div>
@endsection

