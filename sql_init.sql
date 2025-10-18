-- raffle_app_v5 SQL
CREATE DATABASE IF NOT EXISTS raffle_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE raffle_app;

-- users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  cedula VARCHAR(50),
  email VARCHAR(255) NOT NULL UNIQUE,
  telefono VARCHAR(50),
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('cliente','admin') DEFAULT 'cliente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- raffles
CREATE TABLE IF NOT EXISTS raffles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  precio_usd DECIMAL(10,2) NOT NULL DEFAULT 0,
  fecha_sorteo DATE NULL,
  estado ENUM('activa','cerrada') DEFAULT 'activa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- currency_rates
CREATE TABLE IF NOT EXISTS currency_rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tasa DECIMAL(12,6) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- payment_methods
CREATE TABLE IF NOT EXISTS payment_methods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(50) NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  datos JSON,
  activo TINYINT(1) DEFAULT 1
);

-- payments
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  raffle_id INT NOT NULL,
  metodo_pago_slug VARCHAR(50) NOT NULL,
  monto_usd DECIMAL(10,2),
  monto_bs DECIMAL(12,2),
  referencia VARCHAR(100),
  fecha_pago DATE,
  estado ENUM('pendiente','verificado','rechazado') DEFAULT 'pendiente',
  datos_cliente JSON,
  comprobante_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE
);

-- raffle_tickets
CREATE TABLE IF NOT EXISTS raffle_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  raffle_id INT NOT NULL,
  user_id INT NOT NULL,
  payment_id INT NOT NULL,
  numero_ticket VARCHAR(50) NOT NULL,
  asignado_por INT NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
);

-- Seed data
INSERT INTO currency_rates (tasa) VALUES (203.742);

INSERT INTO payment_methods (slug, nombre, datos) VALUES
('zelle', 'Zelle', JSON_OBJECT('identifier', '+16159344027', 'owner','Edixon Marquez')),
('binance', 'Binance Pay', JSON_OBJECT('pay_id','782847805', 'user','astridnuevo@gmail.com')),
('movil', 'Pago Móvil', JSON_OBJECT('bank','Banco de Venezuela (0102)', 'phone','0414-2479734','id','V-20.630.693')),
('transfer', 'Transferencia', JSON_OBJECT('bank','Banco de Venezuela','account','0102-0101-2000-0011-7210','owner','Astrid Alejandra Bravo Duran','id','20.630.693'));

INSERT INTO raffles (titulo, descripcion, precio_usd, fecha_sorteo) VALUES
('PlayStation 5 - Gran Sorteo','Consola PS5 + accesorios', 12.00, '2025-12-20'),
('Viaje a Cancún - Paquete 2 pax','Paquete todo incluido', 50.00, '2025-11-15');

-- Admin user (email: admin@example.com, password: Admin123!)
INSERT INTO users (nombre, email, password_hash, rol) VALUES
('Administrador', 'admin@example.com', '$2b$12$8dizubTzecomO4CuGdw/weLMus2BM1yopQXfqzQNXCqaZpaKdBny2', 'admin');
