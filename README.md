# Rokola RitmoPTY

## Descripción

Rokola RitmoPTY es una plataforma web desarrollada en PHP y MySQL para promover la escena musical panameña. El sistema permite administrar artistas, álbumes, canciones, eventos, locales y planes Premium, además de ofrecer reproducción de audio y compra de entradas.

---

## Tecnologías

- PHP 8
- MySQL
- HTML5
- CSS3
- JavaScript
- PDO
- WampServer
- phpMyAdmin

---

## Estructura

app/
database/
public/
storage/

---

## Módulos

- Usuarios
- Géneros
- Artistas (Solistas, Bandas y Proyectos Musicales)
- Álbumes
- Canciones
- Eventos
- Locales
- Planes Premium
- Favoritos
- Listas musicales
- Compra de entradas
- Panel administrativo

---

## Instalación

1. Clonar el repositorio.

```bash
git clone https://github.com/JoeAaron09/Proyecto-Final-DS-VII---Plataforma-Musical-PTY.git

2. Copiar el proyecto dentro de:

C:\wamp64\www\

3. Iniciar Apache y MySQL

4. Importar la base de datos.

database/rokola_ritmopty.sql

5. Ejecutar el siguiente script por bloque en MySQL para que funcione con las útlimas actualizaciones.

ALTER TABLE rokola_ritmopty.compras
ADD COLUMN metodo_pago ENUM(
    'yappy',
    'tarjeta',
    'transferencia'
) NULL
AFTER estado;

ALTER TABLE rokola_ritmopty.entradas
ADD COLUMN numero_factura VARCHAR(40) NULL UNIQUE
AFTER metodo_pago;

ALTER TABLE rokola_ritmopty.entradas
ADD COLUMN qr_token CHAR(48) NULL UNIQUE
AFTER numero_factura;

CREATE TABLE rokola_ritmopty.entrada_asientos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entrada_id BIGINT UNSIGNED NOT NULL,
    evento_id INT UNSIGNED NOT NULL,
    asiento VARCHAR(10) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_entrada_asientos_entrada
        FOREIGN KEY (entrada_id)
        REFERENCES rokola_ritmopty.entradas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_entrada_asientos_evento
        FOREIGN KEY (evento_id)
        REFERENCES rokola_ritmopty.eventos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uk_evento_asiento
        UNIQUE (evento_id, asiento),

    INDEX idx_entrada_asientos_entrada (entrada_id)
) ENGINE=InnoDB;

ALTER TABLE rokola_ritmopty.entradas
ADD COLUMN numero_factura VARCHAR(40) NULL
AFTER metodo_pago;

ALTER TABLE rokola_ritmopty.entradas
ADD CONSTRAINT uk_entradas_numero_factura
UNIQUE (numero_factura);

ALTER TABLE rokola_ritmopty.entradas
ADD COLUMN qr_token CHAR(48) NULL
AFTER numero_factura;

ALTER TABLE rokola_ritmopty.entradas
ADD CONSTRAINT uk_entradas_qr_token
UNIQUE (qr_token);

6. Abrir el proyecto en el navegador.

http://localhost/RokolaRitmoPTY/public
