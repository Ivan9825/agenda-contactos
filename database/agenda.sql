-- =========================================================
-- Script de base de datos: Agenda Personal de Contactos
-- Motor: MySQL / MariaDB
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
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: contactos
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS contactos (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario INT UNSIGNED NOT NULL,              -- usuario propietario del contacto
  nombre     VARCHAR(100) NOT NULL,
  telefono   VARCHAR(20)  NOT NULL,
  correo     VARCHAR(150) NOT NULL,
  ciudad     VARCHAR(80)  NOT NULL,
  PRIMARY KEY (id),
  KEY idx_contactos_usuario (id_usuario),
  CONSTRAINT fk_contactos_usuarios
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;
