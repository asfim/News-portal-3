@props(['slotKey'])

@php
    $slot = \App\Models\AdSlot::where('key', $slotKey)->where('is_active', true)->with('activeAdvertisements.media')->first();
    $ad = $slot?->activeAdvertisements?->first();
@endphp

@if($slot)
    <div class="ad-slot-wrapper ad-slot-{{ $slotKey }}" style="margin: 15px auto; text-align: center; max-width: 100%;">
        <span style="font-size: 10px; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; display: block; margin-bottom: 4px;">বিজ্ঞাপন</span>
        @if($ad && $ad->media)
            <a href="{{ $ad->destination_url ?? '#' }}" target="_blank" rel="noopener noreferrer sponsored" style="display: inline-block; max-width: 100%;">
                <img src="{{ asset($ad->media->path) }}" alt="{{ $ad->title }}" style="max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            </a>
        @elseif($ad)
            <a href="{{ $ad->destination_url ?? '#' }}" target="_blank" rel="noopener noreferrer sponsored" style="display: block; padding: 20px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; color: #1e293b;">
                <strong style="font-size: 15px; color: #0f172a; display: block; margin-bottom: 4px;">{{ $ad->title }}</strong>
                @if($ad->sponsor_name)
                    <span style="font-size: 12px; color: #64748b;">স্পন্সর: {{ $ad->sponsor_name }}</span>
                @endif
            </a>
        @else
            <div style="padding: 20px 16px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; color: #64748b; font-size: 13px;">
                <strong>এইচ টেক প্রোভিশন আইটি বিজ্ঞাপন স্থান</strong>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">বিজ্ঞাপনের জন্য যোগাযোগ: ads@htechprovision.com</p>
            </div>
        @endif
    </div>
@endif

