<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class MockAIProvider implements AIProviderInterface
{
    public function generate(string $systemPrompt, string $userInput, array $options = []): array
    {
        $action = $options['action'] ?? 'summary';
        $text = '';

        switch ($action) {
            case 'headline':
                $text = "১. " . Str::limit(strip_tags($userInput), 50) . "\n২. বিশেষ প্রতিবেদন: " . Str::limit(strip_tags($userInput), 45);
                break;
            case 'summary':
                $text = "সংবাদ সারসংক্ষেপ: " . Str::limit(strip_tags($userInput), 160) . "। সংশ্লিষ্ট কর্তৃপক্ষ এ বিষয়ে সার্বিক পর্যবেক্ষণ অব্যাহত রেখেছে।";
                break;
            case 'improve':
                $text = "<p>" . strip_tags($userInput) . " (সম্পাদকীয়ভাবে পরিমার্জিত ও নির্ভুল ভাষা)।</p>";
                break;
            case 'seo':
                $text = "SEO Title: " . Str::limit(strip_tags($userInput), 60) . " | প্রথম আলো\nMeta Description: " . Str::limit(strip_tags($userInput), 155);
                break;
            case 'tags':
                $text = "#বাংলাদেশ, #জাতীয়, #খবর, #বিশেষপ্রতিবেদন, #সাম্প্রতিক";
                break;
            case 'category':
                $text = "প্রস্তাবিত ক্যাটাগরি: জাতীয় (National)";
                break;
            case 'social_caption':
                $text = "📌 " . Str::limit(strip_tags($userInput), 80) . "\n\nবিস্তারিত পড়ুন নিচের লিংকে 👇\n#NewsAlert #BengaliNews";
                break;
            case 'paraphrase':
                $cleanInput = strip_tags($userInput);
                $text = json_encode([
                    'title' => 'বিশেষ প্রতিবেদন: ' . Str::limit($cleanInput, 55),
                    'excerpt' => 'সাম্প্রতিক ঘটনাবলির আলোকে প্রস্তুতকৃত বিশেষ সংবাদ প্রতিবেদন। সংশ্লিষ্ট সূত্রের বরাতে প্রাপ্ত তথ্য নিচে তুলে ধরা হলো।',
                    'body_html' => '<p>' . Str::limit($cleanInput, 200) . '</p><p>উক্ত বিষয়ে সংশ্লিষ্ট কর্তৃপক্ষ প্রয়োজনীয় ব্যবস্থা গ্রহণ করেছে। পরিস্থিতি নিবিড়ভাবে পর্যালোচনা করা হচ্ছে বলে নিশ্চিত করেছে সূত্র।</p>',
                ], JSON_UNESCAPED_UNICODE);
                break;
            default:
                $text = "এআই দ্বারা প্রস্তুতকৃত খসড়া: " . strip_tags($userInput);
        }

        return [
            'success' => true,
            'text' => $text,
            'prompt_tokens' => mb_strlen($userInput) / 4,
            'completion_tokens' => mb_strlen($text) / 4,
            'error' => null,
        ];
    }
}
