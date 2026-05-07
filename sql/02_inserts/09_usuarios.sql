-- =============================================
-- USUARIOS — usuarios de muestra para cada rol
-- Password para todos: "password"
-- Hash bcrypt generado con password_hash('password', PASSWORD_DEFAULT)
-- =============================================

INSERT INTO USUARIOS (nombre, email, password, rol, activo) VALUES
-- Editores (pueden crear/editar torneos, partidos, estadísticas)
('Lucía Martínez', 'lucia@rlcs.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'editor', TRUE),

('Marco Torres', 'marco@rlcs.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'editor', TRUE),

-- Viewers (solo pueden consultar datos)
('Ana García', 'ana@rlcs.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'viewer', TRUE),

('Carlos Ruiz', 'carlos@rlcs.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'viewer', TRUE),

('Diana Flores', 'diana@rlcs.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'viewer', FALSE);  -- Usuario desactivado (ejemplo de baja)
