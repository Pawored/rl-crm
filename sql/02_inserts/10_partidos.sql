USE RLCS;

-- ================================================================
-- PARTIDO — 31 partidos en 5 torneos
--
-- Torneos usados:
--   id=1  → NA Regional 2022-23
--   id=2  → EU Regional 2022-23
--   id=7  → Major Múnich 2022-23
--   id=9  → NA Split 1 Regional 2024
--   id=10 → EU Split 1 Regional 2024
--
-- Equipos por región (id_equipo):
--   NA:  G2(1) NRG(2) TL(3) EG(4) FaZe(5) C9(6) SSG(7) SR(8)
--   EU:  VIT(9) KC(10) BDS(11) GM(12) QUAD(13) OXG(14) WLV(15) QESO(16)
-- ================================================================

INSERT INTO PARTIDO (id_torneo, id_equipo1, id_equipo2, id_ganador, fecha_hora, formato) VALUES
-- EU Regional 2022-23 (id_torneo=2) — QF / SF / Final
( 2,  9, 16,  9, '2022-11-04 14:00:00', 'Bo5'),  --  1  VIT vs QESO  3-1 → VIT
( 2, 10, 15, 10, '2022-11-04 16:00:00', 'Bo5'),  --  2  KC  vs WLV   3-1 → KC
( 2, 11, 14, 11, '2022-11-04 18:00:00', 'Bo5'),  --  3  BDS vs OXG   3-2 → BDS
( 2, 12, 13, 12, '2022-11-04 20:00:00', 'Bo5'),  --  4  GM  vs QUAD  3-2 → GM
( 2,  9, 12,  9, '2022-11-05 14:00:00', 'Bo5'),  --  5  VIT vs GM    3-0 → VIT
( 2, 10, 11, 11, '2022-11-05 17:00:00', 'Bo5'),  --  6  KC  vs BDS   2-3 → BDS
( 2,  9, 11,  9, '2022-11-06 17:00:00', 'Bo7'),  --  7  VIT vs BDS   4-2 → VIT
-- NA Regional 2022-23 (id_torneo=1) — QF / SF / Final
( 1,  2,  6,  2, '2022-11-11 14:00:00', 'Bo5'),  --  8  NRG vs C9    3-0 → NRG
( 1,  1,  8,  1, '2022-11-11 16:00:00', 'Bo5'),  --  9  G2  vs SR    3-1 → G2
( 1,  3,  5,  3, '2022-11-11 18:00:00', 'Bo5'),  -- 10  TL  vs FaZe  3-2 → TL
( 1,  7,  4,  7, '2022-11-11 20:00:00', 'Bo5'),  -- 11  SSG vs EG    3-1 → SSG
( 1,  2,  7,  2, '2022-11-12 14:00:00', 'Bo5'),  -- 12  NRG vs SSG   3-1 → NRG
( 1,  1,  3,  1, '2022-11-12 17:00:00', 'Bo5'),  -- 13  G2  vs TL    3-2 → G2
( 1,  2,  1,  2, '2022-11-13 17:00:00', 'Bo7'),  -- 14  NRG vs G2    4-2 → NRG
-- Major Múnich 2022-23 (id_torneo=7) — QF / SF / Final
( 7,  9,  7,  9, '2023-02-23 14:00:00', 'Bo5'),  -- 15  VIT vs SSG   3-1 → VIT
( 7,  2, 11,  2, '2023-02-23 17:00:00', 'Bo5'),  -- 16  NRG vs BDS   3-2 → NRG
( 7, 10,  1, 10, '2023-02-23 19:00:00', 'Bo5'),  -- 17  KC  vs G2    3-0 → KC
( 7,  9,  2,  9, '2023-02-25 14:00:00', 'Bo5'),  -- 18  VIT vs NRG   3-1 → VIT
( 7, 10, 11, 11, '2023-02-25 17:00:00', 'Bo5'),  -- 19  KC  vs BDS   2-3 → BDS
( 7,  9, 11,  9, '2023-02-26 17:00:00', 'Bo7'),  -- 20  VIT vs BDS   4-1 → VIT
-- EU Split 1 Regional 2024 (id_torneo=10) — QF / SF / Final
(10, 11,  9, 11, '2024-03-08 14:00:00', 'Bo5'),  -- 21  BDS vs VIT   3-2 → BDS
(10, 10, 12, 10, '2024-03-08 16:00:00', 'Bo5'),  -- 22  KC  vs GM    3-1 → KC
(10,  9, 13,  9, '2024-03-08 18:00:00', 'Bo5'),  -- 23  VIT vs QUAD  3-0 → VIT
(10, 10, 11, 10, '2024-03-09 15:00:00', 'Bo5'),  -- 24  KC  vs BDS   3-1 → KC
(10,  9, 12,  9, '2024-03-09 18:00:00', 'Bo5'),  -- 25  VIT vs GM    3-2 → VIT
(10, 10,  9, 10, '2024-03-10 17:00:00', 'Bo7'),  -- 26  KC  vs VIT   4-2 → KC
-- NA Split 1 Regional 2024 (id_torneo=9) — QF / SF / Final
( 9,  2,  4,  2, '2024-03-15 14:00:00', 'Bo5'),  -- 27  NRG vs EG    3-0 → NRG
( 9,  1,  5,  1, '2024-03-15 16:00:00', 'Bo5'),  -- 28  G2  vs FaZe  3-1 → G2
( 9,  2,  7,  2, '2024-03-16 14:00:00', 'Bo5'),  -- 29  NRG vs SSG   3-0 → NRG
( 9,  1,  3,  1, '2024-03-16 17:00:00', 'Bo5'),  -- 30  G2  vs TL    3-1 → G2
( 9,  2,  1,  2, '2024-03-17 17:00:00', 'Bo7');  -- 31  NRG vs G2    4-3 → NRG


-- ================================================================
-- JUEGO — Marcadores juego a juego
-- goles_equipo1/2 corresponden a id_equipo1/2 del PARTIDO
-- ================================================================

INSERT INTO JUEGO (id_partido, numero_juego, goles_equipo1, goles_equipo2, duracion_segundos) VALUES
-- P1  VIT vs QESO, VIT 3-1
(1,1, 3,1,300),(1,2, 2,1,300),(1,3, 1,2,307),(1,4, 3,2,305),
-- P2  KC vs WLV, KC 3-1
(2,1, 2,1,300),(2,2, 3,1,300),(2,3, 1,2,300),(2,4, 3,1,300),
-- P3  BDS vs OXG, BDS 3-2
(3,1, 2,1,300),(3,2, 1,2,300),(3,3, 3,2,311),(3,4, 2,3,308),(3,5, 2,1,328),
-- P4  GM vs QUAD, GM 3-2
(4,1, 2,1,300),(4,2, 0,2,300),(4,3, 3,2,300),(4,4, 1,2,302),(4,5, 3,2,321),
-- P5  VIT vs GM, VIT 3-0
(5,1, 3,0,300),(5,2, 2,1,300),(5,3, 3,1,300),
-- P6  KC vs BDS, BDS 3-2 (KC=eq1, BDS=eq2)
(6,1, 2,1,300),(6,2, 1,3,300),(6,3, 1,2,300),(6,4, 3,2,316),(6,5, 1,2,300),
-- P7  VIT vs BDS, VIT 4-2 (Bo7)
(7,1, 3,1,300),(7,2, 1,3,300),(7,3, 2,0,300),(7,4, 0,3,300),(7,5, 3,2,321),(7,6, 2,1,318),
-- P8  NRG vs C9, NRG 3-0
(8,1, 3,0,300),(8,2, 2,1,300),(8,3, 3,1,300),
-- P9  G2 vs SR, G2 3-1
(9,1, 3,1,300),(9,2, 2,1,300),(9,3, 1,2,300),(9,4, 2,1,300),
-- P10 TL vs FaZe, TL 3-2
(10,1, 2,1,300),(10,2, 1,2,300),(10,3, 2,0,300),(10,4, 2,3,308),(10,5, 3,2,325),
-- P11 SSG vs EG, SSG 3-1
(11,1, 2,1,300),(11,2, 3,1,300),(11,3, 0,2,300),(11,4, 3,2,316),
-- P12 NRG vs SSG, NRG 3-1
(12,1, 3,0,300),(12,2, 2,1,300),(12,3, 1,2,300),(12,4, 2,1,300),
-- P13 G2 vs TL, G2 3-2
(13,1, 2,1,300),(13,2, 2,3,313),(13,3, 2,0,300),(13,4, 1,2,300),(13,5, 3,2,325),
-- P14 NRG vs G2, NRG 4-2 (Bo7)
(14,1, 3,1,300),(14,2, 1,2,300),(14,3, 2,1,300),(14,4, 2,1,300),(14,5, 1,3,308),(14,6, 2,1,344),
-- P15 VIT vs SSG, VIT 3-1
(15,1, 2,0,300),(15,2, 3,1,300),(15,3, 1,2,300),(15,4, 3,1,300),
-- P16 NRG vs BDS, NRG 3-2
(16,1, 2,1,300),(16,2, 1,2,300),(16,3, 3,2,317),(16,4, 2,3,310),(16,5, 2,1,322),
-- P17 KC vs G2, KC 3-0
(17,1, 3,1,300),(17,2, 2,0,300),(17,3, 3,1,300),
-- P18 VIT vs NRG, VIT 3-1
(18,1, 2,1,300),(18,2, 3,1,300),(18,3, 1,2,300),(18,4, 3,2,309),
-- P19 KC vs BDS, BDS 3-2 (KC=eq1, BDS=eq2)
(19,1, 2,1,300),(19,2, 1,2,300),(19,3, 2,3,300),(19,4, 3,2,315),(19,5, 1,2,300),
-- P20 VIT vs BDS, VIT 4-1 (Bo7)
(20,1, 2,1,300),(20,2, 3,1,300),(20,3, 2,0,300),(20,4, 2,3,328),(20,5, 2,1,300),
-- P21 BDS vs VIT, BDS 3-2 (BDS=eq1, VIT=eq2)
(21,1, 2,1,300),(21,2, 1,2,300),(21,3, 3,2,307),(21,4, 2,3,300),(21,5, 2,1,318),
-- P22 KC vs GM, KC 3-1
(22,1, 2,1,300),(22,2, 3,0,300),(22,3, 1,2,300),(22,4, 2,1,300),
-- P23 VIT vs QUAD, VIT 3-0
(23,1, 3,1,300),(23,2, 2,0,300),(23,3, 3,1,300),
-- P24 KC vs BDS, KC 3-1
(24,1, 2,1,300),(24,2, 3,1,300),(24,3, 1,2,300),(24,4, 2,1,300),
-- P25 VIT vs GM, VIT 3-2
(25,1, 2,1,300),(25,2, 1,2,300),(25,3, 3,2,311),(25,4, 1,3,300),(25,5, 2,0,300),
-- P26 KC vs VIT, KC 4-2 (Bo7)
(26,1, 3,1,300),(26,2, 1,2,300),(26,3, 2,1,300),(26,4, 2,3,322),(26,5, 2,0,300),(26,6, 2,1,300),
-- P27 NRG vs EG, NRG 3-0
(27,1, 3,0,300),(27,2, 2,1,300),(27,3, 3,1,300),
-- P28 G2 vs FaZe, G2 3-1
(28,1, 2,1,300),(28,2, 3,2,309),(28,3, 1,2,300),(28,4, 3,1,300),
-- P29 NRG vs SSG, NRG 3-0
(29,1, 2,0,300),(29,2, 3,1,300),(29,3, 2,1,300),
-- P30 G2 vs TL, G2 3-1
(30,1, 3,1,300),(30,2, 2,1,300),(30,3, 1,2,300),(30,4, 2,0,300),
-- P31 NRG vs G2, NRG 4-3 (Bo7)
(31,1, 2,1,300),(31,2, 1,2,300),(31,3, 3,2,319),(31,4, 1,3,308),
(31,5, 2,0,300),(31,6, 1,2,300),(31,7, 2,1,347);


-- ================================================================
-- ESTADISTICAS_JUGADOR — 186 filas (6 jugadores × 31 partidos)
--
-- Jugadores por equipo (id_jugador):
--   G2(1):   jstn(1)  Mist(2)  Jessie(3)
--   NRG(2):  GarrettG(4)  Squishy(5)  Chicago(6)
--   TL(3):   Comm(7)  Maaack(8)  Taroco(9)
--   EG(4):   Firstkiller(10)  Chronic(11)  ApparentlyJack(12)
--   FaZe(5): Allushin(13)  Dappur(14)  Jknaps(15)
--   C9(6):   Deol(16)  Retals(17)  Al0t(18)
--   SSG(7):  Arsenal(19)  Sadjunior(20)  Insolences(21)
--   SR(8):   Lj(22)  Lawler(23)  Klassux(24)
--   VIT(9):  Zen(25)  Alpha54(26)  Radosin(27)
--   KC(10):  Vatira(28)  bluejays(29)  Eyignoc(30)
--   BDS(11): Extra(31)  Achieves(32)  M0nkey M00n(33)
--   GM(12):  Marc_by_8(34)  Arju(35)  Atow(36)
--   QUAD(13):Scrub Killa(37)  Kaydop(38)  Turbopolsa(39)
--   OXG(14): JoreuzEU(40)  Deevo(41)  Noly(42)
--   WLV(15): Bluey(43)  Ryscu(44)  Frankkk(45)
--   QESO(16):DmentZa(46)  Caard(47)  Tinizine(48)
-- ================================================================

INSERT INTO ESTADISTICAS_JUGADOR (id_jugador, id_partido, goles, asistencias, salvadas, tiros, mvp) VALUES
-- ── P1: VIT vs QESO, VIT wins 3-1 ──────────────────────────────
(25, 1, 4, 2,  7, 18, TRUE ),  -- Zen       VIT  MVP
(26, 1, 4, 3,  5, 16, FALSE),  -- Alpha54   VIT
(27, 1, 2, 4, 12,  9, FALSE),  -- Radosin   VIT
(46, 1, 2, 2,  8, 11, FALSE),  -- DmentZa   QESO
(47, 1, 2, 1,  6, 10, FALSE),  -- Caard     QESO
(48, 1, 1, 2,  9,  7, FALSE),  -- Tinizine  QESO
-- ── P2: KC vs WLV, KC wins 3-1 ─────────────────────────────────
(28, 2, 5, 2,  6, 20, TRUE ),  -- Vatira    KC   MVP
(29, 2, 3, 3,  4, 15, FALSE),  -- bluejays  KC
(30, 2, 2, 4, 11,  8, FALSE),  -- Eyignoc   KC
(43, 2, 3, 2,  7, 12, FALSE),  -- Bluey     WLV
(44, 2, 1, 2,  5,  9, FALSE),  -- Ryscu     WLV
(45, 2, 1, 1, 10,  6, FALSE),  -- Frankkk   WLV
-- ── P3: BDS vs OXG, BDS wins 3-2 ───────────────────────────────
(31, 3, 4, 3,  8, 16, TRUE ),  -- Extra      BDS  MVP
(32, 3, 4, 4,  7, 15, FALSE),  -- Achieves   BDS
(33, 3, 3, 5, 10, 12, FALSE),  -- M0nkey M00n BDS
(40, 3, 4, 2,  7, 15, FALSE),  -- JoreuzEU   OXG
(41, 3, 3, 3,  9, 12, FALSE),  -- Deevo      OXG
(42, 3, 2, 3, 11,  8, FALSE),  -- Noly       OXG
-- ── P4: GM vs QUAD, GM wins 3-2 ────────────────────────────────
(34, 4, 4, 3,  8, 16, TRUE ),  -- Marc_by_8   GM   MVP
(35, 4, 4, 3,  6, 14, FALSE),  -- Arju        GM
(36, 4, 3, 4, 12,  9, FALSE),  -- Atow        GM
(37, 4, 4, 2,  7, 16, FALSE),  -- Scrub Killa QUAD
(38, 4, 3, 3,  5, 14, FALSE),  -- Kaydop      QUAD
(39, 4, 2, 4, 11,  8, FALSE),  -- Turbopolsa  QUAD
-- ── P5: VIT vs GM, VIT wins 3-0 ────────────────────────────────
(25, 5, 4, 2,  4, 16, TRUE ),  -- Zen      VIT  MVP
(26, 5, 3, 2,  3, 13, FALSE),  -- Alpha54  VIT
(27, 5, 1, 3, 10,  7, FALSE),  -- Radosin  VIT
(34, 5, 1, 1,  7,  8, FALSE),  -- Marc_by_8 GM
(35, 5, 1, 1,  5,  7, FALSE),  -- Arju     GM
(36, 5, 1, 1,  9,  5, FALSE),  -- Atow     GM
-- ── P6: KC vs BDS, BDS wins 3-2 ────────────────────────────────
(28, 6, 5, 2,  6, 19, FALSE),  -- Vatira      KC
(29, 6, 3, 3,  5, 14, FALSE),  -- bluejays    KC
(30, 6, 1, 4, 13,  7, FALSE),  -- Eyignoc     KC
(31, 6, 5, 3,  7, 18, TRUE ),  -- Extra       BDS  MVP
(32, 6, 3, 4,  8, 13, FALSE),  -- Achieves    BDS
(33, 6, 3, 4, 11, 10, FALSE),  -- M0nkey M00n BDS
-- ── P7: VIT vs BDS, VIT wins 4-2 (Bo7) ─────────────────────────
(25, 7, 6, 3,  9, 24, TRUE ),  -- Zen         VIT  MVP
(26, 7, 5, 4,  7, 20, FALSE),  -- Alpha54     VIT
(27, 7, 3, 6, 16, 11, FALSE),  -- Radosin     VIT
(31, 7, 5, 3,  8, 20, FALSE),  -- Extra       BDS
(32, 7, 4, 4,  9, 16, FALSE),  -- Achieves    BDS
(33, 7, 2, 5, 14, 11, FALSE),  -- M0nkey M00n BDS
-- ── P8: NRG vs C9, NRG wins 3-0 ────────────────────────────────
( 4, 8, 4, 2,  3, 16, TRUE ),  -- GarrettG  NRG  MVP
( 5, 8, 3, 3,  4, 14, FALSE),  -- Squishy   NRG
( 6, 8, 1, 3,  9,  8, FALSE),  -- Chicago   NRG
(16, 8, 1, 1,  7,  8, FALSE),  -- Deol      C9
(17, 8, 1, 0,  6,  7, FALSE),  -- Retals    C9
(18, 8, 0, 1,  8,  4, FALSE),  -- Al0t      C9
-- ── P9: G2 vs SR, G2 wins 3-1 ──────────────────────────────────
( 1, 9, 5, 2,  6, 19, TRUE ),  -- jstn    G2  MVP
( 2, 9, 3, 3,  5, 14, FALSE),  -- Mist    G2
( 3, 9, 2, 4, 10,  9, FALSE),  -- Jessie  G2
(22, 9, 3, 1,  7, 13, FALSE),  -- Lj      SR
(23, 9, 1, 2,  5,  9, FALSE),  -- Lawler  SR
(24, 9, 1, 2,  9,  7, FALSE),  -- Klassux SR
-- ── P10: TL vs FaZe, TL wins 3-2 ───────────────────────────────
( 7,10, 4, 3,  7, 15, FALSE),  -- Comm     TL
( 8,10, 4, 3,  8, 15, TRUE ),  -- Maaack   TL  MVP
( 9,10, 3, 4, 11, 10, FALSE),  -- Taroco   TL
(13,10, 4, 2,  7, 15, FALSE),  -- Allushin FaZe
(14,10, 3, 3,  6, 13, FALSE),  -- Dappur   FaZe
(15,10, 2, 3, 10,  9, FALSE),  -- Jknaps   FaZe
-- ── P11: SSG vs EG, SSG wins 3-1 ───────────────────────────────
(19,11, 5, 2,  5, 18, TRUE ),  -- Arsenal       SSG  MVP
(20,11, 3, 3,  4, 13, FALSE),  -- Sadjunior     SSG
(21,11, 2, 4, 11,  8, FALSE),  -- Insolences    SSG
(10,11, 3, 1,  7, 13, FALSE),  -- Firstkiller   EG
(11,11, 1, 2,  6,  9, FALSE),  -- Chronic       EG
(12,11, 1, 2,  9,  6, FALSE),  -- ApparentlyJack EG
-- ── P12: NRG vs SSG, NRG wins 3-1 ──────────────────────────────
( 4,12, 5, 2,  5, 18, TRUE ),  -- GarrettG   NRG  MVP
( 5,12, 3, 3,  4, 14, FALSE),  -- Squishy    NRG
( 6,12, 2, 4, 10,  8, FALSE),  -- Chicago    NRG
(19,12, 3, 1,  7, 13, FALSE),  -- Arsenal    SSG
(20,12, 1, 2,  5,  8, FALSE),  -- Sadjunior  SSG
(21,12, 1, 2,  9,  6, FALSE),  -- Insolences SSG
-- ── P13: G2 vs TL, G2 wins 3-2 ─────────────────────────────────
( 1,13, 5, 3,  8, 21, TRUE ),  -- jstn    G2  MVP
( 2,13, 4, 3,  6, 17, FALSE),  -- Mist    G2
( 3,13, 2, 5, 13, 10, FALSE),  -- Jessie  G2
( 7,13, 4, 2,  8, 14, FALSE),  -- Comm    TL
( 8,13, 3, 3,  7, 13, FALSE),  -- Maaack  TL
( 9,13, 2, 4, 12,  9, FALSE),  -- Taroco  TL
-- ── P14: NRG vs G2, NRG wins 4-2 (Bo7) ─────────────────────────
( 4,14, 6, 3, 10, 26, TRUE ),  -- GarrettG  NRG  MVP
( 5,14, 5, 4,  8, 21, FALSE),  -- Squishy   NRG
( 6,14, 3, 6, 16, 12, FALSE),  -- Chicago   NRG
( 1,14, 5, 3, 11, 22, FALSE),  -- jstn      G2
( 2,14, 4, 4,  9, 18, FALSE),  -- Mist      G2
( 3,14, 2, 5, 15, 11, FALSE),  -- Jessie    G2
-- ── P15: VIT vs SSG, VIT wins 3-1 (Major Múnich) ────────────────
(25,15, 5, 2,  6, 20, TRUE ),  -- Zen        VIT  MVP
(26,15, 3, 3,  5, 15, FALSE),  -- Alpha54    VIT
(27,15, 2, 4, 11,  8, FALSE),  -- Radosin    VIT
(19,15, 3, 1,  7, 13, FALSE),  -- Arsenal    SSG
(20,15, 1, 2,  6,  8, FALSE),  -- Sadjunior  SSG
(21,15, 1, 2,  9,  6, FALSE),  -- Insolences SSG
-- ── P16: NRG vs BDS, NRG wins 3-2 (Major Múnich) ────────────────
( 4,16, 5, 2,  7, 19, TRUE ),  -- GarrettG    NRG  MVP
( 5,16, 4, 3,  6, 16, FALSE),  -- Squishy     NRG
( 6,16, 2, 5, 12,  9, FALSE),  -- Chicago     NRG
(31,16, 4, 2,  8, 17, FALSE),  -- Extra       BDS
(32,16, 3, 3,  7, 14, FALSE),  -- Achieves    BDS
(33,16, 2, 4, 12,  9, FALSE),  -- M0nkey M00n BDS
-- ── P17: KC vs G2, KC wins 3-0 (Major Múnich) ───────────────────
(28,17, 4, 2,  3, 17, TRUE ),  -- Vatira    KC  MVP
(29,17, 3, 2,  4, 13, FALSE),  -- bluejays  KC
(30,17, 1, 3,  8,  7, FALSE),  -- Eyignoc   KC
( 1,17, 1, 1,  7,  9, FALSE),  -- jstn      G2
( 2,17, 1, 0,  5,  7, FALSE),  -- Mist      G2
( 3,17, 0, 1,  8,  4, FALSE),  -- Jessie    G2
-- ── P18: VIT vs NRG, VIT wins 3-1 (Major SF) ────────────────────
(25,18, 5, 2,  5, 20, TRUE ),  -- Zen       VIT  MVP
(26,18, 3, 3,  4, 14, FALSE),  -- Alpha54   VIT
(27,18, 2, 4, 11,  8, FALSE),  -- Radosin   VIT
( 4,18, 3, 1,  7, 13, FALSE),  -- GarrettG  NRG
( 5,18, 1, 2,  5,  9, FALSE),  -- Squishy   NRG
( 6,18, 1, 2,  9,  6, FALSE),  -- Chicago   NRG
-- ── P19: KC vs BDS, BDS wins 3-2 (Major) ───────────────────────
(28,19, 5, 2,  6, 20, FALSE),  -- Vatira      KC
(29,19, 3, 3,  4, 14, FALSE),  -- bluejays    KC
(30,19, 1, 4, 13,  7, FALSE),  -- Eyignoc     KC
(31,19, 5, 3,  7, 19, TRUE ),  -- Extra       BDS  MVP
(32,19, 4, 4,  8, 15, FALSE),  -- Achieves    BDS
(33,19, 2, 5, 12, 10, FALSE),  -- M0nkey M00n BDS
-- ── P20: VIT vs BDS, VIT wins 4-1 (Major Final) ─────────────────
(25,20, 5, 3,  6, 21, TRUE ),  -- Zen         VIT  MVP
(26,20, 5, 4,  5, 18, FALSE),  -- Alpha54     VIT
(27,20, 2, 6, 14, 10, FALSE),  -- Radosin     VIT
(31,20, 4, 2,  7, 17, FALSE),  -- Extra       BDS
(32,20, 3, 3,  6, 14, FALSE),  -- Achieves    BDS
(33,20, 1, 4, 13,  8, FALSE),  -- M0nkey M00n BDS
-- ── P21: BDS vs VIT, BDS wins 3-2 (EU 2024 QF) ─────────────────
(31,21, 5, 3,  7, 18, TRUE ),  -- Extra       BDS  MVP
(32,21, 4, 4,  7, 15, FALSE),  -- Achieves    BDS
(33,21, 2, 5, 12, 10, FALSE),  -- M0nkey M00n BDS
(25,21, 4, 3,  8, 18, FALSE),  -- Zen         VIT
(26,21, 3, 3,  6, 15, FALSE),  -- Alpha54     VIT
(27,21, 2, 4, 12,  9, FALSE),  -- Radosin     VIT
-- ── P22: KC vs GM, KC wins 3-1 (EU 2024 QF) ────────────────────
(28,22, 5, 2,  5, 19, TRUE ),  -- Vatira    KC  MVP
(29,22, 3, 3,  4, 14, FALSE),  -- bluejays  KC
(30,22, 2, 4, 10,  8, FALSE),  -- Eyignoc   KC
(34,22, 3, 1,  7, 12, FALSE),  -- Marc_by_8 GM
(35,22, 1, 2,  5,  9, FALSE),  -- Arju      GM
(36,22, 1, 2, 10,  6, FALSE),  -- Atow      GM
-- ── P23: VIT vs QUAD, VIT wins 3-0 (EU 2024 QF) ────────────────
(25,23, 4, 2,  3, 16, TRUE ),  -- Zen         VIT  MVP
(26,23, 3, 2,  3, 13, FALSE),  -- Alpha54     VIT
(27,23, 1, 3,  8,  6, FALSE),  -- Radosin     VIT
(37,23, 1, 1,  7,  8, FALSE),  -- Scrub Killa QUAD
(38,23, 1, 0,  5,  7, FALSE),  -- Kaydop      QUAD
(39,23, 0, 1,  7,  4, FALSE),  -- Turbopolsa  QUAD
-- ── P24: KC vs BDS, KC wins 3-1 (EU 2024 SF) ───────────────────
(28,24, 5, 2,  5, 19, TRUE ),  -- Vatira      KC  MVP
(29,24, 3, 3,  4, 14, FALSE),  -- bluejays    KC
(30,24, 2, 4, 10,  8, FALSE),  -- Eyignoc     KC
(31,24, 3, 1,  7, 13, FALSE),  -- Extra       BDS
(32,24, 1, 2,  5,  8, FALSE),  -- Achieves    BDS
(33,24, 1, 2,  9,  6, FALSE),  -- M0nkey M00n BDS
-- ── P25: VIT vs GM, VIT wins 3-2 (EU 2024 SF) ──────────────────
(25,25, 5, 3,  8, 20, TRUE ),  -- Zen       VIT  MVP
(26,25, 4, 3,  6, 16, FALSE),  -- Alpha54   VIT
(27,25, 2, 5, 13,  9, FALSE),  -- Radosin   VIT
(34,25, 4, 2,  7, 14, FALSE),  -- Marc_by_8 GM
(35,25, 3, 3,  6, 12, FALSE),  -- Arju      GM
(36,25, 2, 4, 12,  8, FALSE),  -- Atow      GM
-- ── P26: KC vs VIT, KC wins 4-2 (EU 2024 Final Bo7) ────────────
(28,26, 7, 3,  9, 27, TRUE ),  -- Vatira    KC  MVP
(29,26, 4, 4,  7, 20, FALSE),  -- bluejays  KC
(30,26, 3, 6, 16, 11, FALSE),  -- Eyignoc   KC
(25,26, 5, 3, 10, 22, FALSE),  -- Zen       VIT
(26,26, 4, 4,  8, 18, FALSE),  -- Alpha54   VIT
(27,26, 2, 5, 15, 10, FALSE),  -- Radosin   VIT
-- ── P27: NRG vs EG, NRG wins 3-0 (NA 2024 QF) ──────────────────
( 4,27, 4, 2,  3, 16, TRUE ),  -- GarrettG       NRG  MVP
( 5,27, 3, 2,  4, 13, FALSE),  -- Squishy        NRG
( 6,27, 1, 3,  9,  7, FALSE),  -- Chicago        NRG
(10,27, 1, 1,  7,  8, FALSE),  -- Firstkiller    EG
(11,27, 1, 0,  5,  7, FALSE),  -- Chronic        EG
(12,27, 0, 1,  8,  4, FALSE),  -- ApparentlyJack EG
-- ── P28: G2 vs FaZe, G2 wins 3-1 (NA 2024 QF) ──────────────────
( 1,28, 5, 2,  6, 19, TRUE ),  -- jstn    G2   MVP
( 2,28, 3, 3,  5, 14, FALSE),  -- Mist    G2
( 3,28, 2, 4, 10,  9, FALSE),  -- Jessie  G2
(13,28, 3, 1,  7, 13, FALSE),  -- Allushin FaZe
(14,28, 1, 2,  5,  9, FALSE),  -- Dappur   FaZe
(15,28, 1, 2,  9,  7, FALSE),  -- Jknaps   FaZe
-- ── P29: NRG vs SSG, NRG wins 3-0 (NA 2024 SF) ─────────────────
( 4,29, 4, 2,  3, 16, TRUE ),  -- GarrettG   NRG  MVP
( 5,29, 3, 2,  3, 14, FALSE),  -- Squishy    NRG
( 6,29, 1, 3,  9,  7, FALSE),  -- Chicago    NRG
(19,29, 1, 1,  7,  8, FALSE),  -- Arsenal    SSG
(20,29, 1, 0,  5,  7, FALSE),  -- Sadjunior  SSG
(21,29, 0, 1,  8,  4, FALSE),  -- Insolences SSG
-- ── P30: G2 vs TL, G2 wins 3-1 (NA 2024 SF) ────────────────────
( 1,30, 5, 2,  6, 19, TRUE ),  -- jstn    G2  MVP
( 2,30, 3, 3,  5, 14, FALSE),  -- Mist    G2
( 3,30, 2, 4, 10,  9, FALSE),  -- Jessie  G2
( 7,30, 3, 1,  7, 12, FALSE),  -- Comm    TL
( 8,30, 1, 2,  5,  8, FALSE),  -- Maaack  TL
( 9,30, 1, 2,  9,  6, FALSE),  -- Taroco  TL
-- ── P31: NRG vs G2, NRG wins 4-3 (NA 2024 Final Bo7) ────────────
( 4,31, 7, 3, 12, 28, TRUE ),  -- GarrettG  NRG  MVP
( 5,31, 6, 4, 10, 24, FALSE),  -- Squishy   NRG
( 6,31, 3, 7, 18, 13, FALSE),  -- Chicago   NRG
( 1,31, 7, 3, 12, 27, FALSE),  -- jstn      G2
( 2,31, 5, 4, 10, 22, FALSE),  -- Mist      G2
( 3,31, 2, 6, 16, 12, FALSE);  -- Jessie    G2
