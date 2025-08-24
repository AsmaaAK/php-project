<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    public const SESSION_KEY = 'user_id';

    public static function login(int $userId): void
    {
        $_SESSION[self::SESSION_KEY] = $userId;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && is_int($_SESSION[self::SESSION_KEY]);
    }

    public static function id(): ?int
    {
        return self::check() ? $_SESSION[self::SESSION_KEY] : null;
    }
}
