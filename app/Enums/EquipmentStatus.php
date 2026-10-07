<?php

namespace App\Enums;

enum EquipmentStatus: string
{
    case Disponivel = 'disponivel';
    case EmUso = 'em_uso';
    case EmManutencao = 'em_manutencao';
    case Inativo = 'inativo';

    public function label(): string
    {
        return match ($this) {
            self::Disponivel => 'Disponível',
            self::EmUso => 'Em uso',
            self::EmManutencao => 'Em manutenção',
            self::Inativo => 'Inativo',
        };
    }
}
