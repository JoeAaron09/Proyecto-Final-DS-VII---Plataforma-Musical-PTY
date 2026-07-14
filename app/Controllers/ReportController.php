<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Core\Controller;
use App\Helpers\Auth;
use RuntimeException;

final class ReportController extends Controller
{
    public function export(string $type): void
    {
        Auth::requireAdmin();

        $db = Database::connection();

        $queries = [
            'top' => "
                SELECT
                    c.nombre AS cancion,
                    a.nombre AS artista,
                    a.tipo AS tipo_artista,
                    g.nombre AS genero,
                    COUNT(r.id) AS reproducciones
                FROM reproducciones r
                INNER JOIN canciones c
                    ON c.id = r.cancion_id
                INNER JOIN artistas a
                    ON a.id = c.artista_id
                LEFT JOIN generos g
                    ON g.id = a.genero_id
                WHERE r.fecha_hora >= DATE_SUB(
                    NOW(),
                    INTERVAL 30 DAY
                )
                GROUP BY
                    c.id,
                    c.nombre,
                    a.id,
                    a.nombre,
                    a.tipo,
                    g.nombre
                ORDER BY reproducciones DESC
                LIMIT 10
            ",

            'generos' => "
                SELECT
                    g.nombre AS genero,
                    COUNT(r.id) AS reproducciones
                FROM generos g
                LEFT JOIN artistas a
                    ON a.genero_id = g.id
                LEFT JOIN canciones c
                    ON c.artista_id = a.id
                LEFT JOIN reproducciones r
                    ON r.cancion_id = c.id
                GROUP BY
                    g.id,
                    g.nombre
                ORDER BY reproducciones DESC
            ",

            'ventas' => "
                SELECT
                    DATE_FORMAT(fecha_hora, '%Y-%m') AS mes,
                    COUNT(*) AS transacciones,
                    SUM(subtotal) AS subtotal,
                    SUM(itbms) AS itbms,
                    SUM(total) AS total
                FROM compras
                GROUP BY mes
                ORDER BY mes DESC
            ",

            'eventos' => "
                SELECT
                    e.nombre AS evento,
                    e.fecha,
                    e.capacidad,
                    COALESCE(
                        SUM(en.cantidad),
                        0
                    ) AS entradas_vendidas,
                    COALESCE(
                        SUM(en.total),
                        0
                    ) AS ingresos
                FROM eventos e
                LEFT JOIN entradas en
                    ON en.evento_id = e.id
                GROUP BY
                    e.id,
                    e.nombre,
                    e.fecha,
                    e.capacidad
                ORDER BY e.fecha DESC
            ",
        ];

        $sql = $queries[$type] ?? null;

        if ($sql === null) {
            throw new RuntimeException(
                'El tipo de reporte solicitado no existe.'
            );
        }

        $rows = $db->query($sql)->fetchAll();

        header('Content-Type: text/csv; charset=UTF-8');

        header(
            'Content-Disposition: attachment; filename="reporte_'
            . $type
            . '_'
            . date('Ymd')
            . '.csv"'
        );

        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'wb');

        if ($output === false) {
            throw new RuntimeException(
                'No fue posible generar el reporte.'
            );
        }

        if ($rows !== []) {
            fputcsv(
                $output,
                array_keys($rows[0]),
                ';'
            );
        }

        foreach ($rows as $row) {
            fputcsv(
                $output,
                $row,
                ';'
            );
        }

        fclose($output);
    }
}