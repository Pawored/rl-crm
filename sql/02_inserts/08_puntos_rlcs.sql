-- =============================================
-- PUNTOS_RLCS — 144 filas (48 equipos × 3 temporadas)
-- puntos_regionals = suma de puntos obtenidos en torneos tipo 'regional'
-- puntos_majors    = suma de puntos en 'major' y 'lan' (Worlds)
-- puntos_totales   = suma de ambos
--
-- Temporadas: 1=RLCS 2022-23 | 2=RLCS 2024 | 3=RLCS 2025
-- =============================================

-- ================================================================
-- RLCS 2022-23 (id_temporada = 1)
-- Cálculo: regional(id1-6) + Major(id7) + Worlds(id8)
-- ================================================================

-- NA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(1, 1,  125,  450,  575),  -- G2:     NA-reg(125) + Munich(200) + Worlds(250)
(2, 1,  150,  800,  950),  -- NRG:    NA-reg(150) + Munich(450) + Worlds(350)
(3, 1,   95,  590,  685),  -- TL-NA:  NA-reg(95)  + Munich(90)  + Worlds(500)
(4, 1,   65,    0,   65),  -- EG:     NA-reg(65)  + sin major
(5, 1,   65,    0,   65),  -- FaZe:   NA-reg(65)  + sin major
(6, 1,   40,    0,   40),  -- C9:     NA-reg(40)  + sin major
(7, 1,   95,  240,  335),  -- SSG:    NA-reg(95)  + Munich(90)  + Worlds(150)
(8, 1,   40,    0,   40);  -- SR:     NA-reg(40)  + sin major

-- EU
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
( 9, 1,  150, 1600, 1750),  -- VIT:   EU-reg(150) + Munich(600) + Worlds(1000)
(10, 1,  125,  800,  925),  -- KC:    EU-reg(125) + Munich(300) + Worlds(500)
(11, 1,   95, 1050, 1145),  -- BDS:   EU-reg(95)  + Munich(300) + Worlds(750)
(12, 1,   95,  225,  320),  -- GM:    EU-reg(95)  + Munich(150) + Worlds(75)
(13, 1,   65,    0,   65),  -- QUAD:  EU-reg(65)
(14, 1,   65,    0,   65),  -- OXG:   EU-reg(65)
(15, 1,   40,    0,   40),  -- WLV:   EU-reg(40)
(16, 1,   40,    0,   40);  -- QESO:  EU-reg(40)

-- SAM
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(17, 1,  150,  550,  700),  -- FURIA:  SAM-reg(150) + Munich(200) + Worlds(350)
(18, 1,  125,  240,  365),  -- KRÜ:   SAM-reg(125) + Munich(90)  + Worlds(150)
(19, 1,   95,  205,  300),  -- TL-SAM: SAM-reg(95) + Munich(55)  + Worlds(150)
(20, 1,   65,    0,   65),  -- LG:    SAM-reg(65)
(21, 1,   40,    0,   40),  -- ELV:   SAM-reg(40)
(22, 1,   95,    0,   95),  -- FLX:   SAM-reg(95)  + sin major
(23, 1,   65,    0,   65),  -- 9Z:    SAM-reg(65)
(24, 1,   40,    0,   40);  -- WILD:  SAM-reg(40)

-- MENA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(25, 1,  150,  400,  550),  -- FAL:  MENA-reg(150) + Munich(150) + Worlds(250)
(26, 1,  125,  130,  255),  -- TM:   MENA-reg(125) + Munich(55)  + Worlds(75)
(27, 1,   95,   55,  150),  -- SRG:  MENA-reg(95)  + Munich(55)  [no Worlds]
(28, 1,   95,    0,   95),  -- DA:   MENA-reg(95)
(29, 1,   40,    0,   40),  -- BDR:  MENA-reg(40)
(30, 1,   65,    0,   65),  -- SEC:  MENA-reg(65)
(31, 1,   40,    0,   40),  -- ULT:  MENA-reg(40)
(32, 1,   65,    0,   65);  -- GR:   MENA-reg(65)

-- OCE
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(33, 1,  150,  240,  390),  -- GZG:   OCE-reg(150) + Munich(90) + Worlds(150)
(34, 1,   65,    0,   65),  -- DW:    OCE-reg(65)
(35, 1,  125,   75,  200),  -- RNG:   OCE-reg(125) + Worlds(75)  [no Munich]
(36, 1,   95,    0,   95),  -- PIO:   OCE-reg(95)
(37, 1,   65,    0,   65),  -- GRV:   OCE-reg(65)
(38, 1,   40,    0,   40),  -- TMD:   OCE-reg(40)
(39, 1,   95,    0,   95),  -- CHIEF: OCE-reg(95)
(40, 1,   40,    0,   40);  -- GRSM:  OCE-reg(40)

-- SSKC
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(41, 1,   65,    0,   65),  -- BRVD: SSKC-reg(65)
(42, 1,   95,    0,   95),  -- WYG:  SSKC-reg(95)
(43, 1,   65,    0,   65),  -- RF:   SSKC-reg(65)
(44, 1,   95,    0,   95),  -- RNA:  SSKC-reg(95)
(45, 1,   40,    0,   40),  -- UBU:  SSKC-reg(40)
(46, 1,  125,    0,  125),  -- T1:   SSKC-reg(125)
(47, 1,   40,    0,   40),  -- E9:   SSKC-reg(40)
(48, 1,  150,  130,  280);  -- GEN:  SSKC-reg(150) + Munich(55) + Worlds(75)

-- ================================================================
-- RLCS 2024 (id_temporada = 2)
-- Cálculo: S1-reg + S2-reg + Major1(id15) + Major2(id22) + Worlds(id23)
-- ================================================================

-- NA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(1, 2,  275, 1800, 2075),  -- G2:   S1(150)+S2(125)=275 | M1(450)+M2(600)+W(750)=1800
(2, 2,  275,  650,  925),  -- NRG:  S1(125)+S2(150)=275 | M1(200)+M2(200)+W(250)=650
(3, 2,  130,    0,  130),  -- TL:   S1(65)+S2(65)=130   | sin major
(4, 2,  160,   90,  250),  -- EG:   S1(65)+S2(95)=160   | M2(90) [no M1, no Worlds directo — entra vía WC]
(5, 2,  160,  225,  385),  -- FaZe: S1(95)+S2(65)=160   | M1(150)+W(75)=225
(6, 2,   80,    0,   80),  -- C9:   S1(40)+S2(40)=80    | sin major
(7, 2,  190,  390,  580),  -- SSG:  S1(95)+S2(95)=190   | M1(90)+M2(150)+W(150)=390
(8, 2,   80,    0,   80);  -- SR:   S1(40)+S2(40)=80    | sin major

-- EU
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
( 9, 2,  220,  750,  970),  -- VIT:  S1(125)+S2(95)=220  | M1(300)+M2(200)+W(250)=750
(10, 2,  275, 1100, 1375),  -- KC:   S1(150)+S2(125)=275 | M1(300)+M2(300)+W(500)=1100
(11, 2,  245, 1600, 1845),  -- BDS:  S1(95)+S2(150)=245  | M1(150)+M2(450)+W(1000)=1600
(12, 2,  160,  950, 1110),  -- GM:   S1(95)+S2(65)=160   | M1(600)+W(350)=950 [no M2]
(13, 2,  130,    0,  130),  -- QUAD: S1(65)+S2(65)=130   | sin major
(14, 2,  160,  240,  400),  -- OXG:  S1(65)+S2(95)=160   | M2(90)+W(150)=240
(15, 2,   80,    0,   80),  -- WLV:  S1(40)+S2(40)=80
(16, 2,   80,    0,   80);  -- QESO: S1(40)+S2(40)=80

-- SAM
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(17, 2,  300,  740, 1040),  -- FURIA: S1(150)+S2(150)=300 | M1(90)+M2(300)+W(350)=740
(18, 2,  220,  330,  550),  -- KRÜ:  S1(125)+S2(95)=220  | M1(90)+M2(90)+W(150)=330
(19, 2,  190,   55,  245),  -- TL-SAM:S1(95)+S2(95)=190  | M1(55)=55
(20, 2,  130,    0,  130),  -- LG:   S1(65)+S2(65)=130
(21, 2,   80,    0,   80),  -- ELV:  S1(40)+S2(40)=80
(22, 2,  220,   55,  275),  -- FLX:  S1(95)+S2(125)=220  | M2(55)=55
(23, 2,  130,    0,  130),  -- 9Z:   S1(65)+S2(65)=130
(24, 2,   80,    0,   80);  -- WILD: S1(40)+S2(40)=80

-- MENA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(25, 2,  300,  850, 1150),  -- FAL:  S1(150)+S2(150)=300 | M1(200)+M2(150)+W(500)=850
(26, 2,  220,   75,  295),  -- TM:   S1(125)+S2(95)=220  | M1(55)+W(75)=130…
                              --       [TM no va a M2, pero sí al Worlds como 2ª MENA]
(27, 2,  220,   55,  275),  -- SRG:  S1(95)+S2(125)=220  | M2(55)=55 [no Worlds]
(28, 2,  190,    0,  190),  -- DA:   S1(95)+S2(95)=190
(29, 2,  130,    0,  130),  -- BDR:  S1(65)+S2(65)=130
(30, 2,  130,    0,  130),  -- SEC:  S1(65)+S2(65)=130
(31, 2,   80,    0,   80),  -- ULT:  S1(40)+S2(40)=80
(32, 2,   80,    0,   80);  -- GR:   S1(40)+S2(40)=80

-- OCE
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(33, 2,  275,  255,  530),  -- GZG:   S1(150)+S2(125)=275 | M1(90)+M2(90)+W(75)=255
(34, 2,  190,    0,  190),  -- DW:    S1(95)+S2(95)=190   | sin major
(35, 2,  190,    0,  190),  -- RNG:   S1(95)+S2(95)=190   | sin major
(36, 2,  275,  185,  460),  -- PIO:   S1(125)+S2(150)=275 | M1(55)+M2(55)+W(75)=185
(37, 2,  130,    0,  130),  -- GRV:   S1(65)+S2(65)=130
(38, 2,   80,    0,   80),  -- TMD:   S1(40)+S2(40)=80
(39, 2,  130,    0,  130),  -- CHIEF: S1(65)+S2(65)=130
(40, 2,   80,    0,   80);  -- GRSM:  S1(40)+S2(40)=80

-- SSKC
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(41, 2,  160,    0,  160),  -- BRVD: S1(65)+S2(95)=160
(42, 2,  190,    0,  190),  -- WYG:  S1(95)+S2(95)=190
(43, 2,  130,    0,  130),  -- RF:   S1(65)+S2(65)=130
(44, 2,  160,    0,  160),  -- RNA:  S1(95)+S2(65)=160
(45, 2,   80,    0,   80),  -- UBU:  S1(40)+S2(40)=80
(46, 2,  250,    0,  250),  -- T1:   S1(125)+S2(125)=250 | sin major
(47, 2,   80,    0,   80),  -- E9:   S1(40)+S2(40)=80
(48, 2,  300,  260,  560);  -- GEN:  S1(150)+S2(150)=300 | M1(55)+M2(55)+W(150)=260

-- Corrección TM para 2024 (incluye Worlds)
UPDATE PUNTOS_RLCS SET puntos_majors=130, puntos_totales=350
WHERE id_equipo=26 AND id_temporada=2;

-- ================================================================
-- RLCS 2025 (id_temporada = 3) — solo Split 1 + Major 1
-- ================================================================

-- NA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(1, 3,  125,  300,  425),  -- G2:   S1(125) | M1(300, 3º)
(2, 3,  150,  450,  600),  -- NRG:  S1(150) | M1(450, 2º)
(3, 3,   65,    0,   65),  -- TL:   S1(65)
(4, 3,   95,  150,  245),  -- EG:   S1(95)  | M1(150, 7º)
(5, 3,   65,    0,   65),  -- FaZe: S1(65)
(6, 3,   40,    0,   40),  -- C9:   S1(40)
(7, 3,   95,   55,  150),  -- SSG:  S1(95)  | M1(55, 13º-16º)
(8, 3,   40,    0,   40);  -- SR:   S1(40)

-- EU
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
( 9, 3,   95,   55,  150),  -- VIT:  S1(95) | M1(55, 13º-16º)
(10, 3,  125,  200,  325),  -- KC:   S1(125)| M1(200, 5º)
(11, 3,  150,  600,  750),  -- BDS:  S1(150)| M1(600, 1º)
(12, 3,   95,   90,  185),  -- GM:   S1(95) | M1(90, 9º-12º)
(13, 3,   65,    0,   65),  -- QUAD: S1(65)
(14, 3,   65,    0,   65),  -- OXG:  S1(65)
(15, 3,   40,    0,   40),  -- WLV:  S1(40)
(16, 3,   40,    0,   40);  -- QESO: S1(40)

-- SAM
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(17, 3,  150,  300,  450),  -- FURIA: S1(150)| M1(300, 4º)
(18, 3,   95,   55,  150),  -- KRÜ:  S1(95) | M1(55, 13º-16º)
(19, 3,   95,    0,   95),  -- TL-SAM:S1(95)
(20, 3,   65,    0,   65),  -- LG:   S1(65)
(21, 3,   40,    0,   40),  -- ELV:  S1(40)
(22, 3,  125,   90,  215),  -- FLX:  S1(125)| M1(90, 9º-12º)
(23, 3,   65,    0,   65),  -- 9Z:   S1(65)
(24, 3,   40,    0,   40);  -- WILD: S1(40)

-- MENA
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(25, 3,  150,  200,  350),  -- FAL:  S1(150)| M1(200, 6º)
(26, 3,  125,   90,  215),  -- TM:   S1(125)| M1(90, 9º-12º)
(27, 3,   95,    0,   95),  -- SRG:  S1(95)
(28, 3,   95,    0,   95),  -- DA:   S1(95)
(29, 3,   65,    0,   65),  -- BDR:  S1(65)
(30, 3,   40,    0,   40),  -- SEC:  S1(40)
(31, 3,   40,    0,   40),  -- ULT:  S1(40)
(32, 3,   65,    0,   65);  -- GR:   S1(65)

-- OCE
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(33, 3,  125,   90,  215),  -- GZG:   S1(125)| M1(90, 9º-12º)
(34, 3,   95,    0,   95),  -- DW:    S1(95)
(35, 3,   65,    0,   65),  -- RNG:   S1(65)
(36, 3,  150,  150,  300),  -- PIO:   S1(150)| M1(150, 8º)
(37, 3,   65,    0,   65),  -- GRV:   S1(65)
(38, 3,   40,    0,   40),  -- TMD:   S1(40)
(39, 3,   95,    0,   95),  -- CHIEF: S1(95)
(40, 3,   40,    0,   40);  -- GRSM:  S1(40)

-- SSKC
INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales) VALUES
(41, 3,   65,    0,   65),  -- BRVD: S1(65)
(42, 3,   95,    0,   95),  -- WYG:  S1(95)
(43, 3,   65,    0,   65),  -- RF:   S1(65)
(44, 3,   95,    0,   95),  -- RNA:  S1(95)
(45, 3,   40,    0,   40),  -- UBU:  S1(40)
(46, 3,  125,    0,  125),  -- T1:   S1(125)
(47, 3,   40,    0,   40),  -- E9:   S1(40)
(48, 3,  150,   55,  205);  -- GEN:  S1(150)| M1(55, 13º-16º)
