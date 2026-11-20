-- =====================================================
--  Base de datos: tienda_la_firma
--  Proyecto: Tienda Escolar "La Firma"  (XAMPP / MariaDB)
--
--  Conserva TUS tablas: clientes, productos y ventas (con sus datos)
--  y les agrega lo que la página necesita: stock, imágenes,
--  ingredientes de los almuerzos, usuarios con contraseña y pedidos.
--
--  Cómo usar: phpMyAdmin > Importar > elegir este archivo.
--  OJO: si ya existe tienda_la_firma, sus tablas se recrean.
-- =====================================================

SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS tienda_la_firma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_la_firma;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ventas, pedidos, plato_ingredientes, usuarios, productos, clientes;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- clientes (igual a tu tabla original)
-- -----------------------------------------------------
CREATE TABLE clientes (
  id_clientes int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(100) DEFAULT NULL,
  grado int(10) DEFAULT NULL,
  telefono int(10) DEFAULT NULL,
  PRIMARY KEY (id_clientes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO clientes (id_clientes, nombre, grado, telefono) VALUES
(2, 'Juan Perez', 10, 300123468),
(3, 'Mariana López', 9, 393870172),
(4, 'carlos gomez ', 10, 302233983),
(5, 'laura torres', 8, 309238912),
(6, 'Andres ruiz', 11, 30954892),
(7, 'sofia alvarez', 11, 304901229),
(8, 'daniel quintero', 10, 304982192),
(9, 'valentina rojas ', 9, 309489347),
(10, 'mateo lopez', 8, 30498291),
(11, 'camila sanchez', 8, 304928390),
(12, 'felipe morales', 9, 340923093),
(13, 'sara ortiz', 9, 309482901),
(14, 'nicolas herrera ', 10, 309487992),
(15, 'isabela reyes ', 11, 309402390),
(16, 'santiago molina ', 11, 305923981);

-- -----------------------------------------------------
-- productos (tu tabla + Stock, Imagen, Seccion, Dia_Semana, Orden, Activo)
--   Seccion 'cafeteria' = página Cafeteria
--   Seccion 'almuerzo'  = página Menu del día (con Dia_Semana)
--   Imagen puede ser un archivo de img/productos/ o una URL completa
-- -----------------------------------------------------
CREATE TABLE productos (
  Id_Producto int(11) NOT NULL AUTO_INCREMENT,
  Nombre varchar(100) NOT NULL,
  Categoria varchar(100) NOT NULL,
  Precio decimal(10,0) NOT NULL,
  Stock int(10) UNSIGNED NOT NULL DEFAULT 0,
  Imagen varchar(255) DEFAULT NULL,
  Seccion enum('cafeteria','almuerzo') NOT NULL DEFAULT 'cafeteria',
  Dia_Semana enum('Lunes','Martes','Miércoles','Jueves','Viernes') DEFAULT NULL,
  Orden int(11) NOT NULL DEFAULT 0,
  Activo tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (Id_Producto),
  KEY idx_productos_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO productos (Id_Producto, Nombre, Categoria, Precio, Stock, Imagen, Seccion, Dia_Semana, Orden, Activo) VALUES
(1, 'Arepa burguer', 'Comida', 3000, 50, 'arepa-burguer.webp', 'cafeteria', NULL, 1, 1),
(2, 'Pizza', 'Comida', 3000, 9, 'pizza.png', 'cafeteria', NULL, 2, 1),
(3, 'Perro caliente', 'Comida', 4000, 11, 'perro.webp', 'cafeteria', NULL, 3, 1),
(4, 'Panceroti', 'Comida', 3000, 6, 'panceroti.jpg', 'cafeteria', NULL, 4, 1),
(5, 'Papas paquete limón', 'Comida', 3500, 20, 'papas-limon.jpg', 'cafeteria', NULL, 5, 1),
(6, 'Papas paquete BBQ', 'Comida', 3500, 7, 'papas-bbq.jpg', 'cafeteria', NULL, 6, 1),
(7, 'Pastel de pollo', 'Comida', 3000, 18, 'pastel-de-pollo.jpeg', 'cafeteria', NULL, 7, 1),
(8, 'Galletas', 'Dulces', 2500, 0, NULL, 'cafeteria', NULL, 99, 0),
(9, 'Empanada', 'Comida', 2500, 13, 'empanada.jpg', 'cafeteria', NULL, 8, 1),
(10, 'Cifrut', 'Bebida', 2000, 25, 'cifrut.png', 'cafeteria', NULL, 13, 1),
(11, 'Agua saborizada', 'Bebida', 3000, 17, 'agua-saborizada.jpg', 'cafeteria', NULL, 19, 1),
(12, 'Palito de queso', 'Comida', 2000, 16, 'palito-de-queso.jpeg', 'cafeteria', NULL, 10, 1),
(13, 'Chocolatinas Jet', 'Dulces', 4000, 10, 'chocolatinas.webp', 'cafeteria', NULL, 9, 1),
(14, 'Arepa con salchichon', 'Comida', 2000, 30, 'arepa-con-salchichon.webp', 'cafeteria', NULL, 11, 1),
(15, 'Paletas', 'Dulces', 3000, 22, 'paletas.jpg', 'cafeteria', NULL, 14, 1),
(16, 'Arepa con chorizo', 'Comida', 4000, 12, 'arepa-con-chorizo.jpg', 'cafeteria', NULL, 12, 1),
(17, 'Arepa con carne', 'Comida', 4000, 40, 'arepa-con-carne.jpg', 'cafeteria', NULL, 15, 1),
(18, 'Cremas', 'Dulces', 4200, 18, 'cremas.jpg', 'cafeteria', NULL, 16, 1),
(19, 'Mr. Tea', 'Bebida', 3500, 19, 'mr-tea.jpg', 'cafeteria', NULL, 17, 1),
(20, 'Salchipapas', 'Comida', 3000, 9, 'salchipapas.jpg', 'cafeteria', NULL, 18, 1),
(21, 'Arroz con Pollo', 'Almuerzo', 15000, 12, 'arroz-con-pollo.jpeg', 'almuerzo', 'Lunes', 1, 1),
(22, 'Bandeja Paisa', 'Almuerzo', 18000, 8, 'bandeja-paisa.jpeg', 'almuerzo', 'Lunes', 2, 1),
(23, 'Spaghetti', 'Almuerzo', 13000, 14, 'spaghetti.jpeg', 'almuerzo', 'Martes', 3, 1),
(24, 'Frijoles', 'Almuerzo', 16000, 11, 'frijoles.jpg', 'almuerzo', 'Martes', 4, 1),
(25, 'Ensalada Especial', 'Almuerzo', 12000, 9, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=800', 'almuerzo', 'Miércoles', 5, 1),
(26, 'Lentejas', 'Almuerzo', 17000, 10, 'lentejas.jpeg', 'almuerzo', 'Miércoles', 6, 1),
(27, 'Sancocho', 'Almuerzo', 20000, 7, 'sancocho.jpeg', 'almuerzo', 'Jueves', 7, 1),
(28, 'Pescado', 'Almuerzo', 11000, 15, 'pescado.jpg', 'almuerzo', 'Jueves', 8, 1),
(29, 'Sudado de pollo', 'Almuerzo', 10000, 13, 'sudado-de-pollo.webp', 'almuerzo', 'Viernes', 9, 1),
(30, 'Garbanzos', 'Almuerzo', 9000, 20, 'garbanzos.jpeg', 'almuerzo', 'Viernes', 10, 1);

ALTER TABLE productos AUTO_INCREMENT = 31;

-- -----------------------------------------------------
-- Ingredientes de cada almuerzo (los que se pueden quitar)
-- -----------------------------------------------------
CREATE TABLE plato_ingredientes (
  Id_Ingrediente int(11) NOT NULL AUTO_INCREMENT,
  Id_Producto int(11) NOT NULL,
  Ingrediente varchar(80) NOT NULL,
  PRIMARY KEY (Id_Ingrediente),
  KEY Id_Producto (Id_Producto),
  CONSTRAINT plato_ing_ibfk_1 FOREIGN KEY (Id_Producto) REFERENCES productos (Id_Producto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO plato_ingredientes (Id_Producto, Ingrediente) VALUES
(21, 'Arroz'),
(21, 'Pollo'),
(21, 'Arvejas'),
(21, 'Zanahoria'),
(21, 'Habichuela'),
(22, 'Frijoles'),
(22, 'Arroz'),
(22, 'Carne molida'),
(22, 'Chicharrón'),
(22, 'Chorizo'),
(22, 'Huevo'),
(22, 'Plátano maduro'),
(23, 'Pasta'),
(23, 'Salsa de tomate'),
(23, 'Carne'),
(23, 'Queso'),
(23, 'Cebolla'),
(24, 'Frijoles'),
(24, 'Arroz'),
(24, 'Plátano'),
(24, 'Zanahoria'),
(24, 'Hogao'),
(25, 'Lechuga'),
(25, 'Tomate'),
(25, 'Zanahoria'),
(25, 'Maíz'),
(25, 'Pollo'),
(25, 'Queso'),
(26, 'Lentejas'),
(26, 'Papa'),
(26, 'Zanahoria'),
(26, 'Cebolla'),
(26, 'Arroz'),
(27, 'Pollo'),
(27, 'Papa'),
(27, 'Yuca'),
(27, 'Plátano'),
(27, 'Mazorca'),
(27, 'Cilantro'),
(28, 'Pescado'),
(28, 'Arroz'),
(28, 'Ensalada'),
(28, 'Limón'),
(29, 'Pollo'),
(29, 'Papa'),
(29, 'Yuca'),
(29, 'Tomate'),
(29, 'Cebolla'),
(30, 'Garbanzos'),
(30, 'Papa'),
(30, 'Zanahoria'),
(30, 'Tomate'),
(30, 'Cebolla');

-- -----------------------------------------------------
-- usuarios: inicio de sesión (contraseña cifrada con password_hash de PHP)
-- Cada usuario queda ligado a un registro de clientes.
-- -----------------------------------------------------
CREATE TABLE usuarios (
  Id_Usuario int(11) NOT NULL AUTO_INCREMENT,
  Usuario varchar(50) NOT NULL,
  Correo varchar(100) NOT NULL,
  Password_Hash varchar(255) NOT NULL,
  Rol enum('cliente','admin') NOT NULL DEFAULT 'cliente',
  Id_Cliente int(11) DEFAULT NULL,
  Fecha_Registro timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (Id_Usuario),
  UNIQUE KEY uq_usuario (Usuario),
  UNIQUE KEY uq_correo (Correo),
  UNIQUE KEY uq_cliente (Id_Cliente),
  CONSTRAINT usuarios_ibfk_1 FOREIGN KEY (Id_Cliente) REFERENCES clientes (id_clientes) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- pedidos: un pedido agrupa varias líneas de ventas
-- -----------------------------------------------------
CREATE TABLE pedidos (
  Id_Pedido int(11) NOT NULL AUTO_INCREMENT,
  Id_Cliente int(11) NOT NULL,
  Fecha timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Total decimal(12,0) NOT NULL DEFAULT 0,
  Estado enum('pendiente','preparando','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (Id_Pedido),
  KEY Id_Cliente (Id_Cliente),
  CONSTRAINT pedidos_ibfk_1 FOREIGN KEY (Id_Cliente) REFERENCES clientes (id_clientes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- ventas (tu tabla; Cantidad ahora es número) + Id_Pedido,
-- Precio_Unitario y Personalizacion (ingredientes elegidos)
-- Las ventas antiguas quedan con Id_Pedido = NULL.
-- -----------------------------------------------------
CREATE TABLE ventas (
  Id_Venta int(11) NOT NULL AUTO_INCREMENT,
  Id_Cliente int(11) NOT NULL,
  Id_Producto int(11) NOT NULL,
  Cantidad int(10) UNSIGNED NOT NULL,
  Id_Pedido int(11) DEFAULT NULL,
  Precio_Unitario decimal(10,0) DEFAULT NULL,
  Personalizacion varchar(500) DEFAULT NULL,
  PRIMARY KEY (Id_Venta),
  KEY Id_Cliente (Id_Cliente),
  KEY Id_Producto (Id_Producto),
  KEY Id_Pedido (Id_Pedido),
  CONSTRAINT ventas_ibfk_1 FOREIGN KEY (Id_Cliente) REFERENCES clientes (id_clientes),
  CONSTRAINT ventas_ibfk_2 FOREIGN KEY (Id_Producto) REFERENCES productos (Id_Producto),
  CONSTRAINT ventas_ibfk_3 FOREIGN KEY (Id_Pedido) REFERENCES pedidos (Id_Pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ventas (Id_Venta, Id_Cliente, Id_Producto, Cantidad) VALUES
(1, 2, 1, 2),(2, 3, 10, 1),(3, 4, 8, 2),(4, 5, 5, 1),(5, 6, 3, 1),
(6, 7, 14, 1),(7, 8, 4, 2),(8, 9, 11, 1),(9, 10, 13, 2),(10, 11, 7, 1),
(11, 12, 2, 1),(12, 13, 9, 2),(13, 14, 6, 1),(14, 15, 12, 1),(15, 16, 15, 3);

ALTER TABLE clientes AUTO_INCREMENT = 17;
ALTER TABLE ventas   AUTO_INCREMENT = 16;

-- Vista útil: catálogo activo
CREATE OR REPLACE VIEW v_catalogo AS
SELECT Id_Producto, Nombre, Categoria, Seccion, Dia_Semana, Precio, Stock, Imagen
FROM productos WHERE Activo = 1;
