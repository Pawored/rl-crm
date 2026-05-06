<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = intval($_POST['id_torneo'] ?? 0);
    if ($id > 0) {
        $res = mysqli_query($conexion, "DELETE FROM TORNEO WHERE id_torneo = $id");
        $_SESSION[$res ? 'mensaje_exito' : 'mensaje_error'] = $res
            ? "Torneo eliminado."
            : "No se puede eliminar: tiene partidos o participaciones asociados.";
    }
    header("Location: /RLCS/CRM/pages/admin/torneos/index.php");
    exit();
}

$filtro_temp = intval($_GET['temporada'] ?? 0);
$filtro_tipo = mysqli_real_escape_string($conexion, $_GET['tipo'] ?? '');

$where = [];
if ($filtro_temp > 0) $where[] = "t.id_temporada = $filtro_temp";
if (!empty($filtro_tipo)) $where[] = "t.tipo = '$filtro_tipo'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$torneos = mysqli_query($conexion,
    "SELECT t.*, temp.anio,
            COUNT(DISTINCT p.id_participacion) AS num_equipos,
            COUNT(DISTINCT par.id_partido) AS num_partidos
     FROM TORNEO t
     JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     LEFT JOIN PARTICIPACION p ON p.id_torneo = t.id_torneo
     LEFT JOIN PARTIDO par ON par.id_torneo = t.id_torneo
     $where_sql
     GROUP BY t.id_torneo
     ORDER BY temp.anio DESC, t.nombre"
);

$temporadas = mysqli_query($conexion, "SELECT id_temporada, anio FROM TEMPORADA ORDER BY anio DESC");
$tipos_res  = mysqli_query($conexion, "SELECT DISTINCT tipo FROM TORNEO ORDER BY tipo");

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-award"></i> Torneos</h2>
    <div class="d-flex gap-2">
        <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Admin
        </a>
        <a href="/RLCS/CRM/pages/admin/torneos/editar.php" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Torneo
        </a>
    </div>
</div>

<!-- Filtros -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <select name="temporada" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="0">Todas las temporadas</option>
            <?php while ($t = mysqli_fetch_assoc($temporadas)): ?>
            <option value="<?= $t['id_temporada'] ?>" <?= $filtro_temp == $t['id_temporada'] ? 'selected' : '' ?>>
                <?= $t['anio'] ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="tipo" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="">Todos los tipos</option>
            <?php while ($ti = mysqli_fetch_assoc($tipos_res)): ?>
            <option value="<?= htmlspecialchars($ti['tipo']) ?>"
                    <?= $filtro_tipo === $ti['tipo'] ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($ti['tipo'])) ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-info">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="/RLCS/CRM/pages/admin/torneos/index.php" class="btn btn-sm btn-outline-secondary ms-1">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Temporada</th>
                <th>Prize Pool</th>
                <th>Equipos</th>
                <th>Partidos</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($torneos && mysqli_num_rows($torneos) > 0): ?>
            <?php while ($t = mysqli_fetch_assoc($torneos)): ?>
            <?php
            $tipo_badge = match(strtolower($t['tipo'])) {
                'major'    => 'bg-danger',
                'regional' => 'bg-primary',
                'lan'      => 'bg-warning text-dark',
                default    => 'bg-secondary'
            };
            ?>
            <tr>
                <td class="text-white"><?= htmlspecialchars($t['nombre']) ?></td>
                <td><span class="badge <?= $tipo_badge ?>"><?= htmlspecialchars(ucfirst($t['tipo'])) ?></span></td>
                <td class="text-muted"><?= $t['anio'] ?></td>
                <td class="text-warning">
                    <?= $t['prize_pool'] ? '$' . number_format($t['prize_pool'], 0, ',', '.') : '—' ?>
                </td>
                <td><span class="badge bg-info text-dark"><?= $t['num_equipos'] ?></span></td>
                <td><span class="badge bg-secondary"><?= $t['num_partidos'] ?></span></td>
                <td class="text-center">
                    <a href="/RLCS/CRM/pages/torneos/detalle.php?id=<?= $t['id_torneo'] ?>"
                       class="btn btn-sm btn-outline-info" title="Ver">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="/RLCS/CRM/pages/admin/torneos/editar.php?id=<?= $t['id_torneo'] ?>"
                       class="btn btn-sm btn-outline-warning" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if ($t['num_equipos'] == 0 && $t['num_partidos'] == 0): ?>
                    <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalElim<?= $t['id_torneo'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="modal fade" id="modalElim<?= $t['id_torneo'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle text-danger"></i> Eliminar Torneo
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Eliminar <strong><?= htmlspecialchars($t['nombre']) ?></strong>?
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_torneo" value="<?= $t['id_torneo'] ?>">
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
                <td colspan="7" class="text-center text-muted py-5">
                    No hay torneos. <a href="/RLCS/CRM/pages/admin/torneos/editar.php">Crear uno</a>.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
