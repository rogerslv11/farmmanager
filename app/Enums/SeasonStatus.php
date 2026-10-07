<?php

namespace App\Enums;

enum SeasonStatus: string
{
    case Planejada = 'planejada';
    case EmAndamento = 'em_andamento';
    case Colheita = 'colheita';
    case Finalizada = 'finalizada';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Planejada => 'Planejada',
            self::EmAndamento => 'Em andamento',
            self::Colheita => 'Colheita',
            self::Finalizada => 'Finalizada',
            self::Cancelada => 'Cancelada',
        };
    }
}
