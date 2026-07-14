<?php

declare(strict_types=1);

use App\Helpers\Auth;
use App\Helpers\Flash;

$flash = Flash::pull();

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$baseUrl = rtrim(
    $config['base_url'],
    '/'
);

$isAdmin = preg_match(
    '#/admin(?:/|$)#',
    $currentPath
) === 1;

$isAccount = preg_match(
    '#/mi-cuenta(?:/|$)#',
    $currentPath
) === 1;

$isLogin = preg_match(
    '#/login(?:/|$)#',
    $currentPath
) === 1;
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= htmlspecialchars(
            $config['app_name'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <meta
        name="description"
        content="Rokola RitmoPTY, el pulso musical de Panamá."
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl,
            ENT_QUOTES,
            'UTF-8'
        ) ?>/assets/css/main.css?v=<?= time() ?>"
    >

    <?php if ($isAdmin): ?>
        <link
            rel="stylesheet"
            href="<?= htmlspecialchars(
                $baseUrl,
                ENT_QUOTES,
                'UTF-8'
            ) ?>/assets/css/admin.css?v=<?= time() ?>"
        >
    <?php endif; ?>
</head>

<body>

<header class="site-header">
    <div class="site-header-inner">

        <a
            class="logo"
            href="<?= htmlspecialchars(
                $baseUrl,
                ENT_QUOTES,
                'UTF-8'
            ) ?>/"
            aria-label="Ir a la página de inicio"
        >
            ROKOLA <span>RITMOPTY</span>
        </a>

        <nav
            class="main-nav"
            aria-label="Navegación principal"
        >
            <a href="<?= htmlspecialchars($baseUrl) ?>/#artistas">
                Artistas
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#canciones">
                Canciones
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#eventos">
                Eventos
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#locales">
                Locales
            </a>

            <?php if (Auth::check()): ?>

                <a
                    class="<?= $isAccount ? 'active' : '' ?>"
                    href="<?= htmlspecialchars($baseUrl) ?>/mi-cuenta"
                >
                    Mi cuenta
                </a>

                <?php if (
                    in_array(
                        Auth::role(),
                        ['Administrador', 'Operador'],
                        true
                    )
                ): ?>
                    <a
                        class="<?= $isAdmin ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($baseUrl) ?>/admin"
                    >
                        Panel
                    </a>
                <?php endif; ?>

                <a
                    class="nav-button"
                    href="<?= htmlspecialchars($baseUrl) ?>/logout"
                >
                    Salir
                </a>

            <?php else: ?>

                <a
                    class="nav-button <?= $isLogin ? 'active' : '' ?>"
                    href="<?= htmlspecialchars($baseUrl) ?>/login"
                >
                    Acceder
                </a>

            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($flash): ?>
    <?php
    $flashType = htmlspecialchars(
        (string)($flash[0] ?? 'success'),
        ENT_QUOTES,
        'UTF-8'
    );

    $flashMessage = htmlspecialchars(
        (string)($flash[1] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
    ?>

    <div class="flash <?= $flashType ?>">
        <?= $flashMessage ?>
    </div>
<?php endif; ?>

<main></main>