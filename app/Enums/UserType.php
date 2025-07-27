<?php

namespace App\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case SELLER = 'seller';
    case USER = 'user';

    /**
     * Get all user type values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get user type label in Persian
     */
    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'سوپر ادمین',
            self::ADMIN => 'ادمین',
            self::SELLER => 'فروشنده',
            self::USER => 'کاربر',
        };
    }

    /**
     * Get user type label in English
     */
    public function labelEn(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::SELLER => 'Seller',
            self::USER => 'User',
        };
    }

    /**
     * Check if user type is admin or super admin
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if user type is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    /**
     * Check if user type is seller
     */
    public function isSeller(): bool
    {
        return $this === self::SELLER;
    }

    /**
     * Check if user type is regular user
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }
} 