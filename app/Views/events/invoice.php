<?php
declare(strict_types=1);
$escape = static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$baseUrl = rtrim($config['base_url'], '/');
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$verifyUrl = ($isHttps ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $baseUrl . '/entradas/verificar/' . $ticket['qr_token'];
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=12&data=' . rawurlencode($verifyUrl);
$paymentNames = ['yappy' => 'Yappy', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia bancaria'];
?>
<section class="public-section invoice-section">
    <div class="container">
        <div class="invoice-actions"><a href="<?= $escape($baseUrl) ?>/mi-cuenta#mis-entradas">← Mis entradas</a><button class="btn" type="button" onclick="window.print()">Imprimir factura</button></div>
        <article class="invoice-card">
            <header><div><span>Rokola RitmoPTY</span><h1>Entrada confirmada</h1><p>Compra simulada · Sin cobro real</p></div><strong><?= $escape($ticket['numero_factura']) ?></strong></header>
            <div class="invoice-body">
                <div class="invoice-details">
                    <span>Evento</span><h2><?= $escape($ticket['evento']) ?></h2>
                    <div class="invoice-data"><div><small>Fecha y hora</small><strong><?= $escape($ticket['fecha_evento']) ?> · <?= $escape(substr((string)$ticket['hora'], 0, 5)) ?></strong></div><div><small>Lugar</small><strong><?= $escape($ticket['local_nombre'] ?? 'Por confirmar') ?></strong></div><div><small>Comprador</small><strong><?= $escape($ticket['comprador']) ?></strong><span><?= $escape($ticket['correo']) ?></span></div><div><small>Asientos</small><strong><?= $escape($ticket['asientos'] ?: 'Entrada general') ?></strong></div><div><small>Método simulado</small><strong><?= $escape($paymentNames[$ticket['metodo_pago']] ?? $ticket['metodo_pago']) ?></strong></div><div><small>Cantidad</small><strong><?= (int)$ticket['cantidad'] ?></strong></div></div>
                    <dl class="invoice-totals"><div><dt>Subtotal</dt><dd>B/. <?= number_format((float)$ticket['subtotal'], 2) ?></dd></div><div><dt>ITBMS</dt><dd>B/. <?= number_format((float)$ticket['itbms'], 2) ?></dd></div><div><dt>Total</dt><dd>B/. <?= number_format((float)$ticket['total'], 2) ?></dd></div></dl>
                </div>
                <aside class="invoice-qr"><img src="<?= $escape($qrUrl) ?>" alt="Código QR de la entrada"><strong>Presenta este QR al ingresar</strong><small>La entrada puede verificarse al escanearlo.</small></aside>
            </div>
        </article>
    </div>
</section>
