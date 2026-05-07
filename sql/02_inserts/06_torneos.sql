-- =============================================
-- TORNEOS — 30 torneos en 3 temporadas
-- id_temporada=1: RLCS 2022-23 (ids 1-8)
-- id_temporada=2: RLCS 2024     (ids 9-23)
-- id_temporada=3: RLCS 2025     (ids 24-30)
--
-- Puntos por posición (referencia):
-- REGIONAL → 1º:150 2º:125 3º-4º:95 5º-6º:65 7º-8º:40
-- MAJOR    → 1º:600 2º:450 3º-4º:300 5º-6º:200 7º-8º:150 9º-12º:90 13º-16º:55
-- WORLDS   → 1º:1000 2º:750 3º-4º:500 5º-6º:350 7º-8º:250 9º-12º:150 13º-16º:75
-- =============================================

-- -----------------------------------------------
-- RLCS 2022-23 (id_temporada = 1)
-- -----------------------------------------------
INSERT INTO TORNEO (nombre, tipo, id_temporada, prize_pool) VALUES
('RLCS 2022-23 - NA Regional',                    'regional', 1, 100000.00),  -- id 1
('RLCS 2022-23 - EU Regional',                    'regional', 1, 100000.00),  -- id 2
('RLCS 2022-23 - SAM Regional',                   'regional', 1, 100000.00),  -- id 3
('RLCS 2022-23 - MENA Regional',                  'regional', 1, 100000.00),  -- id 4
('RLCS 2022-23 - OCE Regional',                   'regional', 1, 100000.00),  -- id 5
('RLCS 2022-23 - SSKC Regional',                  'regional', 1, 100000.00),  -- id 6
('RLCS 2022-23 - Major (Múnich)',                  'major',    1, 500000.00),  -- id 7
('RLCS 2022-23 - World Championship (Düsseldorf)', 'lan',      1, 2000000.00); -- id 8

-- -----------------------------------------------
-- RLCS 2024 — Season 13 (id_temporada = 2)
-- -----------------------------------------------
INSERT INTO TORNEO (nombre, tipo, id_temporada, prize_pool) VALUES
('RLCS 2024 - NA Split 1 Regional',              'regional', 2, 100000.00),  -- id 9
('RLCS 2024 - EU Split 1 Regional',              'regional', 2, 100000.00),  -- id 10
('RLCS 2024 - SAM Split 1 Regional',             'regional', 2, 100000.00),  -- id 11
('RLCS 2024 - MENA Split 1 Regional',            'regional', 2, 100000.00),  -- id 12
('RLCS 2024 - OCE Split 1 Regional',             'regional', 2, 100000.00),  -- id 13
('RLCS 2024 - SSKC Split 1 Regional',            'regional', 2, 100000.00),  -- id 14
('RLCS 2024 - Major 1 (Copenhague)',              'major',    2, 500000.00),  -- id 15
('RLCS 2024 - NA Split 2 Regional',              'regional', 2, 100000.00),  -- id 16
('RLCS 2024 - EU Split 2 Regional',              'regional', 2, 100000.00),  -- id 17
('RLCS 2024 - SAM Split 2 Regional',             'regional', 2, 100000.00),  -- id 18
('RLCS 2024 - MENA Split 2 Regional',            'regional', 2, 100000.00),  -- id 19
('RLCS 2024 - OCE Split 2 Regional',             'regional', 2, 100000.00),  -- id 20
('RLCS 2024 - SSKC Split 2 Regional',            'regional', 2, 100000.00),  -- id 21
('RLCS 2024 - Major 2 (Londres)',                 'major',    2, 500000.00),  -- id 22
('RLCS 2024 - World Championship (Fort Worth)',   'lan',      2, 2000000.00); -- id 23

-- -----------------------------------------------
-- RLCS 2025 — Season 14 (id_temporada = 3)
-- -----------------------------------------------
INSERT INTO TORNEO (nombre, tipo, id_temporada, prize_pool) VALUES
('RLCS 2025 - NA Split 1 Regional',    'regional', 3, 100000.00),  -- id 24
('RLCS 2025 - EU Split 1 Regional',    'regional', 3, 100000.00),  -- id 25
('RLCS 2025 - SAM Split 1 Regional',   'regional', 3, 100000.00),  -- id 26
('RLCS 2025 - MENA Split 1 Regional',  'regional', 3, 100000.00),  -- id 27
('RLCS 2025 - OCE Split 1 Regional',   'regional', 3, 100000.00),  -- id 28
('RLCS 2025 - SSKC Split 1 Regional',  'regional', 3, 100000.00),  -- id 29
('RLCS 2025 - Major 1 (Mánchester)',   'major',    3, 500000.00);  -- id 30
