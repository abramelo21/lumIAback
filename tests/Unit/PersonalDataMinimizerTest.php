<?php

namespace Tests\Unit;

use App\Services\PersonalDataMinimizer;
use PHPUnit\Framework\TestCase;

class PersonalDataMinimizerTest extends TestCase
{
    public function test_it_masks_personal_identifiers(): void
    {
        $minimizer = new PersonalDataMinimizer;
        $text = $minimizer->minimize('João CPF 123.456.789-00 email joao@fumec.br tel (31) 98888-7777 CEP 30130-000');

        $this->assertStringNotContainsString('123.456.789-00', $text);
        $this->assertStringNotContainsString('joao@fumec.br', $text);
        $this->assertStringContainsString('[CPF_REMOVIDO]', $text);
        $this->assertStringContainsString('[EMAIL_REMOVIDO]', $text);
        $this->assertStringContainsString('[TELEFONE_REMOVIDO]', $text);
    }
}
