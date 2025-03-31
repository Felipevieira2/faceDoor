<?php

namespace App\Factories;


use App\Strategies\AutorizacaoStrategy;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\Intelbras\IntelbrasStrategy;

class AutorizacaoStrategyFactory
{
    public static function criarStrategy(string $fabricante): AutorizacaoStrategy
    {
        return match ($fabricante) {
            'controlid' => new ControlIdStrategy(),
            'intelbras' => new IntelbrasStrategy(),
            default => throw new \InvalidArgumentException('Tipo de dispositivo não suportado'),
        };
    }
} 