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

$baseUrl = rtrim(
    $config['base_url'],
    '/'
);

$artists = $artists ?? [];
$songs = $songs ?? [];
$top = $top ?? [];
$artist = $artist ?? null;
$events = $events ?? [];
$venues = $venues ?? [];
$plans = $plans ?? [];
?>

<!-- HERO -->
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
                Descubre artistas, canciones, locales y eventos
                de la escena musical panameña.
            </p>

            <a class="btn" href="#artistas">
                Explorar la escena
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
            <span>Talento y proyectos nacionales</span>
            <h2>Artistas</h2>
        </header>

        <?php if ($artists === []): ?>
            <div class="public-empty-state">
                <h3>No hay artistas registrados</h3>

                <p>
                    Los artistas activos aparecerán en esta sección.
                </p>
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <?php foreach ($artists as $artistItem): ?>
                <?php
                $artistImage = $artistItem['imagen_url']
                    ?: (
                        'https://placehold.co/'
                        . '600x400/1b1b1b/ffffff'
                        . '?text=Artista'
                    );

                $artistType = $artistItem['tipo']
                    ?? 'Artista';

                $artistGenre = $artistItem['genero']
                    ?? '';

                $artistCountry = $artistItem['pais']
                    ?? '';

                $artistYear = $artistItem['anio_inicio']
                    ?? '';
                ?>

                <article class="content-card artist-card">
                    <img
                        src="<?= $escape($artistImage) ?>"
                        alt="<?= $escape($artistItem['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3>
                            <?= $escape($artistItem['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($artistType) ?>

                            <?php if ($artistGenre !== ''): ?>
                                · <?= $escape($artistGenre) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (
                            $artistCountry !== ''
                            || $artistYear !== ''
                        ): ?>
                            <p class="artist-origin">
                                <?= $escape($artistCountry) ?>

                                <?php if ($artistYear !== ''): ?>
                                    <?= $artistCountry !== ''
                                        ? ' · '
                                        : '' ?>

                                    Desde <?= (int)$artistYear ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($artistItem['biografia'])): ?>
                            <details class="artist-biography">
                                <summary>Leer información</summary>

                                <p>
                                    <?= nl2br($escape(
                                        (string)$artistItem['biografia']
                                    )) ?>
                                </p>
                            </details>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- CANCIONES -->
<section
    id="canciones"
    class="public-section public-section-alt"
>
    <div class="container">

        <header class="section-heading">
            <span>Escucha la escena</span>
            <h2>Canciones</h2>
        </header>

        <?php if ($songs === []): ?>
            <div class="public-empty-state">
                <h3>No hay canciones disponibles</h3>

                <p>
                    Las canciones activas aparecerán en esta sección.
                </p>
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <?php foreach ($songs as $song): ?>
                <?php
                $songImage = $song['imagen_url']
                    ?: (
                        'https://placehold.co/'
                        . '600x400/1b1b1b/ffffff'
                        . '?text=Cancion'
                    );
                ?>

                <article class="content-card song-card">
                    <img
                        src="<?= $escape($songImage) ?>"
                        alt="<?= $escape($song['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3>
                            <?= $escape($song['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($song['artista'] ?? '') ?>

                            <?php if (!empty($song['genero'])): ?>
                                · <?= $escape($song['genero']) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($song['album'])): ?>
                            <p class="song-album">
                                Álbum:
                                <?= $escape($song['album']) ?>
                            </p>
                        <?php else: ?>
                            <p class="song-album">
                                Sencillo
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($song['audio_url'])): ?>
                            <audio
                                class="audio-player"
                                controls
                                preload="none"
                                src="<?= $escape($song['audio_url']) ?>"
                            >
                                Tu navegador no admite reproducción
                                de audio.
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
<section class="public-section">
    <div class="container">

        <header class="section-heading">
            <span>Popularidad de los últimos 30 días</span>
            <h2>Top 10</h2>
        </header>

        <?php if (!empty($artist)): ?>
            <div class="highlight">
                <span>Artista más escuchado:</span>

                <strong>
                    <?= $escape($artist['artista'] ?? '') ?>
                </strong>

                <?php if (!empty($artist['tipo'])): ?>
                    <span>
                        · <?= $escape($artist['tipo']) ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($artist['genero'])): ?>
                    <span>
                        · <?= $escape($artist['genero']) ?>
                    </span>
                <?php endif; ?>

                <small>
                    <?= (int)($artist['total'] ?? 0) ?>
                    reproducciones entre sus canciones
                </small>
            </div>
        <?php endif; ?>

        <?php if ($top === []): ?>
            <div class="public-empty-state">
                <h3>Todavía no hay reproducciones</h3>
                <p>Las canciones más escuchadas aparecerán aquí.</p>
            </div>
        <?php else: ?>
            <?php
            $podiumOrder = [1, 0, 2];
            $podiumLabels = [
                1 => 'Segundo lugar',
                0 => 'Primer lugar',
                2 => 'Tercer lugar',
            ];
            ?>

            <div class="top-podium" aria-label="Podio de canciones más escuchadas">
                <?php foreach ($podiumOrder as $topIndex): ?>
                    <?php if (!isset($top[$topIndex])) { continue; } ?>
                    <?php
                    $topItem = $top[$topIndex];
                    $position = $topIndex + 1;
                    $topImage = $topItem['imagen_url']
                        ?: 'https://placehold.co/600x600/1b1b1b/ffffff?text=Cancion';
                    $plays = (int)($topItem['reproducciones'] ?? 0);
                    ?>
                    <article class="podium-card podium-card-<?= $position ?>">
                        <span class="podium-rank" aria-label="<?= $podiumLabels[$topIndex] ?>">
                            <?= $position ?>
                        </span>
                        <div class="podium-cover">
                            <img
                                src="<?= $escape($topImage) ?>"
                                alt="Portada de <?= $escape($topItem['cancion'] ?? '') ?>"
                                loading="lazy"
                            >
                        </div>
                        <div class="podium-info">
                            <span class="podium-place"><?= $podiumLabels[$topIndex] ?></span>
                            <h3><?= $escape($topItem['cancion'] ?? '') ?></h3>
                            <p><?= $escape($topItem['artista'] ?? '') ?></p>
                            <strong><?= $plays ?> reproducci<?= $plays === 1 ? 'ón' : 'ones' ?></strong>
                        </div>
                        <div class="podium-step" aria-hidden="true">
                            <span><?= $position ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (count($top) > 3): ?>
                <div class="top-ranking" aria-label="Resto del Top 10">
                    <div class="top-ranking-heading">
                        <span>Posición</span>
                        <span>Canción</span>
                        <span>Reproducciones</span>
                    </div>
                    <?php foreach (array_slice($top, 3) as $index => $topItem): ?>
                        <?php
                        $position = $index + 4;
                        $plays = (int)($topItem['reproducciones'] ?? 0);
                        $topImage = $topItem['imagen_url']
                            ?: 'https://placehold.co/200x200/1b1b1b/ffffff?text=Cancion';
                        ?>
                        <article class="ranking-row">
                            <strong class="ranking-position"><?= $position ?></strong>
                            <img src="<?= $escape($topImage) ?>" alt="" loading="lazy">
                            <div class="ranking-song">
                                <h3><?= $escape($topItem['cancion'] ?? '') ?></h3>
                                <p><?= $escape($topItem['artista'] ?? '') ?></p>
                            </div>
                            <span class="ranking-plays">
                                <strong><?= $plays ?></strong>
                                <small>reproducciones</small>
                            </span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<!-- EVENTOS -->
<section
    id="eventos"
    class="public-section public-section-alt"
>
    <div class="container">

        <header class="section-heading">
            <span>Próximas presentaciones</span>
            <h2>Eventos</h2>
        </header>

        <?php if ($events === []): ?>
            <div class="public-empty-state">
                <h3>No hay próximos eventos</h3>

                <p>
                    Los eventos activos aparecerán aquí.
                </p>
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <?php foreach ($events as $event): ?>
                <?php
                $eventImage = $event['imagen_url']
                    ?: (
                        'https://placehold.co/'
                        . '600x400/1b1b1b/ffffff'
                        . '?text=Evento'
                    );
                ?>

                <article class="content-card">
                    <img
                        src="<?= $escape($eventImage) ?>"
                        alt="<?= $escape($event['nombre']) ?>"
                        loading="lazy"
                    >

                    <div class="content-card-body">
                        <h3>
                            <?= $escape($event['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($event['fecha'] ?? '') ?>

                            <?php if (!empty($event['hora'])): ?>
                                ·
                                <?= $escape(
                                    substr(
                                        (string)$event['hora'],
                                        0,
                                        5
                                    )
                                ) ?>
                            <?php endif; ?>
                        </p>

                        <p>
                            <?= $escape(
                                $event['local_nombre']
                                ?? 'Por confirmar'
                            ) ?>
                        </p>

                        <?php if (!empty($event['descripcion'])): ?>
                            <p>
                                <?= $escape(
                                    mb_strimwidth(
                                        (string)$event['descripcion'],
                                        0,
                                        130,
                                        '...'
                                    )
                                ) ?>
                            </p>
                        <?php endif; ?>

                        <strong class="card-price">
                            B/.
                            <?= number_format(
                                (float)($event['precio'] ?? 0),
                                2
                            ) ?>
                        </strong>

                        <?php if (!in_array(Auth::role(), ['Administrador', 'Operador'], true)): ?>
                            <a class="btn event-ticket-link" href="<?= $escape($baseUrl) ?>/eventos/<?= (int)$event['id'] ?>">
                                Ver evento y entradas
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- LOCALES -->
<section id="locales" class="public-section">
    <div class="container">

        <header class="section-heading">
            <span>Espacios para la música</span>
            <h2>Locales</h2>
        </header>

        <?php if ($venues === []): ?>
            <div class="public-empty-state">
                <h3>No hay locales registrados</h3>

                <p>
                    Los locales activos aparecerán aquí.
                </p>
            </div>
        <?php endif; ?>

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
                        <h3>
                            <?= $escape($venue['nombre']) ?>
                        </h3>

                        <p class="card-kicker">
                            <?= $escape($venue['tipo'] ?? '') ?>

                            <?php if (!empty($venue['provincia'])): ?>
                                · <?= $escape($venue['provincia']) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($venue['direccion'])): ?>
                            <p>
                                <?= $escape($venue['direccion']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($venue['capacidad'])): ?>
                            <p class="venue-capacity">
                                Capacidad:
                                <?= (int)$venue['capacidad'] ?>
                                personas
                            </p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- PREMIUM -->
<?php if (!in_array(Auth::role(), ['Administrador', 'Operador'], true)): ?>
<section class="public-section premium-section">
    <div class="container">

        <header class="section-heading">
            <span>Más música y beneficios</span>
            <h2>Premium</h2>
        </header>

        <?php if ($plans === []): ?>
            <div class="public-empty-state">
                <h3>No hay planes disponibles</h3>

                <p>
                    Los planes Premium activos aparecerán aquí.
                </p>
            </div>
        <?php endif; ?>

        <div class="card-grid plans-grid">
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
                            B/.
                            <?= number_format(
                                (float)($plan['precio'] ?? 0),
                                2
                            ) ?>
                        </strong>

                        <p>
                            <?= $escape(
                                $plan['descripcion'] ?? ''
                            ) ?>
                        </p>

                        <?php if (!empty($plan['duracion_dias'])): ?>
                            <p class="plan-duration">
                                Duración:
                                <?= (int)$plan['duracion_dias'] ?>
                                días
                            </p>
                        <?php endif; ?>

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
<?php endif; ?>
