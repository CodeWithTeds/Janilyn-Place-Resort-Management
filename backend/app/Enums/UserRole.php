<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case GUEST = 'guest';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::STAFF => 'Staff',
            self::GUEST => 'Guest',
        };
    }
}
