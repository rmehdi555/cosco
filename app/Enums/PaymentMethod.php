<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'online';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK = 'check';
    case WALLET = 'wallet';
    case CRYPTO = 'crypto';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'پرداخت آنلاین',
            self::CASH => 'پرداخت نقدی',
            self::BANK_TRANSFER => 'انتقال بانکی',
            self::CHECK => 'چک',
            self::WALLET => 'کیف پول',
            self::CRYPTO => 'ارز دیجیتال',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ONLINE => 'primary',
            self::CASH => 'success',
            self::BANK_TRANSFER => 'info',
            self::CHECK => 'warning',
            self::WALLET => 'secondary',
            self::CRYPTO => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::ONLINE->value => self::ONLINE->getLabel(),
            self::CASH->value => self::CASH->getLabel(),
            self::BANK_TRANSFER->value => self::BANK_TRANSFER->getLabel(),
            self::CHECK->value => self::CHECK->getLabel(),
            self::WALLET->value => self::WALLET->getLabel(),
            self::CRYPTO->value => self::CRYPTO->getLabel(),
        ];
    }
} 