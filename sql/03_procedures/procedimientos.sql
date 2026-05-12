-- ============================================================
--  RLCS CRM — 15 Procedimientos Almacenados
--  Ejecutar después de crear el esquema y las vistas.
--  Requiere MySQL 5.7+ o MariaDB 10.3+
-- ============================================================

DELIMITER $$

-- ------------------------------------------------------------
-- 1. transferir_jugador
--    Cierra el roster activo del jugador y abre uno nuevo en
--    el equipo destino. Usado desde jugadores/editar.php.
-- ------------------------------------------------------------
CREATE PROCEDURE transferir_jugador(
    IN p_id_jugador  INT,
    IN p_id_equipo   INT,
    IN p_fecha       DATE
)
BEGIN
    -- Cerrar roster activo actual (si existe)
    UPDATE ROSTER
    SET    fecha_fin = p_fecha
    WHERE  id_jugador = p_id_jugador
      AND  fecha_fin IS NULL;

    -- Abrir nuevo roster en el equipo destino
    INSERT INTO ROSTER (id_jugador, id_equipo, fecha_inicio, titular)
    VALUES (p_id_jugador, p_id_equipo, p_fecha, 1);
END$$


-- ------------------------------------------------------------
-- 2. calcular_puntos_temporada
--    Recalcula PUNTOS_RLCS para todos los equipos de una
--    temporada sumando puntos_ganados de PARTICIPACION por tipo.
-- ------------------------------------------------------------
CREATE PROCEDURE calcular_puntos_temporada(
    IN p_id_temporada INT
)
BEGIN
    DECLARE done    INT DEFAULT FALSE;
    DECLARE v_equipo INT;
    DECLARE v_reg    INT;
    DECLARE v_maj    INT;
    DECLARE v_tot    INT;

    DECLARE cur CURSOR FOR
        SELECT p.id_equipo,
               SUM(CASE WHEN t.tipo = 'regional' THEN p.puntos_ganados ELSE 0 END),
               SUM(CASE WHEN t.tipo = 'major'    THEN p.puntos_ganados ELSE 0 END),
               SUM(p.puntos_ganados)
        FROM   PARTICIPACION p
        INNER JOIN TORNEO t ON t.id_torneo = p.id_torneo
        WHERE  t.id_temporada = p_id_temporada
        GROUP BY p.id_equipo;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    loop_calc: LOOP
        FETCH cur INTO v_equipo, v_reg, v_maj, v_tot;
        IF done THEN LEAVE loop_calc; END IF;

        INSERT INTO PUNTOS_RLCS
               (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales)
        VALUES (v_equipo, p_id_temporada, v_reg, v_maj, v_tot)
        ON DUPLICATE KEY UPDATE
            puntos_regionals = v_reg,
            puntos_majors    = v_maj,
            puntos_totales   = v_tot;
    END LOOP;
    CLOSE cur;
END$$


-- ------------------------------------------------------------
-- 3. registrar_resultado_partido
--    Guarda el ganador de un partido y actualiza puntos de
--    participación si hay lógica de puntos por resultado.
-- ------------------------------------------------------------
CREATE PROCEDURE registrar_resultado_partido(
    IN p_id_partido  INT,
    IN p_id_ganador  INT
)
BEGIN
    UPDATE PARTIDO
    SET    id_ganador = p_id_ganador
    WHERE  id_partido = p_id_partido
      AND  id_ganador IS NULL;
END$$


-- ------------------------------------------------------------
-- 4. liberar_jugador
--    Cierra el roster activo de un jugador (pasa a agente libre).
-- ------------------------------------------------------------
CREATE PROCEDURE liberar_jugador(
    IN p_id_jugador INT,
    IN p_fecha      DATE
)
BEGIN
    UPDATE ROSTER
    SET    fecha_fin = p_fecha
    WHERE  id_jugador = p_id_jugador
      AND  fecha_fin IS NULL;
END$$


-- ------------------------------------------------------------
-- 5. obtener_forma_reciente
--    Devuelve los últimos N resultados de un equipo como
--    cadena de W/L (ej: 'W,L,W,W,L').
-- ------------------------------------------------------------
CREATE PROCEDURE obtener_forma_reciente(
    IN  p_id_equipo INT,
    IN  p_n         INT,
    OUT p_forma     VARCHAR(50)
)
BEGIN
    DECLARE v_forma VARCHAR(50) DEFAULT '';

    SELECT GROUP_CONCAT(resultado ORDER BY fecha_hora DESC SEPARATOR ',')
    INTO   v_forma
    FROM (
        SELECT fecha_hora,
               CASE WHEN id_ganador = p_id_equipo THEN 'W' ELSE 'L' END AS resultado
        FROM   PARTIDO
        WHERE  (id_equipo1 = p_id_equipo OR id_equipo2 = p_id_equipo)
          AND  id_ganador IS NOT NULL
        ORDER BY fecha_hora DESC
        LIMIT  p_n
    ) sub;

    SET p_forma = IFNULL(v_forma, '');
END$$


-- ------------------------------------------------------------
-- 6. clasificacion_temporada
--    Devuelve el ranking completo de una temporada ordenado
--    por puntos_totales DESC.
-- ------------------------------------------------------------
CREATE PROCEDURE clasificacion_temporada(
    IN p_id_temporada INT
)
BEGIN
    SELECT e.id_equipo,
           e.nombre,
           e.tag,
           r.nombre   AS region,
           r.siglas,
           COALESCE(pr.puntos_regionals, 0) AS puntos_regionals,
           COALESCE(pr.puntos_majors,    0) AS puntos_majors,
           COALESCE(pr.puntos_totales,   0) AS puntos_totales,
           RANK() OVER (ORDER BY COALESCE(pr.puntos_totales, 0) DESC) AS posicion
    FROM   EQUIPO e
    LEFT JOIN REGION r       ON r.id_region  = e.id_region
    LEFT JOIN PUNTOS_RLCS pr ON pr.id_equipo = e.id_equipo
                             AND pr.id_temporada = p_id_temporada
    WHERE  e.activo = 1
    ORDER BY puntos_totales DESC, e.nombre ASC;
END$$


-- ------------------------------------------------------------
-- 7. jugadores_agentes_libres
--    Devuelve todos los jugadores activos sin roster vigente.
-- ------------------------------------------------------------
CREATE PROCEDURE jugadores_agentes_libres()
BEGIN
    SELECT j.id_jugador,
           j.nickname,
           j.nombre_real,
           j.pais,
           MAX(r.fecha_fin) AS ultima_salida
    FROM   JUGADOR j
    LEFT JOIN ROSTER r ON r.id_jugador = j.id_jugador
    WHERE  j.activo = 1
      AND  NOT EXISTS (
               SELECT 1 FROM ROSTER r2
               WHERE  r2.id_jugador = j.id_jugador
                 AND  r2.fecha_fin IS NULL
           )
    GROUP BY j.id_jugador, j.nickname, j.nombre_real, j.pais
    ORDER BY j.nickname ASC;
END$$


-- ------------------------------------------------------------
-- 8. historial_h2h
--    Partidos entre dos equipos con el resultado de cada uno.
-- ------------------------------------------------------------
CREATE PROCEDURE historial_h2h(
    IN p_id_equipo1 INT,
    IN p_id_equipo2 INT
)
BEGIN
    SELECT p.id_partido,
           p.fecha_hora,
           t.nombre  AS torneo,
           e1.tag    AS tag_equipo1,
           e2.tag    AS tag_equipo2,
           CASE
               WHEN p.id_ganador = p_id_equipo1 THEN e1.tag
               WHEN p.id_ganador = p_id_equipo2 THEN e2.tag
               ELSE 'Pendiente'
           END AS ganador
    FROM   PARTIDO p
    INNER JOIN TORNEO t  ON t.id_torneo  = p.id_torneo
    INNER JOIN EQUIPO e1 ON e1.id_equipo = p.id_equipo1
    INNER JOIN EQUIPO e2 ON e2.id_equipo = p.id_equipo2
    WHERE  (p.id_equipo1 = p_id_equipo1 AND p.id_equipo2 = p_id_equipo2)
        OR (p.id_equipo1 = p_id_equipo2 AND p.id_equipo2 = p_id_equipo1)
    ORDER BY p.fecha_hora DESC;
END$$


-- ------------------------------------------------------------
-- 9. top_goleadores
--    Top N jugadores con más goles en una temporada.
--    p_id_temporada = 0 → global (todas las temporadas).
-- ------------------------------------------------------------
CREATE PROCEDURE top_goleadores(
    IN p_id_temporada INT,
    IN p_limite       INT
)
BEGIN
    SELECT j.id_jugador,
           j.nickname,
           j.pais,
           SUM(ej.goles)       AS total_goles,
           SUM(ej.asistencias) AS total_asistencias,
           SUM(ej.salvadas)    AS total_salvadas,
           COUNT(DISTINCT ej.id_partido) AS partidos_jugados
    FROM   ESTADISTICAS_JUGADOR ej
    INNER JOIN JUGADOR  j ON j.id_jugador = ej.id_jugador
    INNER JOIN PARTIDO  p ON p.id_partido = ej.id_partido
    INNER JOIN TORNEO   t ON t.id_torneo  = p.id_torneo
    WHERE  (p_id_temporada = 0 OR t.id_temporada = p_id_temporada)
    GROUP BY j.id_jugador, j.nickname, j.pais
    ORDER BY total_goles DESC
    LIMIT  p_limite;
END$$


-- ------------------------------------------------------------
-- 10. equipos_por_region
--     Equipos de una región con sus puntos en la última temporada.
-- ------------------------------------------------------------
CREATE PROCEDURE equipos_por_region(
    IN p_id_region INT
)
BEGIN
    SELECT e.id_equipo,
           e.nombre,
           e.tag,
           e.activo,
           COALESCE(pr.puntos_totales, 0) AS puntos_ultima_temporada
    FROM   EQUIPO e
    LEFT JOIN PUNTOS_RLCS pr ON pr.id_equipo = e.id_equipo
                             AND pr.id_temporada = (
                                 SELECT MAX(id_temporada) FROM TEMPORADA
                             )
    WHERE  e.id_region = p_id_region
    ORDER BY puntos_ultima_temporada DESC, e.nombre ASC;
END$$


-- ------------------------------------------------------------
-- 11. resumen_torneo
--     Datos generales de un torneo: participantes, partidos
--     jugados y pendientes.
-- ------------------------------------------------------------
CREATE PROCEDURE resumen_torneo(
    IN p_id_torneo INT
)
BEGIN
    SELECT t.id_torneo,
           t.nombre,
           t.tipo,
           t.fecha_inicio,
           t.fecha_fin,
           te.anio           AS temporada,
           r.nombre          AS region,
           COUNT(DISTINCT p.id_equipo)    AS equipos_participantes,
           (SELECT COUNT(*) FROM PARTIDO pa WHERE pa.id_torneo = t.id_torneo)         AS total_partidos,
           (SELECT COUNT(*) FROM PARTIDO pa WHERE pa.id_torneo = t.id_torneo AND pa.id_ganador IS NOT NULL) AS partidos_jugados,
           (SELECT COUNT(*) FROM PARTIDO pa WHERE pa.id_torneo = t.id_torneo AND pa.id_ganador IS NULL)     AS partidos_pendientes
    FROM   TORNEO t
    INNER JOIN TEMPORADA te ON te.id_temporada = t.id_temporada
    LEFT  JOIN REGION    r  ON r.id_region     = t.id_region
    LEFT  JOIN PARTICIPACION p ON p.id_torneo  = t.id_torneo
    WHERE  t.id_torneo = p_id_torneo
    GROUP BY t.id_torneo;
END$$


-- ------------------------------------------------------------
-- 12. estadisticas_equipo
--     Estadísticas agregadas de todos los jugadores de un equipo
--     a lo largo de su historial completo.
-- ------------------------------------------------------------
CREATE PROCEDURE estadisticas_equipo(
    IN p_id_equipo INT
)
BEGIN
    SELECT j.id_jugador,
           j.nickname,
           COUNT(DISTINCT ej.id_partido) AS partidos,
           SUM(ej.goles)                 AS goles,
           SUM(ej.asistencias)           AS asistencias,
           SUM(ej.salvadas)              AS salvadas,
           SUM(ej.tiros)                 AS tiros,
           SUM(ej.mvp)                   AS mvps,
           ROUND(SUM(ej.goles) / NULLIF(COUNT(DISTINCT ej.id_partido), 0), 2) AS media_goles
    FROM   ROSTER rs
    INNER JOIN JUGADOR j              ON j.id_jugador  = rs.id_jugador
    LEFT  JOIN ESTADISTICAS_JUGADOR ej ON ej.id_jugador = j.id_jugador
    WHERE  rs.id_equipo = p_id_equipo
    GROUP BY j.id_jugador, j.nickname
    ORDER BY goles DESC;
END$$


-- ------------------------------------------------------------
-- 13. actualizar_estado_jugador
--     Activa o desactiva un jugador (campo activo).
-- ------------------------------------------------------------
CREATE PROCEDURE actualizar_estado_jugador(
    IN p_id_jugador INT,
    IN p_activo     TINYINT(1)
)
BEGIN
    UPDATE JUGADOR
    SET    activo = p_activo
    WHERE  id_jugador = p_id_jugador;

    -- Si se desactiva, también cerrar su roster activo
    IF p_activo = 0 THEN
        UPDATE ROSTER
        SET    fecha_fin = CURDATE()
        WHERE  id_jugador = p_id_jugador
          AND  fecha_fin IS NULL;
    END IF;
END$$


-- ------------------------------------------------------------
-- 14. limpiar_auditoria_antigua
--     Elimina registros de AUDITORIA más antiguos que N días.
-- ------------------------------------------------------------
CREATE PROCEDURE limpiar_auditoria_antigua(
    IN p_dias INT
)
BEGIN
    DECLARE v_eliminados INT;

    DELETE FROM AUDITORIA
    WHERE  timestamp < DATE_SUB(NOW(), INTERVAL p_dias DAY);

    SET v_eliminados = ROW_COUNT();
    SELECT v_eliminados AS registros_eliminados;
END$$


-- ------------------------------------------------------------
-- 15. bracket_torneo
--     Devuelve la estructura de bracket de un torneo agrupada
--     por ronda, con el partido asociado (si existe).
-- ------------------------------------------------------------
CREATE PROCEDURE bracket_torneo(
    IN p_id_torneo INT
)
BEGIN
    SELECT b.id_bracket,
           b.ronda,
           b.fase,
           b.tipo_bracket,
           b.id_partido,
           e1.tag AS tag_equipo1,
           e2.tag AS tag_equipo2,
           CASE
               WHEN p.id_ganador = p.id_equipo1 THEN e1.tag
               WHEN p.id_ganador = p.id_equipo2 THEN e2.tag
               ELSE NULL
           END AS ganador
    FROM   BRACKET b
    LEFT JOIN PARTIDO p  ON p.id_partido = b.id_partido
    LEFT JOIN EQUIPO e1  ON e1.id_equipo = p.id_equipo1
    LEFT JOIN EQUIPO e2  ON e2.id_equipo = p.id_equipo2
    WHERE  b.id_torneo = p_id_torneo
    ORDER BY b.ronda ASC, b.id_bracket ASC;
END$$

DELIMITER ;
