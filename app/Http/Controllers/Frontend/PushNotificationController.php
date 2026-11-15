<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct(
        protected PushNotificationService $pushService
    ) {}

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'nullable|string',
            'keys.auth' => 'nullable|string',
        ]);

        $subscription = $this->pushService->subscribe($validated, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'নিউজ অ্যালার্টে সফলভাবে সাবস্ক্রাইব করা হয়েছে।',
            'id' => $subscription->id,
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|url',
        ]);

        $this->pushService->unsubscribe($validated['endpoint']);

        return response()->json([
            'success' => true,
            'message' => 'নিউজ অ্যালার্ট আনসাবস্ক্রাইব করা হয়েছে।',
        ]);
    }
}

