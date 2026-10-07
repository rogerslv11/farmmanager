<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Gestor = 'gestor';
    case Agronomo = 'agronomo';
    case Estoquista = 'estoquista';
    case Funcionario = 'funcionario';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Gestor => 'Gestor',
            self::Agronomo => 'Agrônomo',
            self::Estoquista => 'Estoquista',
            self::Funcionario => 'Funcionário',
        };
    }
}
