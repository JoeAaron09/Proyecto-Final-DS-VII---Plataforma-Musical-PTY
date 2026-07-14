<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Repository;

final class HomeController extends Controller
{
    public function index(): void
    {
        $repository = new Repository();
        $db = $repository->db();

        $songs = $db->query(
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

        $top = $db->query(
            "SELECT
                c.nombre AS cancion,
                c.imagen_url,
                a.nombre AS artista,
                COUNT(r.id) AS reproducciones
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
                c.imagen_url,
                a.id,
                a.nombre
             ORDER BY reproducciones DESC
             LIMIT 10"
        )->fetchAll();

        $artist = $db->query(
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
        )->fetch() ?: null;

        $events = $db->query(
            "SELECT
                e.*,
                l.nombre AS local_nombre
             FROM eventos e
             LEFT JOIN locales l
                ON l.id = e.local_id
             WHERE e.estado = 1
               AND e.fecha >= CURDATE()
             ORDER BY e.fecha, e.hora
             LIMIT 8"
        )->fetchAll();

        $artists = $db->query(
            "SELECT
                a.*,
                g.nombre AS genero
             FROM artistas a
             LEFT JOIN generos g
                ON g.id = a.genero_id
             WHERE a.estado = 1
             ORDER BY a.nombre"
        )->fetchAll();

        $venues = $db->query(
            "SELECT *
             FROM locales
             WHERE estado = 1
             ORDER BY nombre"
        )->fetchAll();

        $plans = $db->query(
            "SELECT *
             FROM planes
             WHERE estado = 1
             ORDER BY precio"
        )->fetchAll();

        $this->view(
            'public/home',
            compact(
                'songs',
                'top',
                'artist',
                'events',
                'artists',
                'venues',
                'plans'
            )
        );
    }
}
