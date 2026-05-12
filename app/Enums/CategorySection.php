<?php

namespace App\Enums;

enum CategorySection: string
{
    case FILTRO = 'filtro';
    case PRODUTO = 'produto';

    public function label(): string
    {
        return match($this) {
            self::FILTRO => 'Filtro',
            self::PRODUTO => 'Produto',
        };
    }

    /**
     * Get all values as array (['tipo','espessura','produto'])
     * Useful for validation rules.
     */
    public static function values(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }

    /**
     * Get options as value => label for forms
     */
    public static function options(): array
    {
        return array_combine(self::values(), array_map(fn($c) => $c->label(), self::cases()));
    }
}
