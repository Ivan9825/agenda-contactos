-- =========================================================
-- Actualización de la base de datos de la versión 1 a la versión 2
--
-- Úselo SOLO si ya tiene creada la base de datos de la práctica
-- anterior y quiere conservar sus usuarios y contactos.
-- Si va a instalar desde cero, use database/agenda.sql.
-- En phpMyAdmin: seleccione su base de datos y luego Importar este archivo.
-- =========================================================

-- 1. Tabla de categorías
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

-- 2. Nuevos campos en contactos (los contactos existentes quedan en "Otro",
--    no favoritos y con la fecha actual como fecha de creación)
ALTER TABLE contactos
  ADD COLUMN id_categoria   TINYINT UNSIGNED NOT NULL DEFAULT 5 AFTER id_usuario,
  ADD COLUMN favorito       TINYINT(1)       NOT NULL DEFAULT 0 AFTER ciudad,
  ADD COLUMN fecha_creacion DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER favorito,
  ADD KEY idx_contactos_categoria (id_categoria),
  ADD CONSTRAINT fk_contactos_categorias
    FOREIGN KEY (id_categoria) REFERENCES categorias (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
