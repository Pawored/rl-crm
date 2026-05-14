-- =============================================
-- TODOS LOS INSERTS — archivo maestro de datos
-- Ejecutar en orden secuencial
--
-- ORDEN OBLIGATORIO (respeta foreign keys):
-- 1. Regiones
-- 2. Equipos
-- 3. Jugadores
-- 4. Roster (depende de Equipo + Jugador)
-- 5. Temporadas
-- 6. Torneos (depende de Temporada)
-- 7. Participación (depende de Torneo + Equipo)
-- 8. Puntos RLCS (depende de Equipo + Temporada)
-- 9. Usuarios (independiente)
-- 10. Partidos + Juegos + Estadísticas (depende de Torneo + Equipo + Jugador)
--
-- NOTA: El archivo crear_usuarios.sql crea la tabla
-- USUARIOS e inserta el admin por defecto.
-- Este archivo añade los usuarios de muestra adicionales.
-- =============================================

SOURCE 02_inserts/01_regiones.sql;
SOURCE 02_inserts/02_equipos.sql;
SOURCE 02_inserts/03_jugadores.sql;
SOURCE 02_inserts/04_roster.sql;
SOURCE 02_inserts/05_temporadas.sql;
SOURCE 02_inserts/06_torneos.sql;
SOURCE 02_inserts/07_participacion.sql;
SOURCE 02_inserts/08_puntos_rlcs.sql;
SOURCE 02_inserts/09_usuarios.sql;
SOURCE 02_inserts/10_partidos.sql;
SOURCE 02_inserts/11_partidos_resto.sql;
