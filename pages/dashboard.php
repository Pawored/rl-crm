<?php
/**
 * DASHBOARD - Página principal del CRM
 * Muestra 4 tarjetas con estadísticas generales,
 * top 5 clasificación y últimos 5 partidos.
 */

// --- Incluir archivos necesarios ---
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/sesion.php';

// === LÓGICA PHP: Obtener datos para las tarjetas ===

// --- Total de equipos activos ---
$sql_equipos = "SELECT COUNT(*) as total FROM EQUIPO WHERE activo = 1";
$res_equipos = mysqli_query($conexion, $sql_equipos);
$total_equipos = mysqli_fetch_assoc($res_equipos)['total'] ?? 0;

// --- Total de jugadores ---
$sql_jugadores = "SELECT COUNT(*) as total FROM JUGADOR";
$res_jugadores = mysqli_query($conexion, $sql_jugadores);
$total_jugadores = mysqli_fetch_assoc($res_jugadores)['total'] ?? 0;

// --- Total de torneos ---
$sql_torneos = "SELECT COUNT(*) as total FROM TORNEO";
$res_torneos = mysqli_query($conexion, $sql_torneos);
$total_torneos = mysqli_fetch_assoc($res_torneos)['total'] ?? 0;

// --- Total de partidos ---
$sql_partidos = "SELECT COUNT(*) as total FROM PARTIDO";
$res_partidos = mysqli_query($conexion, $sql_partidos);
$total_partidos = mysqli_fetch_assoc($res_partidos)['total'] ?? 0;

// --- Regiones para filtro del Top 5 ---
$sql_regiones = "SELECT id_region, nombre, siglas FROM REGION ORDER BY nombre";
$res_regiones = mysqli_query($conexion, $sql_regiones);

$filtro_region = isset($_GET['region']) ? intval($_GET['region']) : 0;

// --- Top 5 clasificación (temporada más reciente, filtrable por región) ---
$sql_ultima_temp = "SELECT id_temporada FROM TEMPORADA ORDER BY anio DESC LIMIT 1";
$res_ultima_temp = mysqli_query($conexion, $sql_ultima_temp);
$id_temporada_top5 = ($res_ultima_temp && mysqli_num_rows($res_ultima_temp) > 0)
    ? mysqli_fetch_assoc($res_ultima_temp)['id_temporada']
    : 0;

$where_top5_parts = [];
if ($id_temporada_top5 > 0) {
    $where_top5_parts[] = "p.id_temporada = $id_temporada_top5";
}
if ($filtro_region > 0) {
    $where_top5_parts[] = "e.id_region = $filtro_region";
}
$where_top5 = !empty($where_top5_parts) ? "WHERE " . implode(' AND ', $where_top5_parts) : "";

$sql_top5 = "SELECT e.id_equipo, e.nombre AS equipo, e.tag,
                    r.nombre AS region, r.siglas AS region_siglas,
                    t.anio AS temporada,
                    p.puntos_regionals, p.puntos_majors, p.puntos_totales
             FROM PUNTOS_RLCS p
             INNER JOIN EQUIPO e ON p.id_equipo = e.id_equipo
             INNER JOIN REGION r ON e.id_region = r.id_region
             INNER JOIN TEMPORADA t ON p.id_temporada = t.id_temporada
             $where_top5
             ORDER BY p.puntos_totales DESC
             LIMIT 5";
$res_top5 = mysqli_query($conexion, $sql_top5);

// --- Últimos 5 partidos con resultado ---
$sql_ultimos = "SELECT p.id_partido, p.fecha_hora, p.formato,
                       e1.nombre AS equipo1, e1.tag AS tag1,
                       e2.nombre AS equipo2, e2.tag AS tag2,
                       g.nombre AS ganador, g.tag AS tag_ganador,
                       t.nombre AS torneo
                FROM PARTIDO p
                INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
                INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
                LEFT JOIN EQUIPO g ON p.id_ganador = g.id_equipo
                LEFT JOIN TORNEO t ON p.id_torneo = t.id_torneo
                ORDER BY p.fecha_hora DESC
                LIMIT 5";
$res_ultimos = mysqli_query($conexion, $sql_ultimos);

// --- Incluir header ---
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ========== TÍTULO DE PÁGINA ========== -->
<h2 class="text-white mb-4">
    <i class="bi bi-speedometer2"></i> Dashboard
</h2>

<!-- ========== 4 TARJETAS DE ESTADÍSTICAS ========== -->
<div class="row mb-4">
    <!-- Tarjeta: Equipos Activos -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card card-stat card-stat-equipos">
            <div class="card-body text-center">
                <i class="bi bi-people-fill display-4 text-info"></i>
                <h3 class="fw-bold text-white mt-2"><?= $total_equipos ?></h3>
                <p class="text-muted mb-0">Equipos Activos</p>
            </div>
        </div>
    </div>
    <!-- Tarjeta: Jugadores -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card card-stat card-stat-jugadores">
            <div class="card-body text-center">
                <i class="bi bi-person-badge display-4 text-success"></i>
                <h3 class="fw-bold text-white mt-2"><?= $total_jugadores ?></h3>
                <p class="text-muted mb-0">Jugadores</p>
            </div>
        </div>
    </div>
    <!-- Tarjeta: Torneos -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card card-stat card-stat-torneos">
            <div class="card-body text-center">
                <i class="bi bi-award display-4 text-warning"></i>
                <h3 class="fw-bold text-white mt-2"><?= $total_torneos ?></h3>
                <p class="text-muted mb-0">Torneos</p>
            </div>
        </div>
    </div>
    <!-- Tarjeta: Partidos -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card card-stat card-stat-partidos">
            <div class="card-body text-center">
                <i class="bi bi-joystick display-4 text-danger"></i>
                <h3 class="fw-bold text-white mt-2"><?= $total_partidos ?></h3>
                <p class="text-muted mb-0">Partidos</p>
            </div>
        </div>
    </div>
</div>

<!-- ========== CONTENIDO PRINCIPAL: 2 COLUMNAS ========== -->
<div class="row">
    <!-- COLUMNA IZQUIERDA: Top 5 Clasificación -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-trophy"></i> Top 5 Clasificación
                </h5>
                <form method="GET">
                    <select name="region"
                            class="form-select form-select-sm bg-dark text-white border-secondary"
                            style="min-width: 150px"
                            onchange="this.form.submit()">
                        <option value="0">Todas las regiones</option>
                        <?php while ($region = mysqli_fetch_assoc($res_regiones)): ?>
                            <option value="<?= $region['id_region'] ?>"
                                    <?= ($filtro_region == $region['id_region']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['nombre']) ?>
                                (<?= htmlspecialchars($region['siglas']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Equipo</th>
                            <th>Regionals</th>
                            <th>Majors</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_top5 && mysqli_num_rows($res_top5) > 0): ?>
                            <?php $posicion = 1; ?>
                            <?php while ($fila = mysqli_fetch_assoc($res_top5)): ?>
                                <tr>
                                    <td>
                                        <?php
                                        // Colores especiales para top 3
                                        if ($posicion === 1) {
                                            $clase_pos = 'text-warning fw-bold';    // Oro
                                        } elseif ($posicion === 2) {
                                            $clase_pos = 'text-secondary fw-bold';  // Plata
                                        } elseif ($posicion === 3) {
                                            $clase_pos = 'text-bronze fw-bold';     // Bronce
                                        } else {
                                            $clase_pos = 'text-white';
                                        }
                                        ?>
                                        <span class="<?= $clase_pos ?>"><?= $posicion ?>º</span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($fila['equipo'] ?? '') ?></strong>
                                        <small class="text-muted">
                                            [<?= htmlspecialchars($fila['tag'] ?? '') ?>]
                                        </small>
                                    </td>
                                    <td><?= $fila['puntos_regionals'] ?? 0 ?></td>
                                    <td><?= $fila['puntos_majors'] ?? 0 ?></td>
                                    <td><strong class="text-accent"><?= $fila['puntos_totales'] ?? 0 ?></strong></td>
                                </tr>
                                <?php $posicion++; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle"></i> No hay datos de clasificación disponibles.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-dark border-secondary text-end">
                <a href="/RLCS/CRM/pages/clasificacion/index.php?region=<?= $filtro_region ?>"
                   class="text-accent text-decoration-none">
                    Ver clasificación completa <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Últimos 5 Partidos -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-joystick"></i> Últimos Partidos
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Enfrentamiento</th>
                            <th>Ganador</th>
                            <th>Torneo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_ultimos && mysqli_num_rows($res_ultimos) > 0): ?>
                            <?php while ($partido = mysqli_fetch_assoc($res_ultimos)): ?>
                                <tr>
                                    <td class="text-muted">
                                        <?= $partido['fecha_hora']
                                            ? date('d/m/Y', strtotime($partido['fecha_hora']))
                                            : 'Sin fecha' ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($partido['tag1']) ?></strong>
                                        <span class="text-muted">vs</span>
                                        <strong><?= htmlspecialchars($partido['tag2']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($partido['ganador']): ?>
                                            <span class="text-success fw-bold">
                                                <i class="bi bi-trophy-fill"></i>
                                                <?= htmlspecialchars($partido['tag_ganador']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-warning">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($partido['torneo'] ?? 'N/A') ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle"></i> No hay partidos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-dark border-secondary text-end">
                <a href="/RLCS/CRM/pages/partidos/index.php" class="text-accent text-decoration-none">
                    Ver todos los partidos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
