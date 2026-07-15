<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Core\Controller;
use App\Core\HttpException;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Input;
use PDO;
use RuntimeException;
use Throwable;

final class AccountController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $db = Database::connection();
        $userId = (int)Auth::id();

        $favoritesStatement = $db->prepare(
            "SELECT
                c.id,
                c.nombre,
                c.duracion,
                c.audio_url,
                c.imagen_url,
                a.nombre AS artista,
                a.tipo AS tipo_artista,
                g.nombre AS genero,
                al.nombre AS album
             FROM favoritos f
             INNER JOIN canciones c
                ON c.id = f.cancion_id
             INNER JOIN artistas a
                ON a.id = c.artista_id
             LEFT JOIN generos g
                ON g.id = a.genero_id
             LEFT JOIN albumes al
                ON al.id = c.album_id
             WHERE f.usuario_id = ?
             ORDER BY f.creado_en DESC"
        );

        $favoritesStatement->execute([$userId]);
        $favorites = $favoritesStatement->fetchAll();

        $listsStatement = $db->prepare(
            "SELECT
                l.id,
                l.nombre,
                l.descripcion,
                l.creado_en,
                COUNT(lc.cancion_id) AS total_canciones
             FROM listas l
             LEFT JOIN lista_canciones lc
                ON lc.lista_id = l.id
             WHERE l.usuario_id = ?
             GROUP BY
                l.id,
                l.nombre,
                l.descripcion,
                l.creado_en
             ORDER BY l.id DESC"
        );

        $listsStatement->execute([$userId]);
        $lists = $listsStatement->fetchAll();

        $listSongsStatement = $db->prepare(
            "SELECT
                lc.lista_id,
                c.id,
                c.nombre,
                a.nombre AS artista
             FROM lista_canciones lc
             INNER JOIN listas l
                ON l.id = lc.lista_id
             INNER JOIN canciones c
                ON c.id = lc.cancion_id
             INNER JOIN artistas a
                ON a.id = c.artista_id
             WHERE l.usuario_id = ?
             ORDER BY lc.lista_id DESC, lc.agregado_en DESC"
        );

        $listSongsStatement->execute([$userId]);

        $listSongs = [];

        foreach ($listSongsStatement->fetchAll() as $listSong) {
            $listId = (int)$listSong['lista_id'];

            $listSongs[$listId][] = $listSong;
        }

        $purchasesStatement = $db->prepare(
            "SELECT
                co.id,
                co.subtotal,
                co.itbms,
                co.total,
                co.estado,
                co.metodo_pago,
                DATE_FORMAT(
                    co.fecha_hora,
                    '%d/%m/%Y %H:%i'
                ) AS fecha,
                p.nombre AS concepto
             FROM compras co
             LEFT JOIN planes p
                ON p.id = co.plan_id
             WHERE co.usuario_id = ?
             ORDER BY co.id DESC"
        );

        $purchasesStatement->execute([$userId]);
        $purchases = $purchasesStatement->fetchAll();

        $ticketsStatement = $db->prepare(
            "SELECT
                en.id,
                en.cantidad,
                en.precio_unitario,
                en.subtotal,
                en.itbms,
                en.total,
                en.estado,
                en.metodo_pago,
                en.numero_factura,
                (
                    SELECT GROUP_CONCAT(
                        ea.asiento
                        ORDER BY ea.asiento
                        SEPARATOR ', '
                    )
                    FROM entrada_asientos ea
                    WHERE ea.entrada_id = en.id
                ) AS asientos,
                DATE_FORMAT(
                    en.fecha_hora,
                    '%d/%m/%Y %H:%i'
                ) AS fecha_compra,
                e.nombre AS evento,
                e.fecha AS fecha_evento,
                e.hora,
                l.nombre AS local_nombre
             FROM entradas en
             INNER JOIN eventos e
                ON e.id = en.evento_id
             LEFT JOIN locales l
                ON l.id = e.local_id
             WHERE en.usuario_id = ?
             ORDER BY en.id DESC"
        );

        $ticketsStatement->execute([$userId]);
        $tickets = $ticketsStatement->fetchAll();

        $subscriptionStatement = $db->prepare(
            "SELECT
                s.id,
                s.fecha_inicio,
                s.fecha_fin,
                s.estado,
                p.nombre AS plan,
                p.precio
             FROM suscripciones s
             INNER JOIN planes p
                ON p.id = s.plan_id
             WHERE s.usuario_id = ?
               AND s.estado = 1
               AND s.fecha_fin >= CURDATE()
             ORDER BY s.fecha_fin DESC
             LIMIT 1"
        );

        $subscriptionStatement->execute([$userId]);
        $subscription = $subscriptionStatement->fetch() ?: null;

        $plans = $db->query(
            "SELECT *
             FROM planes
             WHERE estado = 1
             ORDER BY precio ASC"
        )->fetchAll();

        $events = $db->query(
            "SELECT
                e.*,
                l.nombre AS local_nombre
             FROM eventos e
             LEFT JOIN locales l
                ON l.id = e.local_id
             WHERE e.estado = 1
               AND e.fecha >= CURDATE()
             ORDER BY e.fecha ASC, e.hora ASC"
        )->fetchAll();

        $songs = $db->query(
            "SELECT
                c.id,
                c.nombre,
                a.nombre AS artista
             FROM canciones c
             INNER JOIN artistas a
                ON a.id = c.artista_id
             WHERE c.estado = 1
               AND a.estado = 1
             ORDER BY c.nombre ASC"
        )->fetchAll();

        $this->view(
            'account/index',
            compact(
                'favorites',
                'lists',
                'listSongs',
                'purchases',
                'tickets',
                'subscription',
                'plans',
                'events',
                'songs'
            )
        );
    }

    public function createList(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        try {
            $name = Input::text($_POST['nombre'] ?? '', 'nombre', 100);
            $description = Input::text($_POST['descripcion'] ?? '', 'descripcion', 1000, false);
        } catch (RuntimeException $exception) {
            Flash::set(
                'error',
                $exception->getMessage()
            );
            Auth::go('/mi-cuenta');
        }

        $statement = Database::connection()->prepare(
            "INSERT INTO listas (
                usuario_id,
                nombre,
                descripcion
             ) VALUES (?, ?, ?)"
        );

        $statement->execute([
            Auth::id(),
            $name,
            $description,
        ]);

        Flash::set(
            'success',
            'Lista creada correctamente.'
        );

        Auth::go('/mi-cuenta');
    }

    public function addSong(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        try {
            $listId = Input::integer($_POST['lista_id'] ?? null, 'lista');
            $songId = Input::integer($_POST['cancion_id'] ?? null, 'cancion');
        } catch (RuntimeException $exception) {
            Flash::set(
                'error',
                $exception->getMessage()
            );

            Auth::go('/mi-cuenta');
        }

        $db = Database::connection();

        $listStatement = $db->prepare(
            "SELECT id
             FROM listas
             WHERE id = ?
               AND usuario_id = ?"
        );

        $listStatement->execute([
            $listId,
            Auth::id(),
        ]);

        if (!$listStatement->fetchColumn()) {
            throw new RuntimeException(
                'La lista seleccionada no pertenece al usuario.'
            );
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

        $insertStatement = $db->prepare(
            "INSERT IGNORE INTO lista_canciones (
                lista_id,
                cancion_id
             ) VALUES (?, ?)"
        );

        $insertStatement->execute([
            $listId,
            $songId,
        ]);

        Flash::set(
            'success',
            'Canción agregada a la lista.'
        );

        Auth::go('/mi-cuenta');
    }

    public function buyPlan(int $planId): void
    {
        Auth::requireLogin();
        if (in_array(Auth::role(), ['Administrador', 'Operador'], true)) {
            throw new HttpException(403, 'El personal del sistema ya cuenta con acceso Premium.');
        }
        Csrf::verify();

        $validPaymentMethods = ['yappy', 'tarjeta', 'transferencia'];
        $paymentMethod = Input::choice($_POST['metodo_pago'] ?? '', $validPaymentMethods, 'metodo de pago');

        $db = Database::connection();

        $planStatement = $db->prepare(
            "SELECT *
             FROM planes
             WHERE id = ?
               AND estado = 1"
        );

        $planStatement->execute([$planId]);
        $plan = $planStatement->fetch();

        if (!$plan) {
            throw new RuntimeException(
                'El plan seleccionado no está disponible.'
            );
        }

        $subtotal = (float)$plan['precio'];
        $tax = round($subtotal * 0.07, 2);
        $total = $subtotal + $tax;

        $startDate = date('Y-m-d');
        $endDate = date(
            'Y-m-d',
            strtotime(
                '+' . (int)$plan['duracion_dias'] . ' days'
            )
        );

        try {
            $db->beginTransaction();

            $deactivateStatement = $db->prepare(
                "UPDATE suscripciones
                 SET estado = 0
                 WHERE usuario_id = ?
                   AND estado = 1"
            );

            $deactivateStatement->execute([
                Auth::id(),
            ]);

            $purchaseStatement = $db->prepare(
                "INSERT INTO compras (
                    usuario_id,
                    plan_id,
                    subtotal,
                    itbms,
                    total,
                    estado,
                    metodo_pago,
                    fecha_hora
                 ) VALUES (?, ?, ?, ?, ?, 'pagada', ?, NOW())"
            );

            $purchaseStatement->execute([
                Auth::id(),
                $planId,
                $subtotal,
                $tax,
                $total,
                $paymentMethod,
            ]);

            $purchaseId = (int)$db->lastInsertId();

            $subscriptionStatement = $db->prepare(
                "INSERT INTO suscripciones (
                    usuario_id,
                    plan_id,
                    compra_id,
                    fecha_inicio,
                    fecha_fin,
                    estado
                 ) VALUES (?, ?, ?, ?, ?, 1)"
            );

            $subscriptionStatement->execute([
                Auth::id(),
                $planId,
                $purchaseId,
                $startDate,
                $endDate,
            ]);

            $userStatement = $db->prepare(
                "UPDATE usuarios
                 SET tipo_usuario = 'premium'
                 WHERE id = ?"
            );

            $userStatement->execute([
                Auth::id(),
            ]);

            if (isset($_SESSION['user'])) {
                $_SESSION['user']['tipo_usuario'] = 'premium';
            }

            $db->commit();
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }

        Flash::set(
            'success',
            'Plan Premium adquirido correctamente.'
        );

        Auth::go('/mi-cuenta');
    }

    public function buyTicket(int $eventId): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $quantity = max(
            1,
            (int)($_POST['cantidad'] ?? 1)
        );

        $db = Database::connection();

        $eventStatement = $db->prepare(
            "SELECT *
             FROM eventos
             WHERE id = ?
               AND estado = 1
               AND fecha >= CURDATE()"
        );

        $eventStatement->execute([$eventId]);
        $event = $eventStatement->fetch();

        if (!$event) {
            throw new RuntimeException(
                'El evento seleccionado no está disponible.'
            );
        }

        $soldStatement = $db->prepare(
            "SELECT COALESCE(SUM(cantidad), 0)
             FROM entradas
             WHERE evento_id = ?
               AND estado <> 'cancelada'"
        );

        $soldStatement->execute([$eventId]);
        $sold = (int)$soldStatement->fetchColumn();

        $capacity = (int)($event['capacidad'] ?? 0);

        if (
            $capacity > 0
            && ($sold + $quantity) > $capacity
        ) {
            throw new RuntimeException(
                'No hay suficientes entradas disponibles.'
            );
        }

        $unitPrice = (float)$event['precio'];
        $subtotal = $unitPrice * $quantity;
        $tax = round($subtotal * 0.07, 2);
        $total = $subtotal + $tax;

        $insertStatement = $db->prepare(
            "INSERT INTO entradas (
                evento_id,
                usuario_id,
                cantidad,
                precio_unitario,
                subtotal,
                itbms,
                total,
                estado,
                fecha_hora
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pagada', NOW())"
        );

        $insertStatement->execute([
            $eventId,
            Auth::id(),
            $quantity,
            $unitPrice,
            $subtotal,
            $tax,
            $total,
        ]);

        Flash::set(
            'success',
            'Entradas compradas correctamente.'
        );

        Auth::go('/mi-cuenta');
    }
}
