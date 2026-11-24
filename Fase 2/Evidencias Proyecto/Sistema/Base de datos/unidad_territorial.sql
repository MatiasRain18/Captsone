-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-10-2026 a las 23:22:13
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `unidad_territorial`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_junta` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `fecha` date NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `id_junta`, `nombre`, `descripcion`, `fecha`, `cupo_maximo`, `fecha_creacion`) VALUES
(1, 1, 'Talller de manualidades', 'Taller para aprender manualidades completamente gratis', '2026-09-29', 25, '2026-09-25 15:47:02'),
(2, 1, 'Taller de futbol', 'Para que la gente pueda aprender un deporte completamente nuevo', '2026-09-28', 2, '2026-09-25 15:49:09'),
(3, 3, 'Taller de manualidades', 'Taller de manualidades correspondiente', '2026-10-09', 1, '2026-10-07 03:38:24'),
(4, 3, 'Pruebas correspondiente', 'pruebas', '2026-10-10', 1, '2026-10-09 03:10:16'),
(5, 3, 'Taller de reciclaje', 'Taller de reclicaje', '2026-10-14', 1, '2026-10-13 18:08:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `id_certificado` int(11) NOT NULL,
  `id_vecino` int(11) NOT NULL,
  `tipo` enum('residencia','socio_activo') NOT NULL,
  `motivo` varchar(250) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `codigo_validacion` varchar(14) DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `certificados`
--

INSERT INTO `certificados` (`id_certificado`, `id_vecino`, `tipo`, `motivo`, `estado`, `codigo_validacion`, `fecha_respuesta`, `fecha_solicitud`) VALUES
(1, 3, 'residencia', 'Postulacion', 'aprobado', '6D11-1203-106F', '2026-09-21 05:13:22', '2026-09-21 05:13:22'),
(2, 2, 'residencia', 'Postulacion', 'aprobado', '6DF2-30BA-A24C', '2026-09-29 21:23:00', '2026-09-29 21:23:00'),
(3, 2, 'socio_activo', 'Postulacion', 'aprobado', '9F1A-B19E-4DA0', '2026-09-29 21:24:39', '2026-09-29 21:24:39'),
(4, 8, 'residencia', 'Hola', 'aprobado', '555A-9741-CAB5', '2026-09-29 21:29:17', '2026-09-29 21:29:17'),
(5, 8, 'socio_activo', 'Hola', 'aprobado', '1783-CD30-0F0E', '2026-09-29 23:10:11', '2026-09-29 23:10:11'),
(6, 10, 'residencia', 'Postulacion a subsidio', 'aprobado', '3C87-5471-7027', '2026-10-03 02:38:06', '2026-10-03 02:38:06'),
(7, 10, 'socio_activo', 'Hola', 'aprobado', 'B2B6-6A0E-854C', '2026-10-03 02:49:31', '2026-10-03 02:49:31'),
(8, 10, 'residencia', '03 de octubre', 'aprobado', '8FBD-BC2F-4B52', '2026-10-03 16:37:21', '2026-10-03 16:37:21'),
(9, 11, 'residencia', 'Postulacion', 'aprobado', 'EADB-F315-C2C7', '2026-10-03 16:55:30', '2026-10-03 16:55:30'),
(10, 10, 'socio_activo', 'Prueba 06 de Octubre', 'aprobado', '22CA-E4E3-123B', '2026-10-06 03:05:19', '2026-10-06 03:05:19'),
(11, 10, 'residencia', 'Prueba 06 de Octubre 02', 'aprobado', '716B-6F24-7C54', '2026-10-06 03:09:40', '2026-10-06 03:09:40'),
(12, 10, 'socio_activo', 'Prueba 06 de Octubre 02 probar socio activo', 'aprobado', '8E41-283C-EABF', '2026-10-06 03:10:48', '2026-10-06 03:10:48'),
(13, 9, 'residencia', 'Prueba 06 de octubre correo con otra junta', 'pendiente', NULL, NULL, '2026-10-06 03:31:30'),
(14, 9, 'socio_activo', 'Prueba 06 de Octubre', 'pendiente', NULL, NULL, '2026-10-06 03:34:41'),
(15, 8, 'residencia', 'Prueba 06 de Octubre 02', 'rechazado', NULL, '2026-10-06 03:35:22', '2026-10-06 03:35:22'),
(16, 12, 'residencia', 'Prueba 06 de octubre correo con otra junta', 'aprobado', 'EF17-B1A6-4CDC', '2026-10-06 03:42:45', '2026-10-06 03:42:45'),
(17, 12, 'socio_activo', 'Por tramites y por que actualice cosas', 'aprobado', '3372-0E45-42F7', '2026-10-07 03:32:34', '2026-10-07 03:32:34'),
(18, 13, 'residencia', 'Prueba 08 de Octubre', 'aprobado', '8E2C-62AE-411C', '2026-10-08 04:24:53', '2026-10-08 04:24:53'),
(19, 15, 'residencia', 'tramite municipal', 'aprobado', 'EBD3-203B-D9CA', '2026-10-08 13:39:50', '2026-10-08 13:39:50'),
(20, 13, 'socio_activo', 'Tramite y actualizacion', 'aprobado', 'D146-35A0-419A', '2026-10-09 02:36:02', '2026-10-09 02:35:42'),
(21, 13, 'residencia', 'Pruebas correspondiente', 'rechazado', NULL, '2026-10-09 02:55:13', '2026-10-09 02:55:03'),
(22, 13, 'socio_activo', '', 'aprobado', '2531-A990-962F', '2026-10-13 17:52:40', '2026-10-09 02:56:16'),
(23, 21, 'socio_activo', 'Revision correspondiente de funcionamiento', 'rechazado', NULL, '2026-10-13 17:52:48', '2026-10-13 17:49:53'),
(24, 21, 'residencia', 'Revision correspondiente de funcionamiento', 'aprobado', '01EA-38B1-5CFF', '2026-10-13 17:54:04', '2026-10-13 17:53:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_actividades`
--

CREATE TABLE `inscripciones_actividades` (
  `id_inscripcion` int(11) NOT NULL,
  `id_actividad` int(11) NOT NULL,
  `id_vecino` int(11) NOT NULL,
  `fecha_inscripcion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones_actividades`
--

INSERT INTO `inscripciones_actividades` (`id_inscripcion`, `id_actividad`, `id_vecino`, `fecha_inscripcion`) VALUES
(1, 1, 3, '2026-09-25 15:48:00'),
(2, 2, 9, '2026-09-25 15:52:14'),
(3, 2, 3, '2026-09-25 15:52:30'),
(4, 3, 12, '2026-10-07 03:40:27'),
(5, 4, 13, '2026-10-09 03:10:37'),
(6, 5, 21, '2026-10-13 18:08:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juntas`
--

CREATE TABLE `juntas` (
  `id_junta` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `comuna` varchar(100) NOT NULL,
  `direccion_sede` varchar(200) DEFAULT NULL,
  `correo_contacto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `juntas`
--

INSERT INTO `juntas` (`id_junta`, `nombre`, `comuna`, `direccion_sede`, `correo_contacto`) VALUES
(1, 'Junta de Vecinos Los Aromos', 'Santiago', 'Sede vecinal', 'contacto@unidadterritorial.cl'),
(2, 'Junta de Vecinos El Bosque', 'Santiago', 'Sede vecinal El Bosque', 'contacto2@unidadterritorial.cl'),
(3, 'Junta De Vecinos Vista Hermosa', 'Santiago', 'Carrascal 6730', 'mrain@gmail.com'),
(4, 'Junta de Vecinos Parque Forestal', 'Santiago', 'Calle Floral', 'CalleFloral@gmail.com'),
(5, 'Junta de Vecinos las Palmas', 'Talca', 'Riu Douro 1212', 'Prueba5@gmail.com'),
(6, 'Junta de Vecinos Cerro Alegre', 'Valparaíso', 'Calle Templeman 450', 'cerroalegre@ejemplo.cl');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `contenido` varchar(500) NOT NULL,
  `tipo` enum('general','grupo','especifico') NOT NULL DEFAULT 'general',
  `id_vecino_destino` int(11) DEFAULT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `id_junta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `titulo`, `contenido`, `tipo`, `id_vecino_destino`, `fecha_publicacion`, `id_junta`) VALUES
(2, 'Quinta Normal corte de luz', 'Corte de luz programado para Quinta Normal', 'grupo', NULL, '2026-09-23 04:53:26', 1),
(3, 'Certificado', 'Tu certificado ha llegado con exito', 'especifico', 3, '2026-09-23 05:03:21', 1),
(9, 'Corte de luz', 'Van a cortar la luz', 'general', NULL, '2026-10-08 13:52:55', 1),
(14, 'Corte de agua programada para la comuna', 'Corte de agua', 'general', NULL, '2026-10-13 17:56:09', 3),
(16, 'Certificado', 'Llego certificado', 'especifico', 21, '2026-10-13 17:57:07', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulaciones`
--

CREATE TABLE `postulaciones` (
  `id_postulacion` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `id_vecino` int(11) NOT NULL,
  `motivacion` varchar(400) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_postulacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `postulaciones`
--

INSERT INTO `postulaciones` (`id_postulacion`, `id_proyecto`, `id_vecino`, `motivacion`, `estado`, `fecha_respuesta`, `fecha_postulacion`) VALUES
(1, 1, 3, 'Me parece una excelente idea!', 'pendiente', NULL, '2026-09-28 17:41:40'),
(2, 2, 8, 'Me parece una excelente idea!', 'aprobado', '2026-10-11 20:06:18', '2026-10-07 03:43:03'),
(3, 2, 13, 'Prueba', 'aprobado', '2026-10-11 20:06:08', '2026-10-08 04:30:57'),
(4, 4, 13, 'Me interesa renovar la sede!', 'rechazado', '2026-10-11 20:06:14', '2026-10-09 03:00:45'),
(5, 4, 21, 'Me interesa ayudar a mejorar las sede de la junta de vecinos', 'aprobado', '2026-10-13 17:45:47', '2026-10-13 17:45:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(400) NOT NULL,
  `fecha_limite` date NOT NULL,
  `estado` enum('abierto','cerrado') NOT NULL DEFAULT 'abierto',
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `id_junta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id_proyecto`, `nombre`, `descripcion`, `fecha_limite`, `estado`, `fecha_publicacion`, `id_junta`) VALUES
(1, 'Mejoramiento de la plaza', 'Proyecto para mejorar la plaza correspondiente para mayor espacio y movilidad de las personas', '2026-09-30', 'cerrado', '2026-09-28 17:40:20', 1),
(2, 'Mejoramiento de la plaza central', 'Mejorar la plaza central para los niños', '2026-10-08', 'cerrado', '2026-10-07 03:35:30', 3),
(3, 'Prueba en octubre jueves 08', 'Es una prueba', '2026-10-13', 'abierto', '2026-10-08 04:40:30', 3),
(4, 'Pintura de la sede', 'Pintar la sede para renovacion', '2026-10-16', 'abierto', '2026-10-09 02:59:51', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_espacios`
--

CREATE TABLE `reservas_espacios` (
  `id_reserva` int(11) NOT NULL,
  `id_vecino` int(11) NOT NULL,
  `espacio` enum('cancha','sala','plaza') NOT NULL,
  `fecha` date NOT NULL,
  `horario` varchar(50) NOT NULL,
  `actividad` varchar(200) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservas_espacios`
--

INSERT INTO `reservas_espacios` (`id_reserva`, `id_vecino`, `espacio`, `fecha`, `horario`, `actividad`, `estado`, `fecha_solicitud`) VALUES
(1, 3, 'cancha', '2026-09-30', '22:00 a 23:00', 'Partido de futbol con amigos', 'aprobado', '2026-09-24 01:59:21'),
(2, 12, 'plaza', '2026-10-20', '22:00 a 23:00', 'Partido de futbol con amigos', 'aprobado', '2026-10-07 03:40:53'),
(3, 13, 'sala', '2026-10-30', '12 PM', 'Salas de reuniones para hacer reuniones', 'aprobado', '2026-10-08 04:26:23'),
(4, 13, 'sala', '2026-10-15', '15:00 a 17:00', 'Salas de reuniones para hacer reuniones', 'aprobado', '2026-10-09 03:06:47'),
(5, 13, 'sala', '2026-10-15', '18:00 a 20:00', 'Cumpleaños', 'aprobado', '2026-10-09 03:07:58'),
(6, 13, 'sala', '2026-10-15', '16:00 a 17:00', 'Cumpleaños', 'rechazado', '2026-10-09 03:08:17'),
(7, 21, 'cancha', '2026-10-20', '10:00 a 12:00', 'Partido de futbol de los amigos de mi hijo', 'aprobado', '2026-10-13 18:04:46'),
(8, 21, 'cancha', '2026-10-20', '11:00 a 13:00', 'Partido de futbol de los amigos de mi hijo', 'rechazado', '2026-10-13 18:06:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vecinos`
--

CREATE TABLE `vecinos` (
  `id_vecino` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `rut` int(10) UNSIGNED NOT NULL,
  `dv` char(1) NOT NULL DEFAULT '0',
  `direccion` varchar(200) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('vecino','directorio') NOT NULL DEFAULT 'vecino',
  `cargo` enum('presidente','secretario','tesorero') DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','baja') NOT NULL DEFAULT 'pendiente',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `es_socio_activo` tinyint(1) NOT NULL DEFAULT 0,
  `id_junta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vecinos`
--

INSERT INTO `vecinos` (`id_vecino`, `nombre`, `rut`, `dv`, `direccion`, `correo`, `clave`, `rol`, `cargo`, `estado`, `fecha_registro`, `es_socio_activo`, `id_junta`) VALUES
(1, 'Benjamin Rain', 21691775, '8', 'Siberia 6730', 'b.rain@duocuc.cl', '$2y$10$twnKWYPkHErQ.QZfwBkwNuJcgJh9KoK5y73ITmFT4tF15useQljyG', 'directorio', 'presidente', 'aprobado', '2026-09-21 04:12:45', 0, 1),
(2, 'Matias Rain', 21691775, 'K', 'Cerro Navia 6730', 'm.cheuquecoy@duocuc.cl', '$2y$10$AWHFoBvrJJK6/Uy92ct.x.huzhidFf5.y5WwwHoOtSGfOj4G8qlcW', 'vecino', NULL, 'aprobado', '2026-09-21 04:33:20', 1, 1),
(3, 'Christopher', 21691775, '1', 'Siberia 6730', 'Cristopher@gmail.com', '$2y$10$Axs3H8j.NaTfwjqmcCvZ0ex5L/UgyQNXy01T1k6mK3jDqhpMDK8Om', 'vecino', NULL, 'aprobado', '2026-09-21 04:40:34', 1, 1),
(6, 'Julian Hernadez', 1111111, '1', 'Llifen 6814', 'Luis@gmail.com', '$2y$10$h/MWmNw0bR.Uj6ImCOd9sOsV3NrzTMBcomw0Yt/S3tjQqFtzhtekq', 'directorio', 'presidente', 'aprobado', '2026-09-23 06:10:14', 0, 2),
(7, 'Luis Hernan Rain', 22222222, 'K', 'Carrascal 6730', 'hernan@gmail.com', '$2y$10$TGTa4A/iMC471Ckbaf76neUD3Odv.b3pu2ulEQC4pQMvf.xdFVUsK', 'directorio', 'presidente', 'aprobado', '2026-09-23 06:27:19', 0, 3),
(8, 'Jocelyn Cheuquecoy', 33333333, 'K', 'Carrascal 6814', 'Jocelyn@gmail.com', '$2y$10$NaLTpngsyp6Uo17gCpUElOweNN6yFpuN8eIgot96RE6JrTmx8GcvW', 'directorio', 'secretario', 'aprobado', '2026-09-23 06:29:54', 1, 3),
(9, 'Geraldine Parra', 9999999, 'K', 'Siberia 6730', 'GeralParra@gmail.com', '$2y$10$gshwGqscxaixeZ95Gs1evu7s38gUcKBP.HqZTNWPxl.1FwNDm9EAK', 'vecino', NULL, 'aprobado', '2026-09-25 15:51:31', 0, 1),
(10, 'Matias Enrique Rain Cheuquecoy', 21691775, '7', 'Siberia 6730', 'mativsenrvquervin@gmail.com', '$2y$10$t1RS.8g3C96h5DEKvXCJiOD8Tv/i.m.sHgOgUnZQW62Za8erG/3FO', 'vecino', NULL, 'aprobado', '2026-10-03 02:37:00', 0, 1),
(11, 'Daniel Cruz', 55555555, 'K', 'Llifen 6814', 'stiveninsanin@gmail.com', '$2y$10$/u0nMI1SPHkzaq6.0xg9F.K8jmj9KfbN1K1rp/iUbagPeNSYfhhhm', 'vecino', NULL, 'aprobado', '2026-10-03 16:54:44', 0, 1),
(12, 'Lucas Rain', 99999999, 'K', 'Siberia 6730', 'matiasrvin@gmail.com', '$2y$10$4kOBpPL8O5nT0QQx17nsAOvTSU9ppWdFFRIDpCsVH5ttfHgWrhf6q', 'directorio', 'tesorero', 'aprobado', '2026-10-06 03:30:45', 0, 3),
(13, 'Hernan Crespo', 12121212, 'K', 'Llifen 6813', 'matiasenriqueraincheuquecoy0@gmail.com', '$2y$10$gb9/IpfSw5zkpKArLE4BH.2FU/jUvSBBuAnS/m9qcwXI5oDs0z0la', 'vecino', NULL, 'aprobado', '2026-10-08 04:22:59', 1, 3),
(14, 'Juan Sebastian', 88888888, '8', 'Calle Floral', 'Juan@gmail.com', '$2y$10$34joue/uYlTnJVjyHim..eS6OcbL9dBoYz9a8d39qbmUUplXLiJEq', 'directorio', 'presidente', 'aprobado', '2026-10-08 04:38:38', 0, 4),
(15, 'Carlos Herrera', 11111111, '1', 'Estacion Central', 'mativsrvin@gmail.com', '$2y$10$aBZ7w3IFHM1rAO4OXyUVNeiquiI.FLNyU/zKjqBClp3e4iCHZNsGK', 'vecino', NULL, 'aprobado', '2026-10-08 13:29:46', 0, 1),
(16, 'Prueba Vecino', 20000000, '5', 'Matucana 6710', 'matyvscheuquecoy@gmail.com', '$2y$10$8pe1i5QlZMTunh374Pfu9u47o416qDgwCNlLLwKkpfgZLUoAJ7Zxq', 'vecino', NULL, 'aprobado', '2026-10-09 02:18:47', 0, 3),
(17, 'Prueba 02 rechazar', 2100000, '0', 'Carrascal 6814', 'Prueba02@gmail.com', '$2y$10$FWSC8V6kd/tCCknIjH8CEuJQ4G0Ow2QAWInV.mwM3DnCEt1VzC/2.', 'vecino', NULL, 'rechazado', '2026-10-09 02:48:26', 0, 3),
(18, 'Probamos Presidente', 11111112, 'K', 'Riu Douro 1212', 'Prueba06@gmail.com', '$2y$10$zzhJFzo7qrxPoMriWH9XxuKj7mqjVkZ0ViiUcMc2bLkmjXBUasuyy', 'directorio', 'presidente', 'aprobado', '2026-10-09 03:18:34', 0, 5),
(19, 'Probamos secretario', 15000000, '9', 'Riu Douro 1212', 'Prueba03@gmail.com', '$2y$10$CQkg1iLfDXef.5YN8F5SJe9UhYrUGTJQyMwsTkY5gnKuRUol7Fu2u', 'directorio', 'secretario', 'aprobado', '2026-10-09 03:18:34', 0, 5),
(20, 'Probamos tesorero', 13000000, '2', 'Riu Douro 1212', 'Prueba04@gmail.com', '$2y$10$fHo/QgawIhgi4/SjPxERveZRNRBgF4Loa4fyAyzKGcDtO/92QyOHa', 'directorio', 'tesorero', 'aprobado', '2026-10-09 03:18:34', 0, 5),
(21, 'Jocelyn Rain Flores', 21000000, '3', 'Carrascal 6814', 'pruebacorreojuntavecinos22@gmail.com', '$2y$10$SQwH0.rSIbSsBw.IbBrG8.FT8B.S.Hsti5eyRRCqMoY9Fji8Tj962', 'vecino', NULL, 'aprobado', '2026-10-13 17:39:22', 1, 3),
(22, 'Rosa Martínez Soto', 16000000, '7', 'Calle Templeman 450', 'rosa.martinez@ejemplo.cl', '$2y$10$V96/x4YnwidrZFuJJr7BMONoyYTs7l4G5mK4RwRExjIhymFzkHupS', 'directorio', 'presidente', 'aprobado', '2026-10-13 18:14:44', 0, 6),
(23, 'Carlos Fuentes Díaz', 17000000, '5', 'Calle Templeman 450', 'carlos.fuentes@ejemplo.cl', '$2y$10$RW4Dkgq.ifSStDjA7mSNhudOWUzZ90Pmvu3Yh4g4mYGOOItTWFaI6', 'directorio', 'secretario', 'aprobado', '2026-10-13 18:14:45', 0, 6),
(24, 'Ana Torres Muñoz', 18000000, '3', 'Calle Templeman 450', 'ana.torres@ejemplo.cl', '$2y$10$5ZNsmU3brH8I3H5QbEDyz.ghXj5N3QFWrJ5z/S8//JIqw0X6aVNS.', 'directorio', 'tesorero', 'aprobado', '2026-10-13 18:14:45', 0, 6);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_junta` (`id_junta`);

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id_certificado`),
  ADD UNIQUE KEY `codigo_validacion` (`codigo_validacion`),
  ADD KEY `id_vecino` (`id_vecino`);

--
-- Indices de la tabla `inscripciones_actividades`
--
ALTER TABLE `inscripciones_actividades`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `unica_inscripcion` (`id_actividad`,`id_vecino`),
  ADD KEY `id_vecino` (`id_vecino`);

--
-- Indices de la tabla `juntas`
--
ALTER TABLE `juntas`
  ADD PRIMARY KEY (`id_junta`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`),
  ADD KEY `id_vecino_destino` (`id_vecino_destino`),
  ADD KEY `id_junta` (`id_junta`);

--
-- Indices de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD PRIMARY KEY (`id_postulacion`),
  ADD KEY `id_proyecto` (`id_proyecto`),
  ADD KEY `id_vecino` (`id_vecino`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD KEY `id_junta` (`id_junta`);

--
-- Indices de la tabla `reservas_espacios`
--
ALTER TABLE `reservas_espacios`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_vecino` (`id_vecino`);

--
-- Indices de la tabla `vecinos`
--
ALTER TABLE `vecinos`
  ADD PRIMARY KEY (`id_vecino`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `rut_dv` (`rut`,`dv`),
  ADD UNIQUE KEY `cargo_por_junta` (`id_junta`,`cargo`),
  ADD KEY `id_junta` (`id_junta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `inscripciones_actividades`
--
ALTER TABLE `inscripciones_actividades`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `juntas`
--
ALTER TABLE `juntas`
  MODIFY `id_junta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id_postulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reservas_espacios`
--
ALTER TABLE `reservas_espacios`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `vecinos`
--
ALTER TABLE `vecinos`
  MODIFY `id_vecino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_junta`) REFERENCES `juntas` (`id_junta`);

--
-- Filtros para la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD CONSTRAINT `certificados_ibfk_1` FOREIGN KEY (`id_vecino`) REFERENCES `vecinos` (`id_vecino`);

--
-- Filtros para la tabla `inscripciones_actividades`
--
ALTER TABLE `inscripciones_actividades`
  ADD CONSTRAINT `inscripciones_actividades_ibfk_1` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`),
  ADD CONSTRAINT `inscripciones_actividades_ibfk_2` FOREIGN KEY (`id_vecino`) REFERENCES `vecinos` (`id_vecino`);

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`id_vecino_destino`) REFERENCES `vecinos` (`id_vecino`),
  ADD CONSTRAINT `noticias_ibfk_2` FOREIGN KEY (`id_junta`) REFERENCES `juntas` (`id_junta`);

--
-- Filtros para la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD CONSTRAINT `postulaciones_ibfk_1` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`),
  ADD CONSTRAINT `postulaciones_ibfk_2` FOREIGN KEY (`id_vecino`) REFERENCES `vecinos` (`id_vecino`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`id_junta`) REFERENCES `juntas` (`id_junta`);

--
-- Filtros para la tabla `reservas_espacios`
--
ALTER TABLE `reservas_espacios`
  ADD CONSTRAINT `reservas_espacios_ibfk_1` FOREIGN KEY (`id_vecino`) REFERENCES `vecinos` (`id_vecino`);

--
-- Filtros para la tabla `vecinos`
--
ALTER TABLE `vecinos`
  ADD CONSTRAINT `vecinos_ibfk_1` FOREIGN KEY (`id_junta`) REFERENCES `juntas` (`id_junta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
