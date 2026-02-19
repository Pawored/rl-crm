<?php
/**
 * JUGADORES - Listado principal
 * Tabla con vista_stats_totales: nickname, país, equipo actual,
 * goles, partidos, MVPs. Buscador, filtro por país y paginación.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener países únicos para el filtro ---
$sql_paises = "SELECT DISTINCT pais FROM JUGADOR WHERE pais IS NOT NULL AND pais != '' ORDER BY pais";
$res_paises = mysqli_query($conexion, $sql_paises);

// --- Filtro por país ---
$filtro_pais = isset($_GET['pais']) ? mysqli_real_escape_string($conexion, $_GET['pais']) : '';

// --- Paginación ---
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// --- Construir WHERE para filtro ---
$where = "";
if (!empty($filtro_pais)) {
    $where = "WHERE j.pais = '$filtro_pais'";
}

// --- Contar total de jugadores (para paginación) ---
$sql_total = "SELECT COUNT(*) as total FROM JUGADOR j $where";
$res_total = mysqli_query($conexion, $sql_total);
$total_registros = mysqli_fetch_assoc($res_total)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// --- Obtener jugadores con stats ---
// Usamos LEFT JOIN para incluir jugadores sin estadísticas
$sql = "SELECT j.id_jugador, j.nickname, j.pais,
               -- Equipo actual (roster sin fecha_fin)
               eq.nombre AS equipo_actual, eq.tag AS tag_equipo,
               -- Stats totales
               COALESCE(SUM(est.goles), 0) AS total_goles,
               COUNT(DISTINCT est.id_partido) AS total_partidos,
               COALESCE(SUM(est.mvp), 0) AS total_mvps
        FROM JUGADOR j
        LEFT JOIN ROSTER ro ON j.id_jugador = ro.id_jugador AND ro.fecha_fin IS NULL
        LEFT JOIN EQUIPO eq ON ro.id_equipo = eq.id_equipo
        LEFT JOIN ESTADISTICAS_JUGADOR est ON j.id_jugador = est.id_jugador
        $where
        GROUP BY j.id_jugador, j.nickname, j.pais, eq.nombre, eq.tag
        ORDER BY j.nickname ASC
        LIMIT $offset, $por_pagina";
$res_jugadores = mysqli_query($conexion, $sql);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO Y BOTÓN AÑADIR ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-person-badge"></i> Jugadores
    </h2>
    <?php if (tieneRol(['admin', 'editor'])): ?>
        <a href="/RLCS/CRM/pages/jugadores/editar.php" class="btn btn-accent">
            <i class="bi bi-plus-circle"></i> Añadir Jugador
        </a>
    <?php endif; ?>
</div>

<!-- ========== FILTROS ========== -->
<div class="row mb-3">
    <!-- Buscador por nickname -->
    <div class="col-md-6 mb-2">
        <input type="text" id="buscadorJugadores"
               class="form-control bg-dark text-white border-secondary"
               placeholder="Buscar por nickname...">
    </div>
    <!-- Filtro por país -->
    <div class="col-md-6 mb-2">
        <form method="GET" class="d-flex">
            <select name="pais" class="form-select bg-dark text-white border-secondary me-2"
                    onchange="this.form.submit()">
                <option value="">Todos los países</option>
                <?php
                mysqli_data_seek($res_paises, 0);
                while ($pais = mysqli_fetch_assoc($res_paises)):
                ?>
                    <option value="<?= htmlspecialchars($pais['pais']) ?>"
                            <?= ($filtro_pais == $pais['pais']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pais['pais']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
</div>

<!-- ========== TABLA DE JUGADORES ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover" id="tablaJugadores">
        <thead>
            <tr>
                <th>Nickname</th>
                <th>País</th>
                <th>Equipo Actual</th>
                <th class="text-center">Goles</th>
                <th class="text-center">Partidos</th>
                <th class="text-center">MVPs</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_jugadores && mysqli_num_rows($res_jugadores) > 0): ?>
                <?php while ($jug = mysqli_fetch_assoc($res_jugadores)): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($jug['nickname']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($jug['pais'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($jug['equipo_actual']): ?>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($jug['tag_equipo']) ?>
                                </span>
                                <?= htmlspecialchars($jug['equipo_actual']) ?>
                            <?php else: ?>
                                <span class="text-muted">Sin equipo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $jug['total_goles'] ?></td>
                        <td class="text-center"><?= $jug['total_partidos'] ?></td>
                        <td class="text-center">
                            <?php if ($jug['total_mvps'] > 0): ?>
                                <span class="text-warning">
                                    <i class="bi bi-star-fill"></i> <?= $jug['total_mvps'] ?>
                                </span>
                            <?php else: ?>
                                0
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="/RLCS/CRM/pages/jugadores/detalle.php?id=<?= $jug['id_jugador'] ?>"
                               class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (tieneRol(['admin', 'editor'])): ?>
                                <a href="/RLCS/CRM/pages/jugadores/editar.php?id=<?= $jug['id_jugador'] ?>"
                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No se encontraron jugadores.
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
               href="?pagina=<?= $pagina - 1 ?>&pais=<?= urlencode($filtro_pais) ?>">
                &laquo; Anterior
            </a>
        </li>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                <a class="page-link <?= ($pagina == $i) ? 'bg-accent border-accent' : 'bg-dark text-white border-secondary' ?>"
                   href="?pagina=<?= $i ?>&pais=<?= urlencode($filtro_pais) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina + 1 ?>&pais=<?= urlencode($filtro_pais) ?>">
                Siguiente &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- ========== SCRIPT: Buscador en tiempo real ========== -->
<script>
document.getElementById('buscadorJugadores').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaJugadores tbody tr');
    filas.forEach(function(fila) {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
