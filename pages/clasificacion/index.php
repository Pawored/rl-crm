<?php
/**
 * CLASIFICACIÓN - Tabla de puntos RLCS
 * Usa vista_clasificacion con filtros por temporada y región.
 * Top 3 con colores oro, plata, bronce.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener temporadas para el filtro ---
$sql_temporadas = "SELECT id_temporada, anio FROM TEMPORADA ORDER BY anio DESC";
$res_temporadas = mysqli_query($conexion, $sql_temporadas);

// --- Obtener regiones para el filtro ---
$sql_regiones = "SELECT id_region, nombre, siglas FROM REGION ORDER BY nombre";
$res_regiones = mysqli_query($conexion, $sql_regiones);

// --- Filtros ---
$filtro_temporada = isset($_GET['temporada']) ? intval($_GET['temporada']) : 0;
$filtro_region    = isset($_GET['region']) ? intval($_GET['region']) : 0;

// Si no se selecciona temporada, usar la más reciente
if ($filtro_temporada == 0) {
    $sql_ultima = "SELECT id_temporada FROM TEMPORADA ORDER BY anio DESC LIMIT 1";
    $res_ultima = mysqli_query($conexion, $sql_ultima);
    if ($res_ultima && mysqli_num_rows($res_ultima) > 0) {
        $filtro_temporada = mysqli_fetch_assoc($res_ultima)['id_temporada'];
    }
}

// --- Construir consulta de clasificación ---
$where_parts = [];
if ($filtro_temporada > 0) {
    $where_parts[] = "vc.id_temporada = $filtro_temporada";
}
if ($filtro_region > 0) {
    $where_parts[] = "e.id_region = $filtro_region";
}

$where = !empty($where_parts) ? "WHERE " . implode(' AND ', $where_parts) : "";

$sql = "SELECT vc.*, e.id_region, r.nombre AS region, r.siglas AS region_siglas
        FROM vista_clasificacion vc
        LEFT JOIN EQUIPO e ON vc.id_equipo = e.id_equipo
        LEFT JOIN REGION r ON e.id_region = r.id_region
        $where
        ORDER BY vc.puntos_totales DESC";
$res_clasificacion = mysqli_query($conexion, $sql);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<h2 class="text-white mb-4">
    <i class="bi bi-trophy"></i> Clasificación RLCS
</h2>

<!-- ========== FILTROS ========== -->
<div class="row mb-4">
    <div class="col-md-12">
        <form method="GET" class="d-flex gap-3 flex-wrap">
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
            <!-- Filtro por región -->
            <div class="flex-fill">
                <select name="region" class="form-select bg-dark text-white border-secondary"
                        onchange="this.form.submit()">
                    <option value="0">Todas las regiones</option>
                    <?php
                    mysqli_data_seek($res_regiones, 0);
                    while ($region = mysqli_fetch_assoc($res_regiones)):
                    ?>
                        <option value="<?= $region['id_region'] ?>"
                                <?= ($filtro_region == $region['id_region']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($region['nombre']) ?>
                            (<?= htmlspecialchars($region['siglas']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- ========== TABLA DE CLASIFICACIÓN ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th class="text-center" style="width: 60px;">Pos</th>
                <th>Equipo</th>
                <th>Tag</th>
                <th>Región</th>
                <th class="text-center">Regionals</th>
                <th class="text-center">Majors</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_clasificacion && mysqli_num_rows($res_clasificacion) > 0): ?>
                <?php $posicion = 1; ?>
                <?php while ($fila = mysqli_fetch_assoc($res_clasificacion)): ?>
                    <?php
                    // Clases especiales para top 3
                    $fila_class = match($posicion) {
                        1 => 'table-row-gold',
                        2 => 'table-row-silver',
                        3 => 'table-row-bronze',
                        default => ''
                    };
                    $pos_class = match($posicion) {
                        1 => 'text-warning fw-bold fs-5',
                        2 => 'text-secondary fw-bold fs-5',
                        3 => 'text-bronze fw-bold fs-5',
                        default => 'text-white'
                    };
                    $icono = match($posicion) {
                        1 => '<i class="bi bi-trophy-fill text-warning"></i>',
                        2 => '<i class="bi bi-trophy-fill text-secondary"></i>',
                        3 => '<i class="bi bi-trophy-fill text-bronze"></i>',
                        default => ''
                    };
                    ?>
                    <tr class="<?= $fila_class ?>">
                        <td class="text-center">
                            <span class="<?= $pos_class ?>"><?= $icono ?> <?= $posicion ?>º</span>
                        </td>
                        <td>
                            <a href="/RLCS/CRM/pages/equipos/detalle.php?id=<?= $fila['id_equipo'] ?>"
                               class="text-accent text-decoration-none fw-bold">
                                <?= htmlspecialchars($fila['nombre'] ?? '') ?>
                            </a>
                        </td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($fila['tag'] ?? '') ?></span></td>
                        <td><?= htmlspecialchars($fila['region'] ?? 'N/A') ?></td>
                        <td class="text-center"><?= $fila['puntos_regionals'] ?? 0 ?></td>
                        <td class="text-center"><?= $fila['puntos_majors'] ?? 0 ?></td>
                        <td class="text-center">
                            <strong class="text-accent fs-5"><?= $fila['puntos_totales'] ?? 0 ?></strong>
                        </td>
                    </tr>
                    <?php $posicion++; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No hay datos de clasificación para estos filtros.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
