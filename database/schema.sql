-- Esquema de base de datos para el sistema de control de horas de
-- Servicio Social / Practicas Profesionales.
--
-- Uso:
--   mysql -u root -p -e "CREATE DATABASE serviciosocial CHARACTER SET utf8mb4"
--   mysql -u root -p serviciosocial < database/schema.sql

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Cuentas de administrador del panel
-- --------------------------------------------------------

CREATE TABLE `cuentas_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  -- Hash generado con password_hash() (bcrypt), nunca texto plano.
  `contraseña` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cuenta demo: usuario "admin", contraseña "Admin123!"
-- Cambiar esta contraseña inmediatamente despues de instalar el sistema.
INSERT INTO `cuentas_admin` (`nombre`, `usuario`, `contraseña`) VALUES
('Administrador Demo', 'admin', '$2y$10$kk2Nhe2S2OMSQkEjpzw3I.nfk.m1d79fEqIEli3M.dzmo5wjB5jQK');

-- --------------------------------------------------------
-- Registro de estudiantes (alta unica)
-- --------------------------------------------------------

CREATE TABLE `registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Entradas y salidas registradas en el kiosco
-- --------------------------------------------------------

CREATE TABLE `registro_entrada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registro_id` int(11) NOT NULL,
  `hora_entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `registro_id` (`registro_id`),
  CONSTRAINT `registro_entrada_ibfk_1` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `registro_salida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registro_id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `hora_salida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `registro_id` (`registro_id`),
  KEY `entrada_id` (`entrada_id`),
  CONSTRAINT `registro_salida_ibfk_1` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`),
  CONSTRAINT `registro_salida_ibfk_2` FOREIGN KEY (`entrada_id`) REFERENCES `registro_entrada` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Rate limiting del login de administrador (ver src/security.php)
-- --------------------------------------------------------

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ip_attempted_idx` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
