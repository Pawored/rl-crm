DROP DATABASE IF EXISTS RLCS;
CREATE DATABASE RLCS;
USE RLCS;

-- TABLAS --
CREATE TABLE REGION (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    siglas VARCHAR(10) NOT NULL,
    plazas_mundial INT NOT NULL
);

CREATE TABLE EQUIPO (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tag VARCHAR(20) NOT NULL,
    id_region INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_region) REFERENCES REGION(id_region)
);

CREATE TABLE JUGADOR (
    id_jugador INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL,
    nombre_real VARCHAR(100),
    fecha_nacimiento DATE,
    pais VARCHAR(50)
);

CREATE TABLE ROSTER (
    id_roster INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    id_jugador INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    titular BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo),
    FOREIGN KEY (id_jugador) REFERENCES JUGADOR(id_jugador)
);

CREATE TABLE TEMPORADA (
    id_temporada INT AUTO_INCREMENT PRIMARY KEY,
    anio YEAR NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    prize_pool DECIMAL(12,2)
);

CREATE TABLE TORNEO (
    id_torneo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    id_temporada INT NOT NULL,
    prize_pool DECIMAL(12,2),
    FOREIGN KEY (id_temporada) REFERENCES TEMPORADA(id_temporada)
);

CREATE TABLE PARTICIPACION (
    id_participacion INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    id_torneo INT NOT NULL,
    puntos_ganados INT DEFAULT 0,
    posicion_final INT,
    FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo),
    FOREIGN KEY (id_torneo) REFERENCES TORNEO(id_torneo)
);

CREATE TABLE PARTIDO (
    id_partido INT AUTO_INCREMENT PRIMARY KEY,
    id_torneo INT NOT NULL,
    id_equipo1 INT NOT NULL,
    id_equipo2 INT NOT NULL,
    id_ganador INT,
    fecha_hora DATETIME NOT NULL,
    formato VARCHAR(10) DEFAULT 'Bo5',
    FOREIGN KEY (id_torneo) REFERENCES TORNEO(id_torneo),
    FOREIGN KEY (id_equipo1) REFERENCES EQUIPO(id_equipo),
    FOREIGN KEY (id_equipo2) REFERENCES EQUIPO(id_equipo),
    FOREIGN KEY (id_ganador) REFERENCES EQUIPO(id_equipo)
);

CREATE TABLE JUEGO (
    id_juego INT AUTO_INCREMENT PRIMARY KEY,
    id_partido INT NOT NULL,
    numero_juego INT NOT NULL,
    goles_equipo1 INT NOT NULL,
    goles_equipo2 INT NOT NULL,
    duracion_segundos INT,
    FOREIGN KEY (id_partido) REFERENCES PARTIDO(id_partido)
);

CREATE TABLE ESTADISTICAS_JUGADOR (
    id_estadistica INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT NOT NULL,
    id_partido INT NOT NULL,
    goles INT DEFAULT 0,
    asistencias INT DEFAULT 0,
    salvadas INT DEFAULT 0,
    tiros INT DEFAULT 0,
    mvp BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_jugador) REFERENCES JUGADOR(id_jugador),
    FOREIGN KEY (id_partido) REFERENCES PARTIDO(id_partido)
);

CREATE TABLE PUNTOS_RLCS (
    id_puntos INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    id_temporada INT NOT NULL,
    puntos_regionals INT DEFAULT 0,
    puntos_majors INT DEFAULT 0,
    puntos_totales INT DEFAULT 0,
    FOREIGN KEY (id_equipo) REFERENCES EQUIPO(id_equipo),
    FOREIGN KEY (id_temporada) REFERENCES TEMPORADA(id_temporada)
);

CREATE TABLE BRACKET (
    id_bracket INT AUTO_INCREMENT PRIMARY KEY,
    id_torneo INT NOT NULL,
    tipo_bracket VARCHAR(50) NOT NULL,
    ronda VARCHAR(50) NOT NULL,
    fase VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_torneo) REFERENCES TORNEO(id_torneo)
);

-- ÍNDICES --

-- Índices en tabla "equipo" --
CREATE INDEX idx_equipo_region ON EQUIPO(id_region);
CREATE INDEX idx_equipo_activo ON EQUIPO(activo);

-- Índices en tabla "roster" --
CREATE INDEX idx_roster_equipo ON ROSTER(id_equipo);
CREATE INDEX idx_roster_jugador ON ROSTER(id_jugador);
CREATE INDEX idx_roster_fechas ON ROSTER(fecha_inicio, fecha_fin);

-- Índice en tabla "torneo" --
CREATE INDEX idx_torneo_temporada ON TORNEO(id_temporada);
CREATE INDEX idx_torneo_tipo ON TORNEO(tipo);

-- Índice en tabla "partido" --
CREATE INDEX idx_partido_torneo ON PARTIDO(id_torneo);
CREATE INDEX idx_partido_equipos ON PARTIDO(id_equipo1, id_equipo2);
CREATE INDEX idx_partido_fecha ON PARTIDO(fecha_hora);

-- Índice en tabla "juego" --
CREATE INDEX idx_juego_partido ON JUEGO(id_partido);

-- Índice en tabla "estadisticas_jugador" --
CREATE INDEX idx_stats_jugador ON ESTADISTICAS_JUGADOR(id_jugador);
CREATE INDEX idx_stats_partido ON ESTADISTICAS_JUGADOR(id_partido);

-- Índices en tabla "puntos_rlcs" --
CREATE INDEX idx_puntos_equipo ON PUNTOS_RLCS(id_equipo);
CREATE INDEX idx_puntos_temporada ON PUNTOS_RLCS(id_temporada);

-- Índices en tabla "participación" --
CREATE INDEX idx_participacion_equipo ON PARTICIPACION(id_equipo);
CREATE INDEX idx_participacion_torneo ON PARTICIPACION(id_torneo);