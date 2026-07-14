<?php

declare(strict_types=1);

use App\Helpers\Auth;
use App\Helpers\Csrf;

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
};

$user = Auth::user() ?? [];
$baseUrl = rtrim($config['base_url'], '/');
?>

<section class="account-section">
    <div class="section-heading">
        <span>Perfil del usuario</span>
        <h1>Mi cuenta</h1>
    </div>

    <div class="panel account-summary">
        <div>
            <span class="account-label">Usuario</span>
            <strong><?= $escape($user['nombre'] ?? '') ?></strong>
        </div>

        <div>
            <span class="account-label">Correo</span>
            <strong><?= $escape($user['correo'] ?? '') ?></strong>
        </div>

        <div>
            <span class="account-label">Tipo de cuenta</span>
            <strong>
                <?= ($user['tipo_usuario'] ?? 'gratuito') === 'premium'
                    ? 'Premium'
                    : 'Gratuita' ?>
            </strong>
        </div>
    </div>

    <?php if (!empty($subscription)): ?>
        <div class="panel account-subscription">
            <div>
                <span class="account-label">
                    Suscripción activa
                </span>

                <h3>
                    <?= $escape($subscription['plan']) ?>
                </h3>
            </div>

            <p>
                Vigente desde
                <strong>
                    <?= $escape($subscription['fecha_inicio']) ?>
                </strong>
                hasta
                <strong>
                    <?= $escape($subscription['fecha_fin']) ?>
                </strong>.
            </p>
        </div>
    <?php endif; ?>

    <div class="account-grid">
        <form
            class="panel account-form"
            method="post"
            action="<?= $escape($baseUrl) ?>/cambiar-password"
        >
            <h2>Cambiar contraseña</h2>

            <?= Csrf::field() ?>

            <label>
                Nueva contraseña

                <input
                    type="password"
                    name="password"
                    minlength="8"
                    required
                >
            </label>

            <button class="btn" type="submit">
                Cambiar contraseña
            </button>
        </form>

        <form
            class="panel account-form"
            method="post"
            action="<?= $escape($baseUrl) ?>/listas/crear"
        >
            <h2>Nueva lista</h2>

            <?= Csrf::field() ?>

            <label>
                Nombre

                <input
                    type="text"
                    name="nombre"
                    required
                >
            </label>

            <label>
                Descripción

                <textarea
                    name="descripcion"
                    rows="4"
                ></textarea>
            </label>

            <button class="btn" type="submit">
                Crear lista
            </button>
        </form>
    </div>
</section>

<section class="public-section public-section-alt">
    <div class="container">
        <div class="section-heading">
            <span>Mejora tu experiencia</span>
            <h2>Planes Premium</h2>
        </div>

        <div class="card-grid">
            <?php foreach ($plans as $plan): ?>
                <article class="content-card plan-card">
                    <div class="content-card-body">
                        <span class="plan-label">
                            Plan Rokola
                        </span>

                        <h3>
                            <?= $escape($plan['nombre']) ?>
                        </h3>

                        <strong class="plan-price">
                            B/. <?= number_format(
                                (float)$plan['precio'],
                                2
                            ) ?>
                        </strong>

                        <p>
                            <?= $escape(
                                $plan['descripcion'] ?? ''
                            ) ?>
                        </p>

                        <p>
                            Duración:
                            <?= (int)$plan['duracion_dias'] ?>
                            días
                        </p>

                        <form
                            method="post"
                            action="<?= $escape($baseUrl) ?>/premium/<?= (int)$plan['id'] ?>/comprar"
                        >
                            <?= Csrf::field() ?>

                            <button class="btn" type="submit">
                                Adquirir plan
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="public-section">
    <div class="container">
        <div class="section-heading">
            <span>Próximas presentaciones</span>
            <h2>Comprar entradas</h2>
        </div>

        <div class="card-grid">
            <?php foreach ($events as $event): ?>
                <article class="content-card">
                    <?php if (!empty($event['imagen_url'])): ?>
                        <img
                            src="<?= $escape($event['imagen_url']) ?>"
                            alt="<?= $escape($event['nombre']) ?>"
                        >
                    <?php endif; ?>

                    <div class="content-card-body">
                        <h3>
                            <?= $escape($event['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($event['fecha']) ?>
                            ·
                            <?= $escape(
                                substr(
                                    (string)$event['hora'],
                                    0,
                                    5
                                )
                            ) ?>
                        </p>

                        <p>
                            <?= $escape(
                                $event['local_nombre']
                                ?? 'Por confirmar'
                            ) ?>
                        </p>

                        <strong class="card-price">
                            B/. <?= number_format(
                                (float)$event['precio'],
                                2
                            ) ?>
                        </strong>

                        <form
                            class="ticket-form"
                            method="post"
                            action="<?= $escape($baseUrl) ?>/eventos/<?= (int)$event['id'] ?>/comprar"
                        >
                            <?= Csrf::field() ?>

                            <label>
                                Cantidad

                                <input
                                    type="number"
                                    name="cantidad"
                                    min="1"
                                    value="1"
                                    required
                                >
                            </label>

                            <button class="btn" type="submit">
                                Comprar entradas
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="public-section public-section-alt">
    <div class="container">
        <div class="section-heading">
            <span>Tu selección musical</span>
            <h2>Favoritos</h2>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="public-empty-state">
                <h3>No tienes favoritos</h3>

                <p>
                    Marca canciones como favoritas desde la página
                    principal.
                </p>
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <?php foreach ($favorites as $favorite): ?>
                <article class="content-card">
                    <?php if (!empty($favorite['imagen_url'])): ?>
                        <img
                            src="<?= $escape($favorite['imagen_url']) ?>"
                            alt="<?= $escape($favorite['nombre']) ?>"
                        >
                    <?php endif; ?>

                    <div class="content-card-body">
                        <h3>
                            <?= $escape($favorite['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($favorite['artista']) ?>

                            <?php if (!empty($favorite['genero'])): ?>
                                · <?= $escape($favorite['genero']) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($favorite['album'])): ?>
                            <p>
                                Álbum:
                                <?= $escape($favorite['album']) ?>
                            </p>
                        <?php else: ?>
                            <p>Sencillo</p>
                        <?php endif; ?>

                        <?php if (!empty($favorite['audio_url'])): ?>
                            <audio
                                class="audio-player"
                                controls
                                preload="none"
                                src="<?= $escape($favorite['audio_url']) ?>"
                            ></audio>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="public-section">
    <div class="container">
        <div class="section-heading">
            <span>Organiza tu música</span>
            <h2>Mis listas</h2>
        </div>

        <?php if (empty($lists)): ?>
            <div class="public-empty-state">
                <h3>No has creado listas</h3>

                <p>
                    Utiliza el formulario superior para crear tu
                    primera lista.
                </p>
            </div>
        <?php endif; ?>

        <div class="account-lists">
            <?php foreach ($lists as $list): ?>
                <article class="panel account-list-card">
                    <div class="account-list-header">
                        <div>
                            <h3>
                                <?= $escape($list['nombre']) ?>
                            </h3>

                            <?php if (!empty($list['descripcion'])): ?>
                                <p>
                                    <?= $escape(
                                        $list['descripcion']
                                    ) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <span>
                            <?= (int)$list['total_canciones'] ?>
                            canciones
                        </span>
                    </div>

                    <form
                        class="account-add-song"
                        method="post"
                        action="<?= $escape($baseUrl) ?>/listas/agregar"
                    >
                        <?= Csrf::field() ?>

                        <input
                            type="hidden"
                            name="lista_id"
                            value="<?= (int)$list['id'] ?>"
                        >

                        <select
                            name="cancion_id"
                            required
                        >
                            <option value="">
                                Seleccione una canción
                            </option>

                            <?php foreach ($songs as $song): ?>
                                <option
                                    value="<?= (int)$song['id'] ?>"
                                >
                                    <?= $escape($song['nombre']) ?>
                                    —
                                    <?= $escape($song['artista']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn" type="submit">
                            Agregar canción
                        </button>
                    </form>

                    <?php if (
                        !empty($listSongs[(int)$list['id']])
                    ): ?>
                        <div class="account-list-songs">
                            <?php foreach (
                                $listSongs[(int)$list['id']]
                                as $listSong
                            ): ?>
                                <div>
                                    <strong>
                                        <?= $escape(
                                            $listSong['nombre']
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= $escape(
                                            $listSong['artista']
                                        ) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="public-section public-section-alt">
    <div class="container">
        <div class="section-heading">
            <span>Transacciones realizadas</span>
            <h2>Historial de compras</h2>
        </div>

        <div class="table-wrap">
            <table class="public-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Subtotal</th>
                        <th>ITBMS</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr>
                            <td
                                class="empty-table"
                                colspan="6"
                            >
                                No hay compras de planes registradas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td>
                                <?= $escape(
                                    $purchase['concepto']
                                    ?? 'Plan Premium'
                                ) ?>
                            </td>

                            <td>
                                B/. <?= number_format(
                                    (float)$purchase['subtotal'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                B/. <?= number_format(
                                    (float)$purchase['itbms'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                B/. <?= number_format(
                                    (float)$purchase['total'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                <?= $escape($purchase['estado']) ?>
                            </td>

                            <td>
                                <?= $escape($purchase['fecha']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="public-section">
    <div class="container">
        <div class="section-heading">
            <span>Entradas adquiridas</span>
            <h2>Mis entradas</h2>
        </div>

        <div class="table-wrap">
            <table class="public-table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Local</th>
                        <th>Fecha del evento</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Compra</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td
                                class="empty-table"
                                colspan="7"
                            >
                                No hay entradas registradas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>
                                <?= $escape($ticket['evento']) ?>
                            </td>

                            <td>
                                <?= $escape(
                                    $ticket['local_nombre']
                                    ?? 'Por confirmar'
                                ) ?>
                            </td>

                            <td>
                                <?= $escape(
                                    $ticket['fecha_evento']
                                ) ?>
                                <?= $escape(
                                    substr(
                                        (string)$ticket['hora'],
                                        0,
                                        5
                                    )
                                ) ?>
                            </td>

                            <td>
                                <?= (int)$ticket['cantidad'] ?>
                            </td>

                            <td>
                                B/. <?= number_format(
                                    (float)$ticket['total'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                <?= $escape($ticket['estado']) ?>
                            </td>

                            <td>
                                <?= $escape(
                                    $ticket['fecha_compra']
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>