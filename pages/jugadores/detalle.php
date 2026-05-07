<?php
/**
 * JUGADORES - Detalle de un jugador
 * Muestra datos personales, stats totales (goles, asistencias,
 * salvadas, tiros, MVPs, media goles/partido) e historial de equipos.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener ID del jugador ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['mensaje_error'] = "Jugador no encontrado.";
    header("Location: /RLCS/CRM/pages/jugadores/index.php");
    exit();
}

// --- Datos del jugador ---
$sql_jugador = "SELECT * FROM JUGADOR WHERE id_jugador = $id";
$res_jugador = mysqli_query($conexion, $sql_jugador);
$jugador = mysqli_fetch_assoc($res_jugador);

if (!$jugador) {
    $_SESSION['mensaje_error'] = "Jugador no encontrado.";
    header("Location: /RLCS/CRM/pages/jugadores/index.php");
    exit();
}

// --- Stats totales del jugador ---
$sql_stats = "SELECT
                COALESCE(SUM(goles), 0) AS total_goles,
                COALESCE(SUM(asistencias), 0) AS total_asistencias,
                COALESCE(SUM(salvadas), 0) AS total_salvadas,
                COALESCE(SUM(tiros), 0) AS total_tiros,
                COALESCE(SUM(mvp), 0) AS total_mvps,
                COUNT(DISTINCT id_partido) AS total_partidos
              FROM ESTADISTICAS_JUGADOR
              WHERE id_jugador = $id";
$res_stats = mysqli_query($conexion, $sql_stats);
$stats = mysqli_fetch_assoc($res_stats);

// Calcular media de goles por partido (evitar división por 0)
$media_goles = ($stats['total_partidos'] > 0)
    ? round($stats['total_goles'] / $stats['total_partidos'], 2)
    : 0;

// --- Tendencia por partido (últimas 15 actuaciones) ---
$sql_trend = "SELECT p.fecha_hora, ej.goles, ej.asistencias, ej.salvadas
              FROM ESTADISTICAS_JUGADOR ej
              INNER JOIN PARTIDO p ON ej.id_partido = p.id_partido
              WHERE ej.id_jugador = $id
              ORDER BY p.fecha_hora ASC
              LIMIT 15";
$res_trend = mysqli_query($conexion, $sql_trend);
$trend_labels = $trend_goles = $trend_asistencias = $trend_salvadas = [];
while ($row = mysqli_fetch_assoc($res_trend)) {
    $trend_labels[]     = date('d/m', strtotime($row['fecha_hora']));
    $trend_goles[]      = (int)$row['goles'];
    $trend_asistencias[] = (int)$row['asistencias'];
    $trend_salvadas[]   = (int)$row['salvadas'];
}

// --- Historial de equipos (ROSTER con fechas) ---
$sql_historial = "SELECT r.fecha_inicio, r.fecha_fin, r.titular,
                         e.nombre AS equipo, e.tag
                  FROM ROSTER r
                  INNER JOIN EQUIPO e ON r.id_equipo = e.id_equipo
                  WHERE r.id_jugador = $id
                  ORDER BY r.fecha_inicio DESC";
$res_historial = mysqli_query($conexion, $sql_historial);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== CABECERA DEL JUGADOR ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1">
                    <i class="bi bi-person-badge"></i>
                    <?= htmlspecialchars($jugador['nickname']) ?>
                </h2>
                <p class="text-muted mb-0">
                    <strong>Nombre real:</strong>
                    <?= htmlspecialchars($jugador['nombre_real'] ?? 'No disponible') ?>
                    &nbsp;|&nbsp;
                    <i class="bi bi-geo-alt"></i>
                    <strong>País:</strong> <?= htmlspecialchars($jugador['pais'] ?? 'N/A') ?>
                    &nbsp;|&nbsp;
                    <i class="bi bi-calendar"></i>
                    <strong>Nacimiento:</strong>
                    <?= $jugador['fecha_nacimiento']
                        ? date('d/m/Y', strtotime($jugador['fecha_nacimiento']))
                        : 'N/A' ?>
                </p>
            </div>
            <div>
                <a href="/RLCS/CRM/pages/jugadores/index.php" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <?php if (tieneRol(['admin', 'editor'])): ?>
                    <a href="/RLCS/CRM/pages/jugadores/editar.php?id=<?= $id ?>"
                       class="btn btn-outline-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ========== STATS TOTALES (tarjetas) ========== -->
<div class="row mb-4">
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-accent fw-bold mb-0"><?= $stats['total_goles'] ?></h4>
                <small class="text-muted">Goles</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-info fw-bold mb-0"><?= $stats['total_asistencias'] ?></h4>
                <small class="text-muted">Asistencias</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-success fw-bold mb-0"><?= $stats['total_salvadas'] ?></h4>
                <small class="text-muted">Salvadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-warning fw-bold mb-0"><?= $stats['total_tiros'] ?></h4>
                <small class="text-muted">Tiros</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-danger fw-bold mb-0">
                    <i class="bi bi-star-fill"></i> <?= $stats['total_mvps'] ?>
                </h4>
                <small class="text-muted">MVPs</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card bg-dark border-secondary text-center">
            <div class="card-body py-3">
                <h4 class="text-white fw-bold mb-0"><?= $media_goles ?></h4>
                <small class="text-muted">Media Goles/Partido</small>
            </div>
        </div>
    </div>
</div>

<!-- ========== GRÁFICA DE TENDENCIA ========== -->
<?php if (!empty($trend_labels)): ?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-graph-up-arrow"></i> Tendencia por Partido
        </h5>
    </div>
    <div class="card-body">
        <canvas id="chartTendencia" height="100"></canvas>
    </div>
</div>
<?php else: ?>
<div class="alert alert-secondary mb-4">
    <i class="bi bi-bar-chart-line"></i> Sin estadísticas registradas para mostrar gráfica.
</div>
<?php endif; ?>

<!-- ========== HISTORIAL DE EQUIPOS ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-clock-history"></i> Historial de Equipos
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Tag</th>
                    <th>Tipo</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res_historial && mysqli_num_rows($res_historial) > 0): ?>
                    <?php while ($hist = mysqli_fetch_assoc($res_historial)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($hist['equipo']) ?></strong></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($hist['tag']) ?></span></td>
                            <td>
                                <?php if ($hist['titular']): ?>
                                    <span class="badge bg-success">Titular</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Suplente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $hist['fecha_inicio']
                                    ? date('d/m/Y', strtotime($hist['fecha_inicio']))
                                    : 'N/A' ?>
                            </td>
                            <td>
                                <?php if ($hist['fecha_fin']): ?>
                                    <?= date('d/m/Y', strtotime($hist['fecha_fin'])) ?>
                                <?php else: ?>
                                    <span class="badge bg-info">Actual</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Sin historial de equipos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($trend_labels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const ctx = document.getElementById('chartTendencia');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trend_labels) ?>,
            datasets: [
                {
                    label: 'Goles',
                    data: <?= json_encode($trend_goles) ?>,
                    borderColor: '#00d4ff',
                    backgroundColor: 'rgba(0,212,255,0.15)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4
                },
                {
                    label: 'Asistencias',
                    data: <?= json_encode($trend_asistencias) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 4
                },
                {
                    label: 'Salvadas',
                    data: <?= json_encode($trend_salvadas) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.1)',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#adb5bd' } }
            },
            scales: {
                x: {
                    ticks: { color: '#adb5bd' },
                    grid: { color: 'rgba(255,255,255,0.07)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#adb5bd', stepSize: 1 },
                    grid: { color: 'rgba(255,255,255,0.07)' }
                }
            }
        }
    });
})();
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
