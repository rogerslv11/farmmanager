<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Pendente = 'pendente';
    case Agendada = 'agendada';
    case EmAndamento = 'em_andamento';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Agendada => 'Agendada',
            self::EmAndamento => 'Em andamento',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }
}
