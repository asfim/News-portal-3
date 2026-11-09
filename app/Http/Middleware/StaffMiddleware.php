<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'আপনার অ্যাকাউন্টটি নিষ্ক্রিয় করা হয়েছে।']);
        }

        if (!$user->hasStaffAccess()) {
            abort(403, 'অ্যাডমিন প্যানেলে প্রবেশের অনুমতি আপনার নেই।');
        }

        return $next($request);
    }
}
