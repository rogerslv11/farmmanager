<?php

namespace App\Enums;

enum PlantingStatus: string
{
    case Planejada = 'planejada';
    case Preparacao = 'preparacao';
    case Plantada = 'plantada';
    case Crescimento = 'crescimento';
    case ProntaColheita = 'pronta_colheita';
    case Colhida = 'colhida';
    case Encerrada = 'encerrada';

    public function label(): string
    {
        return match ($this) {
            self::Planejada => 'Planejada',
            self::Preparacao => 'Preparação',
            self::Plantada => 'Plantada',
            self::Crescimento => 'Em crescimento',
            self::ProntaColheita => 'Pronta p/ colheita',
            self::Colhida => 'Colhida',
            self::Encerrada => 'Encerrada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planejada => 'stone',
            self::Preparacao => 'amber',
            self::Plantada => 'sky',
            self::Crescimento => 'agro',
            self::ProntaColheita => 'orange',
            self::Colhida => 'emerald',
            self::Encerrada => 'zinc',
        };
    }
}
