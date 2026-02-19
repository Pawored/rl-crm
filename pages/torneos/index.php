<?php
/**
 * TORNEOS - Listado principal
 * Tabla con nombre, tipo, temporada y prize_pool.
 * Filtros por tipo y temporada. Paginación.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener tipos únicos de torneo ---
$sql_tipos = "SELECT DISTINCT tipo FROM TORNEO WHERE tipo IS NOT NULL ORDER BY tipo";
$res_tipos = mysqli_query($conexion, $sql_tipos);

// --- Obtener temporadas ---
$sql_temporadas = "SELECT id_temporada, anio FROM TEMPORADA ORDER BY anio DESC";
$res_temporadas = mysqli_query($conexion, $sql_temporadas);

// --- Filtros ---
$filtro_tipo      = isset($_GET['tipo']) ? mysqli_real_escape_string($conexion, $_GET['tipo']) : '';
$filtro_temporada = isset($_GET['temporada']) ? intval($_GET['temporada']) : 0;

// --- Paginación ---
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// --- WHERE ---
$where_parts = [];
if (!empty($filtro_tipo)) {
    $where_parts[] = "t.tipo = '$filtro_tipo'";
}
if ($filtro_temporada > 0) {
    $where_parts[] = "t.id_temporada = $filtro_temporada";
}
$where = !empty($where_parts) ? "WHERE " . implode(' AND ', $where_parts) : "";

// --- Total ---
$sql_total = "SELECT COUNT(*) as total FROM TORNEO t $where";
$res_total = mysqli_query($conexion, $sql_total);
$total_registros = mysqli_fetch_assoc($res_total)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// --- Obtener torneos ---
$sql = "SELECT t.id_torneo, t.nombre, t.tipo, t.prize_pool,
               te.anio AS temporada
        FROM TORNEO t
        LEFT JOIN TEMPORADA te ON t.id_temporada = te.id_temporada
        $where
        ORDER BY te.anio DESC, t.nombre ASC
        LIMIT $offset, $por_pagina";
$res_torneos = mysqli_query($conexion, $sql);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<h2 class="text-white mb-4">
    <i class="bi bi-award"></i> Torneos
</h2>

<!-- ========== FILTROS ========== -->
<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" class="d-flex gap-3 flex-wrap">
            <!-- Filtro por tipo -->
            <div class="flex-fill">
                <select name="tipo" class="form-select bg-dark text-white border-secondary"
                        onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <?php
                    mysqli_data_seek($res_tipos, 0);
                    while ($tipo = mysqli_fetch_assoc($res_tipos)):
                    ?>
                        <option value="<?= htmlspecialchars($tipo['tipo']) ?>"
                                <?= ($filtro_tipo == $tipo['tipo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tipo['tipo']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Filtro por temporada -->
            <div class="flex-fill">
                <select name="temporada" class="form-select bg-dark text-white border-secondary"
                        onchange="this.form.submit()">
                    <option value="0">Todas las temporadas</option>
                    <?php
                    mysqli_data_seek($res_temporadas, 0);
                    while ($temp = mysqli_fetch_assoc($res_temporadas)):
                    ?>
                        <option value="<?= $temp['id_temporada'] ?>"
                                <?= ($filtro_temporada == $temp['id_temporada']) ? 'selected' : '' ?>>
                            Temporada <?= htmlspecialchars($temp['anio']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- ========== TABLA DE TORNEOS ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Temporada</th>
                <th class="text-end">Prize Pool</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_torneos && mysqli_num_rows($res_torneos) > 0): ?>
                <?php while ($torneo = mysqli_fetch_assoc($res_torneos)): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($torneo['nombre']) ?></strong></td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($torneo['tipo'] ?? 'N/A') ?></span></td>
                        <td><?= htmlspecialchars($torneo['temporada'] ?? 'N/A') ?></td>
                        <td class="text-end text-success">
                            <?php if ($torneo['prize_pool']): ?>
                                $<?= number_format($torneo['prize_pool'], 0, ',', '.') ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="/RLCS/CRM/pages/torneos/detalle.php?id=<?= $torneo['id_torneo'] ?>"
                               class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No se encontraron torneos.
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
               href="?pagina=<?= $pagina - 1 ?>&tipo=<?= urlencode($filtro_tipo) ?>&temporada=<?= $filtro_temporada ?>">
                &laquo; Anterior
            </a>
        </li>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                <a class="page-link <?= ($pagina == $i) ? 'bg-accent border-accent' : 'bg-dark text-white border-secondary' ?>"
                   href="?pagina=<?= $i ?>&tipo=<?= urlencode($filtro_tipo) ?>&temporada=<?= $filtro_temporada ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina + 1 ?>&tipo=<?= urlencode($filtro_tipo) ?>&temporada=<?= $filtro_temporada ?>">
                Siguiente &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
