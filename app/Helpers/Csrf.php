<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Core\HttpException;

final class Csrf
{
    public static function token(): string
    {
        if (
            !isset($_SESSION['csrf_token']) ||
            !is_string($_SESSION['csrf_token'])
        ) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        $token = htmlspecialchars(
            self::token(),
            ENT_QUOTES,
            'UTF-8'
        );

        return '<input type="hidden" name="csrf_token" value="' .
            $token .
            '">';
    }

    public static function validate(?string $token): bool
    {
        if (
            !isset($_SESSION['csrf_token']) ||
            !is_string($_SESSION['csrf_token']) ||
            !is_string($token)
        ) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function verify(): void
    {
        $token = $_POST['csrf_token'] ?? null;

        if (!self::validate(is_string($token) ? $token : null)) {
            throw new HttpException(419, 'El formulario expiro. Recargue la pagina e intentelo nuevamente.');
        }
    }
}
