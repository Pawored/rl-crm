<?php
/**
 * INDEX PRINCIPAL - Punto de entrada del CRM
 * Si el usuario tiene sesión activa → redirige al dashboard.
 * Si no tiene sesión → redirige al login.
 */

session_start();

if (isset($_SESSION['id_usuario'])) {
    // Usuario logueado: ir al dashboard
    header("Location: /RLCS/CRM/pages/dashboard.php");
} else {
    // No hay sesión: ir al login
    header("Location: /RLCS/CRM/auth/login.php");
}
exit();
?>
