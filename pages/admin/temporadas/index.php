<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id_temporada'] ?? 0);
    if ($id > 0) {
        $res = mysqli_query($conexion, "DELETE FROM TEMPORADA WHERE id_temporada = $id");
        if ($res) {
            $_SESSION['mensaje_exito'] = "Temporada eliminada.";
        } else {
            $_SESSION['mensaje_error'] = "No se puede eliminar: tiene torneos o datos asociados.";
        }
    }
    header("Location: /RLCS/CRM/pages/admin/temporadas/index.php");
    exit();
}

$temporadas = mysqli_query($conexion,
    "SELECT t.*, COUNT(tor.id_torneo) AS num_torneos
     FROM TEMPORADA t
     LEFT JOIN TORNEO tor ON tor.id_temporada = t.id_temporada
     GROUP BY t.id_temporada
     ORDER BY t.anio DESC"
);

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-calendar3"></i> Temporadas</h2>
    <div class="d-flex gap-2">
        <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Admin
        </a>
        <a href="/RLCS/CRM/pages/admin/temporadas/editar.php" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Temporada
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Año</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Prize Pool</th>
                <th>Torneos</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($temporadas && mysqli_num_rows($temporadas) > 0): ?>
            <?php while ($t = mysqli_fetch_assoc($temporadas)): ?>
            <tr>
                <td><span class="badge bg-accent text-dark fw-bold fs-6"><?= $t['anio'] ?></span></td>
                <td class="text-muted"><?= date('d/m/Y', strtotime($t['fecha_inicio'])) ?></td>
                <td class="text-muted"><?= date('d/m/Y', strtotime($t['fecha_fin'])) ?></td>
                <td class="text-warning">
                    <?= $t['prize_pool'] ? '$' . number_format($t['prize_pool'], 0, ',', '.') : '—' ?>
                </td>
                <td><span class="badge bg-secondary"><?= $t['num_torneos'] ?></span></td>
                <td class="text-center">
                    <a href="/RLCS/CRM/pages/admin/temporadas/editar.php?id=<?= $t['id_temporada'] ?>"
                       class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if ($t['num_torneos'] == 0): ?>
                    <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalElim<?= $t['id_temporada'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="modal fade" id="modalElim<?= $t['id_temporada'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                        Eliminar Temporada
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Eliminar la temporada <strong><?= $t['anio'] ?></strong>?
                                    Esta acción no se puede deshacer.
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_temporada" value="<?= $t['id_temporada'] ?>">
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
                <td colspan="6" class="text-center text-muted py-5">
                    No hay temporadas. <a href="/RLCS/CRM/pages/admin/temporadas/editar.php">Crear una</a>.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
