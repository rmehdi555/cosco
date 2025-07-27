<?php

namespace App\Enums;

enum OrderPaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::UNPAID => 'پرداخت نشده',
            self::PAID => 'پرداخت شده',
            self::REFUNDED => 'بازگشت وجه',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PAID => 'success',
            self::REFUNDED => 'warning',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::UNPAID->value => self::UNPAID->getLabel(),
            self::PAID->value => self::PAID->getLabel(),
            self::REFUNDED->value => self::REFUNDED->getLabel(),
        ];
    }
} 