-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-12-2025 a las 14:09:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uo300798_db`
--
CREATE DATABASE IF NOT EXISTS `uo300798_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `uo300798_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id_dispositivo` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `dispositivos`
--

INSERT INTO `dispositivos` (`id_dispositivo`, `descripcion`) VALUES
(1, 'Ordenador'),
(2, 'Tableta'),
(3, 'Teléfono');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `generos`
--

CREATE TABLE `generos` (
  `id_genero` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `generos`
--

INSERT INTO `generos` (`id_genero`, `descripcion`) VALUES
(1, 'Hombre'),
(2, 'Mujer'),
(3, 'Otro/No binario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones`
--

CREATE TABLE `observaciones` (
  `id_observacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentarios` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `observaciones`
--

INSERT INTO `observaciones` (`id_observacion`, `id_usuario`, `comentarios`) VALUES
(1, 1, 'Completo la tarea rapidamente por esta familiarizado con la pagina. Fue necesario usar el PC para cargar los archivos PHP'),
(2, 2, 'En la pregunta relativa al año pasado, mostro dudas iniciales, dedujo que era del año pasado porque en ningun otro sitio habla de puntos.'),
(3, 3, 'No tuvo problemas para encontrar las respuestas. Manifesto que le gusto la pagina'),
(4, 4, 'No tuvo problemas para encontrar las respuestas. Comento aspectos relacionados con los colores.'),
(5, 5, 'Mostro confusion al no saber qué equipos era el de debut.'),
(6, 6, 'El usuario percibio falta de conetnido en la sección de clasificaciones'),
(7, 7, 'Tuvo que salir y volver a entrar varias veces del juego para entender bien como funcionaba, dijo que la ayuda no explicaba como jugar, solo nombraba el juego');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_respuesta` int(11) NOT NULL,
  `id_resultado` int(11) NOT NULL,
  `numero_pregunta` int(11) NOT NULL,
  `respuesta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_resultado`, `numero_pregunta`, `respuesta`) VALUES
(1, 1, 1, 'Sudáfrica'),
(2, 1, 2, '2011'),
(3, 1, 3, '1995'),
(4, 1, 4, 'RW Racing GP'),
(5, 1, 5, 'Barcelona'),
(6, 1, 6, '1702547'),
(7, 1, 7, '12'),
(8, 1, 8, 'La Unidad de Control Electrónico (ECU) es el cerebro de la moto'),
(9, 1, 9, '217'),
(10, 1, 10, 'Marc Márquez'),
(11, 2, 1, 'Sudáfrica'),
(12, 2, 2, '2011'),
(13, 2, 3, '1995'),
(14, 2, 4, 'RW Racing GP'),
(15, 2, 5, 'Barcelona'),
(16, 2, 6, '1702547'),
(17, 2, 7, '12'),
(18, 2, 8, 'La Unidad de Control Electrónico'),
(19, 2, 9, '217'),
(20, 2, 10, 'Marc Márquez'),
(21, 3, 1, 'Sudáfrica'),
(22, 3, 2, '2011'),
(23, 3, 3, '1995'),
(24, 3, 4, 'RW Racing GP'),
(25, 3, 5, 'Barcelona'),
(26, 3, 6, '1702547'),
(27, 3, 7, '12'),
(28, 3, 8, 'unidad de control electrónico'),
(29, 3, 9, '217'),
(30, 3, 10, 'Marc Márquez'),
(31, 4, 1, 'Sudáfrica'),
(32, 4, 2, '2011'),
(33, 4, 3, '1995'),
(34, 4, 4, 'RW Racing GP'),
(35, 4, 5, 'Barcelona'),
(36, 4, 6, '1702547'),
(37, 4, 7, '12'),
(38, 4, 8, 'Unidad de Control Electrónico'),
(39, 4, 9, '217'),
(40, 4, 10, 'Marc Márquez'),
(41, 5, 1, 'Sudáfrica'),
(42, 5, 2, '2011'),
(43, 5, 3, '1995'),
(44, 5, 4, 'RW Racing GP'),
(45, 5, 5, 'Barcelona'),
(46, 5, 6, '1702547'),
(47, 5, 7, '12'),
(48, 5, 8, 'La Unidad de Control Electrónico (ECU) es el cerebro de la moto'),
(49, 5, 9, '217'),
(50, 5, 10, 'Marc Márquez'),
(51, 6, 1, 'Sudáfrica'),
(52, 6, 2, '2011'),
(53, 6, 3, '1995'),
(54, 6, 4, 'RW Racing GP'),
(55, 6, 5, 'Barcelona'),
(56, 6, 6, '1702547'),
(57, 6, 7, '12'),
(58, 6, 8, 'Unidad de Control Electrónico'),
(59, 6, 9, '217'),
(60, 6, 10, 'Marc Márquez'),
(61, 7, 1, 'Sudáfrica'),
(62, 7, 2, '2011'),
(63, 7, 3, '1995'),
(64, 7, 4, 'RW Racing GP'),
(65, 7, 5, 'Barcelona'),
(66, 7, 6, '1702547'),
(67, 7, 7, '12'),
(68, 7, 8, 'La unidad de control electrónico'),
(69, 7, 9, '217'),
(70, 7, 10, 'Marc Márquez');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados`
--

CREATE TABLE `resultados` (
  `id_resultado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_dispositivo` int(11) NOT NULL,
  `tiempo` int(11) NOT NULL,
  `completado` tinyint(1) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `propuestas_mejora` text DEFAULT NULL,
  `valoracion` tinyint(4) NOT NULL CHECK (`valoracion` between 0 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `resultados`
--

INSERT INTO `resultados` (`id_resultado`, `id_usuario`, `id_dispositivo`, `tiempo`, `completado`, `comentarios`, `propuestas_mejora`, `valoracion`) VALUES
(1, 1, 2, 282, 1, 'OK, pero para responder las preguntas hacen falta lo de php', 'Hacer preguntas puramente del html', 8),
(2, 2, 1, 624, 1, 'Me gusto', 'La tabla de piloto no sabia que era del año pasado', 10),
(3, 3, 3, 649, 1, 'Esta bien', 'No tengo', 10),
(4, 4, 2, 661, 1, 'Me gusta la pagina pero no el color azul', 'Cambiar los colores', 10),
(5, 5, 1, 580, 1, 'Esta bien', 'Listar los equipos por orden de debut', 9),
(6, 6, 3, 603, 1, 'Alguna pagina vacia', 'Sería mejor si se reparte mas la información', 9),
(7, 7, 2, 734, 1, 'La ayuda no aclara mucho como jugar', 'Explicar mejor el juego de las parejas', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `profesion` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `id_genero` int(11) NOT NULL,
  `pericia_informatica` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `profesion`, `edad`, `id_genero`, `pericia_informatica`) VALUES
(1, 'Estudiante EII', 23, 1, 10),
(2, 'Estudian 3 ESO', 15, 1, 6),
(3, 'Arquitecto', 56, 1, 5),
(4, 'Depenienta', 55, 2, 3),
(5, 'Estudiante Derecho', 20, 2, 4),
(6, 'Estudiante Bombero', 21, 1, 5),
(7, 'Estudiante Enfermero', 19, 1, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id_dispositivo`);

--
-- Indices de la tabla `generos`
--
ALTER TABLE `generos`
  ADD PRIMARY KEY (`id_genero`);

--
-- Indices de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD PRIMARY KEY (`id_observacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_resultado` (`id_resultado`);

--
-- Indices de la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD PRIMARY KEY (`id_resultado`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_dispositivo` (`id_dispositivo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_genero` (`id_genero`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `id_dispositivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `generos`
--
ALTER TABLE `generos`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  MODIFY `id_observacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `resultados`
--
ALTER TABLE `resultados`
  MODIFY `id_resultado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD CONSTRAINT `observaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_resultado`) REFERENCES `resultados` (`id_resultado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD CONSTRAINT `resultados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultados_ibfk_2` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id_dispositivo`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_genero`) REFERENCES `generos` (`id_genero`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
