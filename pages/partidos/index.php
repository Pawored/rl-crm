<?php
/**
 * PARTIDOS - Listado de partidos
 * Tabla con fecha, equipos, resultado, ganador y torneo.
 * Filtro por torneo. Ganador resaltado en verde. Paginación.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener torneos para el filtro ---
$sql_torneos = "SELECT id_torneo, nombre FROM TORNEO ORDER BY nombre";
$res_torneos = mysqli_query($conexion, $sql_torneos);

// --- Filtro por torneo ---
$filtro_torneo = isset($_GET['torneo']) ? intval($_GET['torneo']) : 0;

// --- Paginación ---
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// --- WHERE para filtro ---
$where = "";
if ($filtro_torneo > 0) {
    $where = "WHERE p.id_torneo = $filtro_torneo";
}

// --- Total para paginación ---
$sql_total = "SELECT COUNT(*) as total FROM PARTIDO p $where";
$res_total = mysqli_query($conexion, $sql_total);
$total_registros = mysqli_fetch_assoc($res_total)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// --- Obtener partidos ---
$sql = "SELECT p.id_partido, p.fecha_hora, p.formato, p.id_ganador,
               e1.nombre AS equipo1, e1.tag AS tag1, e1.id_equipo AS id_eq1,
               e2.nombre AS equipo2, e2.tag AS tag2, e2.id_equipo AS id_eq2,
               g.nombre AS ganador, g.tag AS tag_ganador,
               t.nombre AS torneo
        FROM PARTIDO p
        INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
        INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
        LEFT JOIN EQUIPO g ON p.id_ganador = g.id_equipo
        LEFT JOIN TORNEO t ON p.id_torneo = t.id_torneo
        $where
        ORDER BY p.fecha_hora DESC
        LIMIT $offset, $por_pagina";
$res_partidos = mysqli_query($conexion, $sql);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-joystick"></i> Partidos
    </h2>
    <?php if (tieneRol(['admin', 'editor'])): ?>
        <a href="/RLCS/CRM/pages/partidos/registrar.php" class="btn btn-accent">
            <i class="bi bi-plus-circle"></i> Registrar Resultado
        </a>
    <?php endif; ?>
</div>

<!-- ========== FILTRO POR TORNEO ========== -->
<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET">
            <select name="torneo" class="form-select bg-dark text-white border-secondary"
                    onchange="this.form.submit()">
                <option value="0">Todos los torneos</option>
                <?php
                mysqli_data_seek($res_torneos, 0);
                while ($torneo = mysqli_fetch_assoc($res_torneos)):
                ?>
                    <option value="<?= $torneo['id_torneo'] ?>"
                            <?= ($filtro_torneo == $torneo['id_torneo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($torneo['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
</div>

<!-- ========== TABLA DE PARTIDOS ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Equipo 1</th>
                <th class="text-center">VS</th>
                <th>Equipo 2</th>
                <th>Ganador</th>
                <th>Formato</th>
                <th>Torneo</th>
                <?php if (tieneRol(['admin', 'editor'])): ?>
                    <th class="text-center">Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_partidos && mysqli_num_rows($res_partidos) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($res_partidos)): ?>
                    <tr>
                        <td class="text-muted">
                            <?= $p['fecha_hora']
                                ? date('d/m/Y H:i', strtotime($p['fecha_hora']))
                                : 'Sin fecha' ?>
                        </td>
                        <td>
                            <span class="<?= ($p['id_ganador'] == $p['id_eq1']) ? 'text-success fw-bold' : '' ?>">
                                <?= htmlspecialchars($p['tag1']) ?> - <?= htmlspecialchars($p['equipo1']) ?>
                            </span>
                        </td>
                        <td class="text-center text-muted">vs</td>
                        <td>
                            <span class="<?= ($p['id_ganador'] == $p['id_eq2']) ? 'text-success fw-bold' : '' ?>">
                                <?= htmlspecialchars($p['tag2']) ?> - <?= htmlspecialchars($p['equipo2']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['ganador']): ?>
                                <span class="text-success fw-bold">
                                    <i class="bi bi-trophy-fill"></i>
                                    <?= htmlspecialchars($p['tag_ganador']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($p['formato'] ?? 'N/A') ?></small></td>
                        <td><small><?= htmlspecialchars($p['torneo'] ?? 'N/A') ?></small></td>
                        <?php if (tieneRol(['admin', 'editor'])): ?>
                            <td class="text-center">
                                <?php if (!$p['id_ganador']): ?>
                                    <a href="/RLCS/CRM/pages/partidos/registrar.php?id=<?= $p['id_partido'] ?>"
                                       class="btn btn-sm btn-outline-success" title="Registrar resultado">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No se encontraron partidos.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========== PAGINACIÓN ========== -->
<?php if ($total_paginas > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina - 1 ?>&torneo=<?= $filtro_torneo ?>">
                &laquo; Anterior
            </a>
        </li>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                <a class="page-link <?= ($pagina == $i) ? 'bg-accent border-accent' : 'bg-dark text-white border-secondary' ?>"
                   href="?pagina=<?= $i ?>&torneo=<?= $filtro_torneo ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina + 1 ?>&torneo=<?= $filtro_torneo ?>">
                Siguiente &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
