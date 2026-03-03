-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 23-02-2026 a las 16:56:36
-- Versión del servidor: 12.2.2-MariaDB
-- Versión de PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `examen`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes`
--

CREATE TABLE `examenes` (
  `id_examen` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `tiempo_total` int(11) DEFAULT NULL,
  `puntaje` decimal(5,2) DEFAULT NULL,
  `total_preguntas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `examenes`
--

INSERT INTO `examenes` (`id_examen`, `id_usuario`, `fecha_inicio`, `fecha_fin`, `puntaje`, `total_preguntas`) VALUES
(1, 2, '2026-02-21 22:33:29', '2026-02-21 22:40:49', 40.00, 10),
(2, 2, '2026-02-21 22:40:54', '2026-02-21 22:41:08', 30.00, 10),
(3, 2, '2026-02-21 22:42:22', '2026-02-21 22:43:58', 100.00, 10),
(4, 2, '2026-02-21 22:53:57', '2026-02-21 22:56:19', 40.00, 10),
(5, 2, '2026-02-21 23:02:17', '2026-02-21 23:03:52', 80.00, 10),
(6, 2, '2026-02-21 23:04:48', '2026-02-21 23:07:48', 80.00, 10),
(14, 2, '2026-02-22 13:56:23', '2026-02-22 14:10:10', 82.81, 128);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_pregunta` int(11) NOT NULL,
  `materia` varchar(100) DEFAULT NULL,
  `enunciado` text DEFAULT NULL,
  `opcion_a` text DEFAULT NULL,
  `opcion_b` text DEFAULT NULL,
  `opcion_c` text DEFAULT NULL,
  `opcion_d` text DEFAULT NULL,
  `respuesta_correcta` char(1) DEFAULT NULL,
  `dificultad` enum('facil','medio','dificil') DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--
-- Los data de preguntas se importarán desde los archivos CSV

-- --------------------------------------------------------

--
-- Volcado de datos para la tabla `respuestas_estudiantes`
--

INSERT INTO `respuestas_estudiantes` (`id_respuesta`, `id_examen`, `id_pregunta`, `respuesta_seleccionada`, `es_correcta`, `tiempo_respuesta`) VALUES
(1, 1, 32, 'D', 0, 0),
(2, 1, 219, 'A', 1, 0),
(3, 1, 117, 'C', 1, 0),
(4, 1, 346, 'B', 0, 0),
(5, 1, 440, 'A', 0, 0),
(6, 1, 43, 'A', 0, 0),
(7, 1, 524, 'A', 1, 0),
(8, 1, 443, 'A', 0, 0),
(9, 1, 190, 'A', 0, 0),
(10, 1, 232, 'A', 1, 0),
(11, 2, 145, 'A', 1, 0),
(12, 2, 254, 'A', 0, 0),
(13, 2, 278, 'A', 0, 0),
(14, 2, 465, 'A', 0, 0),
(15, 2, 382, 'A', 1, 0),
(16, 2, 359, 'A', 1, 0),
(17, 2, 36, 'A', 0, 0),
(18, 2, 46, 'A', 0, 0),
(19, 2, 281, 'A', 0, 0),
(20, 2, 5, 'A', 0, 0),
(21, 3, 210, 'B', 1, 0),
(22, 3, 23, 'A', 1, 0),
(23, 3, 81, 'A', 1, 0),
(24, 3, 203, 'A', 1, 0),
(25, 3, 397, 'A', 1, 0),
(26, 3, 153, 'A', 1, 0),
(27, 3, 249, 'A', 1, 0),
(28, 3, 531, 'A', 1, 0),
(29, 3, 266, 'A', 1, 0),
(30, 3, 376, 'A', 1, 0),
(31, 4, 389, 'C', 0, 0),
(32, 4, 491, 'D', 0, 0),
(33, 4, 535, 'A', 1, 0),
(34, 4, 474, 'D', 0, 0),
(35, 4, 244, 'B', 0, 0),
(36, 4, 51, 'B', 0, 0),
(37, 4, 422, 'A', 1, 0),
(38, 4, 424, 'B', 1, 0),
(39, 4, 75, 'C', 0, 0),
(40, 4, 129, 'A', 1, 0),
(41, 5, 90, 'B', 1, 0),
(42, 5, 169, 'A', 1, 0),
(43, 5, 523, 'A', 1, 0),
(44, 5, 191, 'A', 1, 0),
(45, 5, 137, 'A', 1, 0),
(46, 5, 339, 'A', 1, 0),
(47, 5, 67, 'C', 1, 0),
(48, 5, 498, 'D', 0, 0),
(49, 5, 321, 'D', 0, 0),
(50, 5, 387, 'A', 1, 0),
(51, 6, 249, 'A', 1, 0),
(52, 6, 535, 'A', 1, 0),
(53, 6, 171, 'A', 1, 0),
(54, 6, 91, 'A', 1, 0),
(55, 6, 475, 'A', 1, 0),
(56, 6, 117, 'B', 0, 0),
(57, 6, 75, 'B', 1, 0),
(58, 6, 371, 'D', 0, 0),
(59, 6, 97, 'B', 1, 0),
(60, 6, 260, 'A', 1, 0),
(61, 14, 272, 'A', 1, 0),
(62, 14, 236, 'A', 1, 0),
(63, 14, 149, 'A', 1, 0),
(64, 14, 445, 'B', 1, 0),
(65, 14, 93, 'A', 1, 0),
(66, 14, 356, 'A', 0, 0),
(67, 14, 203, 'A', 1, 0),
(68, 14, 496, 'B', 0, 0),
(69, 14, 152, 'A', 1, 0),
(70, 14, 174, 'A', 1, 0),
(71, 14, 198, 'D', 0, 0),
(72, 14, 334, 'A', 1, 0),
(73, 14, 530, 'A', 0, 0),
(74, 14, 518, 'A', 1, 0),
(75, 14, 157, 'A', 1, 0),
(76, 14, 195, 'A', 1, 0),
(77, 14, 448, 'B', 1, 0),
(78, 14, 65, 'A', 1, 0),
(79, 14, 329, 'B', 1, 0),
(80, 14, 189, 'C', 1, 0),
(81, 14, 85, 'A', 1, 0),
(82, 14, 513, 'A', 1, 0),
(83, 14, 522, 'A', 1, 0),
(84, 14, 99, 'B', 1, 0),
(85, 14, 459, 'C', 1, 0),
(86, 14, 506, 'B', 0, 0),
(87, 14, 224, 'A', 1, 0),
(88, 14, 225, 'A', 1, 0),
(89, 14, 423, 'A', 1, 0),
(90, 14, 122, 'A', 1, 0),
(91, 14, 183, 'C', 0, 0),
(92, 14, 238, 'B', 1, 0),
(93, 14, 97, 'B', 1, 0),
(94, 14, 316, 'D', 0, 0),
(95, 14, 456, 'C', 1, 0),
(96, 14, 490, 'A', 1, 0),
(97, 14, 477, 'B', 1, 0),
(98, 14, 273, 'A', 1, 0),
(99, 14, 15, 'B', 1, 0),
(100, 14, 454, 'C', 1, 0),
(101, 14, 427, 'B', 1, 0),
(102, 14, 126, 'A', 1, 0),
(103, 14, 414, 'D', 0, 0),
(104, 14, 393, 'C', 0, 0),
(105, 14, 119, 'B', 1, 0),
(106, 14, 387, 'A', 1, 0),
(107, 14, 75, 'B', 1, 0),
(108, 14, 248, 'A', 1, 0),
(109, 14, 253, 'A', 1, 0),
(110, 14, 349, 'C', 1, 0),
(111, 14, 267, 'A', 1, 0),
(112, 14, 87, 'B', 1, 0),
(113, 14, 436, 'C', 1, 0),
(114, 14, 391, 'A', 1, 0),
(115, 14, 123, 'B', 1, 0),
(116, 14, 234, 'A', 1, 0),
(117, 14, 179, 'D', 1, 0),
(118, 14, 12, 'A', 1, 0),
(119, 14, 129, 'A', 1, 0),
(120, 14, 193, 'A', 1, 0),
(121, 14, 232, 'A', 1, 0),
(122, 14, 388, 'B', 0, 0),
(123, 14, 226, 'B', 0, 0),
(124, 14, 173, 'C', 1, 0),
(125, 14, 532, 'D', 1, 0),
(126, 14, 505, 'A', 1, 0),
(127, 14, 40, 'A', 1, 0),
(128, 14, 369, 'A', 1, 0),
(129, 14, 202, 'A', 1, 0),
(130, 14, 326, 'A', 1, 0),
(131, 14, 340, 'A', 1, 0),
(132, 14, 331, 'A', 1, 0),
(133, 14, 432, 'C', 1, 0),
(134, 14, 160, 'A', 1, 0),
(135, 14, 261, 'A', 1, 0),
(136, 14, 343, 'C', 1, 0),
(137, 14, 43, 'B', 0, 0),
(138, 14, 480, 'B', 1, 0),
(139, 14, 285, 'B', 1, 0),
(140, 14, 354, 'B', 1, 0),
(141, 14, 419, 'A', 0, 0),
(142, 14, 20, 'A', 1, 0),
(143, 14, 240, 'A', 1, 0),
(144, 14, 52, 'B', 1, 0),
(145, 14, 514, 'C', 0, 0),
(146, 14, 127, 'A', 1, 0),
(147, 14, 482, 'B', 1, 0),
(148, 14, 503, 'A', 1, 0),
(149, 14, 491, 'A', 1, 0),
(150, 14, 196, 'A', 1, 0),
(151, 14, 517, 'A', 1, 0),
(152, 14, 187, 'A', 1, 0),
(153, 14, 494, 'D', 1, 0),
(154, 14, 76, 'A', 1, 0),
(155, 14, 346, 'A', 0, 0),
(156, 14, 452, 'B', 1, 0),
(157, 14, 16, 'B', 0, 0),
(158, 14, 406, 'C', 1, 0),
(159, 14, 147, 'A', 1, 0),
(160, 14, 28, 'A', 1, 0),
(161, 14, 186, 'A', 1, 0),
(162, 14, 111, 'A', 1, 0),
(163, 14, 206, 'A', 1, 0),
(164, 14, 327, 'A', 1, 0),
(165, 14, 218, 'B', 0, 0),
(166, 14, 24, 'A', 1, 0),
(167, 14, 315, 'A', 1, 0),
(168, 14, 420, 'D', 0, 0),
(169, 14, 84, 'A', 1, 0),
(170, 14, 120, 'B', 1, 0),
(171, 14, 219, 'A', 1, 0),
(172, 14, 390, 'A', 1, 0),
(173, 14, 534, 'A', 0, 0),
(174, 14, 13, 'A', 1, 0),
(175, 14, 81, 'A', 1, 0),
(176, 14, 102, 'D', 1, 0),
(177, 14, 92, 'D', 0, 0),
(178, 14, 475, 'C', 0, 0),
(179, 14, 468, 'B', 0, 0),
(180, 14, 282, 'B', 1, 0),
(181, 14, 205, 'A', 1, 0),
(182, 14, 207, 'A', 1, 0),
(183, 14, 434, 'C', 1, 0),
(184, 14, 259, 'A', 1, 0),
(185, 14, 365, 'A', 1, 0),
(186, 14, 531, 'A', 1, 0),
(187, 14, 463, 'C', 1, 0),
(188, 14, 61, 'A', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('estudiante','administrador') NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `contraseña`, `rol`, `fecha_registro`) VALUES
(1, 'Administrador', 'admin@tusistema.com', '$2y$10$Kt4EN943Zz4Ad7uln1QoquqQPZT3877W4uBMeQtpUNLmAZ6206QJa', 'administrador', '2026-02-21 22:26:00'),
(2, 'test', 'diana@test.com', '$2y$10$tQ.ISNo0lizyfTIQKiqf0.xRLb9p9Et3aFGDRsq1dqlYvVZQRc25q', 'estudiante', '2026-02-21 22:33:10');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD PRIMARY KEY (`id_examen`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`);

--
-- Indices de la tabla `respuestas_estudiantes`
--
ALTER TABLE `respuestas_estudiantes`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_examen` (`id_examen`),
  ADD KEY `id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `examenes`
--
ALTER TABLE `examenes`
  MODIFY `id_examen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=536;

--
-- AUTO_INCREMENT de la tabla `respuestas_estudiantes`
--
ALTER TABLE `respuestas_estudiantes`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `examenes`
--
ALTER TABLE `examenes`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `respuestas_estudiantes`
--
ALTER TABLE `respuestas_estudiantes`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_examen`) REFERENCES `examenes` (`id_examen`),
  ADD CONSTRAINT `2` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
