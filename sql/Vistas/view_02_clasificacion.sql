CREATE VIEW vista_clasificacion AS
SELECT 
    e.nombre AS equipo,
    e.tag,
    r.nombre AS region,
    t.anio AS temporada,
    p.puntos_regionals,
    p.puntos_majors,
    p.puntos_totales
FROM PUNTOS_RLCS p
INNER JOIN EQUIPO e ON p.id_equipo = e.id_equipo
INNER JOIN REGION r ON e.id_region = r.id_region
INNER JOIN TEMPORADA t ON p.id_temporada = t.id_temporada
ORDER BY p.puntos_totales DESC;