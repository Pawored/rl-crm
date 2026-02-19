<?php
/**
 * BÚSQUEDA GLOBAL
 * Recibe ?q=término desde la barra de búsqueda del header.
 * Busca en equipos, jugadores y torneos simultáneamente.
 * Muestra resultados separados por secciones.
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/sesion.php';

// === LÓGICA PHP ===

// --- Recoger término de búsqueda ---
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$q_safe = mysqli_real_escape_string($conexion, $q);

$equipos = [];
$jugadores = [];
$torneos = [];

// --- Solo buscar si hay término ---
if (strlen($q) >= 2) {
    // Buscar en EQUIPOS (por nombre o tag)
    $sql_equipos = "SELECT e.id_equipo, e.nombre, e.tag, e.activo,
                           r.nombre AS region
                    FROM EQUIPO e
                    LEFT JOIN REGION r ON e.id_region = r.id_region
                    WHERE e.nombre LIKE '%$q_safe%' OR e.tag LIKE '%$q_safe%'
                    ORDER BY e.nombre ASC
                    LIMIT 20";
    $res_eq = mysqli_query($conexion, $sql_equipos);
    if ($res_eq) {
        while ($fila = mysqli_fetch_assoc($res_eq)) {
            $equipos[] = $fila;
        }
    }

    // Buscar en JUGADORES (por nickname o nombre_real)
    $sql_jugadores = "SELECT j.id_jugador, j.nickname, j.nombre_real, j.pais,
                             eq.nombre AS equipo, eq.tag AS tag_equipo
                      FROM JUGADOR j
                      LEFT JOIN ROSTER ro ON j.id_jugador = ro.id_jugador AND ro.fecha_fin IS NULL
                      LEFT JOIN EQUIPO eq ON ro.id_equipo = eq.id_equipo
                      WHERE j.nickname LIKE '%$q_safe%' OR j.nombre_real LIKE '%$q_safe%'
                      ORDER BY j.nickname ASC
                      LIMIT 20";
    $res_jug = mysqli_query($conexion, $sql_jugadores);
    if ($res_jug) {
        while ($fila = mysqli_fetch_assoc($res_jug)) {
            $jugadores[] = $fila;
        }
    }

    // Buscar en TORNEOS (por nombre)
    $sql_torneos = "SELECT t.id_torneo, t.nombre, t.tipo, t.prize_pool,
                           te.anio AS temporada
                    FROM TORNEO t
                    LEFT JOIN TEMPORADA te ON t.id_temporada = te.id_temporada
                    WHERE t.nombre LIKE '%$q_safe%'
                    ORDER BY te.anio DESC, t.nombre ASC
                    LIMIT 20";
    $res_tor = mysqli_query($conexion, $sql_torneos);
    if ($res_tor) {
        while ($fila = mysqli_fetch_assoc($res_tor)) {
            $torneos[] = $fila;
        }
    }
}

$total_resultados = count($equipos) + count($jugadores) + count($torneos);

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<h2 class="text-white mb-2">
    <i class="bi bi-search"></i> Resultados de Búsqueda
</h2>
<p class="text-muted mb-4">
    <?php if (!empty($q)): ?>
        Se encontraron <strong class="text-accent"><?= $total_resultados ?></strong>
        resultados para "<strong><?= htmlspecialchars($q) ?></strong>"
    <?php else: ?>
        Introduce un término de búsqueda.
    <?php endif; ?>
</p>

<?php if (strlen($q) < 2 && !empty($q)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        El término de búsqueda debe tener al menos 2 caracteres.
    </div>
<?php endif; ?>

<?php if (!empty($q) && strlen($q) >= 2): ?>

    <!-- ========== SECCIÓN: EQUIPOS ========== -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-header bg-dark border-secondary">
            <h5 class="mb-0">
                <i class="bi bi-people-fill text-info"></i>
                Equipos encontrados
                <span class="badge bg-info"><?= count($equipos) ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($equipos)): ?>
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tag</th>
                            <th>Región</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipos as $eq): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($eq['nombre']) ?></strong></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($eq['tag']) ?></span></td>
                                <td><?= htmlspecialchars($eq['region'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge <?= $eq['activo'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $eq['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/RLCS/CRM/pages/equipos/detalle.php?id=<?= $eq['id_equipo'] ?>"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted p-3 mb-0">No se encontraron equipos.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========== SECCIÓN: JUGADORES ========== -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-header bg-dark border-secondary">
            <h5 class="mb-0">
                <i class="bi bi-person-badge text-success"></i>
                Jugadores encontrados
                <span class="badge bg-success"><?= count($jugadores) ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($jugadores)): ?>
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nickname</th>
                            <th>Nombre Real</th>
                            <th>País</th>
                            <th>Equipo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jugadores as $jug): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($jug['nickname']) ?></strong></td>
                                <td class="text-muted"><?= htmlspecialchars($jug['nombre_real'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($jug['pais'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($jug['equipo']): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($jug['tag_equipo']) ?></span>
                                        <?= htmlspecialchars($jug['equipo']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin equipo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/RLCS/CRM/pages/jugadores/detalle.php?id=<?= $jug['id_jugador'] ?>"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted p-3 mb-0">No se encontraron jugadores.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========== SECCIÓN: TORNEOS ========== -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-header bg-dark border-secondary">
            <h5 class="mb-0">
                <i class="bi bi-award text-warning"></i>
                Torneos encontrados
                <span class="badge bg-warning text-dark"><?= count($torneos) ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($torneos)): ?>
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Temporada</th>
                            <th>Prize Pool</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($torneos as $tor): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($tor['nombre']) ?></strong></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($tor['tipo'] ?? 'N/A') ?></span></td>
                                <td><?= htmlspecialchars($tor['temporada'] ?? 'N/A') ?></td>
                                <td class="text-success">
                                    <?= $tor['prize_pool']
                                        ? '$' . number_format($tor['prize_pool'], 0, ',', '.')
                                        : 'N/A' ?>
                                </td>
                                <td>
                                    <a href="/RLCS/CRM/pages/torneos/detalle.php?id=<?= $tor['id_torneo'] ?>"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted p-3 mb-0">No se encontraron torneos.</p>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
