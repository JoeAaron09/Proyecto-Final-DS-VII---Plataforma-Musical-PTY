<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Models\Repository;
use RuntimeException;

final class AdminController extends Controller
{
    private function meta(): array
    {
        return [
            'generos' => [
                'title' => 'Géneros',
                'fields' => [
                    'nombre' => 'text',
                    'descripcion' => 'textarea',
                ],
            ],

            'artistas' => [
                'title' => 'Artistas',
                'fields' => [
                    'nombre' => 'text',
                    'tipo' => 'select_static:Solista|Banda|Proyecto musical',
                    'genero_id' => 'select:generos',
                    'biografia' => 'textarea',
                    'pais' => 'text',
                    'anio_inicio' => 'number',
                    'imagen_url' => 'file:image',
                ],
            ],

            'albumes' => [
                'title' => 'Álbumes',
                'fields' => [
                    'nombre' => 'text',
                    'artista_id' => 'select:artistas',
                    'anio_lanzamiento' => 'number',
                    'portada_url' => 'file:image',
                    'descripcion' => 'textarea',
                ],
            ],

            'canciones' => [
                'title' => 'Canciones',
                'fields' => [
                    'nombre' => 'text',
                    'artista_id' => 'select:artistas',
                    'album_id' => 'select:albumes',
                    'duracion' => 'text',
                    'audio_url' => 'file:audio',
                    'imagen_url' => 'file:image',
                ],
            ],

            'locales' => [
                'title' => 'Locales',
                'fields' => [
                    'nombre' => 'text',
                    'tipo' => 'text',
                    'direccion' => 'text',
                    'provincia' => 'text',
                    'capacidad' => 'number',
                    'telefono' => 'text',
                    'correo' => 'email',
                    'imagen_url' => 'file:image',
                ],
            ],

            'eventos' => [
                'title' => 'Eventos',
                'fields' => [
                    'nombre' => 'text',
                    'descripcion' => 'textarea',
                    'fecha' => 'date',
                    'hora' => 'time',
                    'local_id' => 'select:locales',
                    'precio' => 'number',
                    'capacidad' => 'number',
                    'imagen_url' => 'file:image',
                ],
            ],

            'planes' => [
                'title' => 'Planes Premium',
                'fields' => [
                    'nombre' => 'text',
                    'descripcion' => 'textarea',
                    'precio' => 'number',
                    'duracion_dias' => 'number',
                ],
            ],
        ];
    }

    public function dashboard(): void
    {
        Auth::requireAdmin();

        $db = (new Repository())->db();

        $tables = [
            'usuarios',
            'artistas',
            'albumes',
            'canciones',
            'eventos',
            'reproducciones',
            'compras',
        ];

        $stats = [];

        foreach ($tables as $table) {
            $stats[$table] = (int)$db
                ->query("SELECT COUNT(*) FROM `{$table}`")
                ->fetchColumn();
        }

        $this->view(
            'admin/dashboard',
            compact('stats')
        );
    }

    public function module(
        string $module,
        ?int $edit = null
    ): void {
        Auth::requireAdmin();

        $meta = $this->meta();

        if (!isset($meta[$module])) {
            throw new RuntimeException(
                'El módulo solicitado no existe.'
            );
        }

        $repository = new Repository();

        $rows = $repository->all($module);
        $record = $edit !== null
            ? $repository->find($module, $edit)
            : null;

        $options = [];

        foreach ($meta[$module]['fields'] as $field => $type) {
            if (str_starts_with($type, 'select:')) {
                $table = substr($type, 7);

                $options[$field] = $repository->all(
                    $table,
                    'nombre ASC'
                );
            }

            if (str_starts_with($type, 'select_static:')) {
                $values = explode(
                    '|',
                    substr($type, 14)
                );

                $options[$field] = array_map(
                    static fn(string $value): array => [
                        'id' => $value,
                        'nombre' => $value,
                    ],
                    $values
                );
            }
        }

        $this->view(
            'admin/module',
            [
                'module' => $module,
                'definition' => $meta[$module],
                'rows' => $rows,
                'record' => $record,
                'options' => $options,
            ]
        );
    }

    public function save(string $module): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $meta = $this->meta();

        if (!isset($meta[$module])) {
            throw new RuntimeException(
                'El módulo solicitado no existe.'
            );
        }

        $data = [];

        foreach ($meta[$module]['fields'] as $field => $type) {
            if (str_starts_with($type, 'file:')) {
                $oldValue = $_POST['old_' . $field] ?? null;

                $data[$field] = $this->upload(
                    $field,
                    substr($type, 5)
                ) ?? $oldValue;

                continue;
            }

            $value = trim(
                (string)($_POST[$field] ?? '')
            );

            $data[$field] = $value === ''
                ? null
                : $value;
        }

        $id = isset($_POST['id']) && $_POST['id'] !== ''
            ? (int)$_POST['id']
            : null;

        if ($module === 'canciones') {
            $this->validateSongAlbum(
                $data['artista_id'] ?? null,
                $data['album_id'] ?? null
            );
        }

        (new Repository())->save(
            $module,
            $data,
            $id
        );

        Flash::set(
            'success',
            'Registro guardado correctamente.'
        );

        $this->redirect('/admin/' . $module);
    }

    public function toggle(
        string $module,
        int $id
    ): void {
        Auth::requireAdmin();
        Csrf::verify();

        if (!isset($this->meta()[$module])) {
            throw new RuntimeException(
                'El módulo solicitado no existe.'
            );
        }

        (new Repository())->disable(
            $module,
            $id
        );

        Flash::set(
            'success',
            'Estado actualizado correctamente.'
        );

        $this->redirect('/admin/' . $module);
    }

    public function users(?int $edit = null): void
    {
        Auth::requireAdmin(false);

        $repository = new Repository();
        $db = $repository->db();

        $rows = $db->query(
            'SELECT
                u.*,
                r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r
                ON r.id = u.rol_id
             ORDER BY u.id DESC'
        )->fetchAll();

        $roles = $repository->all(
            'roles',
            'nombre ASC'
        );

        $record = $edit !== null
            ? $repository->find('usuarios', $edit)
            : null;

        $this->view(
            'admin/users',
            compact('rows', 'roles', 'record')
        );
    }

    public function saveUser(): void
    {
        Auth::requireAdmin(false);
        Csrf::verify();

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'rol_id' => (int)($_POST['rol_id'] ?? 0),
            'nombre' => trim(
                (string)($_POST['nombre'] ?? '')
            ),
            'correo' => trim(
                (string)($_POST['correo'] ?? '')
            ),
            'nacionalidad' => trim(
                (string)($_POST['nacionalidad'] ?? '')
            ),
            'tipo_usuario' => trim(
                (string)($_POST['tipo_usuario'] ?? 'gratuito')
            ),
            'estado' => (int)($_POST['estado'] ?? 1),
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash(
                (string)$_POST['password'],
                PASSWORD_DEFAULT
            );
        }

        (new Repository())->save(
            'usuarios',
            $data,
            $id > 0 ? $id : null
        );

        Flash::set(
            'success',
            'Usuario guardado correctamente.'
        );

        $this->redirect('/admin/usuarios');
    }

    private function validateSongAlbum(
        mixed $artistId,
        mixed $albumId
    ): void {
        if ($albumId === null || $albumId === '') {
            return;
        }

        $db = (new Repository())->db();

        $statement = $db->prepare(
            'SELECT COUNT(*)
             FROM albumes
             WHERE id = ?
               AND artista_id = ?'
        );

        $statement->execute([
            (int)$albumId,
            (int)$artistId,
        ]);

        if ((int)$statement->fetchColumn() === 0) {
            throw new RuntimeException(
                'El álbum seleccionado no pertenece al artista indicado.'
            );
        }
    }

    private function upload(string $field, string $kind): ?string
{
    if (!isset($_FILES[$field])) {
        return null;
    }

    $file = $_FILES[$field];
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($error !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE =>
                'El archivo supera el tamaño permitido por PHP.',
            UPLOAD_ERR_FORM_SIZE =>
                'El archivo supera el tamaño permitido por el formulario.',
            UPLOAD_ERR_PARTIAL =>
                'El archivo se cargó parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR =>
                'No existe el directorio temporal de PHP.',
            UPLOAD_ERR_CANT_WRITE =>
                'PHP no pudo escribir el archivo en el servidor.',
            UPLOAD_ERR_EXTENSION =>
                'Una extensión de PHP detuvo la carga.',
        ];

        throw new \RuntimeException(
            $messages[$error]
            ?? 'Ocurrió un error al cargar el archivo.'
        );
    }

    $originalName = (string)($file['name'] ?? '');
    $temporaryPath = (string)($file['tmp_name'] ?? '');

    if (
        $originalName === ''
        || $temporaryPath === ''
        || !is_uploaded_file($temporaryPath)
    ) {
        throw new \RuntimeException(
            'El archivo cargado no es válido.'
        );
    }

    $extension = strtolower(
        pathinfo($originalName, PATHINFO_EXTENSION)
    );

    $allowedExtensions = match ($kind) {
        'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
        default => [],
    };

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new \RuntimeException(
            'Formato no permitido. Formatos admitidos: '
            . implode(', ', $allowedExtensions)
        );
    }

    $maxSize = $kind === 'audio'
        ? 25 * 1024 * 1024
        : 8 * 1024 * 1024;

    if ((int)$file['size'] > $maxSize) {
        throw new \RuntimeException(
            $kind === 'audio'
                ? 'El audio no puede superar los 25 MB.'
                : 'La imagen no puede superar los 8 MB.'
        );
    }

    $uploadDirectory = dirname(__DIR__, 2)
        . '/public/uploads/'
        . $kind;

    if (
        !is_dir($uploadDirectory)
        && !mkdir($uploadDirectory, 0775, true)
        && !is_dir($uploadDirectory)
    ) {
        throw new \RuntimeException(
            'No fue posible crear la carpeta de archivos.'
        );
    }

    $fileName = bin2hex(random_bytes(16))
        . '.'
        . $extension;

    $destination = $uploadDirectory
        . DIRECTORY_SEPARATOR
        . $fileName;

    if (!move_uploaded_file($temporaryPath, $destination)) {
        throw new \RuntimeException(
            'No fue posible guardar el archivo en el servidor.'
        );
    }

    $config = require dirname(__DIR__)
        . '/Config/config.php';

    return rtrim($config['base_url'], '/')
        . '/uploads/'
        . $kind
        . '/'
        . $fileName;
}
}