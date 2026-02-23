<?php

namespace App\Enum;

final class OrderStatus
{
    public const DRAFT = 'BROUILLON';
    public const PENDING_PAYMENT = 'EN_ATTENTE_PAIEMENT';
    public const PAID = 'PAYEE';
    public const PREPARING = 'EN_PREPARATION';
    public const SHIPPED = 'EXPEDIEE';
    public const DELIVERED = 'LIVREE';
    public const CANCELED = 'ANNULEE';

    /** @return array<string, list<string>> */
    public static function transitions(): array
    {
        return [
            self::DRAFT => [self::PENDING_PAYMENT, self::CANCELED],
            self::PENDING_PAYMENT => [self::PAID, self::CANCELED],
            self::PAID => [self::PREPARING, self::CANCELED],
            self::PREPARING => [self::SHIPPED, self::CANCELED],
            self::SHIPPED => [self::DELIVERED],
            self::DELIVERED => [],
            self::CANCELED => [],
        ];
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::transitions()[$from] ?? [], true);
    }
}
