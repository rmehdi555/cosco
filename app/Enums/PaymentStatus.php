<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار',
            self::COMPLETED => 'تکمیل شده',
            self::FAILED => 'ناموفق',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabel(),
            self::COMPLETED->value => self::COMPLETED->getLabel(),
            self::FAILED->value => self::FAILED->getLabel(),
        ];
    }
} 