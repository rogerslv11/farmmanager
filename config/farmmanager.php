<?php

return [
    'name' => 'FarmManager',
    'version' => '1.0.0',
    'units' => ['kg', 'ton', 'sc', 'L', 'un', 'cx'],
    'expense_categories' => ['sementes', 'fertilizantes', 'defensivos', 'combustivel', 'manutencao', 'mao_de_obra', 'maquinas', 'transporte', 'servicos', 'outros'],
    'activity_types' => ['preparo_solo', 'plantio', 'irrigacao', 'fertilizante', 'defensivo', 'poda', 'capina', 'monitoramento', 'colheita', 'manutencao'],
    'demo_login' => env('DEMO_LOGIN_ENABLED', true),
];
