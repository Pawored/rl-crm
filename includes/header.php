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
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>RLCS CRM</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='16' fill='%231a1a2e'/%3E%3Ccircle cx='16' cy='16' r='10' fill='none' stroke='%2300d4ff' stroke-width='3'/%3E%3Ccircle cx='16' cy='16' r='4' fill='%2300d4ff'/%3E%3C/svg%3E">
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
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual == 'comparar.php') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/comparar.php">
                        <i class="bi bi-arrows-angle-contract"></i> Comparar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual == 'agentes_libres.php') ? 'active' : '' ?>"
                       href="/RLCS/CRM/pages/agentes_libres.php">
                        <i class="bi bi-person-x"></i> Agentes Libres
                    </a>
                </li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= ($carpeta_actual == 'admin') ? 'active' : '' ?>"
                       href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark bg-card border-secondary">
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/index.php">
                                <i class="bi bi-speedometer2"></i> Panel Admin
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><span class="dropdown-header text-muted small">DATOS</span></li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/temporadas/index.php">
                                <i class="bi bi-calendar3"></i> Temporadas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/regiones/index.php">
                                <i class="bi bi-globe"></i> Regiones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/torneos/index.php">
                                <i class="bi bi-award"></i> Torneos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/partidos/index.php">
                                <i class="bi bi-joystick"></i> Partidos
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><span class="dropdown-header text-muted small">GESTIÓN</span></li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/participacion/gestionar.php">
                                <i class="bi bi-diagram-3"></i> Participación
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/puntos/gestionar.php">
                                <i class="bi bi-bar-chart"></i> Puntos RLCS
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/roster/gestionar.php">
                                <i class="bi bi-people"></i> Roster
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/estadisticas/entrada.php">
                                <i class="bi bi-graph-up"></i> Estadísticas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/bracket/gestionar.php">
                                <i class="bi bi-trophy"></i> Bracket
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/usuarios.php">
                                <i class="bi bi-person-gear"></i> Usuarios
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><span class="dropdown-header text-muted small">HERRAMIENTAS</span></li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/importar/index.php">
                                <i class="bi bi-upload"></i> Importar CSV/JSON
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/RLCS/CRM/pages/admin/auditoria/index.php">
                                <i class="bi bi-journal-text"></i> Auditoría
                            </a>
                        </li>
                    </ul>
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

<!-- ========== TOASTS (bottom-right, inicializados desde footer.php) ========== -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1200">
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="toast align-items-center text-white bg-success border-0"
         role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-1"></i>
                <?= htmlspecialchars($_SESSION['mensaje_exito']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    <?php unset($_SESSION['mensaje_exito']); endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
    <div class="toast align-items-center text-white bg-danger border-0"
         role="alert" data-bs-autohide="true" data-bs-delay="6000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    <?php unset($_SESSION['mensaje_error']); endif; ?>
</div>

<!-- ========== CONTENIDO PRINCIPAL ========== -->
<div class="container-fluid mt-4 px-4">
