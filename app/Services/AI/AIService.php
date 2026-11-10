<?php

namespace App\Services\AI;

use App\Models\AiSetting;
use App\Models\AiPromptTemplate;
use App\Models\AiGenerationLog;
use App\Models\Category;
use App\Models\User;
use Exception;

class AIService
{
    /**
     * Generate content for editorial AI assistant actions.
     *
     * @param string $action headline, summary, improve, seo, tags, category, social_caption, draft
     * @param string $inputText
     * @param int|null $newsId
     * @param User|null $user
     * @return array
     */
    public function executeAction(string $action, string $inputText, ?int $newsId = null, ?User $user = null): array
    {
        $setting = AiSetting::current();

        // 1. Check daily limit
        $this->checkAndResetDailyLimit($setting);
        if ($setting->today_requests_count >= $setting->daily_request_limit && !app()->environment('testing')) {
            return [
                'success' => false,
                'text' => '',
                'error' => 'আজকের দৈনিক এআই রিকোয়েস্ট সীমা (' . $setting->daily_request_limit . ') পূর্ণ হয়েছে। আগামীকাল পুনরায় সক্রিয় হবে।',
            ];
        }

        // 2. Resolve Provider
        $provider = $this->resolveProvider($setting);

        // 3. Prepare System & User Prompts
        $promptInfo = $this->buildPrompt($action, $inputText);

        // 4. Execute AI Generation
        $result = $provider->generate($promptInfo['system'], $promptInfo['user'], [
            'model' => $setting->model,
            'api_key' => $setting->api_key,
            'action' => $action,
        ]);

        // 5. Track usage & Log
        $setting->increment('today_requests_count');

        AiGenerationLog::create([
            'user_id' => $user?->id ?? auth()->id(),
            'news_id' => $newsId,
            'action' => $action,
            'provider' => $setting->provider,
            'model' => $setting->model,
            'prompt_tokens' => (int) ($result['prompt_tokens'] ?? 0),
            'completion_tokens' => (int) ($result['completion_tokens'] ?? 0),
            'input_text' => $inputText,
            'output_text' => $result['text'] ?? null,
            'status' => $result['success'] ? 'success' : 'failed',
            'error_message' => $result['error'] ?? null,
        ]);

        return $result;
    }

    /**
     * Resolve Provider Instance
     */
    protected function resolveProvider(AiSetting $setting): AIProviderInterface
    {
        if (app()->environment('testing') || empty($setting->api_key) || $setting->provider === 'mock') {
            return new MockAIProvider();
        }

        if ($setting->provider === 'openai') {
            return new OpenAIProvider($setting->api_key, $setting->model ?: 'gpt-4o-mini');
        }

        return new GeminiProvider($setting->api_key, $setting->model ?: 'gemini-1.5-flash');
    }

    /**
     * Check and reset daily request limits when a new day arrives.
     */
    protected function checkAndResetDailyLimit(AiSetting $setting): void
    {
        $today = now()->toDateString();
        if ($setting->last_reset_date !== $today) {
            $setting->update([
                'today_requests_count' => 0,
                'last_reset_date' => $today,
            ]);
        }
    }

    /**
     * Build system prompts with strict journalistic truthfulness and Bengali guidelines.
     */
    protected function buildPrompt(string $action, string $inputText): array
    {
        $baseSystem = "You are a professional senior Bengali news editor and journalist at an authentic, reputable national newspaper. " .
            "Follow strict journalistic ethics: DO NOT invent facts, fake quotes, or unverified claims. " .
            "Use formal, grammatically correct Standard Bengali (প্রমিত বাংলা). " .
            "Never use sensational or misleading clickbait.";

        switch ($action) {
            case 'headline':
                return [
                    'system' => $baseSystem . " Output 3 distinct headline variants in Bengali: 1) Short punchy headline (সর্বোচ্চ ৮ শব্দ), 2) Balanced editorial headline, 3) Descriptive investigative headline. Output each on a new line numbered 1, 2, 3.",
                    'user' => "প্রদত্ত সংবাদ বিষয়বস্তুর ওপর ভিত্তি করে ৩টি আকর্ষণীয় ও প্রমিত বাংলা শিরোনাম তৈরি করুন:\n\n" . $inputText,
                ];

            case 'summary':
                return [
                    'system' => $baseSystem . " Summarize the provided Bengali text into a clear, concise 2-3 sentence overview (under 200 characters) preserving core facts (Who, What, When, Where).",
                    'user' => "নিচের সংবাদটির একটি চমৎকার সংক্ষিপ্ত সারসংক্ষেপ (Summary) বাংলায় লিখুন:\n\n" . $inputText,
                ];

            case 'improve':
                return [
                    'system' => $baseSystem . " Proofread, edit and improve the provided Bengali article. Fix spelling, punctuation, sentence flow and maintain professional journalistic tone. Return the polished HTML or paragraphs.",
                    'user' => "নিচের সংবাদ প্রতিবেদনটির ভাষা, বানান এবং বাক্যের গঠন সাংবাদিকতার মানদণ্ডে পরিমার্জন ও উন্নত করুন:\n\n" . $inputText,
                ];

            case 'seo':
                return [
                    'system' => $baseSystem . " Generate SEO Title (under 60 chars) and Meta Description (under 155 chars) in Bengali. Format strictly:\nSEO Title: ...\nMeta Description: ...",
                    'user' => "এই সংবাদের জন্য সার্চ ইঞ্জিন অপ্টিমাইজেশন (SEO Title & Meta Description) তৈরি করুন:\n\n" . $inputText,
                ];

            case 'tags':
                return [
                    'system' => $baseSystem . " Extract 5 to 8 most relevant Bengali topical tags/keywords separated by commas. No hashtags, just comma-separated names.",
                    'user' => "এই সংবাদের জন্য ৫-৮টি উপযুক্ত বাংলা ট্যাগ প্রস্তাব করুন (কমা দিয়ে পৃথক):\n\n" . $inputText,
                ];

            case 'category':
                $categories = Category::active()->pluck('name')->implode(', ');
                return [
                    'system' => $baseSystem . " Available portal categories are: {$categories}. Recommend the single best matching category from this list, along with 1-sentence reason.",
                    'user' => "এই সংবাদের জন্য উল্লেখিত ক্যাটাগরি তালিকা থেকে সবচেয়ে মানানসই ক্যাটাগরি কোনটি?\n\nসংবাদ:\n" . $inputText,
                ];

            case 'social_caption':
                return [
                    'system' => $baseSystem . " Create an engaging social media post caption (Facebook / X) in Bengali with 1-2 relevant emojis, a hook, key bullet points, and 3 hashtags.",
                    'user' => "এই সংবাদের জন্য সামাজিক মাধ্যমে প্রকাশের উপযোগী একটি আকর্ষণীয় পোস্ট ক্যাপশন তৈরি করুন:\n\n" . $inputText,
                ];

            case 'draft':
            default:
                return [
                    'system' => $baseSystem . " Write a comprehensive, well-structured Bengali news report based on the provided facts/outline. Use inverted pyramid style (Lead, body paragraphs, context). Format in clean HTML (<p>, <h3>).",
                    'user' => "নিচের তথ্য/নোট বা ইংরেজি খসড়া থেকে একটি সম্পূর্ণ ও বস্তুনিষ্ঠ বাংলা সংবাদ প্রতিবেদন প্রস্তুত করুন:\n\n" . $inputText,
                ];
        }
    }
}
