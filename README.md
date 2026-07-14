# Rokola RitmoPTY — instalación

1. Copie la carpeta `RokolaRitmoPTY` en `C:\wamp64\www\`.
2. Inicie Apache y MySQL.
3. Importe `database/rokola_ritmopty.sql` en phpMyAdmin.
4. Revise `config.php`. En WampServer estándar: usuario `root` y contraseña vacía.
5. Abra `http://localhost/RokolaRitmoPTY/public/`.

## Credenciales
- Administrador: `admin@rokola.test` / `Admin123*`
- Operador: `operador@rokola.test` / `Admin123*`
- Usuario: `usuario@rokola.test` / `Admin123*`

## Módulos
Página pública; registro/login/cambio de contraseña; roles; CRUD de usuarios, géneros, artistas, bandas, álbumes, canciones, locales, eventos y planes; carga de imágenes/audio; reproducción con límite gratuito; Top 10 y artista del momento; favoritos; listas; membresías; entradas con ITBMS; historial; reportes CSV.

## Nota sobre rutas
Si cambia el nombre de la carpeta, modifique `base_url` en `config.php` y `RewriteBase` en `public/.htaccess`.
