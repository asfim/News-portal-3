<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\NewsletterService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        protected NewsletterService $newsletterService
    ) {}

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|max:150',
            'name' => 'nullable|string|max:100',
        ]);

        $subscriber = $this->newsletterService->subscribe($validated['email'], $validated['name'] ?? null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'আমাদের নিউজলেটারে সাবস্ক্রিপশন সফল হয়েছে! প্রতিদিনের সেরা সংবাদ আপনার ইমেইলে পৌঁছে যাবে।',
            ]);
        }

        return back()->with('newsletter_success', 'আমাদের নিউজলেটারে সাবস্ক্রিপশন সফল হয়েছে!');
    }

    public function verify(string $token)
    {
        $verified = $this->newsletterService->verify($token);

        if ($verified) {
            return redirect()->route('home')->with('success', 'আপনার ইমেইল ঠিকানা সফলভাবে ভেরিফাই হয়েছে। ধন্যবাদ!');
        }

        return redirect()->route('home')->with('error', 'ভেরিফিকেশন টোকেনটি সঠিক নয় অথবা মেয়াদোত্তীর্ণ হয়ে গেছে।');
    }

    public function unsubscribe(string $token)
    {
        $unsubscribed = $this->newsletterService->unsubscribe($token);

        if ($unsubscribed) {
            return redirect()->route('home')->with('success', 'আপনি সফলভাবে নিউজলেটার আনসাবস্ক্রাইব করেছেন।');
        }

        return redirect()->route('home')->with('error', 'আনসাবস্ক্রাইব লিংকটি সঠিক নয়।');
    }
}

