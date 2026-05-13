-- Ejecutar una vez para añadir las columnas de personalización visual
ALTER TABLE EQUIPO
  ADD COLUMN IF NOT EXISTS color_primario VARCHAR(7)   DEFAULT '#00d4ff',
  ADD COLUMN IF NOT EXISTS logo_url       VARCHAR(500) DEFAULT NULL;

ALTER TABLE JUGADOR
  ADD COLUMN IF NOT EXISTS foto_url VARCHAR(500) DEFAULT NULL;
