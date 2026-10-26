<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    public function name(): string;

    public function model(): string;

    /**
     * @return array<string, mixed>
     */
    public function analyze(string $resumeText, string $professionalArea, string $promptVersion): array;
}
