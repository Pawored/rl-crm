<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id_region'] ?? 0);
    if ($id > 0) {
        $res = mysqli_query($conexion, "DELETE FROM REGION WHERE id_region = $id");
        if ($res) {
            $_SESSION['mensaje_exito'] = "Región eliminada.";
        } else {
            $_SESSION['mensaje_error'] = "No se puede eliminar: tiene equipos asociados.";
        }
    }
    header("Location: /RLCS/CRM/pages/admin/regiones/index.php");
    exit();
}

$regiones = mysqli_query($conexion,
    "SELECT r.*, COUNT(e.id_equipo) AS num_equipos
     FROM REGION r
     LEFT JOIN EQUIPO e ON e.id_region = r.id_region
     GROUP BY r.id_region
     ORDER BY r.nombre"
);

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-globe"></i> Regiones</h2>
    <div class="d-flex gap-2">
        <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Admin
        </a>
        <a href="/RLCS/CRM/pages/admin/regiones/editar.php" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Región
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Siglas</th>
                <th>Plazas Mundial</th>
                <th>Equipos</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($regiones && mysqli_num_rows($regiones) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($regiones)): ?>
            <tr>
                <td class="text-white fw-semibold"><?= htmlspecialchars($r['nombre']) ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($r['siglas']) ?></span></td>
                <td><?= $r['plazas_mundial'] ?></td>
                <td><span class="badge bg-info text-dark"><?= $r['num_equipos'] ?></span></td>
                <td class="text-center">
                    <a href="/RLCS/CRM/pages/admin/regiones/editar.php?id=<?= $r['id_region'] ?>"
                       class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if ($r['num_equipos'] == 0): ?>
                    <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalElim<?= $r['id_region'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="modal fade" id="modalElim<?= $r['id_region'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                        Eliminar Región
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Eliminar la región <strong><?= htmlspecialchars($r['nombre']) ?></strong>?
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_region" value="<?= $r['id_region'] ?>">
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
                <td colspan="5" class="text-center text-muted py-5">No hay regiones.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
