<?php

namespace App\Examples;

use App\Enums\UserType;
use App\Models\User;

/**
 * Example usage of UserType enum
 */
class UserTypeUsage
{
    /**
     * Example of creating a user with enum
     */
    public function createUserExample(): void
    {
        // Create a user with enum
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'cell_phone' => '09123456789',
            'password' => 'password123',
            'type' => UserType::USER, // Using enum instead of string
        ]);
    }

    /**
     * Example of checking user type
     */
    public function checkUserTypeExample(User $user): void
    {
        // Check if user is admin
        if ($user->isAdmin()) {
            echo "User is admin or super admin";
        }

        // Check if user is super admin
        if ($user->isSuperAdmin()) {
            echo "User is super admin";
        }

        // Check if user is seller
        if ($user->isSeller()) {
            echo "User is seller";
        }

        // Check if user is regular user
        if ($user->isUser()) {
            echo "User is regular user";
        }

        // Direct enum comparison
        if ($user->type === UserType::ADMIN) {
            echo "User is admin";
        }
    }

    /**
     * Example of getting enum values
     */
    public function getEnumValuesExample(): void
    {
        // Get all enum values
        $values = UserType::values();
        // Returns: ['super-admin', 'admin', 'seller', 'user']

        // Get enum label in Persian
        $label = UserType::ADMIN->label();
        // Returns: 'ادمین'

        // Get enum label in English
        $labelEn = UserType::ADMIN->labelEn();
        // Returns: 'Admin'
    }

    /**
     * Example of querying users by type
     */
    public function queryUsersByTypeExample(): void
    {
        // Get all admin users
        $adminUsers = User::where('type', UserType::ADMIN)->get();

        // Get all super admin users
        $superAdminUsers = User::where('type', UserType::SUPER_ADMIN)->get();

        // Get all seller users
        $sellerUsers = User::where('type', UserType::SELLER)->get();

        // Get all regular users
        $regularUsers = User::where('type', UserType::USER)->get();
    }

    /**
     * Example of validation rules with enum
     */
    public function validationRulesExample(): array
    {
        return [
            'type' => 'required|in:' . implode(',', UserType::values()),
            // Or using the enum directly in validation
            'type' => ['required', 'enum:' . UserType::class],
        ];
    }
} 