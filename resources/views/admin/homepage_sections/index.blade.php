@extends('layouts.admin')

@section('title', 'হোমপেজ সেকশন কনফিগারেশন')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">হোমপেজ সেকশন ও গ্রিড নিয়ন্ত্রণ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">কোড এডিট না করেই হোমপেজের প্রতিটি ব্লকের ক্রম, লেআউট, ক্যাটাগরি ও সংবাদ সংখ্যা নির্ধারণ করুন।</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.homepage-sections.create') }}" class="btn btn-primary btn-sm">＋ নতুন সেকশন যোগ করুন</a>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary btn-sm">লাইভ হোমপেজ দেখুন ↗</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">হোমপেজে প্রদর্শিত সেকশন তালিকা</h3>
        <span style="font-size: 12px; color: #64748b;">ক্রম পরিবর্তন করে 'আপডেট' বাটনে চাপ দিন</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ক্রম</th>
                        <th>সেকশনের শিরোনাম</th>
                        <th>লেআউট প্রিসেট</th>
                        <th>উৎস (Source)</th>
                        <th>সংবাদ সীমা</th>
                        <th>পিন্ড সংবাদ</th>
                        <th>অবস্থা</th>
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
                                    <input type="number" name="sort_order" value="{{ $sec->sort_order }}" class="form-control" style="width: 65px; font-weight: 700; text-align: center;">
                                </td>
                                <td>
                                    <input type="text" name="title" value="{{ $sec->title }}" class="form-control" style="font-weight: 700; min-width: 170px;">
                                    <small style="color: #94a3b8; font-family: monospace; display: block; margin-top: 2px;">key: {{ $sec->key }}</small>
                                </td>
                                <td>
                                    <select name="layout" class="form-control" style="font-size: 13px; min-width: 150px;">
                                        <option value="hero_3col" {{ $sec->layout === 'hero_3col' ? 'selected' : '' }}>প্রধান সংবাদ (Hero 3-Col)</option>
                                        <option value="tags_bar" {{ $sec->layout === 'tags_bar' ? 'selected' : '' }}>ট্রেন্ডিং টপিকস বার</option>
                                        <option value="grid_4col" {{ $sec->layout === 'grid_4col' ? 'selected' : '' }}>৪-কলাম গ্রিড</option>
                                        <option value="video_carousel" {{ $sec->layout === 'video_carousel' ? 'selected' : '' }}>ভিডিও সেকশন</option>
                                        <option value="mid_3col" {{ $sec->layout === 'mid_3col' ? 'selected' : '' }}>ট্যাবড উইজেট</option>
                                        <option value="section_lead_quad" {{ $sec->layout === 'section_lead_quad' ? 'selected' : '' }}>লিড + ৪ সংবাদ</option>
                                        <option value="opinion_split" {{ $sec->layout === 'opinion_split' ? 'selected' : '' }}>মতামত স্প্লিট</option>
                                        <option value="beige_tech" {{ $sec->layout === 'beige_tech' ? 'selected' : '' }}>টেক ৪-কলাম টেবিল</option>
                                        <option value="dark_video_hub" {{ $sec->layout === 'dark_video_hub' ? 'selected' : '' }}>ভিডিও হাব</option>
                                        <option value="pubs_grid" {{ $sec->layout === 'pubs_grid' ? 'selected' : '' }}>অন্যান্য প্রকাশনা</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="source_type" class="form-control" style="font-size: 13px; min-width: 120px;">
                                        <option value="latest" {{ $sec->source_type === 'latest' ? 'selected' : '' }}>সর্বশেষ (Latest)</option>
                                        <option value="category" {{ $sec->source_type === 'category' ? 'selected' : '' }}>ক্যাটাগরি ভিত্তিক</option>
                                        <option value="popular" {{ $sec->source_type === 'popular' ? 'selected' : '' }}>সর্বাধিক পঠিত</option>
                                        <option value="manual" {{ $sec->source_type === 'manual' ? 'selected' : '' }}>ম্যানুয়াল পিন্ড</option>
                                    </select>
                                    @if($sec->category)
                                        <small style="color: #64748b; display: block; margin-top: 2px;">{{ $sec->category->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="item_limit" value="{{ $sec->item_limit }}" class="form-control" style="width: 70px; text-align: center;">
                                </td>
                                <td style="font-size: 13px; text-align: center;">
                                    <a href="{{ route('admin.homepage-sections.edit', $sec->id) }}" style="text-decoration: none; font-weight: 700; color: #0284c7;">
                                        📌 {{ bn_num($sec->items->count()) }}টি
                                    </a>
                                </td>
                                <td>
                                    @if($sec->is_active)
                                        <span class="status-badge status-published">চালু</span>
                                    @else
                                        <span class="status-badge status-draft">লুকানো</span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <input type="hidden" name="is_active" value="{{ $sec->is_active ? 1 : 0 }}">
                                    <button type="submit" class="btn btn-primary btn-sm" title="সংরক্ষণ করুন">আপডেট</button>
                            </form>
                                    <a href="{{ route('admin.homepage-sections.edit', $sec->id) }}" class="btn btn-secondary btn-sm" title="বিস্তারিত ও পিন্ড নিউজ">পিন্ড নিউজ</a>
                                    <form action="{{ route('admin.homepage-sections.toggle', $sec->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $sec->is_active ? 'হাইড' : 'শো' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.homepage-sections.destroy', $sec->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('আপনি কি নিশ্চিত এই সেকশনটি মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 5px 8px;" title="মুছুন">&times;</button>
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
