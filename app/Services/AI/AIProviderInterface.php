<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * Generate text given a system prompt and user input.
     *
     * @param string $systemPrompt
     * @param string $userInput
     * @param array $options (model, temperature, max_tokens, etc.)
     * @return array ['success' => bool, 'text' => string, 'prompt_tokens' => int, 'completion_tokens' => int, 'error' => ?string]
     */
    public function generate(string $systemPrompt, string $userInput, array $options = []): array;
}
