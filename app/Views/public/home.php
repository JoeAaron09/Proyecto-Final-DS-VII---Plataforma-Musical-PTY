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

$baseUrl = rtrim($config['base_url'], '/');
?>

<!-- HERO PRINCIPAL -->
<section class="home-hero">
    <div class="container home-hero-grid">

        <div class="home-hero-content">
            <span class="home-eyebrow">
                El pulso musical de Panamá
            </span>

            <h1>
                Donde el rock
                <span>se mantiene vivo.</span>
            </h1>

            <p>
                Descubre artistas, bandas, canciones, locales y eventos
                de la escena musical panameña.
            </p>

            <a class="btn" href="#canciones">
                Explorar música
            </a>
        </div>

        <div class="home-stamp" aria-hidden="true">
            <span>PTY</span>
            <strong>ROCK</strong>
        </div>

    </div>
</section>

<!-- ARTISTAS -->
<section id="artistas" class="public-section">
    <div class="container">

        <header class="section-heading">
            <span>Talento nacional</span>
            <h2>Artistas</h2>
        </header>

        <div class="card-grid">
            <?php foreach ($artists as $artistItem): ?>
                <?php
                $artistImage = $artistItem['imagen_url']
                    ?: 'https://placehold.co/600x400/1b1b1b/ffffff?text=Artista';
                ?>

                <article class="content-card">
                    <img
                        src="<?= $escape($artistImage) ?>"
                        alt="<?= $escape($artistItem['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3><?= $escape($artistItem['nombre']) ?></h3>

                        <?php if (!empty($artistItem['nacionalidad'])): ?>
                            <p class="card-kicker">
                                <?= $escape($artistItem['nacionalidad']) ?>
                            </p>
                        <?php endif; ?>

                        <p>
                            <?= $escape(
                                mb_strimwidth(
                                    (string)($artistItem['biografia'] ?? ''),
                                    0,
                                    150,
                                    '...'
                                )
                            ) ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- BANDAS -->
<section id="bandas" class="public-section public-section-alt">
    <div class="container">

        <header class="section-heading">
            <span>La escena nacional</span>
            <h2>Bandas</h2>
        </header>

        <div class="card-grid">
            <?php foreach ($bands as $band): ?>
                <?php
                $bandImage = $band['imagen_url']
                    ?: 'https://placehold.co/600x400/1b1b1b/ffffff?text=Banda';
                ?>

                <article class="content-card">
                    <img
                        src="<?= $escape($bandImage) ?>"
                        alt="<?= $escape($band['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3><?= $escape($band['nombre']) ?></h3>

                        <p class="card-kicker">
                            <?= $escape($band['pais'] ?? '') ?>

                            <?php if (!empty($band['anio_formacion'])): ?>
                                · <?= $escape($band['anio_formacion']) ?>
                            <?php endif; ?>
                        </p>

                        <p>
                            <?= $escape(
                                mb_strimwidth(
                                    (string)($band['descripcion'] ?? ''),
                                    0,
                                    150,
                                    '...'
                                )
                            ) ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- CANCIONES -->
<section id="canciones" class="public-section">
    <div class="container">

        <header class="section-heading">
            <span>Escucha la escena</span>
            <h2>Canciones</h2>
        </header>

        <div class="card-grid">
            <?php foreach ($songs as $song): ?>
                <?php
                $songImage = $song['imagen_url']
                    ?: 'https://placehold.co/600x400/1b1b1b/ffffff?text=Cancion';
                ?>

                <article class="content-card song-card">
                    <img
                        src="<?= $escape($songImage) ?>"
                        alt="<?= $escape($song['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3><?= $escape($song['nombre']) ?></h3>

                        <p class="card-kicker">
                            <?= $escape($song['artista'] ?? '') ?>

                            <?php if (!empty($song['genero'])): ?>
                                · <?= $escape($song['genero']) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($song['audio_url'])): ?>
                            <audio
                                class="audio-player"
                                controls
                                preload="none"
                                src="<?= $escape($song['audio_url']) ?>"
                            >
                                Tu navegador no admite reproducción de audio.
                            </audio>
                        <?php endif; ?>

                        <?php if (Auth::check()): ?>
                            <div class="card-actions">

                                <form
                                    method="post"
                                    action="<?= $escape($baseUrl) ?>/reproducir/<?= (int)$song['id'] ?>"
                                >
                                    <?= Csrf::field() ?>

                                    <button type="submit">
                                        Registrar reproducción
                                    </button>
                                </form>

                                <form
                                    method="post"
                                    action="<?= $escape($baseUrl) ?>/favoritos/<?= (int)$song['id'] ?>"
                                >
                                    <?= Csrf::field() ?>

                                    <button type="submit">
                                        ♥ Favorito
                                    </button>
                                </form>

                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- TOP 10 -->
<section class="public-section public-section-alt">
    <div class="container">

        <header class="section-heading">
            <span>Últimos 30 días</span>
            <h2>Top 10</h2>
        </header>

        <?php if (!empty($artist)): ?>
            <div class="highlight">
                <span>Artista del momento:</span>

                <strong>
                    <?= $escape($artist['artista'] ?? '') ?>
                </strong>

                <small>
                    <?= (int)($artist['total'] ?? 0) ?>
                    reproducciones
                </small>
            </div>
        <?php endif; ?>

        <div class="table-wrap">
            <table class="public-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Canción</th>
                        <th>Artista</th>
                        <th>Reproducciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($top)): ?>
                        <tr>
                            <td colspan="4" class="empty-table">
                                Todavía no hay reproducciones registradas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($top as $index => $topItem): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $escape($topItem['cancion'] ?? '') ?></td>
                            <td><?= $escape($topItem['artista'] ?? '') ?></td>
                            <td><?= (int)($topItem['reproducciones'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

<!-- EVENTOS -->
<section id="eventos" class="public-section">
    <div class="container">

        <header class="section-heading">
            <span>Próximas presentaciones</span>
            <h2>Eventos</h2>
        </header>

        <div class="card-grid">
            <?php foreach ($events as $event): ?>
                <?php
                $eventImage = $event['imagen_url']
                    ?: 'https://placehold.co/600x400/1b1b1b/ffffff?text=Evento';
                ?>

                <article class="content-card">
                    <img
                        src="<?= $escape($eventImage) ?>"
                        alt="<?= $escape($event['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3><?= $escape($event['nombre']) ?></h3>

                        <p class="card-kicker">
                            <?= $escape($event['fecha'] ?? '') ?>

                            <?php if (!empty($event['hora'])): ?>
                                · <?= $escape(substr((string)$event['hora'], 0, 5)) ?>
                            <?php endif; ?>
                        </p>

                        <p>
                            <?= $escape(
                                $event['local_nombre'] ?? 'Por confirmar'
                            ) ?>
                        </p>

                        <strong class="card-price">
                            B/. <?= number_format(
                                (float)($event['precio'] ?? 0),
                                2
                            ) ?>
                        </strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- LOCALES -->
<section id="locales" class="public-section public-section-alt">
    <div class="container">

        <header class="section-heading">
            <span>Espacios para la música</span>
            <h2>Locales</h2>
        </header>

        <div class="card-grid">
            <?php foreach ($venues as $venue): ?>
                <article class="content-card venue-card">

                    <?php if (!empty($venue['imagen_url'])): ?>
                        <img
                            src="<?= $escape($venue['imagen_url']) ?>"
                            alt="<?= $escape($venue['nombre']) ?>"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="card-placeholder">
                            LOCAL
                        </div>
                    <?php endif; ?>

                    <div class="content-card-body">
                        <h3><?= $escape($venue['nombre']) ?></h3>

                        <p class="card-kicker">
                            <?= $escape($venue['tipo'] ?? '') ?>

                            <?php if (!empty($venue['provincia'])): ?>
                                · <?= $escape($venue['provincia']) ?>
                            <?php endif; ?>
                        </p>

                        <p>
                            <?= $escape($venue['direccion'] ?? '') ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- PREMIUM -->
<section class="public-section premium-section">
    <div class="container">

        <header class="section-heading">
            <span>Más música y beneficios</span>
            <h2>Premium</h2>
        </header>

        <div class="card-grid plans-grid">
            <?php foreach ($plans as $plan): ?>
                <article class="content-card plan-card">
                    <div class="content-card-body">

                        <span class="plan-label">
                            Plan Rokola
                        </span>

                        <h3><?= $escape($plan['nombre']) ?></h3>

                        <strong class="plan-price">
                            B/. <?= number_format(
                                (float)($plan['precio'] ?? 0),
                                2
                            ) ?>
                        </strong>

                        <p>
                            <?= $escape($plan['descripcion'] ?? '') ?>
                        </p>

                        <a
                            class="btn"
                            href="<?= $escape($baseUrl) ?>/mi-cuenta"
                        >
                            Adquirir
                        </a>

                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>