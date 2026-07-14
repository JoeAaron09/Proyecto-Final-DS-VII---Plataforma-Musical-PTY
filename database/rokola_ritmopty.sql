-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-07-2026 a las 21:24:56
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rokola_ritmopty`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albumes`
--

DROP TABLE IF EXISTS `albumes`;
CREATE TABLE IF NOT EXISTS `albumes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `artista_id` int UNSIGNED NOT NULL,
  `nombre` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio_lanzamiento` smallint UNSIGNED DEFAULT NULL,
  `portada_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_albumes_artista` (`artista_id`),
  KEY `idx_albumes_estado` (`estado`),
  KEY `idx_albumes_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `albumes`
--

INSERT INTO `albumes` (`id`, `artista_id`, `nombre`, `anio_lanzamiento`, `portada_url`, `descripcion`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Legado', 2025, '/RokolaRitmoPTY/public/uploads/image/4a43dd9bf3181d89a4b145bad6a87732.webp', NULL, 1, '2026-07-14 04:16:11', '2026-07-14 05:32:31'),
(2, 1, 'Noches Rojas', 2024, 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1000&q=80', 'Colección de canciones atmosféricas de Luna Roja.', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(3, 4, 'Metal del Istmo', 2025, 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1000&q=80', 'Álbum de metal moderno inspirado en el paisaje, la historia y la energía del istmo panameño.', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(4, 2, 'You Can Come Out Now', 2025, '/RokolaRitmoPTY/public/uploads/image/36cbd4646f4db96a.jpg', 'Segundo disco lanzado por AlphaWhores.', 1, '2026-07-14 05:01:38', '2026-07-14 05:01:38'),
(5, 4, 'La Patrona', 2018, '/RokolaRitmoPTY/public/uploads/image/0c162b8b787b75045cf3136d778e1992.jpg', NULL, 1, '2026-07-14 05:35:14', '2026-07-14 05:35:14'),
(6, 5, 'Vikorg', 2013, '/RokolaRitmoPTY/public/uploads/image/193cd630a43aee15389bf25296b90248.jpg', NULL, 1, '2026-07-14 05:37:33', '2026-07-14 05:37:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas`
--

DROP TABLE IF EXISTS `artistas`;
CREATE TABLE IF NOT EXISTS `artistas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `genero_id` int UNSIGNED DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('Solista','Banda','Proyecto musical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Solista',
  `biografia` text COLLATE utf8mb4_unicode_ci,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Panamá',
  `anio_inicio` smallint UNSIGNED DEFAULT NULL,
  `imagen_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_artistas_genero` (`genero_id`),
  KEY `idx_artistas_tipo` (`tipo`),
  KEY `idx_artistas_estado` (`estado`),
  KEY `idx_artistas_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `artistas`
--

INSERT INTO `artistas` (`id`, `genero_id`, `nombre`, `tipo`, `biografia`, `pais`, `anio_inicio`, `imagen_url`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Rencilla', 'Banda', 'Rencilla es una banda pionera del hardcore y crossover en Panamá, fundada a finales de la década de 1990 por Justo y Ranfis.\r\nSe distingue en la escena por integrar una filosofía espiritual y positiva en sus letras, bajo el liderazgo vocal de Durmada desde 1999.\r\nA lo largo de más de 25 años de autogestión, han editado seis trabajos discográficos, destacando su álbum más reciente en formato vinilo, Legado (2023).\r\nHan realizado giras por México, Colombia y Costa Rica, compartiendo escenario con leyendas internacionales y consolidándose como un referente esencial de la música subterránea panameña.\r\nActualmente está conformada por:\r\n- Justo Villalaz (guitarra)\r\n- Durmada Damana Das (vocalista)\r\n- Irene Batista (bajo)\r\n- Pedro Caicedo (guitarra)\r\n- Jorge Isaac (batería)', 'Panamá', 1990, '/RokolaRitmoPTY/public/uploads/image/4e3e1ac29756bb0f.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 04:35:22'),
(2, 2, 'AlphaWhores', 'Banda', 'Alphawhores es un dúo de Stoner/Doom Metal panameño integrado por Masiel Pinzón y Juan Carlos García de Paredes (Poti). \r\nSu carrera despegó internacionalmente con el tema \"Same Team\", que les permitió exportar su música.\r\nHan girado por Estados Unidos (2024), Costa Rica y Europa, presentándose en festivales de renombre como el Desert Fest en Londres y Berlín.\r\nSe caracterizan por una ética de trabajo obsesiva y profesional, encargándose ellos mismos de su logística, redes y contenido.\r\nCuentan con el EP en vivo Live in Germany, lanzado tras su primera experiencia europea.', 'Panamá', 2022, '/RokolaRitmoPTY/public/uploads/image/58c67bf3be19f79f.webp', 1, '2026-07-14 04:16:11', '2026-07-14 04:46:17'),
(3, 2, 'Athica', 'Banda', 'Athica es una banda panameña de metal con 23 años de trayectoria, fundada originalmente en 2003 por George y sus compañeros de escuela.\r\nDesde sus inicios, la agrupación se ha caracterizado por un enfoque estricto en la música propia, rechazando el uso de covers para construir una identidad sólida en el género.\r\nEl proyecto dio un salto técnico importante cuando varios de sus miembros estudiaron ingeniería de sonido en Canadá en 2007, lo que les permitió autoproducirse con altos estándares.\r\nEn 2008, Víctor se integró como vocalista y director de arte, consolidando la faceta visual y profesional de la banda. \r\nEntre sus logros más destacados se encuentra su participación en el prestigioso festival Wacken Open Air en Alemania en 2019 y haber abierto el concierto de despedida de Sepultura en Panamá en 2024.\r\nActualmente está conformada por:\r\n- George Barroso (guitarra)\r\n- Victor Arias (vocalista)\r\n- Eduardo Medrano (bajo)\r\n- Jean Muschett (guitarra)\r\n- Tony Sinclair (batería)', 'Panamá', 2003, '/RokolaRitmoPTY/public/uploads/image/77804dc2f7ec5299.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 04:35:51'),
(4, 6, 'Samy y Sandra Sandoval', 'Proyecto musical', 'Samy y Sandra Sandoval son los hermanos más famosos de la música típica panameña. Conocidos como \"Los Patrones de la Cumbia\", son originarios de Monagrillo, Herrera, y han logrado convertir este género tradicional en un fenómeno comercial que atrae a todas las edades y clases sociales en Panamá.\r\nSandra es la carismática vocalista y bailarina, mientras que Samy es el acordeonista y director de la agrupación.', 'Panamá', 1980, '/RokolaRitmoPTY/public/uploads/image/bdacafd1c2df6c0c.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 04:50:38'),
(5, 3, 'Señor Loop', 'Banda', 'Señor Loop es una de las bandas más emblemáticas de rock alternativo y fusión de Panamá. Con más de 20 años de trayectoria, su música mezcla diversos géneros como rock, funk, reggae, salsa y ritmos latinos, manteniendo un sonido tropical muy característico.\r\nEl grupo está conformado por los siguientes músicos:\r\n- Lilo Sánchez (Voz y guitarra)\r\n- Iñaki Iriberri (Guitarra y teclados)\r\n- Carlos Ucar (Bajo y teclados)\r\n- Chale Icaza (Batería)\r\n- Andrés Cevilla (Vientos y teclados)\r\n- Abdiel Morales (Percusión)', 'Panamá', 2000, '/RokolaRitmoPTY/public/uploads/image/cd84df46d768ff7d.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 04:56:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

DROP TABLE IF EXISTS `canciones`;
CREATE TABLE IF NOT EXISTS `canciones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `artista_id` int UNSIGNED NOT NULL,
  `album_id` int UNSIGNED DEFAULT NULL,
  `nombre` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duracion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_canciones_artista` (`artista_id`),
  KEY `idx_canciones_album` (`album_id`),
  KEY `idx_canciones_estado` (`estado`),
  KEY `idx_canciones_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id`, `artista_id`, `album_id`, `nombre`, `duracion`, `audio_url`, `imagen_url`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 3, NULL, 'Parasite', '03:55', '/RokolaRitmoPTY/public/uploads/audio/b6fab89a68b9fbfc5f9b3b7c3441d45a.mp3', '/RokolaRitmoPTY/public/uploads/image/58ade2bc4da809d6.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 20:44:24'),
(2, 5, NULL, 'Señor Loop', '06:17', '/RokolaRitmoPTY/public/uploads/audio/108363af337edcf0863a0e488980e5d7.mp3', '/RokolaRitmoPTY/public/uploads/image/b36e75671e3474cb1eb274cec9f82e19.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 20:55:12'),
(3, 4, 5, 'La Patrona', '05:21', '/RokolaRitmoPTY/public/uploads/audio/6607cad1ff1e9f39723d20983748bd30.mp3', '/RokolaRitmoPTY/public/uploads/image/71d4f62bb1cc996380c52448ca270078.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 20:55:03'),
(4, 1, 1, 'Legado', '03:29', '/RokolaRitmoPTY/public/uploads/audio/ce8fe118206876f73986cc89942564e9.mp3', '/RokolaRitmoPTY/public/uploads/image/89767a5d51d2696f88057092041c29cc.webp', 1, '2026-07-14 04:16:11', '2026-07-14 20:54:53'),
(5, 2, 4, 'Bloodsport', '05:07', '/RokolaRitmoPTY/public/uploads/audio/02b06c15b5440ca1ab52e0152cc55abb.mp3', '/RokolaRitmoPTY/public/uploads/image/7711b8830443239fb38bdf2abc08c798.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 20:54:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `itbms` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('pendiente','pagada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagada',
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_compras_usuario` (`usuario_id`),
  KEY `idx_compras_plan` (`plan_id`),
  KEY `idx_compras_fecha` (`fecha_hora`),
  KEY `idx_compras_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `usuario_id`, `plan_id`, `subtotal`, `itbms`, `total`, `estado`, `fecha_hora`) VALUES
(1, 3, 1, 4.99, 0.35, 5.34, 'pagada', '2026-07-13 23:16:12'),
(2, 4, 2, 12.99, 0.91, 13.90, 'pagada', '2026-07-14 10:11:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

DROP TABLE IF EXISTS `entradas`;
CREATE TABLE IF NOT EXISTS `entradas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `cantidad` int UNSIGNED NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `itbms` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('reservada','pagada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagada',
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entradas_evento` (`evento_id`),
  KEY `idx_entradas_usuario` (`usuario_id`),
  KEY `idx_entradas_fecha` (`fecha_hora`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`id`, `evento_id`, `usuario_id`, `cantidad`, `precio_unitario`, `subtotal`, `itbms`, `total`, `estado`, `fecha_hora`) VALUES
(1, 1, 3, 2, 15.00, 30.00, 2.10, 32.10, 'pagada', '2026-07-13 23:16:12'),
(2, 2, 1, 1, 22.50, 22.50, 1.58, 24.08, 'pagada', '2026-07-13 23:16:12'),
(3, 2, 4, 2, 22.50, 45.00, 3.15, 48.15, 'pagada', '2026-07-14 10:11:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `local_id` int UNSIGNED DEFAULT NULL,
  `nombre` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `capacidad` int UNSIGNED DEFAULT NULL,
  `imagen_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_eventos_local` (`local_id`),
  KEY `idx_eventos_fecha` (`fecha`),
  KEY `idx_eventos_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `local_id`, `nombre`, `descripcion`, `fecha`, `hora`, `precio`, `capacidad`, `imagen_url`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Noche de Distorsión', 'Concierto de rock alternativo panameño con artistas emergentes de la escena local.', '2026-07-28', '20:00:00', 15.00, 350, 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(2, 2, 'Metal del Canal', 'Encuentro de bandas y proyectos de metal nacional.', '2026-08-12', '19:30:00', 22.50, 700, 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1200&q=80', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(3, 3, 'Sesiones del Istmo', 'Festival de artistas independientes, rock alternativo y música experimental.', '2026-09-05', '18:00:00', 18.00, 500, 'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=1200&q=80', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_artistas`
--

DROP TABLE IF EXISTS `evento_artistas`;
CREATE TABLE IF NOT EXISTS `evento_artistas` (
  `evento_id` int UNSIGNED NOT NULL,
  `artista_id` int UNSIGNED NOT NULL,
  `orden_presentacion` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`evento_id`,`artista_id`),
  KEY `idx_evento_artistas_artista` (`artista_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evento_artistas`
--

INSERT INTO `evento_artistas` (`evento_id`, `artista_id`, `orden_presentacion`) VALUES
(1, 1, 1),
(1, 3, 2),
(2, 4, 1),
(3, 2, 1),
(3, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `cancion_id` int UNSIGNED NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_favoritos_usuario_cancion` (`usuario_id`,`cancion_id`),
  KEY `idx_favoritos_usuario` (`usuario_id`),
  KEY `idx_favoritos_cancion` (`cancion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_id`, `cancion_id`, `creado_en`) VALUES
(1, 3, 1, '2026-07-14 04:16:12'),
(2, 3, 3, '2026-07-14 04:16:12'),
(3, 1, 4, '2026-07-14 04:16:12'),
(4, 4, 1, '2026-07-14 15:11:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `generos`
--

DROP TABLE IF EXISTS `generos`;
CREATE TABLE IF NOT EXISTS `generos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_generos_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `generos`
--

INSERT INTO `generos` (`id`, `nombre`, `descripcion`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Crossover', 'El crossover (o crossover thrash) es un subgénero musical que fusiona la velocidad, energía y actitud del hardcore punk con la complejidad técnica, los riffs pesados y la percusión del thrash metal.', 0, '2026-07-14 04:16:11', '2026-07-14 04:28:59'),
(2, 'Metal', 'Música de alta intensidad, guitarras distorsionadas y gran presencia rítmica.', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(3, 'Rock', 'Sonido fuerte, riffs marcados y énfasis en guitarras eléctricas.', 1, '2026-07-14 04:16:11', '2026-07-14 04:19:56'),
(4, 'Punk', 'Rock directo, rápido y asociado a la cultura independiente.', 1, '2026-07-14 04:16:11', '2026-07-14 04:19:22'),
(5, 'Indie', 'Género musical donde los autogestionan sus composiciones y producción.', 1, '2026-07-14 04:16:11', '2026-07-14 04:18:31'),
(6, 'Típico', 'Artistas nacionales del folklore panameño.', 1, '2026-07-14 04:16:11', '2026-07-14 04:19:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listas`
--

DROP TABLE IF EXISTS `listas`;
CREATE TABLE IF NOT EXISTS `listas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_listas_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_canciones`
--

DROP TABLE IF EXISTS `lista_canciones`;
CREATE TABLE IF NOT EXISTS `lista_canciones` (
  `lista_id` int UNSIGNED NOT NULL,
  `cancion_id` int UNSIGNED NOT NULL,
  `agregado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lista_id`,`cancion_id`),
  KEY `idx_lista_canciones_cancion` (`cancion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

DROP TABLE IF EXISTS `locales`;
CREATE TABLE IF NOT EXISTS `locales` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacidad` int UNSIGNED DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_locales_estado` (`estado`),
  KEY `idx_locales_provincia` (`provincia`),
  KEY `idx_locales_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`id`, `nombre`, `tipo`, `direccion`, `provincia`, `capacidad`, `telefono`, `correo`, `imagen_url`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Rock & Folk', 'Sala de conciertos', 'Calle Uruguay', 'Panamá', 500, '6829-7100', 'booking@rockandfolkpanama.com', '/RokolaRitmoPTY/public/uploads/image/70e346a470b7a2b1d1960d716384cb3a.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 05:28:23'),
(2, 'Hangar 18', 'Bar/Venue', 'C. Harry Heno Principal', 'Panamá', 300, '6617-9048', 'barhangarpanama@gmail.com', '/RokolaRitmoPTY/public/uploads/image/646b622f5ea3f8a91810afaa92c8021e.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 05:25:32'),
(3, 'Teatro Escondido', 'Centro cultural', 'Albrook', 'Panamá', 500, '6290-3818', 'teatroescondido.pa@gmail.com', '/RokolaRitmoPTY/public/uploads/image/a2ffd09a657956be2e1e786ea9122b93.jpg', 1, '2026-07-14 04:16:11', '2026-07-14 05:18:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

DROP TABLE IF EXISTS `planes`;
CREATE TABLE IF NOT EXISTS `planes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duracion_dias` int UNSIGNED NOT NULL DEFAULT '30',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_planes_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `nombre`, `descripcion`, `precio`, `duracion_dias`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Premium Mensual', 'Reproducciones ilimitadas y acceso a beneficios exclusivos durante 30 días.', 4.99, 30, 1, '2026-07-14 04:16:12', '2026-07-14 04:16:12'),
(2, 'Premium Trimestral', 'Acceso Premium durante 90 días con precio preferencial.', 12.99, 90, 1, '2026-07-14 04:16:12', '2026-07-14 04:16:12'),
(3, 'Premium Anual', 'Acceso completo durante un año y beneficios especiales en eventos seleccionados.', 44.99, 365, 1, '2026-07-14 04:16:12', '2026-07-14 04:16:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reproducciones`
--

DROP TABLE IF EXISTS `reproducciones`;
CREATE TABLE IF NOT EXISTS `reproducciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cancion_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `nacionalidad_usuario` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reproducciones_cancion` (`cancion_id`),
  KEY `idx_reproducciones_usuario` (`usuario_id`),
  KEY `idx_reproducciones_fecha` (`fecha_hora`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reproducciones`
--

INSERT INTO `reproducciones` (`id`, `cancion_id`, `usuario_id`, `nacionalidad_usuario`, `fecha_hora`) VALUES
(1, 1, 3, 'Panameña', '2026-07-12 23:16:12'),
(2, 1, 3, 'Panameña', '2026-07-11 23:16:12'),
(3, 1, 1, 'Panameña', '2026-07-10 23:16:12'),
(4, 2, 3, 'Panameña', '2026-07-09 23:16:12'),
(5, 2, 1, 'Panameña', '2026-07-08 23:16:12'),
(6, 3, 3, 'Panameña', '2026-07-12 23:16:12'),
(7, 3, 3, 'Panameña', '2026-07-11 23:16:12'),
(8, 3, 1, 'Panameña', '2026-07-10 23:16:12'),
(9, 3, 2, 'Panameña', '2026-07-09 23:16:12'),
(10, 4, 3, 'Panameña', '2026-07-08 23:16:12'),
(11, 5, 3, 'Panameña', '2026-07-07 23:16:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `creado_en`) VALUES
(1, 'Administrador', 'Control total de usuarios, módulos, contenido y reportes.', '2026-07-14 04:16:11'),
(2, 'Operador', 'Gestión de artistas, canciones, álbumes, eventos y locales.', '2026-07-14 04:16:11'),
(3, 'Usuario', 'Acceso al catálogo musical y funciones de usuario.', '2026-07-14 04:16:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

DROP TABLE IF EXISTS `suscripciones`;
CREATE TABLE IF NOT EXISTS `suscripciones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `compra_id` int UNSIGNED DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_suscripciones_compra` (`compra_id`),
  KEY `idx_suscripciones_usuario` (`usuario_id`),
  KEY `idx_suscripciones_plan` (`plan_id`),
  KEY `idx_suscripciones_estado` (`estado`),
  KEY `idx_suscripciones_fechas` (`fecha_inicio`,`fecha_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suscripciones`
--

INSERT INTO `suscripciones` (`id`, `usuario_id`, `plan_id`, `compra_id`, `fecha_inicio`, `fecha_fin`, `estado`, `creado_en`) VALUES
(1, 3, 1, 1, '2026-07-13', '2026-08-12', 1, '2026-07-14 04:16:12'),
(2, 4, 2, 2, '2026-07-14', '2026-10-12', 1, '2026-07-14 15:11:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rol_id` int UNSIGNED NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacionalidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Panameña',
  `tipo_usuario` enum('gratuito','premium') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gratuito',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `idx_usuarios_rol` (`rol_id`),
  KEY `idx_usuarios_estado` (`estado`),
  KEY `idx_usuarios_tipo` (`tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `correo`, `password`, `nacionalidad`, `tipo_usuario`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Administrador Rokola', 'admin@rokola.test', '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu', 'Panameña', 'premium', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(2, 2, 'Operador Rokola', 'operador@rokola.test', '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu', 'Panameña', 'premium', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(3, 3, 'Usuario de prueba', 'usuario@rokola.test', '$2y$12$zFk09zbtCxBS39zOgAYbR.dr2ulWhw6Sz7Ke/TW.Fp1ZidorNciXu', 'Panameña', 'gratuito', 1, '2026-07-14 04:16:11', '2026-07-14 04:16:11'),
(4, 3, 'Joseph Guerra', 'josephG19@gmail.com', '$2y$10$crDMf9jpXA8JavDDDVffWexXhLA5FPRFM1MFIgut7.EW7s8bDUaIa', 'Panameña', 'premium', 1, '2026-07-14 14:50:27', '2026-07-14 15:11:05');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `albumes`
--
ALTER TABLE `albumes`
  ADD CONSTRAINT `fk_albumes_artista` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD CONSTRAINT `fk_artistas_genero` FOREIGN KEY (`genero_id`) REFERENCES `generos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD CONSTRAINT `fk_canciones_album` FOREIGN KEY (`album_id`) REFERENCES `albumes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_canciones_artista` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_plan` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compras_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `fk_entradas_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entradas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `fk_eventos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento_artistas`
--
ALTER TABLE `evento_artistas`
  ADD CONSTRAINT `fk_evento_artistas_artista` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evento_artistas_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_favoritos_cancion` FOREIGN KEY (`cancion_id`) REFERENCES `canciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoritos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `listas`
--
ALTER TABLE `listas`
  ADD CONSTRAINT `fk_listas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_canciones`
--
ALTER TABLE `lista_canciones`
  ADD CONSTRAINT `fk_lista_canciones_cancion` FOREIGN KEY (`cancion_id`) REFERENCES `canciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lista_canciones_lista` FOREIGN KEY (`lista_id`) REFERENCES `listas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  ADD CONSTRAINT `fk_reproducciones_cancion` FOREIGN KEY (`cancion_id`) REFERENCES `canciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reproducciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD CONSTRAINT `fk_suscripciones_compra` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suscripciones_plan` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suscripciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
