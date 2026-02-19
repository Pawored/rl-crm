<?php
/**
 * HEADER - Cabecera común para todas las páginas
 * Incluye la navbar de Bootstrap 5, links de navegación,
 * barra de búsqueda global y datos del usuario logueado.
 */

// --- Detectar la página actual para resaltar el link activo ---
$pagina_actual = basename($_SERVER['PHP_SELF']);
$carpeta_actual = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RLCS CRM - Gestión Rocket League</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="/RLCS/CRM/css/estilos.css" rel="stylesheet">
</head>
<body>

<!-- ========== NAVBAR PRINCIPAL ========== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-navbar sticky-top">
    <div class="container-fluid">
        <!-- Logo / Título -->
        <a class="navbar-brand fw-bold" href="/RLCS/CRM/pages/dashboard.php">
            <i class="bi bi-controller"></i> RLCS CRM
        </a>

        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido de la navbar -->
        <div class="collapse navbar-collapse" id="navbarPrincipal">
            <!-- Links de navegación -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual == 'dashboard.php') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'equipos') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/equipos/index.php">
                        <i class="bi bi-people-fill"></i> Equipos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'jugadores') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/jugadores/index.php">
                        <i class="bi bi-person-badge"></i> Jugadores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'clasificacion') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/clasificacion/index.php">
                        <i class="bi bi-trophy"></i> Clasificación
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'partidos') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/partidos/index.php">
                        <i class="bi bi-joystick"></i> Partidos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'torneos') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/torneos/index.php">
                        <i class="bi bi-award"></i> Torneos
                    </a>
                </li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($carpeta_actual == 'admin') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/admin/usuarios.php">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Barra de búsqueda global -->
            <form class="d-flex me-3" action="/RLCS/CRM/pages/busqueda.php" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           name="q" placeholder="Buscar equipos, jugadores, torneos..."
                           value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                           required minlength="2">
                    <button class="btn btn-outline-info" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Info del usuario -->
            <div class="d-flex align-items-center">
                <span class="text-white me-2">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                </span>
                <!-- Badge del rol con color según tipo -->
                <?php
                $rol = $_SESSION['rol'] ?? 'viewer';
                $badge_class = match($rol) {
                    'admin'  => 'bg-danger',
                    'editor' => 'bg-warning text-dark',
                    'viewer' => 'bg-info',
                    default  => 'bg-secondary'
                };
                ?>
                <span class="badge <?= $badge_class ?> me-3"><?= ucfirst($rol) ?></span>
                <a href="/RLCS/CRM/auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ========== CONTENIDO PRINCIPAL ========== -->
<div class="container-fluid mt-4 px-4">
    <?php
    // --- Mostrar mensajes de éxito/error si existen en sesión ---
    if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= $_SESSION['mensaje_exito'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['mensaje_error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>
