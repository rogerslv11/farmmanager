<?php

namespace App\Enums;

enum MovementType: string
{
    case Entrada = 'entrada';
    case Saida = 'saida';
    case Ajuste = 'ajuste';
    case Transferencia = 'transferencia';

    public function label(): string
    {
        return match ($this) {
            self::Entrada => 'Entrada',
            self::Saida => 'Saída',
            self::Ajuste => 'Ajuste',
            self::Transferencia => 'Transferência',
        };
    }
}
