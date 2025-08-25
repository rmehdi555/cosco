<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار',
            self::PAID => 'پرداخت شده',
            self::SHIPPED => 'ارسال شده',
            self::DELIVERED => 'تحویل داده شده',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::SHIPPED => 'info',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabel(),
            self::PAID->value => self::PAID->getLabel(),
            self::SHIPPED->value => self::SHIPPED->getLabel(),
            self::DELIVERED->value => self::DELIVERED->getLabel(),
            self::CANCELLED->value => self::CANCELLED->getLabel(),
        ];
    }

    public static function getNamePairs(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'name_en' => $case->value,
                'name_fa' => $case->getLabel(),
            ];
        }
        return $result;
    }

}
