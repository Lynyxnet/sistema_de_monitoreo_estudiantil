-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 06, 2024 at 04:37 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoreo`
--

-- --------------------------------------------------------

--
-- Table structure for table `asistencias`
--

CREATE TABLE `asistencias` (
  `idAsistencia` int(11) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `idMateriaDia` int(11) DEFAULT NULL,
  `asistencia` enum('asistio','falto','justifico') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `asistencias`
--

INSERT INTO `asistencias` (`idAsistencia`, `idUsuario`, `idMateriaDia`, `asistencia`) VALUES
(1, 2, 1, 'falto'),
(2, 3, 1, 'falto'),
(3, 4, 1, 'falto'),
(4, 5, 1, 'asistio'),
(5, 6, 1, 'asistio'),
(6, 7, 1, 'asistio'),
(7, 8, 1, 'falto'),
(8, 9, 1, 'falto'),
(9, 10, 1, 'falto'),
(10, 11, 1, 'falto'),
(11, 2, 2, 'asistio'),
(12, 3, 2, 'falto'),
(13, 4, 2, 'falto'),
(14, 5, 2, 'falto'),
(15, 6, 2, 'falto'),
(16, 7, 2, 'falto'),
(17, 8, 2, 'falto'),
(18, 9, 2, 'falto'),
(19, 10, 2, 'falto'),
(20, 11, 2, 'falto');

-- --------------------------------------------------------

--
-- Table structure for table `materia`
--

CREATE TABLE `materia` (
  `idMateria` int(11) NOT NULL,
  `nombreMateria` varchar(255) DEFAULT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFinal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `materia`
--

INSERT INTO `materia` (`idMateria`, `nombreMateria`, `idUsuario`, `semestre`, `fechaInicio`, `fechaFinal`) VALUES
(1, 'Programacion Web', 1, 6, '2024-01-01', '2024-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `materia_alumno`
--

CREATE TABLE `materia_alumno` (
  `idMateriaAlumno` int(11) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `idMateria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `materia_alumno`
--

INSERT INTO `materia_alumno` (`idMateriaAlumno`, `idUsuario`, `idMateria`) VALUES
(1, 2, 1),
(2, 3, 1),
(3, 4, 1),
(4, 5, 1),
(5, 6, 1),
(6, 7, 1),
(7, 8, 1),
(8, 9, 1),
(9, 10, 1),
(10, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `materia_dia`
--

CREATE TABLE `materia_dia` (
  `idMateriaDia` int(11) NOT NULL,
  `idMateria` int(11) DEFAULT NULL,
  `diaSemana` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `materia_dia`
--

INSERT INTO `materia_dia` (`idMateriaDia`, `idMateria`, `diaSemana`, `fecha`) VALUES
(1, 1, 'Monday', '2024-01-01'),
(2, 1, 'Monday', '2024-01-08'),
(3, 1, 'Monday', '2024-01-15'),
(4, 1, 'Monday', '2024-01-22'),
(5, 1, 'Monday', '2024-01-29'),
(6, 1, 'Monday', '2024-02-05'),
(7, 1, 'Monday', '2024-02-12'),
(8, 1, 'Monday', '2024-02-19'),
(9, 1, 'Monday', '2024-02-26'),
(10, 1, 'Monday', '2024-03-04'),
(11, 1, 'Monday', '2024-03-11'),
(12, 1, 'Monday', '2024-03-18'),
(13, 1, 'Monday', '2024-03-25');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `idRole` int(11) NOT NULL,
  `nombreRole` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`idRole`, `nombreRole`) VALUES
(1, 'docente'),
(2, 'usuario');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `idRole` int(11) DEFAULT NULL,
  `matricula` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellidoPaterno` varchar(255) DEFAULT NULL,
  `apellidoMaterno` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `idRole`, `matricula`, `nombre`, `apellidoPaterno`, `apellidoMaterno`, `password`, `correo`) VALUES
(1, 1, '16012358', 'Fabian', 'Gonzalez', 'Torres', '12345678', 'fabian.gonzalez@zapopan.tecmm.edu.mx'),
(2, 2, '21011182', 'Armenta Sandoval Ivan Emmanuel', NULL, NULL, '12345678', 'za21011182@zapopan.tecmm.edu.mx'),
(3, 2, '17011650', 'Gonzalez Gomez Hugo Emmanuel', NULL, NULL, '12345678', 'za17011650@zapopan.tecmm.edu.mx'),
(4, 2, '17011777', 'Jocelyn Gutierrez Sandoval', NULL, NULL, '12345678', 'za17011777@zapopan.tecmm.edu.mx'),
(5, 2, '19011657', 'Andrea Lopez Hernandez', NULL, NULL, '12345678', 'za19011657@zapopan.tecmm.edu.mx'),
(6, 2, '20011657', 'Ana Vallejo Gomez Romero', NULL, NULL, '12345678', 'za20011657@zapopan.tecmm.edu.mx'),
(7, 2, '21215478', 'Berenice Sanchez Hernadez', NULL, NULL, '12345678', 'za21215478@zapopan.tecmm.edu.mx'),
(8, 2, '18114567', 'Laura Vazquez Garcia', NULL, NULL, '12345678', 'za18114567@zapopan.tecmm.edu.mx'),
(9, 2, '18589797', 'Stephanie Ruiz Mendoza', NULL, NULL, '12345678', 'za18589797@zapopan.tecmm.edu.mx'),
(10, 2, '12789222', 'Pilar Vargas Soria ', NULL, NULL, '12345678', 'za12789222@zapopan.tecmm.edu.mx'),
(11, 2, '11111125', 'Kalti Mariela Lomeli Sandoval', NULL, NULL, '12345678', 'za11111125@zapopan.tecmm.edu.mx');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`idAsistencia`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idMateriaDia` (`idMateriaDia`);

--
-- Indexes for table `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`idMateria`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indexes for table `materia_alumno`
--
ALTER TABLE `materia_alumno`
  ADD PRIMARY KEY (`idMateriaAlumno`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idMateria` (`idMateria`);

--
-- Indexes for table `materia_dia`
--
ALTER TABLE `materia_dia`
  ADD PRIMARY KEY (`idMateriaDia`),
  ADD KEY `idMateria` (`idMateria`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRole`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `idRole` (`idRole`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `materia`
--
ALTER TABLE `materia`
  MODIFY `idMateria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `materia_alumno`
--
ALTER TABLE `materia_alumno`
  MODIFY `idMateriaAlumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `materia_dia`
--
ALTER TABLE `materia_dia`
  MODIFY `idMateriaDia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`idMateriaDia`) REFERENCES `materia_dia` (`idMateriaDia`);

--
-- Constraints for table `materia`
--
ALTER TABLE `materia`
  ADD CONSTRAINT `materia_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Constraints for table `materia_alumno`
--
ALTER TABLE `materia_alumno`
  ADD CONSTRAINT `materia_alumno_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `materia_alumno_ibfk_2` FOREIGN KEY (`idMateria`) REFERENCES `materia` (`idMateria`);

--
-- Constraints for table `materia_dia`
--
ALTER TABLE `materia_dia`
  ADD CONSTRAINT `materia_dia_ibfk_1` FOREIGN KEY (`idMateria`) REFERENCES `materia` (`idMateria`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idRole`) REFERENCES `roles` (`idRole`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
