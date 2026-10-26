<?php

namespace App\Enums;

enum ImprovementCategory: string
{
    case Structure = 'estrutura_organizacao';
    case AcademicProjects = 'projetos_academicos';
    case ProfessionalExperience = 'experiencias_profissionais';
    case TechnicalSkills = 'competencias_tecnicas';
    case AreaFit = 'adequacao_area';
    case Clarity = 'clareza_objetividade';
    case ResultsMetrics = 'resultados_metricas';

    public function label(): string
    {
        return match ($this) {
            self::Structure => 'Estrutura e organização',
            self::AcademicProjects => 'Projetos acadêmicos',
            self::ProfessionalExperience => 'Experiências profissionais',
            self::TechnicalSkills => 'Competências técnicas',
            self::AreaFit => 'Adequação à área de interesse',
            self::Clarity => 'Clareza e objetividade',
            self::ResultsMetrics => 'Resultados e métricas',
        };
    }
}
