<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Exception;

class GeminiProvider implements AIProviderInterface
{
    public function __construct(
        protected ?string $apiKey = null,
        protected string $model = 'gemini-1.5-flash'
    ) {}

    public function generate(string $systemPrompt, string $userInput, array $options = []): array
    {
        $apiKey = $options['api_key'] ?? $this->apiKey;
        $model = $options['model'] ?? $this->model;

        if (empty($apiKey)) {
            return [
                'success' => false,
                'text' => '',
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'error' => 'Gemini API Key কনফিগার করা হয়নি। দয়া করে সেটিংস থেকে কী যুক্ত করুন।',
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $userInput]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? 0.4,
                        'maxOutputTokens' => $options['max_tokens'] ?? 2048,
                    ],
                ]);

            if (!$response->successful()) {
                $err = $response->json('error.message') ?? $response->body();
                return [
                    'success' => false,
                    'text' => '',
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'error' => "Gemini API Error: " . $err,
                ];
            }

            $data = $response->json();
            $candidates = $data['candidates'] ?? [];
            $generatedText = $candidates[0]['content']['parts'][0]['text'] ?? '';
            $promptTokens = $data['usageMetadata']['promptTokenCount'] ?? 0;
            $candidateTokens = $data['usageMetadata']['candidatesTokenCount'] ?? 0;

            return [
                'success' => true,
                'text' => trim($generatedText),
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $candidateTokens,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'text' => '',
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'error' => "যোগাযোগ ব্যর্থ হয়েছে: " . $e->getMessage(),
            ];
        }
    }
}
