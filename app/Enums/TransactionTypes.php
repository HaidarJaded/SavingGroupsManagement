<?php

namespace App\Enums;

enum TransactionTypes: string
{
    case Import = 'صادر';
    case Export = 'وارد';
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
