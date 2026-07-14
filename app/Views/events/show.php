<?php
declare(strict_types=1);
use App\Helpers\Csrf;
$escape = static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$baseUrl = rtrim($config['base_url'], '/');
$occupiedMap = array_fill_keys($occupiedSeats, true);
?>

<section class="checkout-hero" style="--event-image: url('<?= $escape($event['imagen_url'] ?? '') ?>')">
    <div class="container checkout-hero-content">
        <a class="checkout-back" href="<?= $escape($baseUrl) ?>/#eventos">← Volver a eventos</a>
        <span>Compra simulada · No se realizará ningún cobro real</span>
        <h1><?= $escape($event['nombre']) ?></h1>
        <p><?= $escape($event['descripcion'] ?? '') ?></p>
        <div class="event-meta">
            <strong><?= $escape($event['fecha']) ?> · <?= $escape(substr((string)$event['hora'], 0, 5)) ?></strong>
            <span><?= $escape($event['local_nombre'] ?? 'Por confirmar') ?></span>
            <span>B/. <?= number_format((float)$event['precio'], 2) ?> por entrada</span>
        </div>
    </div>
</section>

<section class="public-section checkout-section">
    <div class="container checkout-layout">
        <form class="panel checkout-form" method="post" action="<?= $escape($baseUrl) ?>/eventos/<?= (int)$event['id'] ?>/comprar">
            <?= Csrf::field() ?>
            <div class="checkout-step">
                <span>01</span>
                <div><small>Entradas</small><h2><?= $hasAssignedSeats ? 'Elige tus asientos' : 'Elige la cantidad' ?></h2></div>
            </div>

            <?php if ($hasAssignedSeats): ?>
                <input type="hidden" name="asientos" id="selected-seats" required>
                <div class="seat-picker" data-seat-picker>
                    <div class="stage">ESCENARIO</div>
                    <div class="seat-map" aria-label="Mapa de asientos">
                        <?php foreach (range('A', 'H') as $row): ?>
                            <span class="seat-row-label"><?= $row ?></span>
                            <?php foreach (range(1, 12) as $number): ?>
                                <?php $seat = $row . $number; $occupied = isset($occupiedMap[$seat]); ?>
                                <button class="seat<?= $occupied ? ' occupied' : '' ?>" type="button" data-seat="<?= $seat ?>" <?= $occupied ? 'disabled' : '' ?> aria-label="Asiento <?= $seat ?><?= $occupied ? ' ocupado' : '' ?>"><?= $number ?></button>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="seat-legend"><span><i></i>Disponible</span><span><i class="selected"></i>Seleccionado</span><span><i class="occupied"></i>Ocupado</span></div>
                    <p class="seat-selection">Asientos seleccionados: <strong data-seat-summary>Ninguno</strong></p>
                </div>
            <?php else: ?>
                <label class="checkout-field">Cantidad de entradas
                    <input type="number" name="cantidad" min="1" max="10" value="1" required data-ticket-quantity>
                </label>
                <div class="general-admission"><strong>Entrada general</strong><span>Acceso por orden de llegada. No requiere asiento reservado.</span></div>
            <?php endif; ?>

            <div class="checkout-step payment-step">
                <span>02</span>
                <div><small>Pago de demostración</small><h2>Selecciona cómo pagarías</h2></div>
            </div>
            <div class="payment-methods">
                <label><input type="radio" name="metodo_pago" value="yappy" checked><span><strong>Yappy</strong><small>Simulación rápida</small></span></label>
                <label><input type="radio" name="metodo_pago" value="tarjeta"><span><strong>Tarjeta</strong><small>Crédito o débito</small></span></label>
                <label><input type="radio" name="metodo_pago" value="transferencia"><span><strong>Transferencia</strong><small>Banca en línea</small></span></label>
            </div>
            <div class="payment-notice">Este es un proyecto demostrativo. No solicitamos datos bancarios y no se procesará dinero real.</div>
            <button class="btn checkout-submit" type="submit">Confirmar compra simulada</button>
        </form>

        <aside class="panel order-summary" data-unit-price="<?= $escape($event['precio']) ?>">
            <span>Resumen</span><h2>Tu entrada</h2>
            <img src="<?= $escape($event['imagen_url'] ?? '') ?>" alt="">
            <h3><?= $escape($event['nombre']) ?></h3>
            <p><?= $escape($event['local_nombre'] ?? '') ?></p>
            <dl><div><dt>Entradas</dt><dd data-summary-quantity>1</dd></div><div><dt>Subtotal</dt><dd data-summary-subtotal>B/. <?= number_format((float)$event['precio'], 2) ?></dd></div><div><dt>ITBMS (7%)</dt><dd data-summary-tax>B/. <?= number_format((float)$event['precio'] * .07, 2) ?></dd></div><div class="summary-total"><dt>Total</dt><dd data-summary-total>B/. <?= number_format((float)$event['precio'] * 1.07, 2) ?></dd></div></dl>
        </aside>
    </div>
</section>

<script>
(() => {
    const summary = document.querySelector('.order-summary');
    if (!summary) return;
    const price = Number(summary.dataset.unitPrice);
    const update = quantity => {
        const subtotal = price * quantity;
        summary.querySelector('[data-summary-quantity]').textContent = quantity;
        summary.querySelector('[data-summary-subtotal]').textContent = `B/. ${subtotal.toFixed(2)}`;
        summary.querySelector('[data-summary-tax]').textContent = `B/. ${(subtotal * .07).toFixed(2)}`;
        summary.querySelector('[data-summary-total]').textContent = `B/. ${(subtotal * 1.07).toFixed(2)}`;
    };
    const quantity = document.querySelector('[data-ticket-quantity]');
    if (quantity) quantity.addEventListener('input', () => update(Math.max(1, Number(quantity.value) || 1)));
    const picker = document.querySelector('[data-seat-picker]');
    if (picker) {
        const selected = new Set();
        picker.addEventListener('click', event => {
            const button = event.target.closest('[data-seat]');
            if (!button) return;
            selected.has(button.dataset.seat) ? selected.delete(button.dataset.seat) : selected.add(button.dataset.seat);
            button.classList.toggle('selected');
            document.querySelector('#selected-seats').value = [...selected].join(',');
            picker.querySelector('[data-seat-summary]').textContent = selected.size ? [...selected].join(', ') : 'Ninguno';
            update(selected.size || 1);
        });
    }
})();
</script>
