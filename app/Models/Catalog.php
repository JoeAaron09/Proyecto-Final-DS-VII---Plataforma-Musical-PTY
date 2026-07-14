<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

final class Catalog extends Model
{
    private array $allowed = [
        'generos',
        'artistas',
        'albumes',
        'canciones',
        'locales',
        'eventos',
        'planes',
    ];

    private function validateTable(string $table): string
    {
        if (!in_array($table, $this->allowed, true)) {
            throw new InvalidArgumentException(
                'La tabla solicitada no está permitida.'
            );
        }

        return $table;
    }

    public function all(string $table): array
    {
        $table = $this->validateTable($table);

        return $this->db
            ->query(
                "SELECT *
                 FROM `{$table}`
                 ORDER BY id DESC"
            )
            ->fetchAll();
    }

    public function active(string $table): array
    {
        $table = $this->validateTable($table);

        return $this->db
            ->query(
                "SELECT *
                 FROM `{$table}`
                 WHERE estado = 1
                 ORDER BY nombre ASC"
            )
            ->fetchAll();
    }

    public function find(
        string $table,
        int $id
    ): ?array {
        $table = $this->validateTable($table);

        $statement = $this->db->prepare(
            "SELECT *
             FROM `{$table}`
             WHERE id = ?"
        );

        $statement->execute([$id]);

        return $statement->fetch() ?: null;
    }

    public function save(
        string $table,
        array $data,
        ?int $id = null
    ): void {
        $table = $this->validateTable($table);

        if ($data === []) {
            throw new InvalidArgumentException(
                'No se recibieron datos para guardar.'
            );
        }

        $columns = array_keys($data);

        foreach ($columns as $column) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException(
                    'Se detectó una columna no válida.'
                );
            }
        }

        if ($id !== null) {
            $assignments = implode(
                ', ',
                array_map(
                    static fn(string $column): string =>
                        "`{$column}` = ?",
                    $columns
                )
            );

            $values = array_values($data);
            $values[] = $id;

            $statement = $this->db->prepare(
                "UPDATE `{$table}`
                 SET {$assignments}
                 WHERE id = ?"
            );

            $statement->execute($values);

            return;
        }

        $columnNames = implode(
            ', ',
            array_map(
                static fn(string $column): string =>
                    "`{$column}`",
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
    }

    public function delete(
        string $table,
        int $id
    ): void {
        $table = $this->validateTable($table);

        $statement = $this->db->prepare(
            "UPDATE `{$table}`
             SET estado = 0
             WHERE id = ?"
        );

        $statement->execute([$id]);
    }

    public function dashboard(): array
    {
        return [
            'artistas' => (int)$this->db
                ->query(
                    "SELECT COUNT(*)
                     FROM artistas
                     WHERE estado = 1"
                )
                ->fetchColumn(),

            'albumes' => (int)$this->db
                ->query(
                    "SELECT COUNT(*)
                     FROM albumes
                     WHERE estado = 1"
                )
                ->fetchColumn(),

            'canciones' => (int)$this->db
                ->query(
                    "SELECT COUNT(*)
                     FROM canciones
                     WHERE estado = 1"
                )
                ->fetchColumn(),

            'eventos' => (int)$this->db
                ->query(
                    "SELECT COUNT(*)
                     FROM eventos
                     WHERE estado = 1"
                )
                ->fetchColumn(),

            'usuarios' => (int)$this->db
                ->query(
                    "SELECT COUNT(*)
                     FROM usuarios
                     WHERE estado = 1"
                )
                ->fetchColumn(),
        ];
    }
}