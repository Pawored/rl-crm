<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$error = '';
$id_torneo = intval($_GET['torneo'] ?? $_POST['id_torneo'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion    = $_POST['accion'] ?? '';
    $id_torneo = intval($_POST['id_torneo'] ?? 0);

    if ($accion === 'agregar') {
        $tipo_bracket = mysqli_real_escape_string($conexion, trim($_POST['tipo_bracket'] ?? ''));
        $ronda        = mysqli_real_escape_string($conexion, trim($_POST['ronda'] ?? ''));
        $fase         = mysqli_real_escape_string($conexion, trim($_POST['fase'] ?? ''));

        if ($id_torneo <= 0 || empty($tipo_bracket) || empty($ronda) || empty($fase)) {
            $error = "Todos los campos son obligatorios.";
        } else {
            $sql = "INSERT INTO BRACKET (id_torneo, tipo_bracket, ronda, fase)
                    VALUES ($id_torneo, '$tipo_bracket', '$ronda', '$fase')";
            if (mysqli_query($conexion, $sql)) {
                $_SESSION['mensaje_exito'] = "Entrada de bracket añadida.";
            } else {
                $error = "Error: " . mysqli_error($conexion);
            }
        }
    }

    if ($accion === 'eliminar') {
        $id_bracket = intval($_POST['id_bracket'] ?? 0);
        mysqli_query($conexion, "DELETE FROM BRACKET WHERE id_bracket=$id_bracket");
        $_SESSION['mensaje_exito'] = "Entrada eliminada.";
    }

    if (empty($error)) {
        header("Location: /RLCS/CRM/pages/admin/bracket/gestionar.php?torneo=$id_torneo");
        exit();
    }
}

$torneos = mysqli_query($conexion,
    "SELECT t.id_torneo, t.nombre, t.tipo, temp.anio
     FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     ORDER BY temp.anio DESC, t.nombre"
);

$bracket = [];
$torneo_info = null;
if ($id_torneo > 0) {
    $res_info = mysqli_query($conexion,
        "SELECT t.nombre, t.tipo, temp.anio
         FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
         WHERE t.id_torneo = $id_torneo"
    );
    $torneo_info = mysqli_fetch_assoc($res_info);

    $res_b = mysqli_query($conexion,
        "SELECT * FROM BRACKET WHERE id_torneo=$id_torneo ORDER BY id_bracket"
    );
    while ($row = mysqli_fetch_assoc($res_b)) {
        $bracket[] = $row;
    }
}

$tipos_bracket = ['Grupos', 'Eliminación directa', 'Doble eliminación', 'Swiss', 'Round Robin', 'Otro'];
$rondas        = ['Ronda 1', 'Ronda 2', 'Ronda 3', 'Cuartos de final', 'Semifinal', 'Final', 'Gran Final', 'Otro'];
$fases         = ['Fase de grupos', 'Fase eliminatoria', 'Upper bracket', 'Lower bracket', 'Grand Final', 'Otro'];

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-diagram-3-fill"></i> Bracket de Torneos</h2>
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
                        — <?= ucfirst($t['tipo']) ?>
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

<!-- Añadir entrada -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header border-secondary">
        <h6 class="mb-0 text-accent">
            <i class="bi bi-plus-circle"></i> Añadir Entrada —
            <?= htmlspecialchars($torneo_info['nombre']) ?>
            <span class="badge bg-secondary ms-1"><?= $torneo_info['anio'] ?></span>
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_torneo" value="<?= $id_torneo ?>">
            <div class="col-md-4">
                <label class="form-label text-white small">Tipo de Bracket</label>
                <select class="form-select bg-dark text-white border-secondary" name="tipo_bracket" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($tipos_bracket as $tb): ?>
                    <option value="<?= htmlspecialchars($tb) ?>"><?= htmlspecialchars($tb) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small">Ronda</label>
                <select class="form-select bg-dark text-white border-secondary" name="ronda" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($rondas as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small">Fase</label>
                <select class="form-select bg-dark text-white border-secondary" name="fase" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($fases as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-plus-lg"></i> Añadir Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de entradas -->
<h6 class="text-white mb-3">
    <i class="bi bi-list-ol"></i> Entradas del Bracket
    <span class="badge bg-secondary ms-2"><?= count($bracket) ?></span>
</h6>

<?php if (!empty($bracket)): ?>
<div class="table-responsive">
    <table class="table table-dark table-hover align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo de Bracket</th>
                <th>Ronda</th>
                <th>Fase</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($bracket as $i => $b): ?>
        <tr>
            <td class="text-muted small"><?= $i + 1 ?></td>
            <td>
                <span class="badge bg-info bg-opacity-25 text-info">
                    <?= htmlspecialchars($b['tipo_bracket']) ?>
                </span>
            </td>
            <td class="text-white"><?= htmlspecialchars($b['ronda']) ?></td>
            <td class="text-muted"><?= htmlspecialchars($b['fase']) ?></td>
            <td class="text-center">
                <form method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar esta entrada del bracket?')">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_bracket" value="<?= $b['id_bracket'] ?>">
                    <input type="hidden" name="id_torneo" value="<?= $id_torneo ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <div class="alert alert-secondary text-muted">
        <i class="bi bi-info-circle"></i> No hay entradas de bracket para este torneo todavía.
    </div>
<?php endif; ?>

<?php elseif ($id_torneo > 0): ?>
    <div class="alert alert-warning">Torneo no encontrado.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
