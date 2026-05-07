CREATE VIEW vista_rosters_actuales AS
SELECT 
    e.nombre AS equipo,
    e.tag,
    j.nickname AS jugador,
    j.pais,
    r.titular,
    r.fecha_inicio
FROM ROSTER r
INNER JOIN EQUIPO e ON r.id_equipo = e.id_equipo
INNER JOIN JUGADOR j ON r.id_jugador = j.id_jugador
WHERE r.fecha_fin IS NULL
ORDER BY e.nombre, r.titular DESC;