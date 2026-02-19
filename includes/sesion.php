<?php
/**
 * GESTIÓN DE SESIONES
 * Este archivo inicia la sesión y comprueba que el usuario esté logueado.
 * Se incluye en TODAS las páginas privadas del CRM.
 * Si no hay sesión activa, redirige al login.
 */

// --- Iniciar sesión si no está iniciada ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Comprobar si el usuario tiene sesión activa ---
if (!isset($_SESSION['id_usuario'])) {
    // No hay sesión: redirigir al login
    header("Location: /RLCS/CRM/auth/login.php");
    exit();
}

/**
 * Función auxiliar: comprobar si el usuario tiene un rol específico
 * @param string|array $roles_permitidos - Rol o array de roles permitidos
 * @return bool - true si el usuario tiene uno de los roles permitidos
 */
function tieneRol($roles_permitidos) {
    if (!isset($_SESSION['rol'])) {
        return false;
    }
    // Si se pasa un solo rol como string, convertirlo a array
    if (is_string($roles_permitidos)) {
        $roles_permitidos = [$roles_permitidos];
    }
    return in_array($_SESSION['rol'], $roles_permitidos);
}

/**
 * Función auxiliar: redirigir si el usuario NO tiene el rol necesario
 * @param string|array $roles_permitidos - Rol o array de roles permitidos
 */
function requiereRol($roles_permitidos) {
    if (!tieneRol($roles_permitidos)) {
        // Guardar mensaje de error en sesión y redirigir al dashboard
        $_SESSION['mensaje_error'] = "No tienes permisos para acceder a esa página.";
        header("Location: /RLCS/CRM/pages/dashboard.php");
        exit();
    }
}
?>
