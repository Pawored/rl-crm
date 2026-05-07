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

// --- Chart 1: Partidos por mes (últimos 6 meses) ---
$chart_meses = $chart_partidos = [];
$res_meses = mysqli_query($conexion,
    "SELECT DATE_FORMAT(fecha_hora, '%Y-%m') AS mes, COUNT(*) AS total
     FROM PARTIDO
     WHERE fecha_hora >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY mes ORDER BY mes ASC"
);
while ($row = mysqli_fetch_assoc($res_meses)) {
    $chart_meses[]   = $row['mes'];
    $chart_partidos[] = (int)$row['total'];
}

// --- Chart 2: Top 10 equipos por puntos en la última temporada ---
$chart_equipos_names = $chart_equipos_pts = [];
$res_top = mysqli_query($conexion,
    "SELECT e.nombre, pr.puntos_totales
     FROM PUNTOS_RLCS pr
     JOIN EQUIPO e ON e.id_equipo = pr.id_equipo
     JOIN TEMPORADA t ON t.id_temporada = pr.id_temporada
     WHERE t.anio = (SELECT MAX(anio) FROM TEMPORADA)
     ORDER BY pr.puntos_totales DESC
     LIMIT 10"
);
while ($row = mysqli_fetch_assoc($res_top)) {
    $chart_equipos_names[] = $row['nombre'];
    $chart_equipos_pts[]   = (int)$row['puntos_totales'];
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

<!-- ========== CHARTS ========== -->
<div class="row g-4 mb-5">
    <!-- Chart: Partidos por mes -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary h-100">
            <div class="card-header bg-dark border-secondary">
                <h6 class="mb-0 text-accent">
                    <i class="bi bi-calendar-week"></i> Partidos por Mes (últimos 6 meses)
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($chart_meses)): ?>
                <canvas id="chartMeses" height="160"></canvas>
                <?php else: ?>
                <p class="text-muted text-center pt-4">Sin partidos registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Chart: Top equipos por puntos -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary h-100">
            <div class="card-header bg-dark border-secondary">
                <h6 class="mb-0 text-accent">
                    <i class="bi bi-bar-chart-fill"></i> Top Equipos — Puntos Última Temporada
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($chart_equipos_names)): ?>
                <canvas id="chartEquipos" height="160"></canvas>
                <?php else: ?>
                <p class="text-muted text-center pt-4">Sin datos de puntos.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
        ['url' => '/RLCS/CRM/pages/admin/importar/index.php',      'icon' => 'upload',        'title' => 'Importar',      'desc' => 'Carga masiva CSV/JSON'],
        ['url' => '/RLCS/CRM/pages/admin/auditoria/index.php',     'icon' => 'journal-text',  'title' => 'Auditoría',     'desc' => 'Registro de cambios'],
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($chart_meses)): ?>
new Chart(document.getElementById('chartMeses'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_meses) ?>,
        datasets: [{
            label: 'Partidos',
            data: <?= json_encode($chart_partidos) ?>,
            borderColor: '#00d4ff',
            backgroundColor: 'rgba(0,212,255,0.15)',
            fill: true,
            tension: 0.3,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#adb5bd' } } },
        scales: {
            x: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255,255,255,0.07)' } },
            y: { beginAtZero: true, ticks: { color: '#adb5bd', stepSize: 1 },
                 grid: { color: 'rgba(255,255,255,0.07)' } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($chart_equipos_names)): ?>
new Chart(document.getElementById('chartEquipos'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart_equipos_names) ?>,
        datasets: [{
            label: 'Puntos totales',
            data: <?= json_encode($chart_equipos_pts) ?>,
            backgroundColor: [
                'rgba(0,212,255,0.8)','rgba(13,110,253,0.8)','rgba(25,135,84,0.8)',
                'rgba(255,193,7,0.8)','rgba(220,53,69,0.8)','rgba(102,16,242,0.8)',
                'rgba(0,212,255,0.6)','rgba(13,110,253,0.6)','rgba(25,135,84,0.6)',
                'rgba(255,193,7,0.6)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255,255,255,0.07)' } },
            y: { ticks: { color: '#adb5bd' }, grid: { color: 'rgba(255,255,255,0.07)' } }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
