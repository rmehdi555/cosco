<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار',
            self::COMPLETED => 'تکمیل شده',
            self::FAILED => 'ناموفق',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
        };
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::FAILED->value => self::FAILED->label(),
        ];
    }
} 