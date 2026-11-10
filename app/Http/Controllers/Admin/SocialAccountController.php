<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class SocialAccountController extends Controller
{
    public function index()
    {
        $accounts = SocialAccount::withCount('posts')->orderByDesc('created_at')->paginate(10);
        return view('admin.social.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.social.accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:facebook',
            'account_id' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'access_token' => 'required|string',
            'auto_publish' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['auto_publish'] = $request->boolean('auto_publish', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $account = SocialAccount::create($validated);

        return redirect()->route('admin.social.accounts.index')
            ->with('success', "ফেসবুক পেজ '{$account->account_name}' সফলভাবে সংযুক্ত হয়েছে।");
    }

    public function edit(SocialAccount $account)
    {
        return view('admin.social.accounts.edit', compact('account'));
    }

    public function update(Request $request, SocialAccount $account)
    {
        $validated = $request->validate([
            'account_id' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'access_token' => 'nullable|string',
            'auto_publish' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['auto_publish'] = $request->boolean('auto_publish', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        if (empty($validated['access_token'])) {
            unset($validated['access_token']);
        }

        $account->update($validated);

        return redirect()->route('admin.social.accounts.index')
            ->with('success', "সোশ্যাল অ্যাকাউন্ট '{$account->account_name}' সফলভাবে আপডেট হয়েছে।");
    }

    public function toggle(SocialAccount $account)
    {
        $account->update(['is_active' => !$account->is_active]);

        $status = $account->is_active ? 'সক্রিয় করা হয়েছে।' : 'বন্ধ করা হয়েছে।';
        return back()->with('success', "'{$account->account_name}' পেজ {$status}");
    }

    public function destroy(SocialAccount $account)
    {
        $name = $account->account_name;
        $account->delete();

        return redirect()->route('admin.social.accounts.index')
            ->with('success', "'{$name}' পেজের সংযোগ বিচ্ছিন্ন করা হয়েছে।");
    }
}
