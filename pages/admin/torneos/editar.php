<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$es_edicion = ($id > 0);
$torneo = ['nombre' => '', 'tipo' => 'regional', 'id_temporada' => '', 'prize_pool' => ''];
$error = '';

if ($es_edicion) {
    $res = mysqli_query($conexion, "SELECT * FROM TORNEO WHERE id_torneo = $id");
    $torneo = mysqli_fetch_assoc($res);
    if (!$torneo) {
        $_SESSION['mensaje_error'] = "Torneo no encontrado.";
        header("Location: /RLCS/CRM/pages/admin/torneos/index.php");
        exit();
    }
}

$temporadas = mysqli_query($conexion, "SELECT id_temporada, anio FROM TEMPORADA ORDER BY anio DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
    $tipo        = mysqli_real_escape_string($conexion, trim($_POST['tipo'] ?? ''));
    $id_temp     = intval($_POST['id_temporada'] ?? 0);
    $prize_pool  = !empty($_POST['prize_pool']) ? floatval($_POST['prize_pool']) : null;

    if (empty($nombre) || empty($tipo) || $id_temp <= 0) {
        $error = "Nombre, tipo y temporada son obligatorios.";
    } else {
        $prize_sql = $prize_pool !== null ? $prize_pool : 'NULL';

        if ($es_edicion) {
            $sql = "UPDATE TORNEO SET nombre='$nombre', tipo='$tipo',
                    id_temporada=$id_temp, prize_pool=$prize_sql
                    WHERE id_torneo=$id";
        } else {
            $sql = "INSERT INTO TORNEO (nombre, tipo, id_temporada, prize_pool)
                    VALUES ('$nombre', '$tipo', $id_temp, $prize_sql)";
        }

        if (mysqli_query($conexion, $sql)) {
            $_SESSION['mensaje_exito'] = $es_edicion ? "Torneo actualizado." : "Torneo creado.";
            header("Location: /RLCS/CRM/pages/admin/torneos/index.php");
            exit();
        } else {
            $error = "Error al guardar: " . mysqli_error($conexion);
        }
    }

    $torneo['nombre']       = $_POST['nombre'] ?? '';
    $torneo['tipo']         = $_POST['tipo'] ?? '';
    $torneo['id_temporada'] = $_POST['id_temporada'] ?? '';
    $torneo['prize_pool']   = $_POST['prize_pool'] ?? '';
}

$tipos_disponibles = ['regional', 'major', 'lan', 'qualifier', 'invitational', 'otro'];

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-award"></i>
        <?= $es_edicion ? 'Editar Torneo' : 'Nuevo Torneo' ?>
    </h2>
    <a href="/RLCS/CRM/pages/admin/torneos/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card bg-dark border-secondary" style="max-width: 650px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">Nombre del Torneo *</label>
                <input type="text" class="form-control bg-dark text-white border-secondary"
                       name="nombre" required
                       value="<?= htmlspecialchars($torneo['nombre']) ?>"
                       placeholder="Ej: RLCS 2023-24 Major 1">
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-white">Tipo *</label>
                    <select class="form-select bg-dark text-white border-secondary" name="tipo" required>
                        <?php foreach ($tipos_disponibles as $tipo): ?>
                        <option value="<?= $tipo ?>"
                                <?= ($torneo['tipo'] === $tipo) ? 'selected' : '' ?>>
                            <?= ucfirst($tipo) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Temporada *</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            name="id_temporada" required>
                        <option value="">-- Seleccionar --</option>
                        <?php
                        mysqli_data_seek($temporadas, 0);
                        while ($t = mysqli_fetch_assoc($temporadas)):
                        ?>
                        <option value="<?= $t['id_temporada'] ?>"
                                <?= ($torneo['id_temporada'] == $t['id_temporada']) ? 'selected' : '' ?>>
                            <?= $t['anio'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Prize Pool ($)</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           name="prize_pool" min="0" step="0.01"
                           value="<?= htmlspecialchars($torneo['prize_pool'] ?? '') ?>"
                           placeholder="Ej: 250000">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg"></i> <?= $es_edicion ? 'Guardar Cambios' : 'Crear Torneo' ?>
                </button>
                <a href="/RLCS/CRM/pages/admin/torneos/index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
