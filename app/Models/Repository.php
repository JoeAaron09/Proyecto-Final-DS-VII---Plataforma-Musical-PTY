<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use InvalidArgumentException;
use PDO;

final class Repository
{
    private PDO $db;

    private const ALLOWED_TABLES = [
        'roles',
        'usuarios',
        'generos',
        'artistas',
        'albumes',
        'canciones',
        'locales',
        'eventos',
        'planes',
    ];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function db(): PDO
    {
        return $this->db;
    }

    public function all(
        string $table,
        string $order = 'id DESC'
    ): array {
        $this->validateTable($table);
        $this->validateOrder($order);

        return $this->db
            ->query("SELECT * FROM `{$table}` ORDER BY {$order}")
            ->fetchAll();
    }

    public function find(
        string $table,
        int $id
    ): ?array {
        $this->validateTable($table);

        $statement = $this->db->prepare(
            "SELECT * FROM `{$table}` WHERE id = ?"
        );

        $statement->execute([$id]);

        return $statement->fetch() ?: null;
    }

    public function save(
        string $table,
        array $data,
        ?int $id = null
    ): int {
        $this->validateTable($table);

        if ($data === []) {
            throw new InvalidArgumentException(
                'No se proporcionaron datos para guardar.'
            );
        }

        $columns = array_keys($data);

        foreach ($columns as $column) {
            $this->validateIdentifier($column);
        }

        if ($id !== null) {
            $assignments = implode(
                ', ',
                array_map(
                    static fn(string $column): string => "`{$column}` = ?",
                    $columns
                )
            );

            $statement = $this->db->prepare(
                "UPDATE `{$table}`
                 SET {$assignments}
                 WHERE id = ?"
            );

            $statement->execute([
                ...array_values($data),
                $id,
            ]);

            return $id;
        }

        $columnNames = implode(
            ', ',
            array_map(
                static fn(string $column): string => "`{$column}`",
                $columns
            )
        );

        $placeholders = implode(
            ', ',
            array_fill(0, count($columns), '?')
        );

        $statement = $this->db->prepare(
            "INSERT INTO `{$table}` ({$columnNames})
             VALUES ({$placeholders})"
        );

        $statement->execute(array_values($data));

        return (int)$this->db->lastInsertId();
    }

    public function disable(
        string $table,
        int $id
    ): void {
        $this->validateTable($table);

        $statement = $this->db->prepare(
            "UPDATE `{$table}`
             SET estado = IF(estado = 1, 0, 1)
             WHERE id = ?"
        );

        $statement->execute([$id]);
    }

    private function validateTable(string $table): void
    {
        if (!in_array($table, self::ALLOWED_TABLES, true)) {
            throw new InvalidArgumentException(
                'La tabla solicitada no está permitida.'
            );
        }
    }

    private function validateOrder(string $order): void
    {
        if (
            !preg_match(
                '/^[a-zA-Z0-9_]+(?:\s+(?:ASC|DESC))?$/i',
                $order
            )
        ) {
            throw new InvalidArgumentException(
                'El orden solicitado no es válido.'
            );
        }
    }

    private function validateIdentifier(string $identifier): void
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
            throw new InvalidArgumentException(
                'Se detectó un identificador no válido.'
            );
        }
    }
}