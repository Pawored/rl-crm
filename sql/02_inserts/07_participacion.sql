-- =============================================
-- PARTICIPACION — ~272 entradas
-- Cubre todos los torneos de las 3 temporadas
--
-- Equipos por región (id_equipo):
-- NA:   G2(1) NRG(2) TL(3) EG(4) FaZe(5) C9(6) SSG(7) SR(8)
-- EU:   VIT(9) KC(10) BDS(11) GM(12) QUAD(13) OXG(14) WLV(15) QESO(16)
-- SAM:  FURIA(17) KRÜ(18) TL-SAM(19) LG(20) ELV(21) FLX(22) 9Z(23) WILD(24)
-- MENA: FAL(25) TM(26) SRG(27) DA(28) BDR(29) SEC(30) ULT(31) GR(32)
-- OCE:  GZG(33) DW(34) RNG(35) PIO(36) GRV(37) TMD(38) CHIEF(39) GRSM(40)
-- SSKC: BRVD(41) WYG(42) RF(43) RNA(44) UBU(45) T1(46) E9(47) GEN(48)
-- =============================================

-- ================================================================
-- RLCS 2022-23
-- ================================================================

-- id_torneo=1: NA Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(1, 2, 150, 1),  -- NRG
(1, 1, 125, 2),  -- G2
(1, 3,  95, 3),  -- Team Liquid NA
(1, 7,  95, 4),  -- SSG
(1, 5,  65, 5),  -- FaZe
(1, 4,  65, 6),  -- EG
(1, 8,  40, 7),  -- Shopify Rebellion
(1, 6,  40, 8);  -- Cloud9

-- id_torneo=2: EU Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(2,  9, 150, 1),  -- Team Vitality
(2, 10, 125, 2),  -- Karmine Corp
(2, 11,  95, 3),  -- Team BDS
(2, 12,  95, 4),  -- Gentle Mates
(2, 13,  65, 5),  -- Quadrant
(2, 14,  65, 6),  -- Oxygen Esports
(2, 15,  40, 7),  -- Wolves Esports
(2, 16,  40, 8);  -- Team Queso

-- id_torneo=3: SAM Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(3, 17, 150, 1),  -- FURIA
(3, 18, 125, 2),  -- KRÜ
(3, 19,  95, 3),  -- TL SAM
(3, 22,  95, 4),  -- Fluxo
(3, 20,  65, 5),  -- Los Grandes
(3, 23,  65, 6),  -- 9z Team
(3, 21,  40, 7),  -- Elevate
(3, 24,  40, 8);  -- Wildcard Gaming

-- id_torneo=4: MENA Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(4, 25, 150, 1),  -- Team Falcons
(4, 26, 125, 2),  -- Twisted Minds
(4, 27,  95, 3),  -- Sandrock Gaming
(4, 28,  95, 4),  -- Desert Aces
(4, 32,  65, 5),  -- Galaxy Racer
(4, 30,  65, 6),  -- Section
(4, 29,  40, 7),  -- BD Rejects
(4, 31,  40, 8);  -- Ultimatum

-- id_torneo=5: OCE Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(5, 33, 150, 1),  -- Ground Zero Gaming
(5, 35, 125, 2),  -- Renegades
(5, 36,  95, 3),  -- Pioneers
(5, 39,  95, 4),  -- Chiefs Esports
(5, 34,  65, 5),  -- Dire Wolves
(5, 37,  65, 6),  -- Gravitas
(5, 38,  40, 7),  -- Tainted Minds
(5, 40,  40, 8);  -- Greasy Monkeys

-- id_torneo=6: SSKC Regional (2022-23)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(6, 48, 150, 1),  -- Gen.G
(6, 46, 125, 2),  -- T1
(6, 42,  95, 3),  -- Wygers Korea
(6, 44,  95, 4),  -- RunAway
(6, 41,  65, 5),  -- Bravado Gaming
(6, 43,  65, 6),  -- RedFace
(6, 47,  40, 7),  -- Elite 9
(6, 45,  40, 8);  -- Ubuntu

-- id_torneo=7: Major (Múnich) — 16 equipos
-- NA(4): NRG G2 TL SSG | EU(4): VIT KC BDS GM | SAM(3): FURIA KRÜ TL-SAM
-- MENA(2): FAL TM | OCE(1): GZG | SSKC(1): GEN | + SRG wildcard
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(7,  9, 600, 1),   -- Team Vitality — GANADOR
(7,  2, 450, 2),   -- NRG Esports
(7, 11, 300, 3),   -- Team BDS
(7, 10, 300, 4),   -- Karmine Corp
(7, 17, 200, 5),   -- FURIA
(7,  1, 200, 6),   -- G2 Esports
(7, 12, 150, 7),   -- Gentle Mates
(7, 25, 150, 8),   -- Team Falcons
(7,  3,  90, 9),   -- Team Liquid NA
(7,  7,  90, 9),   -- SSG
(7, 18,  90, 9),   -- KRÜ
(7, 33,  90, 9),   -- Ground Zero Gaming
(7, 26,  55, 13),  -- Twisted Minds
(7, 48,  55, 13),  -- Gen.G
(7, 19,  55, 13),  -- TL SAM
(7, 27,  55, 13);  -- Sandrock Gaming

-- id_torneo=8: World Championship (Düsseldorf) — 16 equipos
-- Real: VIT 1º, BDS 2º, KC 3º-4º, TL-NA 3º-4º
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(8,  9, 1000, 1),   -- Team Vitality — CAMPEONA DEL MUNDO 2022-23
(8, 11,  750, 2),   -- Team BDS
(8, 10,  500, 3),   -- Karmine Corp
(8,  3,  500, 4),   -- Team Liquid NA
(8, 17,  350, 5),   -- FURIA
(8,  2,  350, 6),   -- NRG Esports
(8,  1,  250, 7),   -- G2 Esports
(8, 25,  250, 8),   -- Team Falcons
(8,  7,  150, 9),   -- SSG
(8, 18,  150, 9),   -- KRÜ
(8, 33,  150, 9),   -- Ground Zero Gaming
(8, 19,  150, 9),   -- TL SAM
(8, 12,   75, 13),  -- Gentle Mates
(8, 26,   75, 13),  -- Twisted Minds
(8, 48,   75, 13),  -- Gen.G
(8, 35,   75, 13);  -- Renegades

-- ================================================================
-- RLCS 2024 — Season 13
-- ================================================================

-- id_torneo=9: NA Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(9, 1, 150, 1),   -- G2 Esports
(9, 2, 125, 2),   -- NRG Esports
(9, 5,  95, 3),   -- FaZe Clan
(9, 7,  95, 4),   -- Spacestation Gaming
(9, 4,  65, 5),   -- Evil Geniuses
(9, 3,  65, 6),   -- Team Liquid NA
(9, 6,  40, 7),   -- Cloud9
(9, 8,  40, 8);   -- Shopify Rebellion

-- id_torneo=10: EU Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(10, 10, 150, 1),  -- Karmine Corp
(10,  9, 125, 2),  -- Team Vitality
(10, 12,  95, 3),  -- Gentle Mates
(10, 11,  95, 4),  -- Team BDS
(10, 14,  65, 5),  -- Oxygen Esports
(10, 13,  65, 6),  -- Quadrant
(10, 16,  40, 7),  -- Team Queso
(10, 15,  40, 8);  -- Wolves Esports

-- id_torneo=11: SAM Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(11, 17, 150, 1),  -- FURIA
(11, 18, 125, 2),  -- KRÜ
(11, 22,  95, 3),  -- Fluxo
(11, 19,  95, 4),  -- TL SAM
(11, 20,  65, 5),  -- Los Grandes
(11, 23,  65, 6),  -- 9z Team
(11, 21,  40, 7),  -- Elevate
(11, 24,  40, 8);  -- Wildcard Gaming

-- id_torneo=12: MENA Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(12, 25, 150, 1),  -- Team Falcons
(12, 26, 125, 2),  -- Twisted Minds
(12, 27,  95, 3),  -- Sandrock Gaming
(12, 28,  95, 4),  -- Desert Aces
(12, 29,  65, 5),  -- BD Rejects
(12, 30,  65, 6),  -- Section
(12, 32,  40, 7),  -- Galaxy Racer
(12, 31,  40, 8);  -- Ultimatum

-- id_torneo=13: OCE Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(13, 33, 150, 1),  -- Ground Zero Gaming
(13, 36, 125, 2),  -- Pioneers
(13, 34,  95, 3),  -- Dire Wolves
(13, 35,  95, 4),  -- Renegades
(13, 39,  65, 5),  -- Chiefs Esports
(13, 37,  65, 6),  -- Gravitas
(13, 38,  40, 7),  -- Tainted Minds
(13, 40,  40, 8);  -- Greasy Monkeys

-- id_torneo=14: SSKC Split 1 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(14, 48, 150, 1),  -- Gen.G
(14, 46, 125, 2),  -- T1
(14, 42,  95, 3),  -- Wygers Korea
(14, 44,  95, 4),  -- RunAway
(14, 41,  65, 5),  -- Bravado Gaming
(14, 43,  65, 6),  -- RedFace
(14, 47,  40, 7),  -- Elite 9
(14, 45,  40, 8);  -- Ubuntu

-- id_torneo=15: Major 1 (Copenhague) — 16 equipos
-- Clasificados: NA(4):G2 NRG FaZe SSG | EU(4):KC VIT GM BDS
--               SAM(3):FURIA KRÜ TL-SAM | MENA(2):FAL TM
--               OCE(2):GZG PIO | SSKC(1):GEN
-- Real: Gentle Mates 1º, G2 2º
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(15, 12, 600, 1),   -- Gentle Mates — GANADOR Major 1 Copenhague
(15,  1, 450, 2),   -- G2 Esports
(15, 10, 300, 3),   -- Karmine Corp
(15,  9, 300, 4),   -- Team Vitality
(15,  2, 200, 5),   -- NRG Esports
(15, 25, 200, 6),   -- Team Falcons
(15,  5, 150, 7),   -- FaZe Clan
(15, 11, 150, 8),   -- Team BDS
(15, 17,  90, 9),   -- FURIA
(15,  7,  90, 9),   -- SSG
(15, 18,  90, 9),   -- KRÜ
(15, 33,  90, 9),   -- Ground Zero Gaming
(15, 19,  55, 13),  -- TL SAM
(15, 26,  55, 13),  -- Twisted Minds
(15, 36,  55, 13),  -- Pioneers
(15, 48,  55, 13);  -- Gen.G

-- id_torneo=16: NA Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(16,  2, 150, 1),  -- NRG Esports
(16,  1, 125, 2),  -- G2 Esports
(16,  7,  95, 3),  -- Spacestation Gaming
(16,  4,  95, 4),  -- Evil Geniuses
(16,  3,  65, 5),  -- Team Liquid NA
(16,  5,  65, 6),  -- FaZe Clan
(16,  6,  40, 7),  -- Cloud9
(16,  8,  40, 8);  -- Shopify Rebellion

-- id_torneo=17: EU Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(17, 11, 150, 1),  -- Team BDS
(17, 10, 125, 2),  -- Karmine Corp
(17,  9,  95, 3),  -- Team Vitality
(17, 14,  95, 4),  -- Oxygen Esports
(17, 12,  65, 5),  -- Gentle Mates
(17, 13,  65, 6),  -- Quadrant
(17, 15,  40, 7),  -- Wolves Esports
(17, 16,  40, 8);  -- Team Queso

-- id_torneo=18: SAM Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(18, 17, 150, 1),  -- FURIA
(18, 22, 125, 2),  -- Fluxo
(18, 18,  95, 3),  -- KRÜ
(18, 19,  95, 4),  -- TL SAM
(18, 23,  65, 5),  -- 9z Team
(18, 20,  65, 6),  -- Los Grandes
(18, 21,  40, 7),  -- Elevate
(18, 24,  40, 8);  -- Wildcard Gaming

-- id_torneo=19: MENA Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(19, 25, 150, 1),  -- Team Falcons
(19, 27, 125, 2),  -- Sandrock Gaming
(19, 26,  95, 3),  -- Twisted Minds
(19, 28,  95, 4),  -- Desert Aces
(19, 29,  65, 5),  -- BD Rejects
(19, 30,  65, 6),  -- Section
(19, 32,  40, 7),  -- Galaxy Racer
(19, 31,  40, 8);  -- Ultimatum

-- id_torneo=20: OCE Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(20, 36, 150, 1),  -- Pioneers
(20, 33, 125, 2),  -- Ground Zero Gaming
(20, 34,  95, 3),  -- Dire Wolves
(20, 35,  95, 4),  -- Renegades
(20, 39,  65, 5),  -- Chiefs Esports
(20, 37,  65, 6),  -- Gravitas
(20, 38,  40, 7),  -- Tainted Minds
(20, 40,  40, 8);  -- Greasy Monkeys

-- id_torneo=21: SSKC Split 2 Regional
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(21, 48, 150, 1),  -- Gen.G
(21, 46, 125, 2),  -- T1
(21, 41,  95, 3),  -- Bravado Gaming
(21, 42,  95, 4),  -- Wygers Korea
(21, 44,  65, 5),  -- RunAway
(21, 43,  65, 6),  -- RedFace
(21, 45,  40, 7),  -- Ubuntu
(21, 47,  40, 8);  -- Elite 9

-- id_torneo=22: Major 2 (Londres) — 16 equipos
-- Clasificados: NA(4):G2 NRG SSG EG | EU(4):KC BDS VIT OXG
--               SAM(3):FURIA KRÜ FLX | MENA(2):FAL SRG
--               OCE(2):PIO GZG | SSKC(1):GEN
-- Real: G2 1º
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(22,  1, 600, 1),   -- G2 Esports — GANADOR Major 2 Londres
(22, 11, 450, 2),   -- Team BDS
(22, 10, 300, 3),   -- Karmine Corp
(22, 17, 300, 4),   -- FURIA
(22,  2, 200, 5),   -- NRG Esports
(22,  9, 200, 6),   -- Team Vitality
(22, 25, 150, 7),   -- Team Falcons
(22,  7, 150, 8),   -- Spacestation Gaming
(22, 14,  90, 9),   -- Oxygen Esports
(22, 18,  90, 9),   -- KRÜ
(22, 33,  90, 9),   -- Ground Zero Gaming
(22,  4,  90, 9),   -- Evil Geniuses
(22, 36,  55, 13),  -- Pioneers
(22, 27,  55, 13),  -- Sandrock Gaming
(22, 48,  55, 13),  -- Gen.G
(22, 22,  55, 13);  -- Fluxo

-- id_torneo=23: World Championship (Fort Worth) — 16 equipos
-- Clasificados: NA(4):G2 NRG SSG FaZe | EU(5):KC BDS GM VIT OXG
--               SAM(2):FURIA KRÜ | MENA(2):FAL TM
--               OCE(2):GZG PIO | SSKC(1):GEN
-- Real: BDS 1º, G2 2º, Falcons 3º-4º, KC 3º-4º
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(23, 11, 1000, 1),   -- Team BDS — CAMPEÓN DEL MUNDO 2024
(23,  1,  750, 2),   -- G2 Esports
(23, 25,  500, 3),   -- Team Falcons
(23, 10,  500, 4),   -- Karmine Corp
(23, 17,  350, 5),   -- FURIA
(23, 12,  350, 6),   -- Gentle Mates
(23,  2,  250, 7),   -- NRG Esports
(23,  9,  250, 8),   -- Team Vitality
(23, 48,  150, 9),   -- Gen.G
(23,  7,  150, 9),   -- Spacestation Gaming
(23, 18,  150, 9),   -- KRÜ
(23, 14,  150, 9),   -- Oxygen Esports
(23,  5,   75, 13),  -- FaZe Clan
(23, 33,   75, 13),  -- Ground Zero Gaming
(23, 36,   75, 13),  -- Pioneers
(23, 26,   75, 13);  -- Twisted Minds

-- ================================================================
-- RLCS 2025 — Season 14 (Split 1)
-- ================================================================

-- id_torneo=24: NA Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(24,  2, 150, 1),  -- NRG Esports
(24,  1, 125, 2),  -- G2 Esports
(24,  4,  95, 3),  -- Evil Geniuses
(24,  7,  95, 4),  -- Spacestation Gaming
(24,  3,  65, 5),  -- Team Liquid NA
(24,  5,  65, 6),  -- FaZe Clan
(24,  6,  40, 7),  -- Cloud9
(24,  8,  40, 8);  -- Shopify Rebellion

-- id_torneo=25: EU Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(25, 11, 150, 1),  -- Team BDS
(25, 10, 125, 2),  -- Karmine Corp
(25, 12,  95, 3),  -- Gentle Mates
(25,  9,  95, 4),  -- Team Vitality
(25, 14,  65, 5),  -- Oxygen Esports
(25, 13,  65, 6),  -- Quadrant
(25, 16,  40, 7),  -- Team Queso
(25, 15,  40, 8);  -- Wolves Esports

-- id_torneo=26: SAM Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(26, 17, 150, 1),  -- FURIA
(26, 22, 125, 2),  -- Fluxo
(26, 18,  95, 3),  -- KRÜ
(26, 19,  95, 4),  -- TL SAM
(26, 20,  65, 5),  -- Los Grandes
(26, 23,  65, 6),  -- 9z Team
(26, 21,  40, 7),  -- Elevate
(26, 24,  40, 8);  -- Wildcard Gaming

-- id_torneo=27: MENA Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(27, 25, 150, 1),  -- Team Falcons
(27, 26, 125, 2),  -- Twisted Minds
(27, 28,  95, 3),  -- Desert Aces
(27, 27,  95, 4),  -- Sandrock Gaming
(27, 32,  65, 5),  -- Galaxy Racer
(27, 29,  65, 6),  -- BD Rejects
(27, 30,  40, 7),  -- Section
(27, 31,  40, 8);  -- Ultimatum

-- id_torneo=28: OCE Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(28, 36, 150, 1),  -- Pioneers
(28, 33, 125, 2),  -- Ground Zero Gaming
(28, 34,  95, 3),  -- Dire Wolves
(28, 39,  95, 4),  -- Chiefs Esports
(28, 35,  65, 5),  -- Renegades
(28, 37,  65, 6),  -- Gravitas
(28, 38,  40, 7),  -- Tainted Minds
(28, 40,  40, 8);  -- Greasy Monkeys

-- id_torneo=29: SSKC Split 1 Regional (2025)
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(29, 48, 150, 1),  -- Gen.G
(29, 46, 125, 2),  -- T1
(29, 42,  95, 3),  -- Wygers Korea
(29, 44,  95, 4),  -- RunAway
(29, 41,  65, 5),  -- Bravado Gaming
(29, 43,  65, 6),  -- RedFace
(29, 45,  40, 7),  -- Ubuntu
(29, 47,  40, 8);  -- Elite 9

-- id_torneo=30: Major 1 (Mánchester) — 16 equipos
-- Clasificados: NA(4):NRG G2 EG SSG | EU(4):BDS KC GM VIT
--               SAM(3):FURIA FLX KRÜ | MENA(2):FAL TM
--               OCE(2):PIO GZG | SSKC(1):GEN
INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final) VALUES
(30, 11, 600, 1),   -- Team BDS — GANADOR Major 1 Mánchester 2025
(30,  2, 450, 2),   -- NRG Esports
(30,  1, 300, 3),   -- G2 Esports
(30, 17, 300, 4),   -- FURIA
(30, 10, 200, 5),   -- Karmine Corp
(30, 25, 200, 6),   -- Team Falcons
(30,  4, 150, 7),   -- Evil Geniuses
(30, 36, 150, 8),   -- Pioneers
(30, 12,  90, 9),   -- Gentle Mates
(30, 22,  90, 9),   -- Fluxo
(30, 26,  90, 9),   -- Twisted Minds
(30, 33,  90, 9),   -- Ground Zero Gaming
(30,  7,  55, 13),  -- Spacestation Gaming
(30,  9,  55, 13),  -- Team Vitality
(30, 18,  55, 13),  -- KRÜ
(30, 48,  55, 13);  -- Gen.G
