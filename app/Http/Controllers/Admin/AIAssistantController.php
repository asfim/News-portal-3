<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\AiGenerationLog;
use App\Services\AI\AIService;
use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    /**
     * AJAX endpoint called from News Create/Edit editor.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:headline,summary,improve,seo,tags,category,social_caption,draft',
            'input_text' => 'required|string|min:5|max:15000',
            'news_id' => 'nullable|exists:news,id',
        ]);

        $result = $this->aiService->executeAction(
            $validated['action'],
            $validated['input_text'],
            $validated['news_id'] ?? null,
            auth()->user()
        );

        return response()->json($result);
    }

    /**
     * AI Settings View
     */
    public function settings()
    {
        $setting = AiSetting::current();
        return view('admin.ai.settings', compact('setting'));
    }

    /**
     * Update AI Settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:gemini,openai,mock',
            'api_key' => 'nullable|string',
            'model' => 'required|string|max:100',
            'daily_request_limit' => 'required|integer|min:10|max:5000',
            'is_enabled' => 'boolean',
        ]);

        $validated['is_enabled'] = $request->boolean('is_enabled', true);

        $setting = AiSetting::current();
        if (empty($validated['api_key'])) {
            unset($validated['api_key']); // preserve existing encrypted key if left blank
        }

        $setting->update($validated);

        return back()->with('success', 'এআই অ্যাসিস্ট্যান্ট কনফিগারেশন সফলভাবে আপডেট হয়েছে।');
    }

    /**
     * AI Request History / Logs
     */
    public function history()
    {
        $logs = AiGenerationLog::with(['user', 'news'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.ai.history', compact('logs'));
    }
}
