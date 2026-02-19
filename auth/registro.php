<?php
/**
 * REGISTRO - Página de registro de nuevos usuarios
 * Valida datos, comprueba duplicados y guarda con password_hash().
 * Rol por defecto: viewer.
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

$error = '';
$datos = ['nombre' => '', 'email' => '']; // Para mantener datos en el formulario

// --- Procesar el formulario cuando se envía ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar datos
    $nombre   = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
    $email    = mysqli_real_escape_string($conexion, trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $confirmar = trim($_POST['confirmar'] ?? '');

    // Guardar datos para el formulario
    $datos['nombre'] = $nombre;
    $datos['email']  = $email;

    // --- Validaciones ---
    if (empty($nombre) || empty($email) || empty($password) || empty($confirmar)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Comprobar si el email ya está registrado
        $sql_check = "SELECT id_usuario FROM USUARIOS WHERE email = '$email' LIMIT 1";
        $resultado = mysqli_query($conexion, $sql_check);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $error = "Este email ya está registrado.";
        } else {
            // --- Todo correcto: insertar nuevo usuario ---
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql_insert = "INSERT INTO USUARIOS (nombre, email, password, rol, activo)
                           VALUES ('$nombre', '$email', '$password_hash', 'viewer', TRUE)";

            if (mysqli_query($conexion, $sql_insert)) {
                // Éxito: guardar mensaje y redirigir al login
                $_SESSION['registro_exito'] = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
                header("Location: /RLCS/CRM/auth/login.php");
                exit();
            } else {
                $error = "Error al crear la cuenta. Inténtalo de nuevo.";
                error_log("Error registro: " . mysqli_error($conexion));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RLCS CRM</title>
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
                <i class="bi bi-person-plus"></i> Registro
            </h2>
            <p class="text-muted">Crea tu cuenta en RLCS CRM</p>
        </div>

        <!-- Mensaje de error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-sm">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label text-white">
                    <i class="bi bi-person"></i> Nombre
                </label>
                <input type="text" class="form-control bg-dark text-white border-secondary"
                       id="nombre" name="nombre" required
                       placeholder="Tu nombre"
                       value="<?= htmlspecialchars($datos['nombre']) ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label text-white">
                    <i class="bi bi-envelope"></i> Email
                </label>
                <input type="email" class="form-control bg-dark text-white border-secondary"
                       id="email" name="email" required
                       placeholder="tu@email.com"
                       value="<?= htmlspecialchars($datos['email']) ?>">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-white">
                    <i class="bi bi-lock"></i> Contraseña
                </label>
                <input type="password" class="form-control bg-dark text-white border-secondary"
                       id="password" name="password" required
                       placeholder="Mínimo 6 caracteres" minlength="6">
            </div>

            <div class="mb-4">
                <label for="confirmar" class="form-label text-white">
                    <i class="bi bi-lock-fill"></i> Confirmar Contraseña
                </label>
                <input type="password" class="form-control bg-dark text-white border-secondary"
                       id="confirmar" name="confirmar" required
                       placeholder="Repite la contraseña" minlength="6">
            </div>

            <button type="submit" class="btn btn-accent w-100 fw-bold mb-3">
                <i class="bi bi-person-plus"></i> Crear Cuenta
            </button>
        </form>

        <!-- Link al login -->
        <div class="text-center">
            <span class="text-muted">¿Ya tienes cuenta?</span>
            <a href="/RLCS/CRM/auth/login.php" class="text-accent">Inicia sesión</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
