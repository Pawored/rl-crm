<?php
/**
 * EQUIPOS - Listado principal
 * Tabla con todos los equipos: nombre, tag, región, estado.
 * Incluye buscador en tiempo real, filtro por región y paginación.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// === LÓGICA PHP ===

// --- Obtener regiones para el filtro ---
$sql_regiones = "SELECT id_region, nombre, siglas FROM REGION ORDER BY nombre";
$res_regiones = mysqli_query($conexion, $sql_regiones);

// --- Filtro por región (si se selecciona) ---
$filtro_region = isset($_GET['region']) ? intval($_GET['region']) : 0;

// --- Paginación ---
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// --- Construir consulta con filtro opcional ---
$where = "";
if ($filtro_region > 0) {
    $where = "WHERE e.id_region = $filtro_region";
}

// Contar total para paginación
$sql_total = "SELECT COUNT(*) as total FROM EQUIPO e $where";
$res_total = mysqli_query($conexion, $sql_total);
$total_registros = mysqli_fetch_assoc($res_total)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Obtener equipos con región
$sql = "SELECT e.id_equipo, e.nombre, e.tag, e.activo,
               r.nombre AS region, r.siglas AS region_siglas
        FROM EQUIPO e
        LEFT JOIN REGION r ON e.id_region = r.id_region
        $where
        ORDER BY e.nombre ASC
        LIMIT $offset, $por_pagina";
$res_equipos = mysqli_query($conexion, $sql);

// --- Procesar eliminación (solo admin) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id']) && tieneRol('admin')) {
    $id_eliminar = intval($_POST['eliminar_id']);
    $sql_del = "DELETE FROM EQUIPO WHERE id_equipo = $id_eliminar";
    if (mysqli_query($conexion, $sql_del)) {
        $_SESSION['mensaje_exito'] = "Equipo eliminado correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al eliminar el equipo. Puede tener datos relacionados.";
    }
    header("Location: /RLCS/CRM/pages/equipos/index.php");
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO Y BOTÓN AÑADIR ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-people-fill"></i> Equipos
    </h2>
    <?php if (tieneRol(['admin', 'editor'])): ?>
        <a href="/RLCS/CRM/pages/equipos/editar.php" class="btn btn-accent">
            <i class="bi bi-plus-circle"></i> Añadir Equipo
        </a>
    <?php endif; ?>
</div>

<!-- ========== FILTROS ========== -->
<div class="row mb-3">
    <!-- Buscador en tiempo real -->
    <div class="col-md-6 mb-2">
        <input type="text" id="buscadorEquipos"
               class="form-control bg-dark text-white border-secondary"
               placeholder="Buscar por nombre o tag...">
    </div>
    <!-- Filtro por región -->
    <div class="col-md-6 mb-2">
        <form method="GET" class="d-flex">
            <select name="region" class="form-select bg-dark text-white border-secondary me-2"
                    onchange="this.form.submit()">
                <option value="0">Todas las regiones</option>
                <?php
                // Reiniciar el cursor del resultado de regiones
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
        </form>
    </div>
</div>

<!-- ========== TABLA DE EQUIPOS ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover" id="tablaEquipos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tag</th>
                <th>Región</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_equipos && mysqli_num_rows($res_equipos) > 0): ?>
                <?php while ($equipo = mysqli_fetch_assoc($res_equipos)): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($equipo['nombre']) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($equipo['tag']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($equipo['region'] ?? 'Sin región') ?></td>
                        <td>
                            <?php if ($equipo['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Botón ver (todos los roles) -->
                            <a href="/RLCS/CRM/pages/equipos/detalle.php?id=<?= $equipo['id_equipo'] ?>"
                               class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (tieneRol(['admin', 'editor'])): ?>
                                <!-- Botón editar (admin y editor) -->
                                <a href="/RLCS/CRM/pages/equipos/editar.php?id=<?= $equipo['id_equipo'] ?>"
                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (tieneRol('admin')): ?>
                                <!-- Botón eliminar (solo admin) con modal de confirmación -->
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        title="Eliminar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminar<?= $equipo['id_equipo'] ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <!-- Modal de confirmación -->
                                <div class="modal fade" id="modalEliminar<?= $equipo['id_equipo'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark text-white">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle text-danger"></i>
                                                    Confirmar Eliminación
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que quieres eliminar el equipo
                                                <strong><?= htmlspecialchars($equipo['nombre']) ?></strong>?
                                                <br><small class="text-danger">Esta acción no se puede deshacer.</small>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="eliminar_id"
                                                           value="<?= $equipo['id_equipo'] ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No se encontraron equipos.
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
        <!-- Botón Anterior -->
        <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina - 1 ?>&region=<?= $filtro_region ?>">
                &laquo; Anterior
            </a>
        </li>
        <!-- Números de página -->
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                <a class="page-link <?= ($pagina == $i) ? 'bg-accent border-accent' : 'bg-dark text-white border-secondary' ?>"
                   href="?pagina=<?= $i ?>&region=<?= $filtro_region ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        <!-- Botón Siguiente -->
        <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina + 1 ?>&region=<?= $filtro_region ?>">
                Siguiente &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- ========== SCRIPT: Buscador en tiempo real ========== -->
<script>
// Filtrar filas de la tabla mientras el usuario escribe
document.getElementById('buscadorEquipos').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaEquipos tbody tr');

    filas.forEach(function(fila) {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
