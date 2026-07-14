<?php

declare(strict_types=1);

use App\Helpers\Csrf;

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<div class="admin-layout">
    <?php require __DIR__ . '/nav.php'; ?>

    <section class="admin-content">
        <div class="admin-heading">
            <div>
                <span class="admin-eyebrow">
                    Administración
                </span>

                <h1>Usuarios</h1>
            </div>
        </div>

        <form
            class="panel admin-form"
            method="post"
            action="<?= $escape($config['base_url']) ?>/admin/usuarios/guardar"
        >
            <?= Csrf::field() ?>

            <input
                type="hidden"
                name="id"
                value="<?= $escape($record['id'] ?? '') ?>"
            >

            <div class="admin-form-grid">
                <label class="form-field">
                    <span>Nombre</span>

                    <input
                        type="text"
                        name="nombre"
                        value="<?= $escape($record['nombre'] ?? '') ?>"
                        required
                    >
                </label>

                <label class="form-field">
                    <span>Correo</span>

                    <input
                        type="email"
                        name="correo"
                        value="<?= $escape($record['correo'] ?? '') ?>"
                        required
                    >
                </label>

                <label class="form-field">
                    <span>Rol</span>

                    <select name="rol_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option
                                value="<?= (int)$role['id'] ?>"
                                <?= (int)($record['rol_id'] ?? 3)
                                    === (int)$role['id']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= $escape($role['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="form-field">
                    <span>Nacionalidad</span>

                    <input
                        type="text"
                        name="nacionalidad"
                        value="<?= $escape(
                            $record['nacionalidad']
                            ?? 'Panameña'
                        ) ?>"
                    >
                </label>

                <label class="form-field">
                    <span>Tipo de usuario</span>

                    <select name="tipo_usuario" required>
                        <option
                            value="gratuito"
                            <?= ($record['tipo_usuario'] ?? 'gratuito')
                                === 'gratuito'
                                ? 'selected'
                                : '' ?>
                        >
                            Gratuito
                        </option>

                        <option
                            value="premium"
                            <?= ($record['tipo_usuario'] ?? '')
                                === 'premium'
                                ? 'selected'
                                : '' ?>
                        >
                            Premium
                        </option>
                    </select>
                </label>

                <label class="form-field">
                    <span>Estado</span>

                    <select name="estado" required>
                        <option
                            value="1"
                            <?= (int)($record['estado'] ?? 1) === 1
                                ? 'selected'
                                : '' ?>
                        >
                            Activo
                        </option>

                        <option
                            value="0"
                            <?= isset($record['estado'])
                                && (int)$record['estado'] === 0
                                ? 'selected'
                                : '' ?>
                        >
                            Inactivo
                        </option>
                    </select>
                </label>

                <label class="form-field">
                    <span>Contraseña</span>

                    <input
                        type="password"
                        name="password"
                        <?= empty($record) ? 'required' : '' ?>
                    >
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="btn" type="submit">
                    <?= empty($record)
                        ? 'Guardar usuario'
                        : 'Actualizar usuario' ?>
                </button>
            </div>
        </form>

        <div class="admin-list-heading">
            <div>
                <span class="admin-eyebrow">
                    Usuarios registrados
                </span>

                <h2>Registros existentes</h2>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($rows as $userRow): ?>
                        <tr>
                            <td>
                                <?= (int)$userRow['id'] ?>
                            </td>

                            <td>
                                <?= $escape($userRow['nombre']) ?>
                            </td>

                            <td>
                                <?= $escape($userRow['correo']) ?>
                            </td>

                            <td>
                                <?= $escape($userRow['rol']) ?>
                            </td>

                            <td>
                                <?= $userRow['tipo_usuario']
                                    === 'premium'
                                    ? 'Premium'
                                    : 'Gratuito' ?>
                            </td>

                            <td>
                                <?= (int)$userRow['estado'] === 1
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                            </td>

                            <td>
                                <a
                                    href="?edit=<?= (int)$userRow['id'] ?>"
                                >
                                    Editar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>