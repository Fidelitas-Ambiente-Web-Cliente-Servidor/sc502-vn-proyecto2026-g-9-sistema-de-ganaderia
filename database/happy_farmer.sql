-- ═══════════════════════════════════════════
--  HAPPY FARMER — Base de datos MySQL
--  Importar desde phpMyAdmin: localhost:8082
-- ═══════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS happy_farmer
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE happy_farmer;

-- ─── Tabla: usuarios (propietarios de finca) ───
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(100)  NOT NULL,
  finca         VARCHAR(100)  DEFAULT 'Mi finca',
  correo        VARCHAR(150)  UNIQUE NOT NULL,
  clave         VARCHAR(255)  NOT NULL,
  fecha_registro TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─── Tabla: animales ───
CREATE TABLE IF NOT EXISTS animales (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  identificacion  VARCHAR(30)  NOT NULL,
  nombre          VARCHAR(100),
  raza            VARCHAR(60)  NOT NULL,
  sexo            VARCHAR(10)  DEFAULT 'Hembra',
  fecha_nacimiento DATE,
  estado          VARCHAR(30)  DEFAULT 'Activo',
  observaciones   TEXT,
  id_usuario      INT          NOT NULL,
  fecha_registro  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
  UNIQUE KEY uk_ident_usuario (identificacion, id_usuario)
) ENGINE=InnoDB;

-- ─── Tabla: salud_animal ───
CREATE TABLE IF NOT EXISTS salud_animal (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  animal_ref      VARCHAR(150) NOT NULL,
  tipo            VARCHAR(50)  NOT NULL,
  fecha           DATE         NOT NULL,
  proximo_control DATE,
  veterinario     VARCHAR(100),
  medicamento     VARCHAR(100),
  detalle         TEXT,
  id_usuario      INT          NOT NULL,
  fecha_registro  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Tabla: gastos ───
CREATE TABLE IF NOT EXISTS gastos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  categoria    VARCHAR(50)    NOT NULL,
  monto        DECIMAL(12,2)  NOT NULL,
  fecha        DATE           NOT NULL,
  metodo_pago  VARCHAR(30)    DEFAULT 'Efectivo',
  descripcion  TEXT,
  id_usuario   INT            NOT NULL,
  fecha_registro TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Tabla: usuarios_finca (usuarios adicionales) ───
CREATE TABLE IF NOT EXISTS usuarios_finca (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(100) NOT NULL,
  rol          VARCHAR(50)  DEFAULT 'Consulta',
  estado       VARCHAR(20)  DEFAULT 'Activo',
  id_propietario INT        NOT NULL,
  FOREIGN KEY (id_propietario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
