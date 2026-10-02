-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2026 a las 06:06:59
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
(2, 1, 'Taller de futbol', 'Para que la gente pueda aprender un deporte completamente nuevo', '2026-09-28', 2, '2026-09-25 15:49:09');

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
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `certificados`
--

INSERT INTO `certificados` (`id_certificado`, `id_vecino`, `tipo`, `motivo`, `estado`, `fecha_solicitud`) VALUES
(1, 3, 'residencia', 'Postulacion', 'aprobado', '2026-09-21 05:13:22'),
(2, 2, 'residencia', 'Postulacion', 'aprobado', '2026-09-29 21:23:00'),
(3, 2, 'socio_activo', 'Postulacion', 'aprobado', '2026-09-29 21:24:39'),
(4, 8, 'residencia', 'Hola', 'aprobado', '2026-09-29 21:29:17'),
(5, 8, 'socio_activo', 'Hola', 'aprobado', '2026-09-29 23:10:11');

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
(3, 2, 3, '2026-09-25 15:52:30');

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
(3, 'Junta De Vecinos Vista Hermosa', 'Santiago', 'Carrascal 6730', 'mrain@gmail.com');

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
(3, 'Certificado', 'Tu certificado ha llegado con exito', 'especifico', 3, '2026-09-23 05:03:21', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulaciones`
--

CREATE TABLE `postulaciones` (
  `id_postulacion` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `id_vecino` int(11) NOT NULL,
  `motivacion` varchar(400) DEFAULT NULL,
  `fecha_postulacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `postulaciones`
--

INSERT INTO `postulaciones` (`id_postulacion`, `id_proyecto`, `id_vecino`, `motivacion`, `fecha_postulacion`) VALUES
(1, 1, 3, 'Me parece una excelente idea!', '2026-09-28 17:41:40');

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
(1, 'Mejoramiento de la plaza', 'Proyecto para mejorar la plaza correspondiente para mayor espacio y movilidad de las personas', '2026-09-30', 'cerrado', '2026-09-28 17:40:20', 1);

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
(1, 3, 'cancha', '2026-09-30', '22:00 a 23:00', 'Partido de futbol con amigos', 'aprobado', '2026-09-24 01:59:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vecinos`
--

CREATE TABLE `vecinos` (
  `id_vecino` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `rut` varchar(12) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `junta` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('vecino','directorio') NOT NULL DEFAULT 'vecino',
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `es_socio_activo` tinyint(1) NOT NULL DEFAULT 0,
  `id_junta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vecinos`
--

INSERT INTO `vecinos` (`id_vecino`, `nombre`, `rut`, `direccion`, `junta`, `correo`, `clave`, `rol`, `estado`, `fecha_registro`, `es_socio_activo`, `id_junta`) VALUES
(1, 'Benjamin Rain', '21.691.775-8', 'Siberia 6730', 'Las Lumas', 'b.rain@duocuc.cl', '$2y$10$twnKWYPkHErQ.QZfwBkwNuJcgJh9KoK5y73ITmFT4tF15useQljyG', 'directorio', 'aprobado', '2026-09-21 04:12:45', 0, 1),
(2, 'Matias Rain', '21.691.775-K', 'Cerro Navia 6730', 'Las lumas', 'm.cheuquecoy@duocuc.cl', '$2y$10$AWHFoBvrJJK6/Uy92ct.x.huzhidFf5.y5WwwHoOtSGfOj4G8qlcW', 'vecino', 'aprobado', '2026-09-21 04:33:20', 1, 1),
(3, 'Christopher', '21.691.775-1', 'Siberia 6730', 'Las Lumas', 'Cristopher@gmail.com', '$2y$10$Axs3H8j.NaTfwjqmcCvZ0ex5L/UgyQNXy01T1k6mK3jDqhpMDK8Om', 'vecino', 'aprobado', '2026-09-21 04:40:34', 1, 1),
(6, 'Julian Hernadez', '1.111.111-1', 'Llifen 6814', '', 'Luis@gmail.com', '$2y$10$h/MWmNw0bR.Uj6ImCOd9sOsV3NrzTMBcomw0Yt/S3tjQqFtzhtekq', 'directorio', 'aprobado', '2026-09-23 06:10:14', 0, 2),
(7, 'Luis Hernan Rain', '22.222.222-k', 'Carrascal 6730', '', 'hernan@gmail.com', '$2y$10$TGTa4A/iMC471Ckbaf76neUD3Odv.b3pu2ulEQC4pQMvf.xdFVUsK', 'directorio', 'aprobado', '2026-09-23 06:27:19', 0, 3),
(8, 'Jocelyn Cheuquecoy', '33.333.333-K', 'Carrascal 6814', '', 'Jocelyn@gmail.com', '$2y$10$NaLTpngsyp6Uo17gCpUElOweNN6yFpuN8eIgot96RE6JrTmx8GcvW', 'vecino', 'aprobado', '2026-09-23 06:29:54', 0, 3),
(9, 'Geraldine Parra', '9.999.999-K', 'Siberia 6730', '', 'GeralParra@gmail.com', '$2y$10$gshwGqscxaixeZ95Gs1evu7s38gUcKBP.HqZTNWPxl.1FwNDm9EAK', 'vecino', 'aprobado', '2026-09-25 15:51:31', 0, 1);

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
  ADD UNIQUE KEY `rut` (`rut`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_junta` (`id_junta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inscripciones_actividades`
--
ALTER TABLE `inscripciones_actividades`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `juntas`
--
ALTER TABLE `juntas`
  MODIFY `id_junta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id_postulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reservas_espacios`
--
ALTER TABLE `reservas_espacios`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vecinos`
--
ALTER TABLE `vecinos`
  MODIFY `id_vecino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
