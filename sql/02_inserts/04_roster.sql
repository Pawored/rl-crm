-- =============================================
-- ROSTER — 144 jugadores + transferencias RLCS 2023-24
-- Jugadores activos (sin fecha_fin): 139 entradas
-- Salidas y traspasos mid-season: 5 + 1 nueva entrada
-- =============================================

-- -----------------------------------------------
-- ROSTER ACTIVOS (fecha_fin = NULL = siguen activos)
-- -----------------------------------------------
INSERT INTO ROSTER (id_equipo, id_jugador, fecha_inicio, titular) VALUES
-- NA — G2 Esports (1) → jugadores 1-3
(1, 1,  '2023-09-06', TRUE),
(1, 2,  '2023-09-06', TRUE),
(1, 3,  '2023-11-01', TRUE),   -- Jessie fichado en noviembre
-- NA — NRG Esports (2) → jugadores 4-5 (Chicago fichado pero saldrá en enero)
(2, 4,  '2023-09-06', TRUE),
(2, 5,  '2023-09-06', TRUE),
-- NA — Team Liquid (3) → jugadores 7-9
(3, 7,  '2023-09-06', TRUE),
(3, 8,  '2023-09-06', TRUE),
(3, 9,  '2023-09-06', TRUE),
-- NA — Evil Geniuses (4) → jugadores 10-12
(4, 10, '2023-09-06', TRUE),
(4, 11, '2023-09-06', TRUE),
(4, 12, '2023-09-06', TRUE),
-- NA — FaZe Clan (5) → jugadores 13, 14-15 (Allushin saldrá en junio)
(5, 14, '2023-09-06', TRUE),
(5, 15, '2023-09-06', TRUE),
-- NA — Cloud9 (6) → jugadores 16-18 (Retals saldrá en enero, Al0t continúa)
(6, 16, '2023-09-06', TRUE),
(6, 18, '2023-09-06', TRUE),
-- NA — Spacestation Gaming (7) → jugadores 19-21
(7, 19, '2023-09-06', TRUE),
(7, 20, '2023-09-06', TRUE),
(7, 21, '2023-09-06', TRUE),
-- NA — Shopify Rebellion (8) → jugadores 22-24
(8, 22, '2023-09-06', TRUE),
(8, 23, '2023-09-06', TRUE),
(8, 24, '2023-09-06', TRUE),
-- EU — Team Vitality (9) → jugadores 25-27
(9,  25, '2023-09-06', TRUE),
(9,  26, '2023-09-06', TRUE),
(9,  27, '2023-09-06', TRUE),
-- EU — Karmine Corp (10) → jugadores 28-30
(10, 28, '2023-09-06', TRUE),
(10, 29, '2023-09-06', TRUE),
(10, 30, '2023-09-06', TRUE),
-- EU — Team BDS (11) → jugadores 31-33
(11, 31, '2023-09-06', TRUE),
(11, 32, '2023-09-06', TRUE),
(11, 33, '2023-09-06', TRUE),
-- EU — Gentle Mates (12) → jugadores 34-36
(12, 34, '2023-09-06', TRUE),
(12, 35, '2023-09-06', TRUE),
(12, 36, '2023-09-06', TRUE),
-- EU — Quadrant (13) → jugadores 37-38 (Kaydop saldrá en mayo, Turbopolsa continúa)
(13, 37, '2023-09-06', TRUE),
(13, 39, '2023-09-06', TRUE),
-- EU — Oxygen Esports (14) → jugadores 40-42
(14, 40, '2023-09-06', TRUE),
(14, 41, '2023-09-06', TRUE),
(14, 42, '2023-09-06', TRUE),
-- EU — Wolves Esports (15) → jugadores 43-45
(15, 43, '2023-09-06', TRUE),
(15, 44, '2023-09-06', TRUE),
(15, 45, '2023-09-06', TRUE),
-- EU — Team Queso (16) → jugadores 46-48
(16, 46, '2023-09-06', TRUE),
(16, 47, '2023-09-06', TRUE),
(16, 48, '2023-09-06', TRUE),
-- SAM — Furia Esports (17) → jugadores 49-51
(17, 49, '2023-09-06', TRUE),
(17, 50, '2023-09-06', TRUE),
(17, 51, '2023-09-06', TRUE),
-- SAM — KRÜ Esports (18) → jugadores 52-54
(18, 52, '2023-09-06', TRUE),
(18, 53, '2023-09-06', TRUE),
(18, 54, '2023-09-06', TRUE),
-- SAM — Team Liquid SAM (19) → jugadores 55-56 (Nolz saldrá en abril)
(19, 55, '2023-09-06', TRUE),
(19, 56, '2023-09-06', TRUE),
-- SAM — Los Grandes (20) → jugadores 58-60
(20, 58, '2023-09-06', TRUE),
(20, 59, '2023-09-06', TRUE),
(20, 60, '2023-09-06', TRUE),
-- SAM — Elevate (21) → jugadores 61-63
(21, 61, '2023-09-06', TRUE),
(21, 62, '2023-09-06', TRUE),
(21, 63, '2023-09-06', TRUE),
-- SAM — Fluxo (22) → jugadores 64-66
(22, 64, '2023-09-06', TRUE),
(22, 65, '2023-09-06', TRUE),
(22, 66, '2023-09-06', TRUE),
-- SAM — 9z Team (23) → jugadores 67-69
(23, 67, '2023-09-06', TRUE),
(23, 68, '2023-09-06', TRUE),
(23, 69, '2023-09-06', TRUE),
-- SAM — Wildcard Gaming (24) → jugadores 70-72
(24, 70, '2023-09-06', TRUE),
(24, 71, '2023-09-06', TRUE),
(24, 72, '2023-09-06', TRUE),
-- MENA — Team Falcons (25) → jugadores 73-75
(25, 73, '2023-09-06', TRUE),
(25, 74, '2023-09-06', TRUE),
(25, 75, '2023-09-06', TRUE),
-- MENA — Twisted Minds (26) → jugadores 76-78
(26, 76, '2023-09-06', TRUE),
(26, 77, '2023-09-06', TRUE),
(26, 78, '2023-09-06', TRUE),
-- MENA — Sandrock Gaming (27) → jugadores 79-81
(27, 79, '2023-09-06', TRUE),
(27, 80, '2023-09-06', TRUE),
(27, 81, '2023-09-06', TRUE),
-- MENA — Desert Aces (28) → jugadores 82-84
(28, 82, '2023-09-06', TRUE),
(28, 83, '2023-09-06', TRUE),
(28, 84, '2023-09-06', TRUE),
-- MENA — BD Rejects (29) → jugadores 85-87
(29, 85, '2023-09-06', TRUE),
(29, 86, '2023-09-06', TRUE),
(29, 87, '2023-09-06', TRUE),
-- MENA — Section (30) → jugadores 88-90
(30, 88, '2023-09-06', TRUE),
(30, 89, '2023-09-06', TRUE),
(30, 90, '2023-09-06', TRUE),
-- MENA — Ultimatum (31) → jugadores 91-93
(31, 91, '2023-09-06', TRUE),
(31, 92, '2023-09-06', TRUE),
(31, 93, '2023-09-06', TRUE),
-- MENA — Galaxy Racer (32) → jugadores 94-96
(32, 94, '2023-09-06', TRUE),
(32, 95, '2023-09-06', TRUE),
(32, 96, '2023-09-06', TRUE),
-- OCE — Ground Zero Gaming (33) → jugadores 97-99
(33, 97,  '2023-09-06', TRUE),
(33, 98,  '2023-09-06', TRUE),
(33, 99,  '2023-09-06', TRUE),
-- OCE — Dire Wolves (34) → jugadores 100-102
(34, 100, '2023-09-06', TRUE),
(34, 101, '2023-09-06', TRUE),
(34, 102, '2023-09-06', TRUE),
-- OCE — Renegades (35) → jugadores 103-105
(35, 103, '2023-09-06', TRUE),
(35, 104, '2023-09-06', TRUE),
(35, 105, '2023-09-06', TRUE),
-- OCE — Pioneers (36) → jugadores 106-108
(36, 106, '2023-09-06', TRUE),
(36, 107, '2023-09-06', TRUE),
(36, 108, '2023-09-06', TRUE),
-- OCE — Gravitas (37) → jugadores 109-111
(37, 109, '2023-09-06', TRUE),
(37, 110, '2023-09-06', TRUE),
(37, 111, '2023-09-06', TRUE),
-- OCE — Tainted Minds (38) → jugadores 112-114
(38, 112, '2023-09-06', TRUE),
(38, 113, '2023-09-06', TRUE),
(38, 114, '2023-09-06', TRUE),
-- OCE — Chiefs Esports Club (39) → jugadores 115-117
(39, 115, '2023-09-06', TRUE),
(39, 116, '2023-09-06', TRUE),
(39, 117, '2023-09-06', TRUE),
-- OCE — Greasy Monkeys (40) → jugadores 118-120
(40, 118, '2023-09-06', TRUE),
(40, 119, '2023-09-06', TRUE),
(40, 120, '2023-09-06', TRUE),
-- SSKC — Bravado Gaming (41) → jugadores 121-123
(41, 121, '2023-09-06', TRUE),
(41, 122, '2023-09-06', TRUE),
(41, 123, '2023-09-06', TRUE),
-- SSKC — Wygers Korea (42) → jugadores 124-126
(42, 124, '2023-09-06', TRUE),
(42, 125, '2023-09-06', TRUE),
(42, 126, '2023-09-06', TRUE),
-- SSKC — RedFace (43) → jugadores 127-129
(43, 127, '2023-09-06', TRUE),
(43, 128, '2023-09-06', TRUE),
(43, 129, '2023-09-06', TRUE),
-- SSKC — RunAway (44) → jugadores 130-132
(44, 130, '2023-09-06', TRUE),
(44, 131, '2023-09-06', TRUE),
(44, 132, '2023-09-06', TRUE),
-- SSKC — Ubuntu (45) → jugadores 133-135
(45, 133, '2023-09-06', TRUE),
(45, 134, '2023-09-06', TRUE),
(45, 135, '2023-09-06', TRUE),
-- SSKC — T1 (46) → jugadores 136-138
(46, 136, '2023-09-06', TRUE),
(46, 137, '2023-09-06', TRUE),
(46, 138, '2023-09-06', TRUE),
-- SSKC — Elite 9 (47) → jugadores 139-141
(47, 139, '2023-09-06', TRUE),
(47, 140, '2023-09-06', TRUE),
(47, 141, '2023-09-06', TRUE),
-- SSKC — Gen.G (48) → jugadores 142-144
(48, 142, '2023-09-06', TRUE),
(48, 143, '2023-09-06', TRUE),
(48, 144, '2023-09-06', TRUE);

-- -----------------------------------------------
-- SALIDAS MID-SEASON RLCS 2023-24
-- Jugadores que salieron durante la temporada 2024
-- -----------------------------------------------
INSERT INTO ROSTER (id_equipo, id_jugador, fecha_inicio, fecha_fin, titular) VALUES
-- Chicago (id=6) sale de NRG antes del Split 1
(2,  6,  '2023-09-06', '2024-01-20', TRUE),
-- Retals (id=17) sale de Cloud9, ficha por NRG
(6,  17, '2023-09-06', '2024-01-20', TRUE),
-- Kaydop (id=38) se retira de Quadrant tras el Major 1
(13, 38, '2023-09-06', '2024-05-15', TRUE),
-- Allushin (id=13) sale de FaZe tras el Major 2
(5,  13, '2023-09-06', '2024-06-01', TRUE),
-- Nolz (id=57) sale de Team Liquid SAM (Split 2)
(19, 57, '2023-09-06', '2024-04-01', TRUE);

-- -----------------------------------------------
-- NUEVAS INCORPORACIONES RLCS 2024
-- -----------------------------------------------
INSERT INTO ROSTER (id_equipo, id_jugador, fecha_inicio, titular) VALUES
-- Retals (id=17) ficha por NRG Esports para el Split 1 de RLCS 2024
(2, 17, '2024-01-25', TRUE);
