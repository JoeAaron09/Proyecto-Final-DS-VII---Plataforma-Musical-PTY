<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

final class Music
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function songs(): array
    {
        return $this->db->query(
            "SELECT
                c.*,
                a.nombre AS artista,
                a.tipo AS tipo_artista,
                g.nombre AS genero,
                al.nombre AS album
             FROM canciones c
             INNER JOIN artistas a
                ON a.id = c.artista_id
             LEFT JOIN generos g
                ON g.id = a.genero_id
             LEFT JOIN albumes al
                ON al.id = c.album_id
             WHERE c.estado = 1
               AND a.estado = 1
             ORDER BY c.id DESC"
        )->fetchAll();
    }

    public function play(
        int $songId,
        int $userId
    ): array {
        $config = require dirname(__DIR__, 2)
            . '/config.php';

        $userStatement = $this->db->prepare(
            'SELECT tipo_usuario
             FROM usuarios
             WHERE id = ?'
        );

        $userStatement->execute([$userId]);

        $userType = $userStatement->fetchColumn();

        if ($userType !== 'premium') {
            $countStatement = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM reproducciones
                 WHERE usuario_id = ?
                   AND YEAR(fecha_hora) = YEAR(CURRENT_DATE())
                   AND MONTH(fecha_hora) = MONTH(CURRENT_DATE())'
            );

            $countStatement->execute([$userId]);

            $count = (int)$countStatement->fetchColumn();

            if ($count >= $config['free_monthly_limit']) {
                return [
                    'ok' => false,
                    'message' => (
                        'Alcanzaste el límite mensual gratuito. '
                        . 'Activa Premium para continuar.'
                    ),
                ];
            }
        }

        $songStatement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM canciones
             WHERE id = ?
               AND estado = 1'
        );

        $songStatement->execute([$songId]);

        if ((int)$songStatement->fetchColumn() === 0) {
            return [
                'ok' => false,
                'message' => 'La canción seleccionada no está disponible.',
            ];
        }

        $insertStatement = $this->db->prepare(
            'INSERT INTO reproducciones (
                cancion_id,
                usuario_id,
                nacionalidad_usuario,
                fecha_hora
             )
             SELECT
                ?,
                id,
                nacionalidad,
                NOW()
             FROM usuarios
             WHERE id = ?'
        );

        $insertStatement->execute([
            $songId,
            $userId,
        ]);

        return [
            'ok' => true,
            'message' => 'Reproducción registrada.',
        ];
    }

    public function top10(): array
    {
        return $this->db->query(
            "SELECT
                c.nombre AS cancion,
                a.nombre AS artista,
                COUNT(r.id) AS total
             FROM reproducciones r
             INNER JOIN canciones c
                ON c.id = r.cancion_id
             INNER JOIN artistas a
                ON a.id = c.artista_id
             WHERE r.fecha_hora >= DATE_SUB(
                NOW(),
                INTERVAL 30 DAY
             )
             GROUP BY
                c.id,
                c.nombre,
                a.id,
                a.nombre
             ORDER BY total DESC
             LIMIT 10"
        )->fetchAll();
    }

    public function artistNow(): ?array
    {
        $statement = $this->db->query(
            "SELECT
                a.nombre AS artista,
                a.tipo,
                g.nombre AS genero,
                COUNT(r.id) AS total
             FROM artistas a
             INNER JOIN canciones c
                ON c.artista_id = a.id
             INNER JOIN reproducciones r
                ON r.cancion_id = c.id
             LEFT JOIN generos g
                ON g.id = a.genero_id
             WHERE r.fecha_hora >= DATE_SUB(
                NOW(),
                INTERVAL 30 DAY
             )
             GROUP BY
                a.id,
                a.nombre,
                a.tipo,
                g.nombre
             ORDER BY total DESC
             LIMIT 1"
        );

        return $statement->fetch() ?: null;
    }

    public function events(): array
    {
        return $this->db->query(
            "SELECT
                e.*,
                l.nombre AS local_nombre
             FROM eventos e
             LEFT JOIN locales l
                ON l.id = e.local_id
             WHERE e.estado = 1
               AND e.fecha >= CURRENT_DATE()
             ORDER BY e.fecha, e.hora"
        )->fetchAll();
    }
}