<?php

declare(strict_types=1);

use App\Helpers\Auth;

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$baseUrl = rtrim(
    $config['base_url'],
    '/'
);

$menuItems = [
    'generos' => 'Géneros',
    'artistas' => 'Artistas',
    'albumes' => 'Álbumes',
    'canciones' => 'Canciones',
    'locales' => 'Locales',
    'eventos' => 'Eventos',
    'planes' => 'Premium',
];

$isActive = static function (
    string $path
) use ($currentPath): bool {
    return rtrim($currentPath, '/')
        === rtrim($path, '/');
};
?>

<aside
    class="admin-nav"
    aria-label="Navegación administrativa"
>
    <div class="admin-nav-title">
        <span>Administración</span>
        <strong>Rokola RitmoPTY</strong>
    </div>

    <nav class="admin-nav-links">
        <a
            class="<?= $isActive($baseUrl . '/admin')
                ? 'active'
                : '' ?>"
            href="<?= htmlspecialchars(
                $baseUrl . '/admin',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <span class="admin-nav-icon">⌂</span>
            <span>Resumen</span>
        </a>

        <?php if (Auth::role() === 'Administrador'): ?>
            <a
                class="<?= $isActive(
                    $baseUrl . '/admin/usuarios'
                ) ? 'active' : '' ?>"
                href="<?= htmlspecialchars(
                    $baseUrl . '/admin/usuarios',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                <span class="admin-nav-icon">●</span>
                <span>Usuarios</span>
            </a>
        <?php endif; ?>

        <?php foreach ($menuItems as $slug => $label): ?>
            <?php
            $url = $baseUrl
                . '/admin/'
                . $slug;
            ?>

            <a
                class="<?= $isActive($url)
                    ? 'active'
                    : '' ?>"
                href="<?= htmlspecialchars(
                    $url,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                <span class="admin-nav-icon">◆</span>

                <span>
                    <?= htmlspecialchars(
                        $label,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>