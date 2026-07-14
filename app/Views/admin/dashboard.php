<?php

declare(strict_types=1);

$labels = [
    'usuarios' => 'Usuarios',
    'artistas' => 'Artistas',
    'albumes' => 'Álbumes',
    'canciones' => 'Canciones',
    'eventos' => 'Eventos',
    'reproducciones' => 'Reproducciones',
    'compras' => 'Compras',
];
?>

<div class="admin-layout">
    <?php require __DIR__ . '/nav.php'; ?>

    <section class="admin-content">
        <div class="admin-heading">
            <div>
                <span class="admin-eyebrow">
                    Resumen general
                </span>

                <h1>Panel administrativo</h1>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php foreach ($stats as $key => $value): ?>
                <article class="stat-card">
                    <strong>
                        <?= (int)$value ?>
                    </strong>

                    <span>
                        <?= htmlspecialchars(
                            $labels[$key]
                                ?? ucfirst($key),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </div>

        <section class="dashboard-reports">
            <div class="admin-list-heading">
                <div>
                    <span class="admin-eyebrow">
                        Exportación de datos
                    </span>

                    <h2>Reportes</h2>
                </div>
            </div>

            <div class="report-buttons">
                <a
                    class="btn"
                    href="<?= $config['base_url'] ?>/reportes/top"
                >
                    Top 10 CSV
                </a>

                <a
                    class="btn"
                    href="<?= $config['base_url'] ?>/reportes/generos"
                >
                    Géneros CSV
                </a>

                <a
                    class="btn"
                    href="<?= $config['base_url'] ?>/reportes/ventas"
                >
                    Ventas CSV
                </a>

                <a
                    class="btn"
                    href="<?= $config['base_url'] ?>/reportes/eventos"
                >
                    Eventos CSV
                </a>
            </div>
        </section>
    </section>
</div>