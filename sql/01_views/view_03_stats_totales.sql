CREATE VIEW vista_stats_totales AS
SELECT 
    j.nickname,
    j.pais,
    COUNT(DISTINCT ej.id_partido) AS partidos_jugados,
    SUM(ej.goles) AS total_goles,
    SUM(ej.asistencias) AS total_asistencias,
    SUM(ej.salvadas) AS total_salvadas,
    SUM(ej.tiros) AS total_tiros,
    SUM(ej.mvp) AS total_mvps,
    ROUND(SUM(ej.goles) / COUNT(DISTINCT ej.id_partido), 2) AS media_goles_partido
FROM JUGADOR j
LEFT JOIN ESTADISTICAS_JUGADOR ej ON j.id_jugador = ej.id_jugador
GROUP BY j.id_jugador, j.nickname, j.pais
ORDER BY total_goles DESC;