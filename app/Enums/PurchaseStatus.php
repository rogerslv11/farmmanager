<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case Rascunho = 'rascunho';
    case Solicitada = 'solicitada';
    case Aprovada = 'aprovada';
    case Recebida = 'recebida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::Solicitada => 'Solicitada',
            self::Aprovada => 'Aprovada',
            self::Recebida => 'Recebida',
            self::Cancelada => 'Cancelada',
        };
    }
}
