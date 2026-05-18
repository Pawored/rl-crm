<?php
/**
 * EQUIPOS - Detalle de un equipo
 * Muestra cabecera con datos, roster actual, historial de torneos
 * y puntos por temporada.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener ID del equipo ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['mensaje_error'] = "Equipo no encontrado.";
    header("Location: /RLCS/CRM/pages/equipos/index.php");
    exit();
}

// --- Datos del equipo ---
$sql_equipo = "SELECT e.*, r.nombre AS region, r.siglas AS region_siglas
               FROM EQUIPO e
               LEFT JOIN REGION r ON e.id_region = r.id_region
               WHERE e.id_equipo = $id";
$res_equipo = mysqli_query($conexion, $sql_equipo);
$equipo = mysqli_fetch_assoc($res_equipo);

if (!$equipo) {
    $_SESSION['mensaje_error'] = "Equipo no encontrado.";
    header("Location: /RLCS/CRM/pages/equipos/index.php");
    exit();
}

// --- Roster actual ---
$sql_roster = "SELECT j.id_jugador, j.nickname, j.pais, ro.titular, ro.fecha_inicio
               FROM ROSTER ro
               INNER JOIN JUGADOR j ON j.id_jugador = ro.id_jugador
               WHERE ro.id_equipo = $id AND ro.fecha_fin IS NULL
               ORDER BY ro.titular DESC, j.nickname ASC";
$res_roster = mysqli_query($conexion, $sql_roster);

// --- Historial de torneos (participaciones) ---
$sql_torneos = "SELECT t.nombre AS torneo, t.tipo, te.anio AS temporada,
                       p.posicion_final, p.puntos_ganados
                FROM PARTICIPACION p
                INNER JOIN TORNEO t ON p.id_torneo = t.id_torneo
                LEFT JOIN TEMPORADA te ON t.id_temporada = te.id_temporada
                WHERE p.id_equipo = $id
                ORDER BY te.anio DESC, t.nombre ASC";
$res_torneos = mysqli_query($conexion, $sql_torneos);

// --- Puntos por temporada ---
$sql_puntos = "SELECT pr.*, te.anio
               FROM PUNTOS_RLCS pr
               INNER JOIN TEMPORADA te ON pr.id_temporada = te.id_temporada
               WHERE pr.id_equipo = $id
               ORDER BY te.anio DESC";
$res_puntos = mysqli_query($conexion, $sql_puntos);

// Recoger puntos en array para reutilizar en chart + tabla
$puntos_arr = [];
while ($p = mysqli_fetch_assoc($res_puntos)) {
    $puntos_arr[] = $p;
}
// Preparar datos para el chart (orden cronológico ASC)
$chart_anios      = array_column(array_reverse($puntos_arr), 'anio');
$chart_regionals  = array_column(array_reverse($puntos_arr), 'puntos_regionals');
$chart_majors     = array_column(array_reverse($puntos_arr), 'puntos_majors');
$chart_totales    = array_column(array_reverse($puntos_arr), 'puntos_totales');

$page_title = htmlspecialchars($equipo['nombre']);
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== CABECERA DEL EQUIPO ========== -->
<?php $color = htmlspecialchars($equipo['color_primario'] ?? '#00d4ff'); ?>
<div class="card bg-dark border-secondary mb-4"
     style="border-top: 4px solid <?= $color ?> !important">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <?php if (!empty($equipo['logo_url'])): ?>
                <img src="<?= htmlspecialchars($equipo['logo_url']) ?>"
                     alt="Logo <?= htmlspecialchars($equipo['nombre']) ?>"
                     style="width:64px;height:64px;object-fit:contain;flex-shrink:0"
                     onerror="this.style.display='none'">
                <?php endif; ?>
                <div>
                    <h2 class="text-white mb-1">
                        <?= htmlspecialchars($equipo['nombre']) ?>
                        <span class="badge bg-secondary fs-6"><?= htmlspecialchars($equipo['tag']) ?></span>
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt"></i>
                        Región: <strong><?= htmlspecialchars($equipo['region'] ?? 'Sin región') ?></strong>
                        (<?= htmlspecialchars($equipo['region_siglas'] ?? '') ?>)
                        &nbsp;|&nbsp;
                        Estado:
                        <?php if ($equipo['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div>
                <a href="/RLCS/CRM/pages/equipos/index.php" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <?php if (tieneRol(['admin', 'editor'])): ?>
                    <a href="/RLCS/CRM/pages/equipos/editar.php?id=<?= $id ?>"
                       class="btn btn-outline-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ========== ROSTER ACTUAL ========== -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-person-badge"></i> Roster Actual
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>País</th>
                            <th>Tipo</th>
                            <th>Desde</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_roster && mysqli_num_rows($res_roster) > 0): ?>
                            <?php while ($jugador = mysqli_fetch_assoc($res_roster)): ?>
                                <tr>
                                    <td>
                                        <a href="/RLCS/CRM/pages/jugadores/detalle.php?id=<?= $jugador['id_jugador'] ?>"
                                           class="text-accent text-decoration-none">
                                            <?= htmlspecialchars($jugador['nickname']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($jugador['pais'] ?? '') ?></td>
                                    <td>
                                        <?php if ($jugador['titular']): ?>
                                            <span class="badge bg-success">Titular</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Suplente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted">
                                        <?= $jugador['fecha_inicio']
                                            ? date('d/m/Y', strtotime($jugador['fecha_inicio']))
                                            : 'N/A' ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Sin jugadores en el roster actual.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== PUNTOS POR TEMPORADA ========== -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-graph-up"></i> Puntos por Temporada
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Temporada</th>
                            <th>Regionals</th>
                            <th>Majors</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($puntos_arr)): ?>
                            <?php foreach ($puntos_arr as $punto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($punto['anio']) ?></td>
                                    <td><?= $punto['puntos_regionals'] ?></td>
                                    <td><?= $punto['puntos_majors'] ?></td>
                                    <td><strong class="text-accent"><?= $punto['puntos_totales'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Sin datos de puntos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== GRÁFICA DE PUNTOS POR TEMPORADA ========== -->
<?php if (!empty($puntos_arr)): ?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-bar-chart-fill"></i> Evolución de Puntos por Temporada
        </h5>
    </div>
    <div class="card-body">
        <canvas id="chartPuntos" height="80"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- ========== HISTORIAL DE TORNEOS ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-award"></i> Historial de Torneos
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Torneo</th>
                    <th>Tipo</th>
                    <th>Temporada</th>
                    <th>Posición</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res_torneos && mysqli_num_rows($res_torneos) > 0): ?>
                    <?php while ($torneo = mysqli_fetch_assoc($res_torneos)): ?>
                        <tr>
                            <td><?= htmlspecialchars($torneo['torneo']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($torneo['tipo'] ?? '') ?></span></td>
                            <td><?= htmlspecialchars($torneo['temporada'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $pos = $torneo['posicion_final'];
                                $clase = match($pos) {
                                    1 => 'text-warning fw-bold',
                                    2 => 'text-secondary fw-bold',
                                    3 => 'text-bronze fw-bold',
                                    default => 'text-white'
                                };
                                ?>
                                <span class="<?= $clase ?>"><?= $pos ? $pos . 'º' : 'N/A' ?></span>
                            </td>
                            <td><?= $torneo['puntos_ganados'] ?? 0 ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Sin participaciones en torneos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($puntos_arr)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    new Chart(document.getElementById('chartPuntos'), {
        data: {
            labels: <?= json_encode($chart_anios) ?>,
            datasets: [
                {
                    type: 'bar',
                    label: 'Regionals',
                    data: <?= json_encode($chart_regionals) ?>,
                    backgroundColor: 'rgba(0,212,255,0.7)',
                    borderColor: '#00d4ff',
                    borderWidth: 1
                },
                {
                    type: 'bar',
                    label: 'Majors',
                    data: <?= json_encode($chart_majors) ?>,
                    backgroundColor: 'rgba(13,110,253,0.7)',
                    borderColor: '#0d6efd',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Total',
                    data: <?= json_encode($chart_totales) ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255,193,7,0.15)',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    borderWidth: 2,
                    yAxisID: 'y'
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
                    stacked: false,
                    ticks: { color: '#adb5bd' },
                    grid: { color: 'rgba(255,255,255,0.07)' }
                }
            }
        }
    });
})();
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
