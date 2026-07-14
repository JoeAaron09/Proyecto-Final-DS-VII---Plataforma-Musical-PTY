<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use RuntimeException;

final class MusicController
{
    public function play(int $songId): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $db = Database::connection();
        $user = Auth::user();

        if (!$user) {
            Auth::go('/login');
        }

        $songStatement = $db->prepare(
            "SELECT id
             FROM canciones
             WHERE id = ?
               AND estado = 1"
        );

        $songStatement->execute([$songId]);

        if (!$songStatement->fetchColumn()) {
            throw new RuntimeException(
                'La canción seleccionada no está disponible.'
            );
        }

        if (
            ($user['tipo_usuario'] ?? 'gratuito')
            === 'gratuito'
        ) {
            $countStatement = $db->prepare(
                "SELECT COUNT(*)
                 FROM reproducciones
                 WHERE usuario_id = ?
                   AND YEAR(fecha_hora) = YEAR(CURDATE())
                   AND MONTH(fecha_hora) = MONTH(CURDATE())"
            );

            $countStatement->execute([
                Auth::id(),
            ]);

            $config = require dirname(__DIR__, 2)
                . '/config.php';

            $limit = (int)$config['free_monthly_limit'];

            if ((int)$countStatement->fetchColumn() >= $limit) {
                Flash::set(
                    'error',
                    'Alcanzaste el límite mensual gratuito. '
                    . 'Adquiere Premium para continuar.'
                );

                Auth::go('/mi-cuenta');
            }
        }

        $insertStatement = $db->prepare(
            "INSERT INTO reproducciones (
                cancion_id,
                usuario_id,
                nacionalidad_usuario,
                fecha_hora
             ) VALUES (?, ?, ?, NOW())"
        );

        $insertStatement->execute([
            $songId,
            Auth::id(),
            $user['nacionalidad'] ?? null,
        ]);

        Flash::set(
            'success',
            'Reproducción registrada.'
        );

        Auth::go('/#canciones');
    }

    public function favorite(int $songId): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $db = Database::connection();

        $songStatement = $db->prepare(
            "SELECT id
             FROM canciones
             WHERE id = ?
               AND estado = 1"
        );

        $songStatement->execute([$songId]);

        if (!$songStatement->fetchColumn()) {
            throw new RuntimeException(
                'La canción seleccionada no está disponible.'
            );
        }

        $favoriteStatement = $db->prepare(
            "SELECT id
             FROM favoritos
             WHERE usuario_id = ?
               AND cancion_id = ?"
        );

        $favoriteStatement->execute([
            Auth::id(),
            $songId,
        ]);

        $favoriteId = $favoriteStatement->fetchColumn();

        if ($favoriteId) {
            $deleteStatement = $db->prepare(
                "DELETE FROM favoritos
                 WHERE usuario_id = ?
                   AND cancion_id = ?"
            );

            $deleteStatement->execute([
                Auth::id(),
                $songId,
            ]);

            Flash::set(
                'success',
                'Canción eliminada de favoritos.'
            );
        } else {
            $insertStatement = $db->prepare(
                "INSERT INTO favoritos (
                    usuario_id,
                    cancion_id
                 ) VALUES (?, ?)"
            );

            $insertStatement->execute([
                Auth::id(),
                $songId,
            ]);

            Flash::set(
                'success',
                'Canción agregada a favoritos.'
            );
        }

        Auth::go('/mi-cuenta');
    }
}