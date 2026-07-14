/* =========================================================
   ROKOLA RITMOPTY
   BASE DE DATOS COMPLETA Y REESTRUCTURADA

   Nuevo modelo:
   - Artistas incluye solistas, bandas y proyectos musicales.
   - Álbumes pertenecen a un artista.
   - Canciones pertenecen a un artista y opcionalmente a un álbum.
   - Ya no existe la tabla bandas.
   ========================================================= */

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS rokola_ritmopty;

CREATE DATABASE rokola_ritmopty
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rokola_ritmopty;

/* =========================================================
   1. ROLES
   ========================================================= */

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


/* =========================================================
   2. USUARIOS
   ========================================================= */

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nacionalidad VARCHAR(100) NULL DEFAULT 'Panameña',

    tipo_usuario ENUM(
        'gratuito',
        'premium'
    ) NOT NULL DEFAULT 'gratuito',

    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_usuarios_rol (rol_id),
    INDEX idx_usuarios_estado (estado),
    INDEX idx_usuarios_tipo (tipo_usuario)
) ENGINE=InnoDB;


/* =========================================================
   3. GÉNEROS MUSICALES
   ========================================================= */

CREATE TABLE generos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_generos_estado (estado)
) ENGINE=InnoDB;


/* =========================================================
   4. ARTISTAS
   Incluye solistas, bandas y proyectos musicales.
   ========================================================= */

CREATE TABLE artistas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    genero_id INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,

    tipo ENUM(
        'Solista',
        'Banda',
        'Proyecto musical'
    ) NOT NULL DEFAULT 'Solista',

    biografia TEXT NULL,
    pais VARCHAR(100) NULL DEFAULT 'Panamá',
    anio_inicio SMALLINT UNSIGNED NULL,
    imagen_url VARCHAR(500) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_artistas_genero
        FOREIGN KEY (genero_id)
        REFERENCES generos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_artistas_genero (genero_id),
    INDEX idx_artistas_tipo (tipo),
    INDEX idx_artistas_estado (estado),
    INDEX idx_artistas_nombre (nombre)
) ENGINE=InnoDB;


/* =========================================================
   5. ÁLBUMES
   Cada álbum pertenece a un único artista.
   ========================================================= */

CREATE TABLE albumes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artista_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(180) NOT NULL,
    anio_lanzamiento SMALLINT UNSIGNED NULL,
    portada_url VARCHAR(500) NULL,
    descripcion TEXT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_albumes_artista
        FOREIGN KEY (artista_id)
        REFERENCES artistas(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_albumes_artista (artista_id),
    INDEX idx_albumes_estado (estado),
    INDEX idx_albumes_nombre (nombre)
) ENGINE=InnoDB;


/* =========================================================
   6. CANCIONES
   El álbum es opcional para permitir sencillos.
   El género se obtiene desde el artista.
   ========================================================= */

CREATE TABLE canciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artista_id INT UNSIGNED NOT NULL,
    album_id INT UNSIGNED NULL,
    nombre VARCHAR(180) NOT NULL,
    duracion VARCHAR(10) NULL,
    audio_url VARCHAR(500) NULL,
    imagen_url VARCHAR(500) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_canciones_artista
        FOREIGN KEY (artista_id)
        REFERENCES artistas(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_canciones_album
        FOREIGN KEY (album_id)
        REFERENCES albumes(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_canciones_artista (artista_id),
    INDEX idx_canciones_album (album_id),
    INDEX idx_canciones_estado (estado),
    INDEX idx_canciones_nombre (nombre)
) ENGINE=InnoDB;


/* =========================================================
   7. LOCALES
   ========================================================= */

CREATE TABLE locales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(100) NULL,
    direccion VARCHAR(255) NULL,
    provincia VARCHAR(100) NULL,
    capacidad INT UNSIGNED NULL,
    telefono VARCHAR(50) NULL,
    correo VARCHAR(150) NULL,
    imagen_url VARCHAR(500) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_locales_estado (estado),
    INDEX idx_locales_provincia (provincia),
    INDEX idx_locales_nombre (nombre)
) ENGINE=InnoDB;


/* =========================================================
   8. EVENTOS
   ========================================================= */

CREATE TABLE eventos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    local_id INT UNSIGNED NULL,
    nombre VARCHAR(180) NOT NULL,
    descripcion TEXT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    capacidad INT UNSIGNED NULL,
    imagen_url VARCHAR(500) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_eventos_local
        FOREIGN KEY (local_id)
        REFERENCES locales(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_eventos_local (local_id),
    INDEX idx_eventos_fecha (fecha),
    INDEX idx_eventos_estado (estado)
) ENGINE=InnoDB;


/* =========================================================
   9. ARTISTAS PARTICIPANTES EN EVENTOS
   Permite vincular varios artistas con un mismo evento.
   ========================================================= */

CREATE TABLE evento_artistas (
    evento_id INT UNSIGNED NOT NULL,
    artista_id INT UNSIGNED NOT NULL,
    orden_presentacion INT UNSIGNED NULL,

    PRIMARY KEY (evento_id, artista_id),

    CONSTRAINT fk_evento_artistas_evento
        FOREIGN KEY (evento_id)
        REFERENCES eventos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_evento_artistas_artista
        FOREIGN KEY (artista_id)
        REFERENCES artistas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_evento_artistas_artista (artista_id)
) ENGINE=InnoDB;


/* =========================================================
   10. PLANES PREMIUM
   ========================================================= */

CREATE TABLE planes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duracion_dias INT UNSIGNED NOT NULL DEFAULT 30,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_planes_estado (estado)
) ENGINE=InnoDB;


/* =========================================================
   11. COMPRAS DE PLANES
   Compatible con el reporte de ventas.
   ========================================================= */

CREATE TABLE compras (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    itbms DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    estado ENUM(
        'pendiente',
        'pagada',
        'cancelada'
    ) NOT NULL DEFAULT 'pagada',

    metodo_pago ENUM(
        'yappy',
        'tarjeta',
        'transferencia'
    ) NULL,

    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_compras_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_compras_plan
        FOREIGN KEY (plan_id)
        REFERENCES planes(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_compras_usuario (usuario_id),
    INDEX idx_compras_plan (plan_id),
    INDEX idx_compras_fecha (fecha_hora),
    INDEX idx_compras_estado (estado)
) ENGINE=InnoDB;


/* =========================================================
   12. SUSCRIPCIONES PREMIUM
   ========================================================= */

CREATE TABLE suscripciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    compra_id INT UNSIGNED NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_suscripciones_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_suscripciones_plan
        FOREIGN KEY (plan_id)
        REFERENCES planes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_suscripciones_compra
        FOREIGN KEY (compra_id)
        REFERENCES compras(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_suscripciones_usuario (usuario_id),
    INDEX idx_suscripciones_plan (plan_id),
    INDEX idx_suscripciones_estado (estado),
    INDEX idx_suscripciones_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB;


/* =========================================================
   13. REPRODUCCIONES
   ========================================================= */

CREATE TABLE reproducciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cancion_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    nacionalidad_usuario VARCHAR(100) NULL,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reproducciones_cancion
        FOREIGN KEY (cancion_id)
        REFERENCES canciones(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_reproducciones_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_reproducciones_cancion (cancion_id),
    INDEX idx_reproducciones_usuario (usuario_id),
    INDEX idx_reproducciones_fecha (fecha_hora)
) ENGINE=InnoDB;


/* =========================================================
   14. FAVORITOS
   ========================================================= */

CREATE TABLE favoritos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    cancion_id INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_favoritos_usuario_cancion
        UNIQUE (usuario_id, cancion_id),

    CONSTRAINT fk_favoritos_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_favoritos_cancion
        FOREIGN KEY (cancion_id)
        REFERENCES canciones(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_favoritos_usuario (usuario_id),
    INDEX idx_favoritos_cancion (cancion_id)
) ENGINE=InnoDB;


/* =========================================================
   15. ENTRADAS DE EVENTOS
   Compatible con el reporte de eventos.
   ========================================================= */

CREATE TABLE entradas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    evento_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    itbms DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    estado ENUM(
        'reservada',
        'pagada',
        'cancelada'
    ) NOT NULL DEFAULT 'pagada',

    metodo_pago ENUM(
        'yappy',
        'tarjeta',
        'transferencia'
    ) NULL,
    numero_factura VARCHAR(40) NULL UNIQUE,
    qr_token CHAR(48) NULL UNIQUE,

    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_entradas_evento
        FOREIGN KEY (evento_id)
        REFERENCES eventos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_entradas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_entradas_evento (evento_id),
    INDEX idx_entradas_usuario (usuario_id),
    INDEX idx_entradas_fecha (fecha_hora)
) ENGINE=InnoDB;


/* =========================================================
   16. ASIENTOS ASIGNADOS A ENTRADAS
   ========================================================= */

CREATE TABLE entrada_asientos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entrada_id BIGINT UNSIGNED NOT NULL,
    evento_id INT UNSIGNED NOT NULL,
    asiento VARCHAR(10) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_entrada_asientos_entrada
        FOREIGN KEY (entrada_id)
        REFERENCES entradas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_entrada_asientos_evento
        FOREIGN KEY (evento_id)
        REFERENCES eventos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uk_evento_asiento UNIQUE (evento_id, asiento),
    INDEX idx_entrada_asientos_entrada (entrada_id)
) ENGINE=InnoDB;


/* =========================================================
   DATOS INICIALES
   ========================================================= */

/* ROLES */

INSERT INTO roles (
    nombre,
    descripcion
) VALUES
(
    'Administrador',
    'Control total de usuarios, módulos, contenido y reportes.'
),
(
    'Operador',
    'Gestión de artistas, canciones, álbumes, eventos y locales.'
),
(
    'Usuario',
    'Acceso al catálogo musical y funciones de usuario.'
);


/* =========================================================
   USUARIOS
   Contraseña del administrador: Admin123*
   ========================================================= */

INSERT INTO usuarios (
    rol_id,
    nombre,
    correo,
    password,
    nacionalidad,
    tipo_usuario,
    estado
) VALUES
(
    1,
    'Administrador Rokola',
    'admin@rokola.test',
    '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu',
    'Panameña',
    'premium',
    1
),
(
    2,
    'Operador Rokola',
    'operador@rokola.test',
    '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu',
    'Panameña',
    'premium',
    1
),
(
    3,
    'Usuario de prueba',
    'usuario@rokola.test',
    '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu',
    'Panameña',
    'gratuito',
    1
);


/* =========================================================
   GÉNEROS
   ========================================================= */

INSERT INTO generos (
    nombre,
    descripcion
) VALUES
(
    'Rock alternativo',
    'Rock caracterizado por enfoques independientes, experimentales y contemporáneos.'
),
(
    'Metal',
    'Música de alta intensidad, guitarras distorsionadas y gran presencia rítmica.'
),
(
    'Hard rock',
    'Rock de sonido fuerte, riffs marcados y énfasis en guitarras eléctricas.'
),
(
    'Punk rock',
    'Rock directo, rápido y asociado a la cultura independiente.'
),
(
    'Indie rock',
    'Rock desarrollado desde circuitos independientes y propuestas alternativas.'
),
(
    'Blues rock',
    'Fusión de estructuras del blues con instrumentación y energía del rock.'
);


/* =========================================================
   ARTISTAS
   ========================================================= */

INSERT INTO artistas (
    genero_id,
    nombre,
    tipo,
    biografia,
    pais,
    anio_inicio,
    imagen_url,
    estado
) VALUES
(
    1,
    'Luna Roja',
    'Solista',
    'Proyecto solista panameño de rock alternativo, reconocido por sus composiciones atmosféricas y letras introspectivas.',
    'Panamá',
    2020,
    'https://images.unsplash.com/photo-1524650359799-842906ca1c06?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    6,
    'Marco Stone',
    'Solista',
    'Guitarrista y compositor independiente con influencias del blues, el rock clásico y la música alternativa.',
    'Panamá',
    2018,
    'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    1,
    'Canal Distorsión',
    'Banda',
    'Banda panameña de rock alternativo formada en Ciudad de Panamá, con un sonido basado en guitarras densas y melodías urbanas.',
    'Panamá',
    2018,
    'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    2,
    'Istmo Negro',
    'Banda',
    'Banda de metal moderno que integra elementos de la identidad panameña con una producción pesada y contemporánea.',
    'Panamá',
    2016,
    'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    4,
    'Ruido Urbano',
    'Proyecto musical',
    'Proyecto colectivo de punk rock enfocado en relatos sobre la vida urbana, la cultura independiente y la crítica social.',
    'Panamá',
    2021,
    'https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?auto=format&fit=crop&w=1200&q=80',
    1
);


/* =========================================================
   ÁLBUMES
   ========================================================= */

INSERT INTO albumes (
    artista_id,
    nombre,
    anio_lanzamiento,
    portada_url,
    descripcion,
    estado
) VALUES
(
    3,
    'Puente de Acero',
    2025,
    'https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?auto=format&fit=crop&w=1000&q=80',
    'Primer álbum de estudio de Canal Distorsión, inspirado en la identidad urbana de Ciudad de Panamá.',
    1
),
(
    1,
    'Noches Rojas',
    2024,
    'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1000&q=80',
    'Colección de canciones atmosféricas de Luna Roja.',
    1
),
(
    4,
    'Metal del Istmo',
    2025,
    'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1000&q=80',
    'Álbum de metal moderno inspirado en el paisaje, la historia y la energía del istmo panameño.',
    1
);


/* =========================================================
   CANCIONES
   ========================================================= */

INSERT INTO canciones (
    artista_id,
    album_id,
    nombre,
    duracion,
    audio_url,
    imagen_url,
    estado
) VALUES
(
    3,
    1,
    'Ciudad Eléctrica',
    '03:42',
    'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
    'https://images.unsplash.com/photo-1524650359799-842906ca1c06?auto=format&fit=crop&w=1000&q=80',
    1
),
(
    3,
    1,
    'Cruzar el Canal',
    '04:10',
    'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
    'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=1000&q=80',
    1
),
(
    4,
    3,
    'Ruido del Istmo',
    '03:55',
    'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
    'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1000&q=80',
    1
),
(
    1,
    2,
    'Luz de Medianoche',
    '04:02',
    'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',
    'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1000&q=80',
    1
),
(
    2,
    NULL,
    'Cuerdas del Casco',
    '03:38',
    'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',
    'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1000&q=80',
    1
);


/* =========================================================
   LOCALES
   ========================================================= */

INSERT INTO locales (
    nombre,
    tipo,
    direccion,
    provincia,
    capacidad,
    telefono,
    correo,
    imagen_url,
    estado
) VALUES
(
    'Sala El Sótano',
    'Sala de conciertos',
    'Vía Argentina',
    'Panamá',
    350,
    '6000-0000',
    'eventos@sotano.test',
    'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    'Teatro del Istmo',
    'Teatro',
    'Casco Antiguo',
    'Panamá',
    700,
    '6000-1111',
    'info@istmo.test',
    'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    'Hangar Alternativo',
    'Centro cultural',
    'San Francisco',
    'Panamá',
    500,
    '6000-2222',
    'contacto@hangar.test',
    'https://images.unsplash.com/photo-1521337581100-8ca9a73a5f79?auto=format&fit=crop&w=1200&q=80',
    1
);


/* =========================================================
   EVENTOS
   Fechas futuras respecto de julio de 2026.
   ========================================================= */

INSERT INTO eventos (
    local_id,
    nombre,
    descripcion,
    fecha,
    hora,
    precio,
    capacidad,
    imagen_url,
    estado
) VALUES
(
    1,
    'Noche de Distorsión',
    'Concierto de rock alternativo panameño con artistas emergentes de la escena local.',
    '2026-07-28',
    '20:00:00',
    15.00,
    350,
    'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    2,
    'Metal del Canal',
    'Encuentro de bandas y proyectos de metal nacional.',
    '2026-08-12',
    '19:30:00',
    22.50,
    700,
    'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1200&q=80',
    1
),
(
    3,
    'Sesiones del Istmo',
    'Festival de artistas independientes, rock alternativo y música experimental.',
    '2026-09-05',
    '18:00:00',
    18.00,
    500,
    'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=1200&q=80',
    1
);


/* Artistas participantes */

INSERT INTO evento_artistas (
    evento_id,
    artista_id,
    orden_presentacion
) VALUES
(1, 1, 1),
(1, 3, 2),
(2, 4, 1),
(3, 2, 1),
(3, 5, 2);


/* =========================================================
   PLANES PREMIUM
   ========================================================= */

INSERT INTO planes (
    nombre,
    descripcion,
    precio,
    duracion_dias,
    estado
) VALUES
(
    'Premium Mensual',
    'Reproducciones ilimitadas y acceso a beneficios exclusivos durante 30 días.',
    4.99,
    30,
    1
),
(
    'Premium Trimestral',
    'Acceso Premium durante 90 días con precio preferencial.',
    12.99,
    90,
    1
),
(
    'Premium Anual',
    'Acceso completo durante un año y beneficios especiales en eventos seleccionados.',
    44.99,
    365,
    1
);


/* =========================================================
   DATOS DE PRUEBA PARA ESTADÍSTICAS Y REPORTES
   ========================================================= */

/* Reproducciones recientes */

INSERT INTO reproducciones (
    cancion_id,
    usuario_id,
    nacionalidad_usuario,
    fecha_hora
) VALUES
(1, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 1, 'Panameña', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 1, 'Panameña', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 1, 'Panameña', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 2, 'Panameña', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 3, 'Panameña', DATE_SUB(NOW(), INTERVAL 6 DAY));


/* Favoritos */

INSERT INTO favoritos (
    usuario_id,
    cancion_id
) VALUES
(3, 1),
(3, 3),
(1, 4);


/* Compra de prueba */

INSERT INTO compras (
    usuario_id,
    plan_id,
    subtotal,
    itbms,
    total,
    estado,
    metodo_pago,
    fecha_hora
) VALUES
(
    3,
    1,
    4.99,
    0.35,
    5.34,
    'pagada',
    'yappy',
    NOW()
);


/* Suscripción relacionada */

INSERT INTO suscripciones (
    usuario_id,
    plan_id,
    compra_id,
    fecha_inicio,
    fecha_fin,
    estado
) VALUES
(
    3,
    1,
    1,
    CURDATE(),
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    1
);


/* Entradas de prueba */

INSERT INTO entradas (
    evento_id,
    usuario_id,
    cantidad,
    precio_unitario,
    subtotal,
    itbms,
    total,
    estado,
    metodo_pago,
    numero_factura,
    qr_token,
    fecha_hora
) VALUES
(
    1,
    3,
    2,
    15.00,
    30.00,
    2.10,
    32.10,
    'pagada',
    'yappy',
    'RRP-DEMO-0001',
    '111111111111111111111111111111111111111111111111',
    NOW()
),
(
    2,
    1,
    1,
    22.50,
    22.50,
    1.58,
    24.08,
    'pagada',
    'tarjeta',
    'RRP-DEMO-0002',
    '222222222222222222222222222222222222222222222222',
    NOW()
);


/* =========================================================
   FINALIZACIÓN
   ========================================================= */

SET FOREIGN_KEY_CHECKS = 1;


/* =========================================================
   VERIFICACIONES
   ========================================================= */

SELECT
    a.id,
    a.nombre,
    a.tipo,
    g.nombre AS genero,
    a.pais,
    a.anio_inicio,
    a.estado
FROM artistas a
LEFT JOIN generos g
    ON g.id = a.genero_id
ORDER BY a.nombre;


SELECT
    al.id,
    al.nombre AS album,
    a.nombre AS artista,
    a.tipo AS tipo_artista,
    al.anio_lanzamiento
FROM albumes al
INNER JOIN artistas a
    ON a.id = al.artista_id
ORDER BY al.nombre;


SELECT
    c.id,
    c.nombre AS cancion,
    a.nombre AS artista,
    a.tipo AS tipo_artista,
    g.nombre AS genero,
    COALESCE(al.nombre, 'Sencillo') AS album
FROM canciones c
INNER JOIN artistas a
    ON a.id = c.artista_id
LEFT JOIN generos g
    ON g.id = a.genero_id
LEFT JOIN albumes al
    ON al.id = c.album_id
ORDER BY c.nombre;

USE rokola_ritmopty;

CREATE TABLE listas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_listas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_listas_usuario (usuario_id)
) ENGINE=InnoDB;


CREATE TABLE lista_canciones (
    lista_id INT UNSIGNED NOT NULL,
    cancion_id INT UNSIGNED NOT NULL,
    agregado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (lista_id, cancion_id),

    CONSTRAINT fk_lista_canciones_lista
        FOREIGN KEY (lista_id)
        REFERENCES listas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_lista_canciones_cancion
        FOREIGN KEY (cancion_id)
        REFERENCES canciones(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_lista_canciones_cancion (cancion_id)
) ENGINE=InnoDB;
