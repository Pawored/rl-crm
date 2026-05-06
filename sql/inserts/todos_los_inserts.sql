-- =================================================================
-- TODOS LOS INSERTS — RLCS CRM
-- Orden de ejecución: REGION → EQUIPO → JUGADOR → ROSTER
-- =================================================================

-- =============================================
-- 1. REGIONES (6)
-- =============================================
INSERT INTO REGION (nombre, siglas, plazas_mundial) VALUES
('North America',              'NA',   3),
('Europe',                     'EU',   3),
('South America',              'SAM',  2),
('Middle East & North Africa', 'MENA', 2),
('Oceania',                    'OCE',  1),
('Sub-Saharan Africa & Korea', 'SSKC', 1);

-- =============================================
-- 2. EQUIPOS (48 — 8 por región)
-- =============================================

-- NA (id_region = 1)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('G2 Esports',          'G2',    1),
('NRG Esports',         'NRG',   1),
('Team Liquid',         'LIQ',   1),
('Evil Geniuses',       'EG',    1),
('FaZe Clan',           'FaZe',  1),
('Cloud9',              'C9',    1),
('Spacestation Gaming', 'SSG',   1),
('Shopify Rebellion',   'SR',    1);

-- EU (id_region = 2)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('Team Vitality',  'VIT',  2),
('Karmine Corp',   'KC',   2),
('Team BDS',       'BDS',  2),
('Gentle Mates',   'GM',   2),
('Quadrant',       'QUAD', 2),
('Oxygen Esports', 'OXG',  2),
('Wolves Esports', 'WLV',  2),
('Team Queso',     'QESO', 2);

-- SAM (id_region = 3)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('Furia Esports',   'FURIA', 3),
('KRÜ Esports',     'KRU',   3),
('Team Liquid',     'LIQ',   3),
('Los Grandes',     'LG',    3),
('Elevate',         'ELV',   3),
('Fluxo',           'FLX',   3),
('9z Team',         '9Z',    3),
('Wildcard Gaming', 'WILD',  3);

-- MENA (id_region = 4)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('Team Falcons',    'FAL', 4),
('Twisted Minds',   'TM',  4),
('Sandrock Gaming', 'SRG', 4),
('Desert Aces',     'DA',  4),
('BD Rejects',      'BDR', 4),
('Section',         'SEC', 4),
('Ultimatum',       'ULT', 4),
('Galaxy Racer',    'GR',  4);

-- OCE (id_region = 5)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('Ground Zero Gaming',  'GZG',   5),
('Dire Wolves',         'DW',    5),
('Renegades',           'RNG',   5),
('Pioneers',            'PIO',   5),
('Gravitas',            'GRV',   5),
('Tainted Minds',       'TMD',   5),
('Chiefs Esports Club', 'CHIEF', 5),
('Greasy Monkeys',      'GRSM',  5);

-- SSKC (id_region = 6)
INSERT INTO EQUIPO (nombre, tag, id_region) VALUES
('Bravado Gaming', 'BRVD', 6),
('Wygers Korea',   'WYG',  6),
('RedFace',        'RF',   6),
('RunAway',        'RNA',  6),
('Ubuntu',         'UBU',  6),
('T1',             'T1',   6),
('Elite 9',        'E9',   6),
('Gen.G',          'GEN',  6);

-- =============================================
-- 3. JUGADORES (144 — 3 por equipo)
-- =============================================

-- NA — G2 Esports (id_equipo = 1 | id_jugador 1-3)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('jstn',           'Justin Morales',        '2002-08-07', 'Estados Unidos'),
('Mist',           'Marc Datuin',           '2002-03-15', 'Estados Unidos'),
('Jessie',         'Jessie Caulfield',      '2001-06-20', 'Estados Unidos');

-- NA — NRG Esports (id_equipo = 2 | id_jugador 4-6)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('GarrettG',       'Garrett Gordon',        '1999-09-30', 'Estados Unidos'),
('Squishy',        'Cameron Mackay',        '1999-07-12', 'Canadá'),
('Chicago',        'Reed Wilen',            '2001-04-22', 'Estados Unidos');

-- NA — Team Liquid (id_equipo = 3 | id_jugador 7-9)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Comm',           'Jake Zwilling',         '2000-01-15', 'Estados Unidos'),
('Maaack',         'Makoa Sonawane',        '2001-11-08', 'Estados Unidos'),
('Taroco',         'Carlos Tovar',          '2002-05-17', 'Venezuela');

-- NA — Evil Geniuses (id_equipo = 4 | id_jugador 10-12)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Firstkiller',    'Ahmad Yunos',           '2003-02-14', 'Estados Unidos'),
('Chronic',        'Joshua Harrington',     '2001-08-23', 'Estados Unidos'),
('ApparentlyJack', 'Jackson Decker',        '2000-12-03', 'Canadá');

-- NA — FaZe Clan (id_equipo = 5 | id_jugador 13-15)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Allushin',       'Aleksandr Allushin',    '1997-07-19', 'Canadá'),
('Dappur',         'Marcus Carter',         '1997-03-11', 'Estados Unidos'),
('Jknaps',         'Jake Kuhn',             '2000-06-14', 'Estados Unidos');

-- NA — Cloud9 (id_equipo = 6 | id_jugador 16-18)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Deol',           'Nolan Deol',            '2001-05-14', 'Canadá'),
('Retals',         'Matías Pérez',          '2002-02-28', 'Chile'),
('Al0t',           'Joakim Aalstad',        '2000-08-19', 'Noruega');

-- NA — Spacestation Gaming (id_equipo = 7 | id_jugador 19-21)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Arsenal',        'Kyle Doyle',            '1998-11-16', 'Australia'),
('Sadjunior',      'Guilherme Junior',      '2003-07-04', 'Brasil'),
('Insolences',     'Lewis Fox',             '2001-03-22', 'Estados Unidos');

-- NA — Shopify Rebellion (id_equipo = 8 | id_jugador 22-24)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Lj',             'Lucas Jussiau',         '2001-08-31', 'Canadá'),
('Lawler',         'Hayden Lawler',         '2000-05-14', 'Estados Unidos'),
('Klassux',        'Kristian Laakso',       '1999-12-03', 'Suecia');

-- EU — Team Vitality (id_equipo = 9 | id_jugador 25-27)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Zen',            'Alexis Brayze',         '2002-04-08', 'Francia'),
('Alpha54',        'Yanis Champenois',      '2001-09-16', 'Francia'),
('Radosin',        'Radoslaw Kolodziej',    '2000-11-23', 'Polonia');

-- EU — Karmine Corp (id_equipo = 10 | id_jugador 28-30)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Vatira',         'Yanis Darras',          '2003-11-19', 'Francia'),
('bluejays',       'Stefano Pinna',         '2001-07-04', 'Italia'),
('Eyignoc',        'Clément Gicquel',       '2002-03-28', 'Francia');

-- EU — Team BDS (id_equipo = 11 | id_jugador 31-33)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Extra',          'David Malo',            '2003-01-09', 'Francia'),
('Achieves',       'Simon Sundström',       '2002-08-22', 'Suecia'),
('M0nkey M00n',    'William Beadle',        '2002-05-17', 'Francia');

-- EU — Gentle Mates (id_equipo = 12 | id_jugador 34-36)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Marc_by_8',      'Marc Domingo',          '2001-06-14', 'España'),
('Arju',           'Arjun Holt',            '2002-10-05', 'Reino Unido'),
('Atow',           'Antoine Moreau',        '2003-02-19', 'Francia');

-- EU — Quadrant (id_equipo = 13 | id_jugador 37-39)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Scrub Killa',    'Kyle Robertson',        '2001-11-08', 'Reino Unido'),
('Kaydop',         'Alexandre Courant',     '1998-02-21', 'Francia'),
('Turbopolsa',     'Ronaky Larsson',        '1995-04-05', 'Suecia');

-- EU — Oxygen Esports (id_equipo = 14 | id_jugador 40-42)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('JoreuzEU',       'Joris Robben',          '2002-07-11', 'Países Bajos'),
('Deevo',          'Victor Sjöqvist',       '1997-09-14', 'Suecia'),
('Noly',           'Maxime Dupont',         '2003-03-07', 'Francia');

-- EU — Wolves Esports (id_equipo = 15 | id_jugador 43-45)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Bluey',          'Thomas Hughes',         '2002-08-16', 'Reino Unido'),
('Ryscu',          'Krzysztof Sobieraj',    '2001-12-04', 'Polonia'),
('Frankkk',        'Frank Doyle',           '2003-05-22', 'Irlanda');

-- EU — Team Queso (id_equipo = 16 | id_jugador 46-48)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('DmentZa',        'Borja García',          '2000-09-28', 'España'),
('Caard',          'Evann Isoard',          '2001-04-16', 'Francia'),
('Tinizine',       'Mehdi Tinizine',        '2002-11-11', 'Francia');

-- SAM — Furia Esports (id_equipo = 17 | id_jugador 49-51)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Drufinho',       'Guilherme Dru',         '2002-06-15', 'Brasil'),
('CaioTG1',        'Caio Gomide',           '2001-09-22', 'Brasil'),
('Lostt',          'Paulo Duarte',          '2003-01-07', 'Brasil');

-- SAM — KRÜ Esports (id_equipo = 18 | id_jugador 52-54)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('reysbull',       'Raúl Moreno',           '2001-03-11', 'Chile'),
('Sebadam',        'Sebastián Adam',        '2002-08-19', 'Argentina'),
('kevpert',        'Kevin Espinoza',        '2003-05-25', 'Perú');

-- SAM — Team Liquid (id_equipo = 19 | id_jugador 55-57)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Turinturo',      'Sebastián Rubio',       '2000-07-14', 'Brasil'),
('Gustavow',       'Gustavo Wainer',        '2001-11-30', 'Brasil'),
('Nolz',           'Lucas Pereira',         '2002-04-08', 'Brasil');

-- SAM — Los Grandes (id_equipo = 20 | id_jugador 58-60)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Electronik',     'Tomás Rodríguez',       '2002-02-17', 'Chile'),
('Turbito',        'Alejandro García',      '2001-08-05', 'Argentina'),
('Ponpi',          'Gonzalo Ponpilone',     '2003-03-22', 'Argentina');

-- SAM — Elevate (id_equipo = 21 | id_jugador 61-63)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Joao',           'João Silva',            '2002-10-14', 'Brasil'),
('Nwpo',           'Nicolás Wirtz',         '2001-07-06', 'Venezuela'),
('Togy',           'Thiago Gomes',          '2003-01-28', 'Brasil');

-- SAM — Fluxo (id_equipo = 22 | id_jugador 64-66)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Drago',          'Diego Dragoni',         '2002-05-09', 'Brasil'),
('Vitinho',        'Vitor Santos',          '2001-12-17', 'Brasil'),
('Arteeh',         'Arthur Henrique',       '2003-08-04', 'Brasil');

-- SAM — 9z Team (id_equipo = 23 | id_jugador 67-69)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('PKay',           'Patricio López',        '2001-09-13', 'Argentina'),
('Patooon',        'Patricio García',       '2002-04-21', 'Argentina'),
('0ver',           'Ignacio Fuentes',       '2003-07-15', 'Argentina');

-- SAM — Wildcard Gaming (id_equipo = 24 | id_jugador 70-72)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Tomate',         'Tomás Gómez',           '2002-03-19', 'Chile'),
('Fakii',          'Facundo Iglesias',      '2001-11-25', 'Argentina'),
('Chelo',          'Marcelo Vásquez',       '2003-06-09', 'Argentina');

-- MENA — Team Falcons (id_equipo = 25 | id_jugador 73-75)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Msdossary',      'Mohammed Al-Dossary',   '2000-05-10', 'Arabia Saudí'),
('Petucky',        'Petko Stoychev',        '1998-04-06', 'Bulgaria'),
('Anemo',          'Anass Karboubi',        '2002-01-14', 'Arabia Saudí');

-- MENA — Twisted Minds (id_equipo = 26 | id_jugador 76-78)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Tigreee',        'Malik Asad',            '2001-06-18', 'Egipto'),
('Wolfie',         'Omar Al-Hassan',        '2000-09-27', 'Emiratos Árabes'),
('Muriloku',       'Murilo Krauze',         '2002-12-03', 'Brasil');

-- MENA — Sandrock Gaming (id_equipo = 27 | id_jugador 79-81)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('GoldBridge',     'Abdullah Al-Harbi',     '2001-07-22', 'Arabia Saudí'),
('Majed',          'Majed Al-Otaibi',       '2002-03-14', 'Arabia Saudí'),
('Jokoo',          'Joao Costa',            '2001-11-08', 'Portugal');

-- MENA — Desert Aces (id_equipo = 28 | id_jugador 82-84)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Zer0',           'Ahmed Al-Rashid',       '2002-08-30', 'Emiratos Árabes'),
('Smatr',          'Sami Khaled',           '2001-04-15', 'Marruecos'),
('Chxse',          'Chase Williams',        '2000-10-22', 'Sudáfrica');

-- MENA — BD Rejects (id_equipo = 29 | id_jugador 85-87)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Flash',          'Fahad Al-Sayed',        '2002-05-17', 'Arabia Saudí'),
('Xilent',         'Youssef Benali',        '2001-09-03', 'Marruecos'),
('Cyber',          'Omar Al-Naser',         '2003-02-28', 'Kuwait');

-- MENA — Section (id_equipo = 30 | id_jugador 88-90)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Kami',           'Khalid Al-Muhanna',     '2001-12-11', 'Arabia Saudí'),
('RoKo',           'Romain Kowalski',       '2002-07-19', 'Francia'),
('Shadow',         'Saad Mansour',          '2003-04-06', 'Egipto');

-- MENA — Ultimatum (id_equipo = 31 | id_jugador 91-93)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Vexz',           'Saleh Al-Ghamdi',       '2002-01-25', 'Arabia Saudí'),
('Blazik',         'Blaz Kavanic',          '2001-08-14', 'Eslovenia'),
('Hstng',          'Hassan Ting',           '2003-06-30', 'Emiratos Árabes');

-- MENA — Galaxy Racer (id_equipo = 32 | id_jugador 94-96)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Maro',           'Maroun Makhoul',        '2001-03-08', 'Líbano'),
('Nasr',           'Nasreddine Dali',       '2002-10-17', 'Argelia'),
('Ryoo',           'Yousuf Al-Riyami',      '2003-01-22', 'Omán');

-- OCE — Ground Zero Gaming (id_equipo = 33 | id_jugador 97-99)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Cub',            'Callum Carew',          '2002-04-17', 'Australia'),
('Drippay',        'Jordan Kay',            '2001-08-09', 'Australia'),
('Marceloh',       'Marcelo Higa',          '2002-11-23', 'Australia');

-- OCE — Dire Wolves (id_equipo = 34 | id_jugador 100-102)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Bananahead',     'Bayley Blair',          '2001-06-04', 'Australia'),
('CJCJ',           'James Callaghan',       '2000-10-28', 'Australia'),
('Rocket',         'Caleb Farquhar',        '2002-03-15', 'Australia');

-- OCE — Renegades (id_equipo = 35 | id_jugador 103-105)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Joltzt',         'Joel Tanner',           '2001-12-07', 'Australia'),
('Surreal',        'Sam Blake',             '2002-05-20', 'Nueva Zelanda'),
('Rezears',        'Jason Rees',            '2000-09-14', 'Australia');

-- OCE — Pioneers (id_equipo = 36 | id_jugador 106-108)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Denza',          'Denis Zhang',           '2002-07-31', 'Australia'),
('Bobblehead',     'Robert Larkin',         '2001-11-15', 'Australia'),
('Hiero',          'Hieronymus Blake',      '2003-04-08', 'Nueva Zelanda');

-- OCE — Gravitas (id_equipo = 37 | id_jugador 109-111)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Decka',          'Declan Smith',          '2001-09-22', 'Australia'),
('Rapid',          'Daniel Murphy',         '2002-06-11', 'Australia'),
('Astro',          'Nathan Pierce',         '2003-02-19', 'Australia');

-- OCE — Tainted Minds (id_equipo = 38 | id_jugador 112-114)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Psych',          'Tim Larson',            '2001-04-30', 'Australia'),
('Orbit',          'Sam Wheeler',           '2002-08-16', 'Australia'),
('Knox',           'Bradley Knox',          '2003-06-24', 'Nueva Zelanda');

-- OCE — Chiefs Esports Club (id_equipo = 39 | id_jugador 115-117)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Drills',         'Reginald O\'Brien',     '2000-11-09', 'Australia'),
('Qlusive',        'Quentin Morris',        '2001-07-26', 'Australia'),
('Pulse',          'Patrick Sullivan',      '2002-04-13', 'Australia');

-- OCE — Greasy Monkeys (id_equipo = 40 | id_jugador 118-120)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Donut',          'Daniel Norton',         '2002-03-14', 'Australia'),
('Redeye',         'Richard Hayes',         '2001-10-22', 'Australia'),
('Gizmo',          'George Stone',          '2003-07-08', 'Nueva Zelanda');

-- SSKC — Bravado Gaming (id_equipo = 41 | id_jugador 121-123)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Antaly',         'Anele Msomi',           '2000-06-15', 'Sudáfrica'),
('Exo',            'Exxel Potgieter',       '2001-03-28', 'Sudáfrica'),
('Riizy',          'Ryan Jackson',          '2002-09-11', 'Sudáfrica');

-- SSKC — Wygers Korea (id_equipo = 42 | id_jugador 124-126)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Yoomi',          'Yoo Min-jun',           '2001-08-14', 'Corea del Sur'),
('Freakii',        'Park Jae-hyun',         '2002-04-07', 'Corea del Sur'),
('Sangs',          'Kim Sang-woo',          '2003-01-19', 'Corea del Sur');

-- SSKC — RedFace (id_equipo = 43 | id_jugador 127-129)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Azure',          'Aziz Ndlovu',           '2001-11-30', 'Sudáfrica'),
('Pingu',          'Lee Mwangi',            '2002-06-18', 'Kenia'),
('Bolt',           'Bongani Mokoena',       '2003-03-25', 'Sudáfrica');

-- SSKC — RunAway (id_equipo = 44 | id_jugador 130-132)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Seol',           'Seol Byeong-woo',       '2001-05-09', 'Corea del Sur'),
('Shine',          'Choi Hyun-seok',        '2002-10-14', 'Corea del Sur'),
('Flame',          'Jung Min-ho',           '2003-02-28', 'Corea del Sur');

-- SSKC — Ubuntu (id_equipo = 45 | id_jugador 133-135)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Apex',           'Sipho Dlamini',         '2001-07-22', 'Sudáfrica'),
('Nkosi',          'Nkosi Mthembu',         '2002-04-16', 'Sudáfrica'),
('Zulu',           'Thabo Khumalo',         '2003-09-03', 'Sudáfrica');

-- SSKC — T1 (id_equipo = 46 | id_jugador 136-138)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Hawk',           'Lee Ji-hwan',           '2001-12-07', 'Corea del Sur'),
('Storm',          'Kim Tae-yang',          '2002-08-21', 'Corea del Sur'),
('Arc',            'Cho Seung-jun',         '2003-05-15', 'Corea del Sur');

-- SSKC — Elite 9 (id_equipo = 47 | id_jugador 139-141)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Atlas',          'Atlehang Motsepe',      '2000-09-18', 'Sudáfrica'),
('Volt',           'Victor Nkosi',          '2001-05-27', 'Sudáfrica'),
('Ember',          'Emilio Ferreira',       '2002-12-04', 'Mozambique');

-- SSKC — Gen.G (id_equipo = 48 | id_jugador 142-144)
INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais) VALUES
('Gwang',          'Gwang Seok-jin',        '2001-03-11', 'Corea del Sur'),
('Nova',           'Park Hyun-woo',         '2002-07-25', 'Corea del Sur'),
('Eden',           'Cho Eun-soo',           '2003-01-09', 'Corea del Sur');

-- =============================================
-- 4. ROSTER (144 — 3 titulares por equipo)
-- =============================================
INSERT INTO ROSTER (id_equipo, id_jugador, fecha_inicio, titular) VALUES
-- NA
(1,   1,  '2023-09-01', TRUE), (1,   2,  '2023-09-01', TRUE), (1,   3,  '2023-09-01', TRUE),
(2,   4,  '2023-09-01', TRUE), (2,   5,  '2023-09-01', TRUE), (2,   6,  '2023-09-01', TRUE),
(3,   7,  '2023-09-01', TRUE), (3,   8,  '2023-09-01', TRUE), (3,   9,  '2023-09-01', TRUE),
(4,   10, '2023-09-01', TRUE), (4,   11, '2023-09-01', TRUE), (4,   12, '2023-09-01', TRUE),
(5,   13, '2023-09-01', TRUE), (5,   14, '2023-09-01', TRUE), (5,   15, '2023-09-01', TRUE),
(6,   16, '2023-09-01', TRUE), (6,   17, '2023-09-01', TRUE), (6,   18, '2023-09-01', TRUE),
(7,   19, '2023-09-01', TRUE), (7,   20, '2023-09-01', TRUE), (7,   21, '2023-09-01', TRUE),
(8,   22, '2023-09-01', TRUE), (8,   23, '2023-09-01', TRUE), (8,   24, '2023-09-01', TRUE),
-- EU
(9,   25, '2023-09-01', TRUE), (9,   26, '2023-09-01', TRUE), (9,   27, '2023-09-01', TRUE),
(10,  28, '2023-09-01', TRUE), (10,  29, '2023-09-01', TRUE), (10,  30, '2023-09-01', TRUE),
(11,  31, '2023-09-01', TRUE), (11,  32, '2023-09-01', TRUE), (11,  33, '2023-09-01', TRUE),
(12,  34, '2023-09-01', TRUE), (12,  35, '2023-09-01', TRUE), (12,  36, '2023-09-01', TRUE),
(13,  37, '2023-09-01', TRUE), (13,  38, '2023-09-01', TRUE), (13,  39, '2023-09-01', TRUE),
(14,  40, '2023-09-01', TRUE), (14,  41, '2023-09-01', TRUE), (14,  42, '2023-09-01', TRUE),
(15,  43, '2023-09-01', TRUE), (15,  44, '2023-09-01', TRUE), (15,  45, '2023-09-01', TRUE),
(16,  46, '2023-09-01', TRUE), (16,  47, '2023-09-01', TRUE), (16,  48, '2023-09-01', TRUE),
-- SAM
(17,  49, '2023-09-01', TRUE), (17,  50, '2023-09-01', TRUE), (17,  51, '2023-09-01', TRUE),
(18,  52, '2023-09-01', TRUE), (18,  53, '2023-09-01', TRUE), (18,  54, '2023-09-01', TRUE),
(19,  55, '2023-09-01', TRUE), (19,  56, '2023-09-01', TRUE), (19,  57, '2023-09-01', TRUE),
(20,  58, '2023-09-01', TRUE), (20,  59, '2023-09-01', TRUE), (20,  60, '2023-09-01', TRUE),
(21,  61, '2023-09-01', TRUE), (21,  62, '2023-09-01', TRUE), (21,  63, '2023-09-01', TRUE),
(22,  64, '2023-09-01', TRUE), (22,  65, '2023-09-01', TRUE), (22,  66, '2023-09-01', TRUE),
(23,  67, '2023-09-01', TRUE), (23,  68, '2023-09-01', TRUE), (23,  69, '2023-09-01', TRUE),
(24,  70, '2023-09-01', TRUE), (24,  71, '2023-09-01', TRUE), (24,  72, '2023-09-01', TRUE),
-- MENA
(25,  73, '2023-09-01', TRUE), (25,  74, '2023-09-01', TRUE), (25,  75, '2023-09-01', TRUE),
(26,  76, '2023-09-01', TRUE), (26,  77, '2023-09-01', TRUE), (26,  78, '2023-09-01', TRUE),
(27,  79, '2023-09-01', TRUE), (27,  80, '2023-09-01', TRUE), (27,  81, '2023-09-01', TRUE),
(28,  82, '2023-09-01', TRUE), (28,  83, '2023-09-01', TRUE), (28,  84, '2023-09-01', TRUE),
(29,  85, '2023-09-01', TRUE), (29,  86, '2023-09-01', TRUE), (29,  87, '2023-09-01', TRUE),
(30,  88, '2023-09-01', TRUE), (30,  89, '2023-09-01', TRUE), (30,  90, '2023-09-01', TRUE),
(31,  91, '2023-09-01', TRUE), (31,  92, '2023-09-01', TRUE), (31,  93, '2023-09-01', TRUE),
(32,  94, '2023-09-01', TRUE), (32,  95, '2023-09-01', TRUE), (32,  96, '2023-09-01', TRUE),
-- OCE
(33,  97, '2023-09-01', TRUE), (33,  98, '2023-09-01', TRUE), (33,  99, '2023-09-01', TRUE),
(34, 100, '2023-09-01', TRUE), (34, 101, '2023-09-01', TRUE), (34, 102, '2023-09-01', TRUE),
(35, 103, '2023-09-01', TRUE), (35, 104, '2023-09-01', TRUE), (35, 105, '2023-09-01', TRUE),
(36, 106, '2023-09-01', TRUE), (36, 107, '2023-09-01', TRUE), (36, 108, '2023-09-01', TRUE),
(37, 109, '2023-09-01', TRUE), (37, 110, '2023-09-01', TRUE), (37, 111, '2023-09-01', TRUE),
(38, 112, '2023-09-01', TRUE), (38, 113, '2023-09-01', TRUE), (38, 114, '2023-09-01', TRUE),
(39, 115, '2023-09-01', TRUE), (39, 116, '2023-09-01', TRUE), (39, 117, '2023-09-01', TRUE),
(40, 118, '2023-09-01', TRUE), (40, 119, '2023-09-01', TRUE), (40, 120, '2023-09-01', TRUE),
-- SSKC
(41, 121, '2023-09-01', TRUE), (41, 122, '2023-09-01', TRUE), (41, 123, '2023-09-01', TRUE),
(42, 124, '2023-09-01', TRUE), (42, 125, '2023-09-01', TRUE), (42, 126, '2023-09-01', TRUE),
(43, 127, '2023-09-01', TRUE), (43, 128, '2023-09-01', TRUE), (43, 129, '2023-09-01', TRUE),
(44, 130, '2023-09-01', TRUE), (44, 131, '2023-09-01', TRUE), (44, 132, '2023-09-01', TRUE),
(45, 133, '2023-09-01', TRUE), (45, 134, '2023-09-01', TRUE), (45, 135, '2023-09-01', TRUE),
(46, 136, '2023-09-01', TRUE), (46, 137, '2023-09-01', TRUE), (46, 138, '2023-09-01', TRUE),
(47, 139, '2023-09-01', TRUE), (47, 140, '2023-09-01', TRUE), (47, 141, '2023-09-01', TRUE),
(48, 142, '2023-09-01', TRUE), (48, 143, '2023-09-01', TRUE), (48, 144, '2023-09-01', TRUE);
