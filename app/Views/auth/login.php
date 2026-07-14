<?php

use App\Helpers\Csrf;
?>

<section class="auth-grid">
    <form
        class="panel"
        method="post"
        action="<?= $config['base_url'] ?>/login"
    >
        <h2>INICIAR SESIÓN</h2>

        <?= Csrf::field() ?>

        <label>
            Correo
            <input type="email" name="correo" required>
        </label>

        <label>
            Contraseña
            <input type="password" name="password" required>
        </label>

        <button class="btn" type="submit">Acceder</button>

        <small>
            Demo: admin@rokola.test / Admin123*
        </small>
    </form>

    <form
        class="panel"
        method="post"
        action="<?= $config['base_url'] ?>/register"
    >
        <h2>CREAR CUENTA</h2>

        <?= Csrf::field() ?>

        <label>
            Nombre
            <input name="nombre" required>
        </label>

        <label>
            Correo
            <input type="email" name="correo" required>
        </label>

        <label>
            Nacionalidad
            <input name="nacionalidad" value="Panameña">
        </label>

        <label>
            Contraseña
            <input
                type="password"
                name="password"
                minlength="8"
                required
            >
        </label>

        <button class="btn" type="submit">
            Registrarme
        </button>
    </form>
</section>