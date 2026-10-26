<?php

namespace App\Enums;

enum ProfessionalArea: string
{
    case SoftwareDevelopment = 'desenvolvimento_software';
    case DataBi = 'dados_bi';
    case InformationSecurity = 'seguranca_informacao';
    case Infrastructure = 'infraestrutura';
    case TechnicalSupport = 'suporte_tecnico';
    case CloudComputing = 'cloud_computing';
    case ItManagement = 'gestao_ti';

    public function label(): string
    {
        return match ($this) {
            self::SoftwareDevelopment => 'Desenvolvimento de software',
            self::DataBi => 'Dados e Business Intelligence',
            self::InformationSecurity => 'Segurança da informação',
            self::Infrastructure => 'Infraestrutura',
            self::TechnicalSupport => 'Suporte técnico',
            self::CloudComputing => 'Cloud Computing',
            self::ItManagement => 'Gestão de Tecnologia da Informação',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $area) => ['value' => $area->value, 'label' => $area->label()],
            self::cases()
        );
    }
}
