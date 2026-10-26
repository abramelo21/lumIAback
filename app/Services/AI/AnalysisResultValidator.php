<?php

namespace App\Services\AI;

use App\Enums\ImprovementCategory;
use InvalidArgumentException;

class AnalysisResultValidator
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function validate(array $payload): array
    {
        foreach (['summary', 'identified_skills', 'strengths', 'improvement_points', 'project_recommendations', 'experience_recommendations', 'action_plan'] as $field) {
            if (! array_key_exists($field, $payload)) {
                throw new InvalidArgumentException("Campo obrigatório ausente na resposta da IA: {$field}");
            }
        }

        if (! is_string($payload['summary']) || mb_strlen($payload['summary']) < 20) {
            throw new InvalidArgumentException('O resumo da análise é inválido.');
        }

        $skills = $this->normalizeSkills($payload['identified_skills']);
        $strengths = $this->stringList($payload['strengths'], 'strengths');
        $improvements = $this->normalizeImprovements($payload['improvement_points']);

        return [
            'summary' => mb_substr($payload['summary'], 0, 4000),
            'identified_skills' => $skills,
            'strengths' => $strengths,
            'improvement_points' => $improvements,
            'project_recommendations' => $this->stringList($payload['project_recommendations'], 'project_recommendations'),
            'experience_recommendations' => $this->stringList($payload['experience_recommendations'], 'experience_recommendations'),
            'action_plan' => $this->stringList($payload['action_plan'], 'action_plan'),
            'structure_notes' => is_array($payload['structure_notes'] ?? null) ? $payload['structure_notes'] : null,
            'area_fit' => is_array($payload['area_fit'] ?? null) ? $payload['area_fit'] : null,
        ];
    }

    /**
     * @return array<int, array{name: string, evidence: string}>
     */
    private function normalizeSkills(mixed $skills): array
    {
        if (! is_array($skills)) {
            throw new InvalidArgumentException('identified_skills deve ser uma lista.');
        }

        return array_values(array_map(function ($skill) {
            if (is_string($skill)) {
                return ['name' => $skill, 'evidence' => 'Mencionada no currículo'];
            }

            if (! is_array($skill) || empty($skill['name'])) {
                throw new InvalidArgumentException('Competência identificada inválida.');
            }

            return [
                'name' => (string) $skill['name'],
                'evidence' => (string) ($skill['evidence'] ?? 'Mencionada no currículo'),
            ];
        }, $skills));
    }

    /**
     * @return array<int, array{title: string, recommendation: string, category: string}>
     */
    private function normalizeImprovements(mixed $items): array
    {
        if (! is_array($items)) {
            throw new InvalidArgumentException('improvement_points deve ser uma lista.');
        }

        $allowed = array_map(fn (ImprovementCategory $c) => $c->value, ImprovementCategory::cases());

        return array_values(array_map(function ($item) use ($allowed) {
            if (is_string($item)) {
                return [
                    'title' => $item,
                    'recommendation' => $item,
                    'category' => ImprovementCategory::Clarity->value,
                ];
            }

            $category = (string) ($item['category'] ?? ImprovementCategory::Clarity->value);
            if (! in_array($category, $allowed, true)) {
                $category = ImprovementCategory::Clarity->value;
            }

            return [
                'title' => (string) ($item['title'] ?? 'Oportunidade de melhoria'),
                'recommendation' => (string) ($item['recommendation'] ?? ''),
                'category' => $category,
            ];
        }, $items));
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $items, string $field): array
    {
        if (! is_array($items)) {
            throw new InvalidArgumentException("{$field} deve ser uma lista.");
        }

        return array_values(array_map(fn ($item) => is_string($item) ? $item : json_encode($item), $items));
    }
}
