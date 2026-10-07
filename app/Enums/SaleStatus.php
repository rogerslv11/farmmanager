<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Pendente = 'pendente';
    case Confirmada = 'confirmada';
    case Faturada = 'faturada';
    case Entregue = 'entregue';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Confirmada => 'Confirmada',
            self::Faturada => 'Faturada',
            self::Entregue => 'Entregue',
            self::Cancelada => 'Cancelada',
        };
    }
}
