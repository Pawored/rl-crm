<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = intval($_POST['id_partido'] ?? 0);
    if ($id > 0) {
        $res = mysqli_query($conexion, "DELETE FROM PARTIDO WHERE id_partido = $id");
        $_SESSION[$res ? 'mensaje_exito' : 'mensaje_error'] = $res
            ? "Partido eliminado."
            : "No se puede eliminar: tiene juegos o estadísticas asociados.";
    }
    header("Location: /RLCS/CRM/pages/admin/partidos/index.php");
    exit();
}

$filtro_torneo = intval($_GET['torneo'] ?? 0);
$filtro_estado = $_GET['estado'] ?? '';

$where = [];
if ($filtro_torneo > 0) $where[] = "p.id_torneo = $filtro_torneo";
if ($filtro_estado === 'pendiente') $where[] = "p.id_ganador IS NULL";
if ($filtro_estado === 'jugado')    $where[] = "p.id_ganador IS NOT NULL";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$por_pagina = 15;
$pagina     = max(1, intval($_GET['pagina'] ?? 1));
$offset     = ($pagina - 1) * $por_pagina;

$total_res = mysqli_query($conexion, "SELECT COUNT(*) FROM PARTIDO p $where_sql");
$total     = mysqli_fetch_row($total_res)[0];
$paginas   = ceil($total / $por_pagina);

$partidos = mysqli_query($conexion,
    "SELECT p.*,
            e1.nombre AS equipo1, e2.nombre AS equipo2, eg.nombre AS ganador,
            t.nombre AS torneo
     FROM PARTIDO p
     JOIN EQUIPO e1 ON e1.id_equipo = p.id_equipo1
     JOIN EQUIPO e2 ON e2.id_equipo = p.id_equipo2
     LEFT JOIN EQUIPO eg ON eg.id_equipo = p.id_ganador
     JOIN TORNEO t ON t.id_torneo = p.id_torneo
     $where_sql
     ORDER BY p.fecha_hora DESC
     LIMIT $por_pagina OFFSET $offset"
);

$torneos = mysqli_query($conexion,
    "SELECT t.id_torneo, t.nombre, temp.anio
     FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     ORDER BY temp.anio DESC, t.nombre"
);

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-joystick"></i> Partidos</h2>
    <div class="d-flex gap-2">
        <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Admin
        </a>
        <a href="/RLCS/CRM/pages/admin/partidos/crear.php" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Partido
        </a>
    </div>
</div>

<!-- Filtros -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <select name="torneo" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="0">Todos los torneos</option>
            <?php while ($t = mysqli_fetch_assoc($torneos)): ?>
            <option value="<?= $t['id_torneo'] ?>" <?= $filtro_torneo == $t['id_torneo'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nombre']) ?> (<?= $t['anio'] ?>)
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="estado" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="">Todos</option>
            <option value="pendiente" <?= $filtro_estado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
            <option value="jugado"    <?= $filtro_estado === 'jugado'    ? 'selected' : '' ?>>Jugados</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-info">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="/RLCS/CRM/pages/admin/partidos/index.php" class="btn btn-sm btn-outline-secondary ms-1">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Torneo</th>
                <th>Equipo 1</th>
                <th>Equipo 2</th>
                <th>Ganador</th>
                <th>Formato</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($partidos && mysqli_num_rows($partidos) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($partidos)): ?>
            <tr>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($p['fecha_hora'])) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($p['torneo']) ?></td>
                <td class="text-white"><?= htmlspecialchars($p['equipo1']) ?></td>
                <td class="text-white"><?= htmlspecialchars($p['equipo2']) ?></td>
                <td>
                    <?php if ($p['ganador']): ?>
                        <span class="text-success fw-semibold"><?= htmlspecialchars($p['ganador']) ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Pendiente</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($p['formato']) ?></span></td>
                <td class="text-center">
                    <?php if (!$p['id_ganador']): ?>
                    <a href="/RLCS/CRM/pages/partidos/registrar.php"
                       class="btn btn-sm btn-outline-success" title="Registrar resultado">
                        <i class="bi bi-check2-circle"></i>
                    </a>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalElim<?= $p['id_partido'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="modal fade" id="modalElim<?= $p['id_partido'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle text-danger"></i> Eliminar Partido
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Eliminar el partido
                                    <strong><?= htmlspecialchars($p['equipo1']) ?> vs <?= htmlspecialchars($p['equipo2']) ?></strong>?
                                    <br><small class="text-muted">Se borrarán también los juegos y estadísticas asociados si los tiene.</small>
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_partido" value="<?= $p['id_partido'] ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted py-5">No hay partidos.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($paginas > 1): ?>
<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $paginas; $i++): ?>
        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
            <a class="page-link"
               href="?torneo=<?= $filtro_torneo ?>&estado=<?= $filtro_estado ?>&pagina=<?= $i ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
