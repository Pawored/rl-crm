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

$por_pagina = 15;
$pagina     = max(1, intval($_GET['pagina'] ?? 1));
$offset     = ($pagina - 1) * $por_pagina;

$total_reg    = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM REGION"))[0];
$total_paginas = max(1, ceil($total_reg / $por_pagina));

$regiones = mysqli_query($conexion,
    "SELECT r.*, COUNT(e.id_equipo) AS num_equipos
     FROM REGION r
     LEFT JOIN EQUIPO e ON e.id_region = r.id_region
     GROUP BY r.id_region
     ORDER BY r.nombre
     LIMIT $offset, $por_pagina"
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

<?php if ($total_paginas > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina-1 ?>">&laquo;</a>
        </li>
        <?php for ($i = max(1,$pagina-2); $i <= min($total_paginas,$pagina+2); $i++): ?>
        <li class="page-item <?= $i===$pagina?'active':'' ?>">
            <a class="page-link <?= $i===$pagina?'bg-accent border-accent':'bg-dark text-white border-secondary' ?>"
               href="?pagina=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $pagina>=$total_paginas?'disabled':'' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina+1 ?>">&raquo;</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
