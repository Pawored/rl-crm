<?php
/**
 * LOGIN - Página de inicio de sesión
 * Formulario centrado con diseño oscuro estilo esports.
 * Comprueba email y contraseña usando password_verify().
 */

// --- Iniciar sesión ---
session_start();

// --- Si ya está logueado, redirigir al dashboard ---
if (isset($_SESSION['id_usuario'])) {
    header("Location: /RLCS/CRM/pages/dashboard.php");
    exit();
}

// --- Incluir conexión a la BD ---
require_once __DIR__ . '/../config/conexion.php';

$error = '';      // Variable para mensajes de error
$exito = '';      // Variable para mensajes de éxito (viene del registro)

// --- Recoger mensaje de éxito del registro ---
if (isset($_SESSION['registro_exito'])) {
    $exito = $_SESSION['registro_exito'];
    unset($_SESSION['registro_exito']);
}

// --- Procesar el formulario cuando se envía ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar datos del formulario
    $email = mysqli_real_escape_string($conexion, trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        $error = "Debes rellenar todos los campos.";
    } else {
        // Buscar el usuario por email
        $sql = "SELECT id_usuario, nombre, email, password, rol, activo
                FROM USUARIOS WHERE email = '$email' LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) === 1) {
            $usuario = mysqli_fetch_assoc($resultado);

            // Comprobar si la cuenta está activa
            if (!$usuario['activo']) {
                $error = "Tu cuenta está desactivada. Contacta al administrador.";
            }
            // Verificar la contraseña con password_verify()
            elseif (password_verify($password, $usuario['password'])) {
                // --- Login correcto: crear sesión ---
                session_regenerate_id(true); // Regenerar ID por seguridad

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['email']      = $usuario['email'];
                $_SESSION['rol']        = $usuario['rol'];

                // Redirigir al dashboard
                header("Location: /RLCS/CRM/pages/dashboard.php");
                exit();
            } else {
                $error = "Email o contraseña incorrectos.";
            }
        } else {
            $error = "Email o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RLCS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/RLCS/CRM/css/estilos.css" rel="stylesheet">
</head>
<body class="bg-login d-flex align-items-center justify-content-center min-vh-100">

<div class="card card-login shadow-lg" style="width: 420px;">
    <div class="card-body p-5">
        <!-- Título -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-accent">
                <i class="bi bi-controller"></i> RLCS CRM
            </h2>
            <p class="text-muted">Inicia sesión para continuar</p>
        </div>

        <!-- Mensaje de éxito (viene del registro) -->
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success alert-sm">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($exito) ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje de error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-sm">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label text-white">
                    <i class="bi bi-envelope"></i> Email
                </label>
                <input type="email" class="form-control bg-dark text-white border-secondary"
                       id="email" name="email" required
                       placeholder="tu@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-white">
                    <i class="bi bi-lock"></i> Contraseña
                </label>
                <input type="password" class="form-control bg-dark text-white border-secondary"
                       id="password" name="password" required
                       placeholder="Tu contraseña">
            </div>

            <button type="submit" class="btn btn-accent w-100 fw-bold mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
            </button>
        </form>

        <!-- Link a registro -->
        <div class="text-center">
            <span class="text-muted">¿No tienes cuenta?</span>
            <a href="/RLCS/CRM/auth/registro.php" class="text-accent">Regístrate aquí</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
