CREATE OR REPLACE VIEW vista_rosters_actuales AS
SELECT
    e.id_equipo,
    e.nombre AS equipo,
    e.tag,
    j.id_jugador,
    j.nickname,
    j.pais,
    r.titular,
    r.fecha_inicio
FROM ROSTER r
INNER JOIN EQUIPO e ON r.id_equipo = e.id_equipo
INNER JOIN JUGADOR j ON r.id_jugador = j.id_jugador
WHERE r.fecha_fin IS NULL
ORDER BY e.nombre, r.titular DESC;
