-- =========================================================
-- Script de base de datos: Agenda Personal de Contactos (versión 2)
-- Motor: MySQL / MariaDB
--
-- Uso en LOCAL (XAMPP): importar este archivo completo en phpMyAdmin.
-- Uso en HOSTING (InfinityFree u otro): importar database/agenda_hosting.sql,
-- que es igual pero sin las líneas CREATE DATABASE / USE, porque en el
-- hosting la base de datos ya viene creada.
-- =========================================================

CREATE DATABASE IF NOT EXISTS agenda_contactos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agenda_contactos;

-- ---------------------------------------------------------
-- Tabla: usuarios
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre         VARCHAR(100) NOT NULL,
  correo         VARCHAR(150) NOT NULL,
  password       VARCHAR(255) NOT NULL,          -- guarda el hash, nunca la contraseña real
  fecha_registro DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_correo (correo)         -- impide correos repetidos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Tabla: categorias (NUEVA en la versión 2)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
  id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50)      NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categorias (id, nombre) VALUES
  (1, 'Familia'),
  (2, 'Amigos'),
  (3, 'Trabajo'),
  (4, 'Universidad'),
  (5, 'Otro');

-- ---------------------------------------------------------
-- Tabla: contactos
-- Nuevos campos en la versión 2: id_categoria, favorito, fecha_creacion
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS contactos (
  id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  id_usuario     INT UNSIGNED     NOT NULL,      -- usuario propietario del contacto
  id_categoria   TINYINT UNSIGNED NOT NULL DEFAULT 5,  -- cada contacto pertenece a una categoría (5 = Otro)
  nombre         VARCHAR(100)     NOT NULL,
  telefono       VARCHAR(20)      NOT NULL,
  correo         VARCHAR(150)     NOT NULL,
  ciudad         VARCHAR(80)      NOT NULL,
  favorito       TINYINT(1)       NOT NULL DEFAULT 0,  -- 1 = Sí, 0 = No
  fecha_creacion DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contactos_usuario (id_usuario),
  KEY idx_contactos_categoria (id_categoria),
  CONSTRAINT fk_contactos_usuarios
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_contactos_categorias
    FOREIGN KEY (id_categoria) REFERENCES categorias (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
