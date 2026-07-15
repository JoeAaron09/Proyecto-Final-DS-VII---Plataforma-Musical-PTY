<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeImmutable;
use RuntimeException;

/** Centraliza la sanitizacion y validacion de toda entrada del usuario. */
final class Input
{
    public static function text(mixed $value, string $field, int $max = 255, bool $required = true): ?string
    {
        if (is_array($value) || is_object($value)) {
            throw new RuntimeException("El campo {$field} no es valido.");
        }

        $clean = trim(strip_tags((string)$value));
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean) ?? '';

        if ($clean === '') {
            if ($required) {
                throw new RuntimeException("El campo {$field} es obligatorio.");
            }
            return null;
        }

        if (mb_strlen($clean, 'UTF-8') > $max) {
            throw new RuntimeException("El campo {$field} no puede superar {$max} caracteres.");
        }

        return $clean;
    }

    public static function email(mixed $value, string $field = 'correo'): string
    {
        $email = strtolower((string)(self::text($value, $field, 254) ?? ''));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException("El campo {$field} debe ser un correo valido.");
        }
        return $email;
    }

    public static function integer(mixed $value, string $field, int $min = 1, ?int $max = null): int
    {
        $options = ['options' => ['min_range' => $min]];
        if ($max !== null) {
            $options['options']['max_range'] = $max;
        }
        $integer = filter_var($value, FILTER_VALIDATE_INT, $options);
        if ($integer === false) {
            throw new RuntimeException("El campo {$field} debe ser un numero entero valido.");
        }
        return $integer;
    }

    public static function decimal(mixed $value, string $field, float $min = 0): string
    {
        if (is_array($value) || !is_numeric($value) || (float)$value < $min) {
            throw new RuntimeException("El campo {$field} debe ser un numero valido.");
        }
        return number_format((float)$value, 2, '.', '');
    }

    public static function choice(mixed $value, array $allowed, string $field): string
    {
        $clean = (string)(self::text($value, $field, 100) ?? '');
        if (!in_array($clean, $allowed, true)) {
            throw new RuntimeException("El valor seleccionado para {$field} no es valido.");
        }
        return $clean;
    }

    public static function date(mixed $value, string $field): string
    {
        $clean = (string)(self::text($value, $field, 10) ?? '');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $clean);
        if (!$date || $date->format('Y-m-d') !== $clean) {
            throw new RuntimeException("El campo {$field} debe contener una fecha valida.");
        }
        return $clean;
    }

    public static function time(mixed $value, string $field): string
    {
        $clean = (string)(self::text($value, $field, 8) ?? '');
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $clean)) {
            throw new RuntimeException("El campo {$field} debe contener una hora valida.");
        }
        return $clean;
    }

    public static function password(mixed $value, string $field = 'contrasena'): string
    {
        if (!is_string($value) || strlen($value) < 8 || strlen($value) > 255) {
            throw new RuntimeException("La {$field} debe tener entre 8 y 255 caracteres.");
        }
        return $value;
    }
}
