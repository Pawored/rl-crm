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

// --- Roster actual (vista_rosters_actuales) ---
$sql_roster = "SELECT * FROM vista_rosters_actuales
               WHERE id_equipo = $id ORDER BY titular DESC, nickname ASC";
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

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== CABECERA DEL EQUIPO ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
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
                        <?php if ($res_puntos && mysqli_num_rows($res_puntos) > 0): ?>
                            <?php while ($punto = mysqli_fetch_assoc($res_puntos)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($punto['anio']) ?></td>
                                    <td><?= $punto['puntos_regionals'] ?></td>
                                    <td><?= $punto['puntos_majors'] ?></td>
                                    <td><strong class="text-accent"><?= $punto['puntos_totales'] ?></strong></td>
                                </tr>
                            <?php endwhile; ?>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
