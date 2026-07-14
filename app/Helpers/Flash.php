<?php

declare(strict_types=1);

namespace App\Helpers;

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'] = [$type, $message];
    }

    public static function pull(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        return $flash;
    }
}
