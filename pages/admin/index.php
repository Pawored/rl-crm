<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

requiereRol('admin');

$stats = [];

$queries = [
    'equipos'    => "SELECT COUNT(*) FROM EQUIPO",
    'jugadores'  => "SELECT COUNT(*) FROM JUGADOR",
    'torneos'    => "SELECT COUNT(*) FROM TORNEO",
    'temporadas' => "SELECT COUNT(*) FROM TEMPORADA",
    'pendientes' => "SELECT COUNT(*) FROM PARTIDO WHERE id_ganador IS NULL",
    'usuarios'   => "SELECT COUNT(*) FROM USUARIOS WHERE activo = 1",
];

foreach ($queries as $key => $sql) {
    $res = mysqli_query($conexion, $sql);
    $stats[$key] = mysqli_fetch_row($res)[0];
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-shield-lock"></i> Panel de Administración
    </h2>
</div>

<!-- KPIs -->
<div class="row g-3 mb-5">
    <?php
    $kpis = [
        ['label' => 'Equipos',            'value' => $stats['equipos'],    'icon' => 'people-fill',    'color' => 'text-accent'],
        ['label' => 'Jugadores',          'value' => $stats['jugadores'],  'icon' => 'person-badge',   'color' => 'text-accent'],
        ['label' => 'Torneos',            'value' => $stats['torneos'],    'icon' => 'award',          'color' => 'text-warning'],
        ['label' => 'Temporadas',         'value' => $stats['temporadas'], 'icon' => 'calendar3',      'color' => 'text-warning'],
        ['label' => 'Partidos Pendientes','value' => $stats['pendientes'], 'icon' => 'hourglass-split','color' => 'text-danger'],
        ['label' => 'Usuarios Activos',   'value' => $stats['usuarios'],   'icon' => 'person-gear',    'color' => 'text-success'],
    ];
    foreach ($kpis as $kpi): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card bg-card border-secondary h-100 text-center">
            <div class="card-body py-3">
                <i class="bi bi-<?= $kpi['icon'] ?> fs-2 <?= $kpi['color'] ?>"></i>
                <div class="fs-3 fw-bold text-white mt-1"><?= $kpi['value'] ?></div>
                <div class="small text-muted"><?= $kpi['label'] ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Accesos rápidos -->
<h5 class="text-white mb-3"><i class="bi bi-grid"></i> Módulos</h5>
<div class="row g-3">
    <?php
    $modulos = [
        ['url' => '/RLCS/CRM/pages/admin/temporadas/index.php',    'icon' => 'calendar3',     'title' => 'Temporadas',    'desc' => 'Gestionar temporadas RLCS'],
        ['url' => '/RLCS/CRM/pages/admin/regiones/index.php',      'icon' => 'globe',         'title' => 'Regiones',      'desc' => 'Gestionar regiones y plazas'],
        ['url' => '/RLCS/CRM/pages/admin/torneos/index.php',       'icon' => 'award',         'title' => 'Torneos',       'desc' => 'Crear y editar torneos'],
        ['url' => '/RLCS/CRM/pages/admin/partidos/index.php',      'icon' => 'joystick',      'title' => 'Partidos',      'desc' => 'Crear y gestionar partidos'],
        ['url' => '/RLCS/CRM/pages/admin/participacion/gestionar.php','icon' => 'diagram-3',  'title' => 'Participación', 'desc' => 'Asignar equipos a torneos'],
        ['url' => '/RLCS/CRM/pages/admin/puntos/gestionar.php',    'icon' => 'bar-chart',     'title' => 'Puntos RLCS',   'desc' => 'Gestionar puntos por temporada'],
        ['url' => '/RLCS/CRM/pages/admin/roster/gestionar.php',    'icon' => 'people',        'title' => 'Roster',        'desc' => 'Gestión directa de rosters'],
        ['url' => '/RLCS/CRM/pages/admin/estadisticas/entrada.php','icon' => 'graph-up',      'title' => 'Estadísticas',  'desc' => 'Introducir stats por partido'],
        ['url' => '/RLCS/CRM/pages/admin/bracket/gestionar.php',   'icon' => 'trophy',        'title' => 'Bracket',       'desc' => 'Gestionar fases de torneos'],
        ['url' => '/RLCS/CRM/pages/admin/usuarios.php',            'icon' => 'person-gear',   'title' => 'Usuarios',      'desc' => 'Roles y acceso al sistema'],
        ['url' => '/RLCS/CRM/pages/equipos/editar.php',            'icon' => 'people-fill',   'title' => 'Nuevo Equipo',  'desc' => 'Crear un equipo'],
        ['url' => '/RLCS/CRM/pages/jugadores/editar.php',          'icon' => 'person-badge',  'title' => 'Nuevo Jugador', 'desc' => 'Crear un jugador'],
    ];
    foreach ($modulos as $m): ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="<?= $m['url'] ?>" class="card bg-card border-secondary text-decoration-none h-100
                  admin-module-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="bi bi-<?= $m['icon'] ?> fs-2 text-accent flex-shrink-0"></i>
                <div>
                    <div class="text-white fw-semibold"><?= $m['title'] ?></div>
                    <div class="small text-muted"><?= $m['desc'] ?></div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<style>
.admin-module-card { transition: border-color .2s, transform .2s; }
.admin-module-card:hover { border-color: var(--color-accent) !important; transform: translateY(-2px); }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
