<?php

declare(strict_types=1);

use App\Helpers\Auth;
use App\Helpers\Flash;

$flash = Flash::pull();

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$isAdmin = str_contains($currentPath, '/admin');
$isAccount = str_contains($currentPath, '/mi-cuenta');
$isLogin = str_contains($currentPath, '/login');

$baseUrl = rtrim($config['base_url'], '/');
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars($baseUrl) ?>/assets/css/main.css?v=<?= time() ?>"
    >

    <?php if ($isAdmin): ?>
        <link
            rel="stylesheet"
            href="<?= htmlspecialchars($baseUrl) ?>/assets/css/admin.css?v=<?= time() ?>"
        >
    <?php endif; ?>
</head>

<body>

<header class="site-header">
    <div class="site-header-inner">

        <a
            class="logo"
            href="<?= htmlspecialchars($baseUrl) ?>/"
        >
            ROKOLA <span>RITMOPTY</span>
        </a>

        <nav class="main-nav" aria-label="Navegación principal">
            <a href="<?= htmlspecialchars($baseUrl) ?>/#artistas">
                Artistas
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#bandas">
                Bandas
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#canciones">
                Canciones
            </a>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#eventos">
                Eventos
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
    <div class="flash <?= htmlspecialchars((string)$flash[0]) ?>">
        <?= htmlspecialchars((string)$flash[1]) ?>
    </div>
<?php endif; ?>

<main></main>