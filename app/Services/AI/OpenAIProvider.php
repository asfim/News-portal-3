<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProviderInterface
{
    public function __construct(
        protected ?string $apiKey = null,
        protected string $model = 'gpt-4o-mini'
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
                'error' => 'OpenAI API Key কনফিগার করা হয়নি।',
            ];
        }

        try {
            $response = Http::timeout(25)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userInput],
                    ],
                    'temperature' => $options['temperature'] ?? 0.4,
                    'max_tokens' => $options['max_tokens'] ?? 2048,
                ]);

            if (!$response->successful()) {
                $err = $response->json('error.message') ?? $response->body();
                return [
                    'success' => false,
                    'text' => '',
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'error' => "OpenAI Error: " . $err,
                ];
            }

            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? '';
            $promptTokens = $data['usage']['prompt_tokens'] ?? 0;
            $completionTokens = $data['usage']['completion_tokens'] ?? 0;

            return [
                'success' => true,
                'text' => trim($text),
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'text' => '',
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }
}
