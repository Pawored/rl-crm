<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$error = '';
$id_torneo = intval($_GET['torneo'] ?? $_POST['id_torneo'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $id_torneo_post = intval($_POST['id_torneo'] ?? 0);
        $id_equipo      = intval($_POST['id_equipo'] ?? 0);
        $puntos         = intval($_POST['puntos_ganados'] ?? 0);
        $posicion       = !empty($_POST['posicion_final']) ? intval($_POST['posicion_final']) : 'NULL';

        if ($id_torneo_post <= 0 || $id_equipo <= 0) {
            $error = "Torneo y equipo son obligatorios.";
        } else {
            $check = mysqli_query($conexion,
                "SELECT id_participacion FROM PARTICIPACION
                 WHERE id_torneo = $id_torneo_post AND id_equipo = $id_equipo"
            );
            if (mysqli_num_rows($check) > 0) {
                $error = "Este equipo ya está inscrito en ese torneo.";
            } else {
                $sql = "INSERT INTO PARTICIPACION (id_torneo, id_equipo, puntos_ganados, posicion_final)
                        VALUES ($id_torneo_post, $id_equipo, $puntos, $posicion)";
                if (mysqli_query($conexion, $sql)) {
                    $_SESSION['mensaje_exito'] = "Equipo añadido al torneo.";
                } else {
                    $error = "Error: " . mysqli_error($conexion);
                }
            }
        }
        $id_torneo = $id_torneo_post;
    }

    if ($accion === 'actualizar') {
        $id_part  = intval($_POST['id_participacion'] ?? 0);
        $puntos   = intval($_POST['puntos_ganados'] ?? 0);
        $posicion = !empty($_POST['posicion_final']) ? intval($_POST['posicion_final']) : 'NULL';

        $sql = "UPDATE PARTICIPACION SET puntos_ganados=$puntos, posicion_final=$posicion
                WHERE id_participacion=$id_part";
        if (mysqli_query($conexion, $sql)) {
            $_SESSION['mensaje_exito'] = "Participación actualizada.";
        } else {
            $error = "Error: " . mysqli_error($conexion);
        }
    }

    if ($accion === 'eliminar') {
        $id_part = intval($_POST['id_participacion'] ?? 0);
        mysqli_query($conexion, "DELETE FROM PARTICIPACION WHERE id_participacion=$id_part");
        $_SESSION['mensaje_exito'] = "Equipo eliminado del torneo.";
    }

    header("Location: /RLCS/CRM/pages/admin/participacion/gestionar.php?torneo=$id_torneo");
    exit();
}

$torneos = mysqli_query($conexion,
    "SELECT t.id_torneo, t.nombre, temp.anio
     FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     ORDER BY temp.anio DESC, t.nombre"
);

$participaciones = [];
$torneo_info = null;
if ($id_torneo > 0) {
    $res_info = mysqli_query($conexion,
        "SELECT t.nombre, temp.anio FROM TORNEO t
         JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
         WHERE t.id_torneo = $id_torneo"
    );
    $torneo_info = mysqli_fetch_assoc($res_info);

    $res_part = mysqli_query($conexion,
        "SELECT p.*, e.nombre AS equipo, e.tag
         FROM PARTICIPACION p JOIN EQUIPO e ON e.id_equipo = p.id_equipo
         WHERE p.id_torneo = $id_torneo
         ORDER BY p.posicion_final ASC, e.nombre"
    );
    while ($row = mysqli_fetch_assoc($res_part)) {
        $participaciones[] = $row;
    }
}

// Equipos no inscritos aún en este torneo
$ids_inscritos = array_column($participaciones, 'id_equipo');
$not_in = $ids_inscritos ? 'AND e.id_equipo NOT IN (' . implode(',', $ids_inscritos) . ')' : '';
$equipos_libres = mysqli_query($conexion,
    "SELECT e.id_equipo, e.nombre, e.tag, r.siglas
     FROM EQUIPO e JOIN REGION r ON r.id_region = e.id_region
     WHERE e.activo = 1 $not_in
     ORDER BY r.siglas, e.nombre"
);

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-diagram-3"></i> Participación en Torneos</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Selector de torneo -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label text-white small">Seleccionar Torneo</label>
                <select name="torneo" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar torneo --</option>
                    <?php while ($t = mysqli_fetch_assoc($torneos)): ?>
                    <option value="<?= $t['id_torneo'] ?>" <?= $id_torneo == $t['id_torneo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?> (<?= $t['anio'] ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-info">
                <i class="bi bi-funnel"></i> Ver
            </button>
        </form>
    </div>
</div>

<?php if ($id_torneo > 0 && $torneo_info): ?>

<!-- Añadir equipo -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header border-secondary">
        <h6 class="mb-0 text-accent"><i class="bi bi-plus-circle"></i> Añadir Equipo a <?= htmlspecialchars($torneo_info['nombre']) ?></h6>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_torneo" value="<?= $id_torneo ?>">
            <div class="col-md-5">
                <label class="form-label text-white small">Equipo</label>
                <select class="form-select bg-dark text-white border-secondary" name="id_equipo" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                    $region_actual = '';
                    while ($e = mysqli_fetch_assoc($equipos_libres)):
                        if ($e['siglas'] !== $region_actual) {
                            if ($region_actual !== '') echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($e['siglas']) . '">';
                            $region_actual = $e['siglas'];
                        }
                    ?>
                    <option value="<?= $e['id_equipo'] ?>">
                        <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                    </option>
                    <?php endwhile; if ($region_actual) echo '</optgroup>'; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-white small">Puntos ganados</label>
                <input type="number" class="form-control bg-dark text-white border-secondary"
                       name="puntos_ganados" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label class="form-label text-white small">Posición final</label>
                <input type="number" class="form-control bg-dark text-white border-secondary"
                       name="posicion_final" min="1" placeholder="—">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-accent w-100">
                    <i class="bi bi-plus-lg"></i> Añadir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de participantes -->
<h6 class="text-white mb-3">
    <i class="bi bi-people"></i> Equipos inscritos
    <span class="badge bg-secondary ms-2"><?= count($participaciones) ?></span>
</h6>

<?php if (!empty($participaciones)): ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Posición</th>
                <th>Puntos</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($participaciones as $p): ?>
        <tr>
            <td>
                <strong class="text-white"><?= htmlspecialchars($p['equipo']) ?></strong>
                <span class="badge bg-secondary ms-1"><?= htmlspecialchars($p['tag']) ?></span>
            </td>
            <td>
                <?php if ($p['posicion_final']): ?>
                    <span class="fw-bold <?= $p['posicion_final'] <= 3 ? 'text-warning' : 'text-white' ?>">
                        #<?= $p['posicion_final'] ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
            <td class="text-accent fw-semibold"><?= $p['puntos_ganados'] ?></td>
            <td class="text-center">
                <!-- Editar inline -->
                <button class="btn btn-sm btn-outline-warning"
                        data-bs-toggle="collapse"
                        data-bs-target="#edit<?= $p['id_participacion'] ?>">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" class="d-inline"
                      onsubmit="return confirm('¿Quitar este equipo del torneo?')">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_participacion" value="<?= $p['id_participacion'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>
            </td>
        </tr>
        <tr class="collapse" id="edit<?= $p['id_participacion'] ?>">
            <td colspan="4" class="bg-card">
                <form method="POST" class="d-flex gap-3 align-items-end p-2">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id_participacion" value="<?= $p['id_participacion'] ?>">
                    <div>
                        <label class="form-label text-white small mb-1">Posición</label>
                        <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                               name="posicion_final" min="1" style="width:80px"
                               value="<?= $p['posicion_final'] ?>">
                    </div>
                    <div>
                        <label class="form-label text-white small mb-1">Puntos</label>
                        <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                               name="puntos_ganados" min="0" style="width:100px"
                               value="<?= $p['puntos_ganados'] ?>">
                    </div>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <p class="text-muted">No hay equipos inscritos en este torneo todavía.</p>
<?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
