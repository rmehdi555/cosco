<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'online';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'آنلاین',
            self::CASH => 'نقدی',
            self::BANK_TRANSFER => 'انتقال بانکی',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ONLINE => 'success',
            self::CASH => 'warning',
            self::BANK_TRANSFER => 'info',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return $this->color();
    }

    public static function options(): array
    {
        return [
            self::ONLINE->value => self::ONLINE->label(),
            self::CASH->value => self::CASH->label(),
            self::BANK_TRANSFER->value => self::BANK_TRANSFER->label(),
        ];
    }

    public static function getOptions(): array
    {
        return self::options();
    }
} 