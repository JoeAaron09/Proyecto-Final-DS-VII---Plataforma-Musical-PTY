<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Core\Controller;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use RuntimeException;
use Throwable;

final class EventController extends Controller
{
    public function show(int $eventId): void
    {
        Auth::requireLogin();
        $this->requireCustomer();

        $db = Database::connection();
        $event = $this->findEvent($eventId);
        $hasAssignedSeats = $this->hasAssignedSeats(
            (string)($event['local_tipo'] ?? '')
        );
        $occupiedSeats = [];

        if ($hasAssignedSeats) {
            $statement = $db->prepare(
                "SELECT ea.asiento
                 FROM entrada_asientos ea
                 INNER JOIN entradas en ON en.id = ea.entrada_id
                 WHERE en.evento_id = ?
                   AND en.estado <> 'cancelada'"
            );
            $statement->execute([$eventId]);
            $occupiedSeats = $statement->fetchAll(\PDO::FETCH_COLUMN);
        }

        $this->view(
            'events/show',
            compact('event', 'hasAssignedSeats', 'occupiedSeats')
        );
    }

    public function buy(int $eventId): void
    {
        Auth::requireLogin();
        $this->requireCustomer();
        Csrf::verify();

        $db = Database::connection();
        $event = $this->findEvent($eventId);
        $hasAssignedSeats = $this->hasAssignedSeats(
            (string)($event['local_tipo'] ?? '')
        );
        $paymentMethod = (string)($_POST['metodo_pago'] ?? '');
        $validMethods = ['yappy', 'tarjeta', 'transferencia'];

        if (!in_array($paymentMethod, $validMethods, true)) {
            throw new RuntimeException('Seleccione un método de pago válido.');
        }

        $seats = [];
        if ($hasAssignedSeats) {
            $rawSeats = explode(',', (string)($_POST['asientos'] ?? ''));
            $seats = array_values(array_unique(array_filter(array_map(
                static fn(string $seat): string => strtoupper(trim($seat)),
                $rawSeats
            ))));

            foreach ($seats as $seat) {
                if (!preg_match('/^[A-H](?:[1-9]|1[0-2])$/', $seat)) {
                    throw new RuntimeException('La selección de asientos no es válida.');
                }
            }

            if ($seats === []) {
                throw new RuntimeException('Seleccione al menos un asiento.');
            }
            $quantity = count($seats);
        } else {
            $quantity = max(1, min(10, (int)($_POST['cantidad'] ?? 1)));
        }

        $unitPrice = (float)$event['precio'];
        $subtotal = round($unitPrice * $quantity, 2);
        $tax = round($subtotal * 0.07, 2);
        $total = $subtotal + $tax;
        $invoiceNumber = 'RRP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $qrToken = bin2hex(random_bytes(24));

        try {
            $db->beginTransaction();

            $soldStatement = $db->prepare(
                "SELECT COALESCE(SUM(cantidad), 0)
                 FROM entradas
                 WHERE evento_id = ?
                   AND estado <> 'cancelada'
                 FOR UPDATE"
            );
            $soldStatement->execute([$eventId]);
            $sold = (int)$soldStatement->fetchColumn();
            $capacity = (int)($event['capacidad'] ?? 0);

            if ($capacity > 0 && ($sold + $quantity) > $capacity) {
                throw new RuntimeException('No hay suficientes entradas disponibles.');
            }

            $insertStatement = $db->prepare(
                "INSERT INTO entradas (
                    evento_id, usuario_id, cantidad, precio_unitario,
                    subtotal, itbms, total, estado, metodo_pago,
                    numero_factura, qr_token, fecha_hora
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pagada', ?, ?, ?, NOW())"
            );
            $insertStatement->execute([
                $eventId,
                Auth::id(),
                $quantity,
                $unitPrice,
                $subtotal,
                $tax,
                $total,
                $paymentMethod,
                $invoiceNumber,
                $qrToken,
            ]);
            $ticketId = (int)$db->lastInsertId();

            if ($seats !== []) {
                $seatStatement = $db->prepare(
                    "INSERT INTO entrada_asientos (entrada_id, evento_id, asiento)
                     VALUES (?, ?, ?)"
                );
                foreach ($seats as $seat) {
                    $seatStatement->execute([$ticketId, $eventId, $seat]);
                }
            }

            $db->commit();
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ((string)$exception->getCode() === '23000') {
                throw new RuntimeException(
                    'Uno de los asientos acaba de ser ocupado. Seleccione otro.'
                );
            }
            throw $exception;
        }

        Flash::set('success', 'Compra simulada completada. Tu entrada ya está disponible.');
        Auth::go('/facturas/entrada/' . $ticketId);
    }

    public function invoice(int $ticketId): void
    {
        Auth::requireLogin();
        $ticket = $this->findTicket($ticketId, (int)Auth::id());
        $this->view('events/invoice', compact('ticket'));
    }

    public function verify(string $token): void
    {
        $db = Database::connection();
        $statement = $db->prepare(
            "SELECT en.numero_factura, en.estado, en.cantidad,
                    e.nombre AS evento, e.fecha, e.hora,
                    l.nombre AS local_nombre
             FROM entradas en
             INNER JOIN eventos e ON e.id = en.evento_id
             LEFT JOIN locales l ON l.id = e.local_id
             WHERE en.qr_token = ?
             LIMIT 1"
        );
        $statement->execute([$token]);
        $ticket = $statement->fetch() ?: null;
        $this->view('events/verify', compact('ticket'));
    }

    private function findEvent(int $eventId): array
    {
        $statement = Database::connection()->prepare(
            "SELECT e.*, l.nombre AS local_nombre, l.tipo AS local_tipo,
                    l.direccion, l.provincia
             FROM eventos e
             LEFT JOIN locales l ON l.id = e.local_id
             WHERE e.id = ? AND e.estado = 1 AND e.fecha >= CURDATE()"
        );
        $statement->execute([$eventId]);
        $event = $statement->fetch();
        if (!$event) {
            throw new RuntimeException('El evento seleccionado no está disponible.');
        }
        return $event;
    }

    private function findTicket(int $ticketId, int $userId): array
    {
        $statement = Database::connection()->prepare(
            "SELECT en.*, e.nombre AS evento, e.fecha AS fecha_evento,
                    e.hora, e.imagen_url, l.nombre AS local_nombre,
                    l.direccion, u.nombre AS comprador, u.correo,
                    (
                        SELECT GROUP_CONCAT(
                            ea.asiento ORDER BY ea.asiento SEPARATOR ', '
                        )
                        FROM entrada_asientos ea
                        WHERE ea.entrada_id = en.id
                    ) AS asientos
             FROM entradas en
             INNER JOIN eventos e ON e.id = en.evento_id
             INNER JOIN usuarios u ON u.id = en.usuario_id
             LEFT JOIN locales l ON l.id = e.local_id
             WHERE en.id = ? AND en.usuario_id = ?"
        );
        $statement->execute([$ticketId, $userId]);
        $ticket = $statement->fetch();
        if (!$ticket) {
            throw new RuntimeException('No se encontró esta factura.');
        }
        return $ticket;
    }

    private function hasAssignedSeats(string $venueType): bool
    {
        return (bool)preg_match('/teatro|estadio|arena|auditorio/i', $venueType);
    }

    private function requireCustomer(): void
    {
        if (in_array(Auth::role(), ['Administrador', 'Operador'], true)) {
            http_response_code(403);
            exit('El personal del sistema no realiza compras de entradas.');
        }
    }
}
