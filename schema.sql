CREATE DATABASE IF NOT EXISTS `farmaplus` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `farmaplus`;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `rol` ENUM('admin', 'farmaceutico') NOT NULL DEFAULT 'farmaceutico',
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Obras Sociales / Seguros Médicos
CREATE TABLE IF NOT EXISTS `seguros` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(20) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `cobertura_porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `telefono` VARCHAR(30) NULL,
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Inventario / Medicamentos
CREATE TABLE IF NOT EXISTS `medicamentos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo_barras` VARCHAR(50) NOT NULL UNIQUE,
  `nombre` VARCHAR(120) NOT NULL,
  `laboratorio` VARCHAR(100) NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `requiere_receta` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Dispensaciones (Ventas / Entrega de Medicamentos)
CREATE TABLE IF NOT EXISTS `dispensaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `paciente_nombre` VARCHAR(100) NOT NULL,
  `seguro_id` INT NULL,
  `nro_afiliado` VARCHAR(50) NULL,
  `monto_subtotal` DECIMAL(10,2) NOT NULL,
  `monto_cobertura` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `monto_total` DECIMAL(10,2) NOT NULL,
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`),
  FOREIGN KEY (`seguro_id`) REFERENCES `seguros`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Detalle de Dispensaciones
CREATE TABLE IF NOT EXISTS `dispensacion_detalles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dispensacion_id` INT NOT NULL,
  `medicamento_id` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`dispensacion_id`) REFERENCES `dispensaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos iniciales
-- Contraseña para ambos usuarios: 123456
INSERT INTO `usuarios` (`username`, `password`, `nombre`, `rol`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF0E85K7Y1V6fPZ.5aA9K0g9dE9H5ZKi', 'Administrador General', 'admin'),
('farmaceutico', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF0E85K7Y1V6fPZ.5aA9K0g9dE9H5ZKi', 'Carlos López', 'farmaceutico')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `seguros` (`codigo`, `nombre`, `cobertura_porcentaje`, `telefono`, `estado`) VALUES
('SEG-OSDE', 'OSDE Salud', 40.00, '0800-555-6733', 'activo'),
('SEG-SWISS', 'Swiss Medical', 50.00, '0810-333-8888', 'activo'),
('SEG-MEDIFE', 'Medifé Seguro', 35.00, '0800-444-6334', 'activo'),
('SEG-PART', 'Particular (Sin Seguro)', 0.00, '-', 'activo')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `medicamentos` (`codigo_barras`, `nombre`, `laboratorio`, `precio`, `stock`, `requiere_receta`) VALUES
('7790001001', 'Paracetamol 500mg (x20 comp)', 'Bayer', 1500.00, 50, 0),
('7790001002', 'Ibuprofeno 600mg (x10 cáps)', 'Roemmers', 2200.00, 30, 0),
('7790001003', 'Amoxicilina 500mg (x16 comp)', 'Bagó', 4500.00, 15, 1),
('7790001004', 'Loratadina 10mg (x10 comp)', 'Elea', 1800.00, 25, 0),
('7790001005', 'Omeprazol 20mg (x28 cáps)', 'Baliarda', 3800.00, 40, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;
