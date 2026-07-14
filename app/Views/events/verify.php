<?php declare(strict_types=1); $escape = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>
<section class="public-section verification-section"><div class="container"><article class="panel verification-card">
<?php if ($ticket): ?><span class="verification-icon">✓</span><small>Entrada verificada</small><h1><?= $escape($ticket['evento']) ?></h1><p><?= $escape($ticket['fecha']) ?> · <?= $escape(substr((string)$ticket['hora'], 0, 5)) ?></p><p><?= $escape($ticket['local_nombre'] ?? '') ?></p><strong><?= $escape($ticket['numero_factura']) ?> · <?= (int)$ticket['cantidad'] ?> entrada(s)</strong><em>Estado: <?= $escape($ticket['estado']) ?></em>
<?php else: ?><span class="verification-icon invalid">×</span><small>Entrada no válida</small><h1>No pudimos verificar este QR</h1><p>Revisa que el enlace esté completo.</p><?php endif; ?>
</article></div></section>
