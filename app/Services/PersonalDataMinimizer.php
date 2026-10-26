<?php

namespace App\Services;

class PersonalDataMinimizer
{
    public function minimize(string $text): string
    {
        $text = preg_replace('/\b\d{3}\.?\d{3}\.?\d{3}-?\d{2}\b/', '[CPF_REMOVIDO]', $text) ?? $text;
        $text = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[EMAIL_REMOVIDO]', $text) ?? $text;
        $text = preg_replace('/(\+?55\s?)?(\(?\d{2}\)?\s?)?(9?\d{4}[-\s]?\d{4})/', '[TELEFONE_REMOVIDO]', $text) ?? $text;
        $text = preg_replace('/\b\d{5}-?\d{3}\b/', '[CEP_REMOVIDO]', $text) ?? $text;

        return trim($text);
    }
}
