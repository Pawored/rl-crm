-- =============================================
-- AUDITORIA — tabla de trazabilidad de cambios
-- Ejecutar tras crear la BD principal
-- =============================================

CREATE TABLE IF NOT EXISTS AUDITORIA (
    id_auditoria  INT AUTO_INCREMENT PRIMARY KEY,
    usuario       VARCHAR(100) NOT NULL,
    id_usuario    INT DEFAULT NULL,
    accion        VARCHAR(50)  NOT NULL,       -- 'INSERT','UPDATE','DELETE','IMPORT','CALCULAR'
    tabla         VARCHAR(50)  NOT NULL,
    id_registro   INT DEFAULT NULL,
    detalle       TEXT,
    timestamp     DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tabla    (tabla),
    INDEX idx_usuario  (id_usuario),
    INDEX idx_timestamp (timestamp)
);
