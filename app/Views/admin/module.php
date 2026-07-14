<?php

declare(strict_types=1);

use App\Helpers\Csrf;

/*
|--------------------------------------------------------------------------
| Valores predeterminados
|--------------------------------------------------------------------------
*/

$rows = $rows ?? [];
$record = $record ?? [];
$options = $options ?? [];
$definition = $definition ?? [
    'title' => 'Módulo',
    'fields' => [],
];

$module = $module ?? '';

/*
|--------------------------------------------------------------------------
| Funciones auxiliares
|--------------------------------------------------------------------------
*/

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
};

$labels = [
    'id' => 'ID',
    'nombre' => 'Nombre',
    'titulo' => 'Título',
    'biografia' => 'Biografía',
    'descripcion' => 'Descripción',
    'nacionalidad' => 'Nacionalidad',
    'pais' => 'País',
    'anio_formacion' => 'Año de formación',
    'anio_lanzamiento' => 'Año de lanzamiento',
    'artista_id' => 'Artista',
    'banda_id' => 'Banda',
    'album_id' => 'Álbum',
    'genero_id' => 'Género',
    'local_id' => 'Local',
    'imagen_url' => 'Imagen',
    'portada_url' => 'Portada',
    'audio_url' => 'Archivo de audio',
    'duracion' => 'Duración',
    'direccion' => 'Dirección',
    'provincia' => 'Provincia',
    'capacidad' => 'Capacidad',
    'telefono' => 'Teléfono',
    'correo' => 'Correo electrónico',
    'fecha' => 'Fecha',
    'hora' => 'Hora',
    'precio' => 'Precio',
    'meses' => 'Duración en meses',
    'limite_reproducciones' => 'Límite de reproducciones',
    'estado' => 'Estado',
    'creado_en' => 'Fecha de creación',
    'actualizado_en' => 'Última actualización',
];

$getLabel = static function (string $field) use ($labels): string {
    if (isset($labels[$field])) {
        return $labels[$field];
    }

    return ucfirst(
        str_replace(
            '_',
            ' ',
            str_replace('_id', '', $field)
        )
    );
};

$getFieldClass = static function (
    string $field,
    string $type
): string {
    if (
        $type === 'textarea'
        || in_array($field, ['descripcion', 'biografia'], true)
    ) {
        return 'form-field form-field-wide';
    }

    if (str_starts_with($type, 'file:')) {
        return 'form-field form-field-file';
    }

    return 'form-field';
};

$getOptionName = static function (
    array $allOptions,
    string $field,
    mixed $value
): string {
    if ($value === null || $value === '') {
        return 'No asignado';
    }

    foreach ($allOptions[$field] ?? [] as $option) {
        if (
            isset($option['id'])
            && (string)$option['id'] === (string)$value
        ) {
            return (string)($option['nombre'] ?? $option['titulo'] ?? $value);
        }
    }

    return '#' . (string)$value;
};

$formatValue = static function (
    string $column,
    mixed $value
) use ($options, $getOptionName): string {
    if ($value === null || $value === '') {
        return '—';
    }

    if (str_ends_with($column, '_id')) {
        return $getOptionName($options, $column, $value);
    }

    if ($column === 'estado') {
        return (int)$value === 1 ? 'Activo' : 'Inactivo';
    }

    if ($column === 'precio') {
        return '$' . number_format((float)$value, 2);
    }

    if ($column === 'fecha') {
        $timestamp = strtotime((string)$value);

        return $timestamp !== false
            ? date('d/m/Y', $timestamp)
            : (string)$value;
    }

    if ($column === 'hora') {
        $timestamp = strtotime((string)$value);

        return $timestamp !== false
            ? date('g:i a', $timestamp)
            : (string)$value;
    }

    if ($column === 'imagen_url') {
        return 'Imagen cargada';
    }

    if ($column === 'portada_url') {
        return 'Portada cargada';
    }

    if ($column === 'audio_url') {
        return 'Audio cargado';
    }

    if (
        in_array(
            $column,
            ['biografia', 'descripcion', 'direccion'],
            true
        )
    ) {
        return mb_strimwidth(
            (string)$value,
            0,
            120,
            '…'
        );
    }

    return mb_strimwidth(
        (string)$value,
        0,
        70,
        '…'
    );
};

/*
|--------------------------------------------------------------------------
| Columnas visibles por módulo
|--------------------------------------------------------------------------
*/

$tableColumns = [
    'generos' => [
        'id',
        'nombre',
        'descripcion',
        'estado',
    ],

    'artistas' => [
        'id',
        'nombre',
        'nacionalidad',
        'biografia',
        'imagen_url',
        'estado',
    ],

    'bandas' => [
        'id',
        'nombre',
        'pais',
        'anio_formacion',
        'descripcion',
        'imagen_url',
        'estado',
    ],

    'albumes' => [
        'id',
        'nombre',
        'artista_id',
        'banda_id',
        'genero_id',
        'anio_lanzamiento',
        'portada_url',
        'descripcion',
        'estado',
    ],

    'canciones' => [
        'id',
        'nombre',
        'artista_id',
        'banda_id',
        'album_id',
        'genero_id',
        'duracion',
        'audio_url',
        'estado',
    ],

    'locales' => [
        'id',
        'nombre',
        'tipo',
        'direccion',
        'provincia',
        'capacidad',
        'telefono',
        'correo',
        'imagen_url',
        'estado',
    ],

    'eventos' => [
        'id',
        'nombre',
        'descripcion',
        'fecha',
        'hora',
        'local_id',
        'precio',
        'capacidad',
        'imagen_url',
        'estado',
    ],

    'planes' => [
        'id',
        'nombre',
        'precio',
        'meses',
        'limite_reproducciones',
        'descripcion',
        'estado',
    ],

    'premium' => [
        'id',
        'nombre',
        'precio',
        'meses',
        'limite_reproducciones',
        'descripcion',
        'estado',
    ],
];

$visibleColumns = $tableColumns[$module]
    ?? ($rows !== [] ? array_keys($rows[0]) : []);

?>

<div class="admin-layout">
    <?php require __DIR__ . '/nav.php'; ?>

    <section class="admin-content">

        <!-- Encabezado del módulo -->
        <div class="admin-heading">
            <div>
                <span class="admin-eyebrow">
                    Administración
                </span>

                <h1>
                    <?= $escape($definition['title']) ?>
                </h1>
            </div>

            <?php if ($record !== []): ?>
                <a
                    class="btn btn-secondary"
                    href="<?= $escape($config['base_url']) ?>/admin/<?= $escape($module) ?>"
                >
                    Cancelar edición
                </a>
            <?php endif; ?>
        </div>

        <!-- Formulario -->
        <form
            class="panel admin-form"
            method="post"
            enctype="multipart/form-data"
            action="<?= $escape($config['base_url']) ?>/admin/<?= $escape($module) ?>/guardar"
        >
            <?= Csrf::field() ?>

            <input
                type="hidden"
                name="id"
                value="<?= $escape($record['id'] ?? '') ?>"
            >

            <div class="admin-form-grid">
                <?php foreach ($definition['fields'] as $field => $type): ?>
                    <?php
                    $label = $getLabel($field);
                    $currentValue = $record[$field] ?? '';
                    $fieldClass = $getFieldClass($field, $type);
                    ?>

                    <label class="<?= $escape($fieldClass) ?>">
                        <span>
                            <?= $escape($label) ?>
                        </span>

                        <?php if ($type === 'textarea'): ?>

                            <textarea
                                name="<?= $escape($field) ?>"
                                placeholder="Escriba <?= $escape(mb_strtolower($label)) ?>"
                            ><?= $escape($currentValue) ?></textarea>

                        <?php elseif (str_starts_with($type, 'select:')): ?>

                            <select name="<?= $escape($field) ?>">
                                <option value="">
                                    Seleccione una opción
                                </option>

                                <?php foreach ($options[$field] ?? [] as $option): ?>
                                    <?php
                                    $optionId = $option['id'] ?? '';
                                    $optionName = $option['nombre']
                                        ?? $option['titulo']
                                        ?? 'Opción';
                                    ?>

                                    <option
                                        value="<?= $escape($optionId) ?>"
                                        <?= (string)$currentValue === (string)$optionId
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= $escape($optionName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif (str_starts_with($type, 'file:')): ?>

                            <input
                                type="hidden"
                                name="old_<?= $escape($field) ?>"
                                value="<?= $escape($currentValue) ?>"
                            >

                            <input
                                type="file"
                                name="<?= $escape($field) ?>"
                                accept="<?= str_contains($type, 'audio')
                                    ? 'audio/*'
                                    : 'image/jpeg,image/png,image/webp' ?>"
                            >

                            <?php if ($currentValue !== ''): ?>
                                <?php
                                $filePath = parse_url(
                                    (string)$currentValue,
                                    PHP_URL_PATH
                                );

                                $fileName = basename(
                                    $filePath ?: (string)$currentValue
                                );
                                ?>

                                <small class="current-file">
                                    Archivo actual:
                                    <?= $escape($fileName) ?>
                                </small>
                            <?php endif; ?>

                        <?php else: ?>

                            <input
                                type="<?= $escape($type) ?>"
                                name="<?= $escape($field) ?>"
                                value="<?= $escape($currentValue) ?>"
                                <?= $type === 'number'
                                    ? 'step="any"'
                                    : '' ?>
                            >

                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="admin-form-actions">
                <button class="btn" type="submit">
                    <?= $record !== []
                        ? 'Actualizar registro'
                        : 'Guardar registro' ?>
                </button>

                <?php if ($record !== []): ?>
                    <span class="editing-indicator">
                        Editando el registro
                        #<?= (int)($record['id'] ?? 0) ?>
                    </span>
                <?php endif; ?>
            </div>
        </form>

        <!-- Encabezado de registros -->
        <div class="admin-list-heading">
            <div>
                <span class="admin-eyebrow">
                    Información registrada
                </span>

                <h2>Registros existentes</h2>
            </div>

            <span class="record-counter">
                <?= count($rows) ?>
                <?= count($rows) === 1
                    ? 'registro'
                    : 'registros' ?>
            </span>
        </div>

        <!-- Registros -->
        <div class="admin-records">

            <?php if ($rows === []): ?>
                <div class="panel empty-records">
                    <h3>No hay registros</h3>

                    <p>
                        Utiliza el formulario superior para agregar
                        el primer registro de este módulo.
                    </p>
                </div>
            <?php endif; ?>

            <?php foreach ($rows as $row): ?>
                <?php
                $recordTitle = $row['nombre']
                    ?? $row['titulo']
                    ?? 'Registro';

                $rowId = (int)($row['id'] ?? 0);
                ?>

                <article class="admin-record-card">
                    <div class="record-main">

                        <div class="record-title-row">
                            <div>
                                <span class="record-id">
                                    Registro #<?= $rowId ?>
                                </span>

                                <h3>
                                    <?= $escape($recordTitle) ?>
                                </h3>
                            </div>

                            <?php if (array_key_exists('estado', $row)): ?>
                                <span
                                    class="status-badge <?= (int)$row['estado'] === 1
                                        ? 'status-active'
                                        : 'status-inactive' ?>"
                                >
                                    <?= (int)$row['estado'] === 1
                                        ? 'Activo'
                                        : 'Inactivo' ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="record-details">
                            <?php foreach ($visibleColumns as $column): ?>
                                <?php
                                if (
                                    in_array(
                                        $column,
                                        [
                                            'id',
                                            'nombre',
                                            'titulo',
                                            'estado',
                                        ],
                                        true
                                    )
                                ) {
                                    continue;
                                }

                                if (!array_key_exists($column, $row)) {
                                    continue;
                                }

                                $rawValue = $row[$column];
                                $displayValue = $formatValue(
                                    $column,
                                    $rawValue
                                );
                                ?>

                                <div class="record-detail">
                                    <span class="record-label">
                                        <?= $escape(
                                            $getLabel($column)
                                        ) ?>
                                    </span>

                                    <span
                                        class="record-value"
                                        title="<?= $escape($rawValue) ?>"
                                    >
                                        <?= $escape($displayValue) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="record-actions">
                        <a
                            class="action-button"
                            href="?edit=<?= $rowId ?>"
                        >
                            Editar
                        </a>

                        <?php if (array_key_exists('estado', $row)): ?>
                            <form
                                method="post"
                                action="<?= $escape($config['base_url']) ?>/admin/<?= $escape($module) ?>/<?= $rowId ?>/estado"
                            >
                                <?= Csrf::field() ?>

                                <button
                                    class="action-button action-danger"
                                    type="submit"
                                >
                                    <?= (int)$row['estado'] === 1
                                        ? 'Deshabilitar'
                                        : 'Activar' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

        </div>
    </section>
</div>