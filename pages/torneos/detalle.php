<?php
/**
 * TORNEOS - Detalle de un torneo
 * Muestra info del torneo, equipos participantes con posición,
 * partidos del torneo e info del bracket.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['mensaje_error'] = "Torneo no encontrado.";
    header("Location: /RLCS/CRM/pages/torneos/index.php");
    exit();
}

// --- Datos del torneo ---
$sql_torneo = "SELECT t.*, te.anio AS temporada
               FROM TORNEO t
               LEFT JOIN TEMPORADA te ON t.id_temporada = te.id_temporada
               WHERE t.id_torneo = $id";
$res_torneo = mysqli_query($conexion, $sql_torneo);
$torneo = mysqli_fetch_assoc($res_torneo);

if (!$torneo) {
    $_SESSION['mensaje_error'] = "Torneo no encontrado.";
    header("Location: /RLCS/CRM/pages/torneos/index.php");
    exit();
}

// --- Equipos participantes ---
$sql_equipos = "SELECT p.posicion_final, p.puntos_ganados,
                       e.id_equipo, e.nombre, e.tag
                FROM PARTICIPACION p
                INNER JOIN EQUIPO e ON p.id_equipo = e.id_equipo
                WHERE p.id_torneo = $id
                ORDER BY p.posicion_final ASC, e.nombre ASC";
$res_equipos = mysqli_query($conexion, $sql_equipos);

// --- Partidos del torneo ---
$sql_partidos = "SELECT p.id_partido, p.fecha_hora, p.formato,
                        e1.tag AS tag1, e1.nombre AS equipo1,
                        e2.tag AS tag2, e2.nombre AS equipo2,
                        g.tag AS tag_ganador, p.id_ganador,
                        e1.id_equipo AS id_eq1, e2.id_equipo AS id_eq2
                 FROM PARTIDO p
                 INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
                 INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
                 LEFT JOIN EQUIPO g ON p.id_ganador = g.id_equipo
                 WHERE p.id_torneo = $id
                 ORDER BY p.fecha_hora ASC";
$res_partidos = mysqli_query($conexion, $sql_partidos);

// --- Info del bracket ---
$sql_bracket = "SELECT * FROM BRACKET WHERE id_torneo = $id ORDER BY ronda ASC, fase ASC";
$res_bracket = mysqli_query($conexion, $sql_bracket);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== CABECERA DEL TORNEO ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1">
                    <i class="bi bi-award"></i>
                    <?= htmlspecialchars($torneo['nombre']) ?>
                </h2>
                <p class="text-muted mb-0">
                    <span class="badge bg-info"><?= htmlspecialchars($torneo['tipo'] ?? 'N/A') ?></span>
                    &nbsp;|&nbsp;
                    <i class="bi bi-calendar"></i>
                    Temporada: <strong><?= htmlspecialchars($torneo['temporada'] ?? 'N/A') ?></strong>
                    &nbsp;|&nbsp;
                    <i class="bi bi-cash"></i>
                    Prize Pool:
                    <strong class="text-success">
                        <?= $torneo['prize_pool']
                            ? '$' . number_format($torneo['prize_pool'], 0, ',', '.')
                            : 'N/A' ?>
                    </strong>
                </p>
            </div>
            <a href="/RLCS/CRM/pages/torneos/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- ========== EQUIPOS PARTICIPANTES ========== -->
    <div class="col-lg-5 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-people"></i> Equipos Participantes
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Equipo</th>
                            <th class="text-center">Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_equipos && mysqli_num_rows($res_equipos) > 0): ?>
                            <?php while ($eq = mysqli_fetch_assoc($res_equipos)): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $pos = $eq['posicion_final'];
                                        $clase = match($pos) {
                                            1 => 'text-warning fw-bold',
                                            2 => 'text-secondary fw-bold',
                                            3 => 'text-bronze fw-bold',
                                            default => 'text-white'
                                        };
                                        ?>
                                        <span class="<?= $clase ?>">
                                            <?= $pos ? $pos . 'º' : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/RLCS/CRM/pages/equipos/detalle.php?id=<?= $eq['id_equipo'] ?>"
                                           class="text-accent text-decoration-none">
                                            <span class="badge bg-secondary"><?= htmlspecialchars($eq['tag']) ?></span>
                                            <?= htmlspecialchars($eq['nombre']) ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?= $eq['puntos_ganados'] ?? 0 ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    Sin equipos participantes.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== PARTIDOS DEL TORNEO ========== -->
    <div class="col-lg-7 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent">
                    <i class="bi bi-joystick"></i> Partidos del Torneo
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Enfrentamiento</th>
                            <th>Ganador</th>
                            <th>Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_partidos && mysqli_num_rows($res_partidos) > 0): ?>
                            <?php while ($p = mysqli_fetch_assoc($res_partidos)): ?>
                                <tr>
                                    <td class="text-muted">
                                        <?= $p['fecha_hora']
                                            ? date('d/m/Y H:i', strtotime($p['fecha_hora']))
                                            : 'N/A' ?>
                                    </td>
                                    <td>
                                        <span class="<?= ($p['id_ganador'] == $p['id_eq1']) ? 'text-success fw-bold' : '' ?>">
                                            <?= htmlspecialchars($p['tag1']) ?>
                                        </span>
                                        <span class="text-muted"> vs </span>
                                        <span class="<?= ($p['id_ganador'] == $p['id_eq2']) ? 'text-success fw-bold' : '' ?>">
                                            <?= htmlspecialchars($p['tag2']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($p['tag_ganador']): ?>
                                            <span class="text-success">
                                                <i class="bi bi-trophy-fill"></i>
                                                <?= htmlspecialchars($p['tag_ganador']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-warning">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= htmlspecialchars($p['formato'] ?? '') ?></small></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Sin partidos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== INFO DEL BRACKET ========== -->
<?php if ($res_bracket && mysqli_num_rows($res_bracket) > 0): ?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-diagram-3"></i> Estructura del Bracket
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Ronda</th>
                    <th>Fase</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($res_bracket)): ?>
                    <tr>
                        <td><span class="badge bg-info"><?= htmlspecialchars($b['tipo_bracket'] ?? '') ?></span></td>
                        <td><?= htmlspecialchars($b['ronda'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['fase'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
